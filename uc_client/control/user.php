<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: user.php 1174 2014-11-03 04:38:12Z hypowang $
*/

!defined('IN_UC') && exit('Access Denied');

define('UC_USER_CHECK_USERNAME_FAILED', -1);
define('UC_USER_USERNAME_BADWORD', -2);
define('UC_USER_USERNAME_EXISTS', -3);
define('UC_USER_EMAIL_FORMAT_ILLEGAL', -4);
define('UC_USER_EMAIL_ACCESS_ILLEGAL', -5);
define('UC_USER_EMAIL_EXISTS', -6);

class usercontrol extends base {


	function __construct() {
		$this->usercontrol();
	}

	function usercontrol() {
		parent::__construct();
		$this->load('user');
		$this->app = $this->cache['apps'][UC_APPID];
	}

	function onsynlogin() {
		$this->init_input();
		$uid = $this->input('uid');
		if($this->app['synlogin']) {
			if($this->user = $_ENV['user']->get_user_by_uid($uid)) {
				$synstr = '';
				foreach($this->cache['apps'] as $appid => $app) {
					if($app['synlogin'] && $app['appid'] != $this->app['appid']) {
						$synstr .= '<script type="text/javascript" src="'.$app['url'].'/api/uc.php?time='.$this->time.'&code='.urlencode($this->authcode('action=synlogin&username='.$this->user['username'].'&uid='.$this->user['uid'].'&password='.$this->user['password']."&time=".$this->time, 'ENCODE', $app['authkey'])).'"></script>';
					}
				}
				return $synstr;
			}
		}
		return '';
	}

	function onsynlogout() {
		$this->init_input();
		if($this->app['synlogin']) {
			$synstr = '';
			foreach($this->cache['apps'] as $appid => $app) {
				if($app['synlogin'] && $app['appid'] != $this->app['appid']) {
					$synstr .= '<script type="text/javascript" src="'.$app['url'].'/api/uc.php?time='.$this->time.'&code='.urlencode($this->authcode('action=synlogout&time='.$this->time, 'ENCODE', $app['authkey'])).'"></script>';
				}
			}
			return $synstr;
		}
		return '';
	}

	function onregister() {
		$this->init_input();
		$username = $this->input('username');
		$password =  $this->input('password');
		$email = $this->input('email');
		$questionid = $this->input('questionid');
		$answer = $this->input('answer');
		$regip = $this->input('regip');

		if(($status = $this->_check_username($username)) < 0) {
			return $status;
		}
		if(($status = $this->_check_email($email)) < 0) {
			return $status;
		}
		$uid = $_ENV['user']->add_user($username, $password, $email, 0, $questionid, $answer, $regip);
		return $uid;
	}

	function onedit() {
		$this->init_input();
		$username = $this->input('username');
		$oldpw = $this->input('oldpw');
		$newpw = $this->input('newpw');
		$email = $this->input('email');
		$ignoreoldpw = $this->input('ignoreoldpw');
		$questionid = $this->input('questionid');
		$answer = $this->input('answer');

		if(!$ignoreoldpw && $email && ($status = $this->_check_email($email, $username)) < 0) {
			return $status;
		}
		$status = $_ENV['user']->edit_user($username, $oldpw, $newpw, $email, $ignoreoldpw, $questionid, $answer);

		if($newpw && $status > 0) {
			$this->load('note');
			$_ENV['note']->add('updatepw', 'username='.urlencode($username).'&password=');
			$_ENV['note']->send();
		}
		return $status;
	}

	function onlogin() {
		$this->init_input();
		$isuid = $this->input('isuid');
		$username = $this->input('username');
		$password = $this->input('password');
		$checkques = $this->input('checkques');
		$questionid = $this->input('questionid');
		$answer = $this->input('answer');
		$ip = $this->input('ip');

		$this->settings['login_failedtime'] = is_null($this->settings['login_failedtime']) ? 5 : $this->settings['login_failedtime'];

		if($ip && $this->settings['login_failedtime'] && !$loginperm = $_ENV['user']->can_do_login($username, $ip)) {
			$status = -4;
			return array($status, '', $password, '', 0);
		}

		if($isuid == 1) {
			$user = $_ENV['user']->get_user_by_uid($username);
		} elseif($isuid == 2) {
			$user = $_ENV['user']->get_user_by_email($username);
		} else {
			$user = $_ENV['user']->get_user_by_username($username);
		}

		$passwordmd5 = preg_match('/^\w{32}$/', $password) ? $password : md5($password);
		if(empty($user)) {
			$status = -1;
		} elseif($user['password'] != md5($passwordmd5.$user['salt'])) {
			$status = -2;
		} elseif($checkques && $user['secques'] != $_ENV['user']->quescrypt($questionid, $answer)) {
			$status = -3;
		} else {
			$status = $user['uid'];
		}
		if($ip && $this->settings['login_failedtime'] && $status <= 0) {
			$_ENV['user']->loginfailed($username, $ip);
		}
		$merge = $status != -1 && !$isuid && $_ENV['user']->check_mergeuser($username) ? 1 : 0;
		return array($status, $user['username'], $password, $user['email'], $merge);
	}

	function onlogincheck() {
		$this->init_input();
		$username = $this->input('username');
		$ip = $this->input('ip');
		return $_ENV['user']->can_do_login($username, $ip);
	}

	function oncheck_email() {
		$this->init_input();
		$email = $this->input('email');
		return $this->_check_email($email);
	}

	function oncheck_username() {
		$this->init_input();
		$username = $this->input('username');
		if(($status = $this->_check_username($username)) < 0) {
			return $status;
		} else {
			return 1;
		}
	}

	function onget_user() {
		$this->init_input();
		$username = $this->input('username');
		if(!$this->input('isuid')) {
			$status = $_ENV['user']->get_user_by_username($username);
		} else {
			$status = $_ENV['user']->get_user_by_uid($username);
		}
		if($status) {
			return array($status['uid'],$status['username'],$status['email']);
		} else {
			return 0;
		}
	}


	function ongetprotected() {
		$this->init_input();
		$protectedmembers = $this->db->fetch_all("SELECT uid,username FROM ".UC_DBTABLEPRE."protectedmembers GROUP BY username");
		return $protectedmembers;
	}

	function ondelete() {
		$this->init_input();
		$uid = $this->input('uid');
		return $_ENV['user']->delete_user($uid);
	}

	function onaddprotected() {
		$this->init_input();
		$username = $this->input('username');
		$admin = $this->input('admin');
		$appid = $this->app['appid'];
		$usernames = (array)$username;
		foreach($usernames as $username) {
			$user = $_ENV['user']->get_user_by_username($username);
			$uid = $user['uid'];
			$this->db->query("REPLACE INTO ".UC_DBTABLEPRE."protectedmembers SET uid='$uid', username='$username', appid='$appid', dateline='{$this->time}', admin='$admin'", 'SILENT');
		}
		return $this->db->errno() ? -1 : 1;
	}

	function ondeleteprotected() {
		$this->init_input();
		$username = $this->input('username');
		$appid = $this->app['appid'];
		$usernames = (array)$username;
		foreach($usernames as $username) {
			$this->db->query("DELETE FROM ".UC_DBTABLEPRE."protectedmembers WHERE username='$username' AND appid='$appid'");
		}
		return $this->db->errno() ? -1 : 1;
	}

	function onmerge() {
		$this->init_input();
		$oldusername = $this->input('oldusername');
		$newusername = $this->input('newusername');
		$uid = $this->input('uid');
		$password = $this->input('password');
		$email = $this->input('email');
		if(($status = $this->_check_username($newusername)) < 0) {
			return $status;
		}
		$uid = $_ENV['user']->add_user($newusername, $password, $email, $uid);
		$this->db->query("DELETE FROM ".UC_DBTABLEPRE."mergemembers WHERE appid='".$this->app['appid']."' AND username='$oldusername'");
		return $uid;
	}

	function onmerge_remove() {
		$this->init_input();
		$username = $this->input('username');
		$this->db->query("DELETE FROM ".UC_DBTABLEPRE."mergemembers WHERE appid='".$this->app['appid']."' AND username='$username'");
		return NULL;
	}

	function _check_username($username) {
		$username = addslashes(trim(stripslashes($username)));
		if(!$_ENV['user']->check_username($username)) {
			return UC_USER_CHECK_USERNAME_FAILED;
		} elseif(!$_ENV['user']->check_usernamecensor($username)) {
			return UC_USER_USERNAME_BADWORD;
		} elseif($_ENV['user']->check_usernameexists($username)) {
			return UC_USER_USERNAME_EXISTS;
		}
		return 1;
	}

	function _check_email($email, $username = '') {
		if(empty($this->settings)) {
			$this->settings = $this->cache('settings');
		}
		if(!$_ENV['user']->check_emailformat($email)) {
			return UC_USER_EMAIL_FORMAT_ILLEGAL;
		} elseif(!$_ENV['user']->check_emailaccess($email)) {
			return UC_USER_EMAIL_ACCESS_ILLEGAL;
		} elseif(!$this->settings['doublee'] && $_ENV['user']->check_emailexists($email, $username)) {
			return UC_USER_EMAIL_EXISTS;
		} else {
			return 1;
		}
	}

	function onuploadavatar() {
	}

	function onrectavatar() {
	}
	function flashdata_decode($s) {
	}
    
    // add by zhangjh 2015-09-23
    function onget_uid_by_openid()
    {
        $this->init_input();
        $openid = $this->input('openid');
        $uid = $_ENV['user']->get_uid_by_openid($openid);
        return $uid;
    }
    
    // add by zhangjh 2015-09-23
    function onget_member_by_email()
    {
        $this->init_input();
        $email = $this->input('email');
        $member = $_ENV['user']->get_member_by_email($email);
        return $member;
    }
    
    function onrenameuser()
    {
        $arr_return = array('success' => FALSE, 'message' => '');
        
        $this->init_input();
        $uid = $this->input('uid');
        $username = $this->input('username');
        $newusername = $this->input('newusername');
        
        $this->user = $_ENV['user']->get_user_by_uid($uid);
        if(!$this->user['isfounder'])
        {
            $isprotected = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."protectedmembers WHERE uid = '$uid'");
            
            if($isprotected)
            {
                $arr_return['message'] = 'user_edit_noperm';
                return $arr_return;
            }
        }
        
        if($username != $newusername)
        {
            if($_ENV['user']->get_user_by_username($newusername))
            {
                $arr_return['message'] = 'admin_user_exists';
                return $arr_return;
            }
            $sqladd = "username='$newusername' ";
            $this->load('note');
            $_ENV['note']->add('renameuser', 'uid='.$uid.'&oldusername='.urlencode($username).'&newusername='.urlencode($newusername));
            
            
            $this->db->query("UPDATE ".UC_DBTABLEPRE."members SET $sqladd WHERE uid='$uid'");
            
            $arr_return['success'] = TRUE;
            $arr_return['message'] = 'subimt_success';
            
            // add by zhangjh 2015-10-27
            //include_once(UC_ROOT.'../source/plugin/fansclub/function.inc.php');
            $note = 'uid='.$uid.'&oldusername='.urlencode($username).'&newusername='.urlencode($newusername);
            //fansclub_use_log('ucc_renameuser', $_ENV, $note);
            $this->_uc_memberlog('renameuser', $note);
        }
        else
        {
            $arr_return['message'] = 'username_is_same';
        }
        
        return $arr_return;
    }
    
    function onget_user_fields()
    {
        $this->init_input();
        $uid = $this->input('uid');
        $member = $_ENV['user']->get_user_fields($uid);
        return $member;
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