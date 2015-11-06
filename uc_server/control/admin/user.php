<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: user.php 1166 2014-11-03 01:49:32Z hypowang $
*/

!defined('IN_UC') && exit('Access Denied');

define('UC_USER_CHECK_USERNAME_FAILED', -1);
define('UC_USER_USERNAME_BADWORD', -2);
define('UC_USER_USERNAME_EXISTS', -3);
define('UC_USER_EMAIL_FORMAT_ILLEGAL', -4);
define('UC_USER_EMAIL_ACCESS_ILLEGAL', -5);
define('UC_USER_EMAIL_EXISTS', -6);

define('UC_LOGIN_SUCCEED', 0);
define('UC_LOGIN_ERROR_FOUNDER_PW', -1);
define('UC_LOGIN_ERROR_ADMIN_PW', -2);
define('UC_LOGIN_ERROR_ADMIN_NOT_EXISTS', -3);
define('UC_LOGIN_ERROR_SECCODE', -4);
define('UC_LOGIN_ERROR_FAILEDLOGIN', -5);

class control extends adminbase {

	function __construct() {
		$this->control();
	}

	function control() {
		parent::__construct();
		if(getgpc('a') != 'login' && getgpc('a') != 'logout') {
			if(!$this->user['isfounder'] && !$this->user['allowadminuser']) {
				$this->message('no_permission_for_this_module');
			}
		}
		$this->load('user');
	}

	function onlogin() {
		$authkey = md5(UC_KEY.$_SERVER['HTTP_USER_AGENT'].$this->onlineip);

		$this->load('user');
		$username = getgpc('username', 'P');
		$password = getgpc('password', 'P');
		$iframe	  = getgpc('iframe') ? 1 : 0;

		$isfounder = intval(getgpc('isfounder', 'P'));
		$rand = rand(100000, 999999);
		$seccodeinit = rawurlencode($this->authcode($rand, 'ENCODE', $authkey, 180));
		$errorcode = 0;
		if($this->submitcheck()) {

			if($isfounder == 1) {
				$username = 'UCenterAdministrator';
			}

			$can_do_login = $_ENV['user']->can_do_login($username, $this->onlineip);

			if(!$can_do_login) {
     			$errorcode = UC_LOGIN_ERROR_FAILEDLOGIN;
			} else {
				$seccodehidden = urldecode(getgpc('seccodehidden', 'P'));
				$seccode = strtoupper(getgpc('seccode', 'P'));
				$seccodehidden = $this->authcode($seccodehidden, 'DECODE', $authkey);
				require UC_ROOT.'./lib/seccode.class.php';
				if(!seccode::seccode_check($seccodehidden, $seccode)) {
					$errorcode = UC_LOGIN_ERROR_SECCODE;
				} else {
					$errorcode = UC_LOGIN_SUCCEED;
					$this->user['username'] = $username;
					if($isfounder == 1) {
						$this->user['username'] = 'UCenterAdministrator';
						$md5password =  md5(md5($password).UC_FOUNDERSALT);
						if($md5password == UC_FOUNDERPW) {
							$username = $this->user['username'];
							$this->view->sid = $this->sid_encode($this->user['username']);
						} else {
							$errorcode = UC_LOGIN_ERROR_FOUNDER_PW;
						}
					} else {
						$admin = $this->db->fetch_first("SELECT a.uid,m.username,m.salt,m.password FROM ".UC_DBTABLEPRE."admins a LEFT JOIN ".UC_DBTABLEPRE."members m USING(uid) WHERE a.username='$username'");
						if(!empty($admin)) {
							$md5password =  md5(md5($password).$admin['salt']);
							if($admin['password'] == $md5password) {
								$this->view->sid = $this->sid_encode($admin['username']);
							} else {
								$errorcode = UC_LOGIN_ERROR_ADMIN_PW;
							}
						} else {
							$errorcode = UC_LOGIN_ERROR_ADMIN_NOT_EXISTS;
						}
					}

					if($errorcode == 0) {
						$this->setcookie('sid', $this->view->sid, 86400);
						$pwlen = strlen($password);
						$this->user['admin'] = 1;
						$this->writelog('login', 'succeed');
						if($iframe) {
							header('location: admin.php?m=frame&a=main&iframe=1'.($this->cookie_status ? '' : '&sid='.$this->view->sid));
							exit;
						} else {
							header('location: admin.php'.($this->cookie_status ? '' : '?sid='.$this->view->sid));
							exit;
						}
					} else {
						$this->writelog('login', 'error: user='.$this->user['username'].'; password='.($pwlen > 2 ? preg_replace("/^(.{".round($pwlen / 4)."})(.+?)(.{".round($pwlen / 6)."})$/s", "\\1***\\3", $password) : $password));
						$_ENV['user']->loginfailed($username, $this->onlineip);
					}
				}
			}
		}
		$username = dhtmlspecialchars($username);
		$password = dhtmlspecialchars($password);
		$this->view->assign('seccodeinit', $seccodeinit);
		$this->view->assign('username', $username);
		$this->view->assign('password', $password);
		$this->view->assign('isfounder', $isfounder);
		$this->view->assign('errorcode', $errorcode);
		$this->view->assign('iframe', $iframe);
		$this->view->display('admin_login');
	}

	function onlogout() {
		$this->writelog('logout');
		$this->setcookie('sid', '');
		header('location: admin.php');
	}

	function onadd() {
		if(!$this->submitcheck('submit')) {
			exit;
		}
		$username = getgpc('addname', 'P');
		$password =  getgpc('addpassword', 'P');
		$email = getgpc('addemail', 'P');

		if(($status = $this->_check_username($username)) < 0) {
			if($status == UC_USER_CHECK_USERNAME_FAILED) {
				$this->message('user_add_username_ignore', 'BACK');
			} elseif($status == UC_USER_USERNAME_BADWORD) {
				$this->message('user_add_username_badwords', 'BACK');
			} elseif($status == UC_USER_USERNAME_EXISTS) {
				$this->message('user_add_username_exists', 'BACK');
			}
		}
		if(($status = $this->_check_email($email)) < 0) {
			if($status == UC_USER_EMAIL_FORMAT_ILLEGAL) {
				$this->message('user_add_email_formatinvalid', 'BACK');
			} elseif($status == UC_USER_EMAIL_ACCESS_ILLEGAL) {
				$this->message('user_add_email_ignore', 'BACK');
			} elseif($status == UC_USER_EMAIL_EXISTS) {
				$this->message('user_add_email_exists', 'BACK');
			}
		}
		$uid = $_ENV['user']->add_user($username, $password, $email);
		$this->message('user_add_succeed', 'admin.php?m=user&a=ls');
	}

	function onls() {

		include_once UC_ROOT.'view/default/admin.lang.php';

		$status = 0;
		if(!empty($_POST['addname']) && $this->submitcheck()) {
			$this->check_priv();
			$username = getgpc('addname', 'P');
			$password =  getgpc('addpassword', 'P');
			$email = getgpc('addemail', 'P');

			if(($status = $this->_check_username($username)) >= 0) {
				if(($status = $this->_check_email($email)) >= 0) {
					$_ENV['user']->add_user($username, $password, $email);
					$status = 1;
					$this->writelog('user_add', "username=$username");
				}
			}
		}

		if($this->submitcheck() && !empty($_POST['delete'])) {
			$_ENV['user']->delete_user($_POST['delete']);
			$status = 2;
			$this->writelog('user_delete', "uid=".implode(',', $_POST['delete']));
		}
		$srchname = getgpc('srchname', 'R');
		$srchregdatestart = getgpc('srchregdatestart', 'R');
		$srchregdateend = getgpc('srchregdateend', 'R');
		$srchuid = intval(getgpc('srchuid', 'R'));
		$srchregip = trim(getgpc('srchregip', 'R'));
		$srchemail = trim(getgpc('srchemail', 'R'));

		$sqladd = $urladd = '';
		if($srchname) {
			$sqladd .= " AND username LIKE '$srchname%'";
			$this->view->assign('srchname', $srchname);
		}
		if($srchuid) {
			$sqladd .= " AND uid='$srchuid'";
			$this->view->assign('srchuid', $srchuid);
		}
		if($srchemail) {
			$sqladd .= " AND email='$srchemail'";
			$this->view->assign('srchemail', $srchemail);
		}
		if($srchregdatestart) {
			$urladd .= '&srchregdatestart='.$srchregdatestart;
			$sqladd .= " AND regdate>'".strtotime($srchregdatestart)."'";
			$this->view->assign('srchregdatestart', $srchregdatestart);
		}
		if($srchregdateend) {
			$urladd .= '&srchregdateend='.$srchregdateend;
			$sqladd .= " AND regdate<'".strtotime($srchregdateend)."'";
			$this->view->assign('srchregdateend', $srchregdateend);
		}
		if($srchregip) {
			$urladd .= '&srchregip='.$srchregip;
			$sqladd .= " AND regip='$srchregip'";
			$this->view->assign('srchregip', $srchregip);
		}
		$sqladd = $sqladd ? " WHERE 1 $sqladd" : '';

		$num = $_ENV['user']->get_total_num($sqladd);
		$userlist = $_ENV['user']->get_list($_GET['page'], UC_PPP, $num, $sqladd);
		foreach($userlist as $key => $user) {
			$user['smallavatar'] = '<img src="avatar.php?uid='.$user['uid'].'&size=small">';
			$userlist[$key] = $user;
		}
		$multipage = $this->page($num, UC_PPP, $_GET['page'], 'admin.php?m=user&a=ls&srchname='.$srchname.$urladd);

		$this->_format_userlist($userlist);
		$this->view->assign('userlist', $userlist);
		$adduser = getgpc('adduser');
		$a = getgpc('a');
		$this->view->assign('multipage', $multipage);
		$this->view->assign('adduser', $adduser);
		$this->view->assign('a', $a);
		$this->view->assign('status', $status);
		$this->view->display('admin_user');

	}

	function onedit() {
        
        
        $uid = getgpc('uid');
		$status = 0;
		if(!$this->user['isfounder']) {
			$isprotected = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."protectedmembers WHERE uid = '$uid'");
			if($isprotected) {
				$this->message('user_edit_noperm');
			}
		}

		if($this->submitcheck()) {
			$username = getgpc('username', 'P');
			$newusername = getgpc('newusername', 'P');
			$password = getgpc('password', 'P');
			$email = getgpc('email', 'P');
			$delavatar = getgpc('delavatar', 'P');
			$rmrecques = getgpc('rmrecques', 'P');
			$sqladd = '';
			if($username != $newusername) {
                
				if($_ENV['user']->get_user_by_username($newusername)) {
					$this->message('admin_user_exists');
				}
				$sqladd .= "username='$newusername', ";
				$this->load('note');
				$_ENV['note']->add('renameuser', 'uid='.$uid.'&oldusername='.urlencode($username).'&newusername='.urlencode($newusername));
                
                // add by zhangjh 2015-10-27
                //include_once(UC_ROOT.'../source/plugin/fansclub/function.inc.php');
                $note = 'uid='.$uid.'&oldusername='.urlencode($username).'&newusername='.urlencode($newusername);
                //fansclub_use_log('ucs_renameuser', $_ENV, $note);
                $this->_uc_memberlog('renameuser', $note);
			}
			if($password) {
				$salt = substr(uniqid(rand()), 0, 6);
				$orgpassword = $password;
				$password = md5(md5($password).$salt);
				$sqladd .= "password='$password', salt='$salt', ";
				$this->load('note');
				$_ENV['note']->add('updatepw', 'username='.urlencode($username).'&password=');
                
                // add by zhangjh 2015-10-27
                //include_once(UC_ROOT.'../source/plugin/fansclub/function.inc.php');
                $note = 'username='.urlencode($username).'&password=';
                //fansclub_use_log('ucs_updatepw', $_ENV, $note);
                $this->_uc_memberlog('updatepw', $note);
			}
			if($rmrecques) {
				$sqladd .= "secques='', ";
			}
			if(!empty($delavatar)) {
				$_ENV['user']->delete_useravatar($uid);
			}

			$this->db->query("UPDATE ".UC_DBTABLEPRE."members SET $sqladd email='$email' WHERE uid='$uid'");
			$status = $this->db->errno() ? -1 : 1;
		}
		$user = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."members WHERE uid='$uid'");
		$user['bigavatar'] = '<img src="avatar.php?uid='.$uid.'&size=big">';
		$user['bigavatarreal'] = '<img src="avatar.php?uid='.$uid.'&size=big&type=real">';
		$this->view->assign('uid', $uid);
		$this->view->assign('user', $user);
		$this->view->assign('status', $status);
		$this->view->display('admin_user');
	}


	function _check_username($username) {
		$username = addslashes(trim(stripslashes($username)));
		if(!$_ENV['user']->check_username($username)) {
			return UC_USER_CHECK_USERNAME_FAILED;
		} elseif($_ENV['user']->check_usernameexists($username)) {
			return UC_USER_USERNAME_EXISTS;
		}
		return 1;
	}

	function _check_email($email) {
		if(!$_ENV['user']->check_emailformat($email)) {
			return UC_USER_EMAIL_FORMAT_ILLEGAL;
		} elseif(!$_ENV['user']->check_emailaccess($email)) {
			return UC_USER_EMAIL_ACCESS_ILLEGAL;
		} elseif(!$this->settings['doublee'] && $_ENV['user']->check_emailexists($email)) {
			return UC_USER_EMAIL_EXISTS;
		} else {
			return 1;
		}
	}

	function _format_userlist(&$userlist) {
		if(is_array($userlist)) {
			foreach($userlist AS $key => $user) {
				$userlist[$key]['regdate'] = $this->date($user['regdate']);
			}
		}
	}
    
    function _uc_memberlog($log_type, $uc_server_note)
    {
        /*
        CREATE TABLE `pre_ucenter_memberlog` (
          `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `log_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志时间',
          `log_type` char(20) NOT NULL DEFAULT '' COMMENT '日志类型',
          `g_uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '_G的uid',
          `g_username` char(25) NOT NULL DEFAULT '',
          `g_clientip` char(25) NOT NULL DEFAULT '',
          `uc_server_note` char(255) NOT NULL DEFAULT '',
          `s_server_addr` char(100) NOT NULL DEFAULT '' COMMENT '_SERVER的server_addr',
          `s_server_name` char(100) NOT NULL DEFAULT '',
          `s_http_host` char(100) NOT NULL DEFAULT '',
          `s_request_uri` char(255) NOT NULL DEFAULT '',
          `s_php_self` char(100) NOT NULL DEFAULT '',
          `s_remote_addr` char(25) NOT NULL DEFAULT '',
          `s_http_user_agent` char(255) NOT NULL DEFAULT '',
          `s_http_referer` char(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`log_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='UC用户行为记录表';
        */

        $_tmp_user = $_ENV['user']->base->user;
        $_tmp_ip = $_ENV['user']->base->onlineip;
    
        $arr_data = array();
        $arr_data['log_type'] = $log_type;
        $arr_data['log_time'] = time();
        $arr_data['g_uid'] = $_tmp_user['uid'];
        $arr_data['g_username'] = $_tmp_user['username'];
        $arr_data['g_clientip'] = $_tmp_ip;
        $arr_data['uc_server_note'] = $uc_server_note;
       
        $arr_data['s_server_addr'] = $_SERVER['SERVER_ADDR'];
        $arr_data['s_server_name'] = $_SERVER['SERVER_NAME'];
        $arr_data['s_http_host'] = $_SERVER['HTTP_HOST'];
        $arr_data['s_request_uri'] = $_SERVER['REQUEST_URI'];
        $arr_data['s_php_self'] = $_SERVER['PHP_SELF'];
        $arr_data['s_remote_addr'] = $_SERVER['REMOTE_ADDR'];
        $arr_data['s_http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $arr_data['s_http_referer'] = $_SERVER['HTTP_REFERER'];
        
        $sqladd = '';
        foreach($arr_data as $key => $value)
        {
            $sqladd .= "`".$key."`='".$value."',";
        }
        $sqladd = substr($sqladd, 0, -1);
        $this->db->query("INSERT INTO ".UC_DBTABLEPRE."memberlog SET ".$sqladd);
        $log_id = $this->db->insert_id();
    }


}



?>