<?php
if(!defined('IN_DISCUZ')) exit('Access Denied1');

$inajax = empty($_GET['inajax']) ? '' : '1';

if($inajax == '1') // add by zhangjh 2015-05-18 手机注册相关ajax
{
	$arr_return = array('success' => FALSE, 'message' => '');
	
	$step = trim($_GET['step']); // 步骤
	$method = trim($_GET['method']); // 页面方法，输入框动作
	switch($step)
	{
		case 'check_username' : // 检查用户名
		{
			$arr_return = fansclub_check_username($_GET['username']);
			break;
		}
		case 'check_mobile' : // 检查手机号
		{
			$arr_return = fansclub_check_mobile($_GET['mobile']);
			break;
		}
		case 'send_sms' : // 发送验证码
		{
			$arr_return = fansclub_send_sms($_GET['mobile'], 0, $_GET['geetest_challenge'], $_GET['geetest_validate'], $_GET['geetest_seccode']);
			break;
		}
		case 'check_sms' : // 检查验证码
		{
			$arr_return = fansclub_check_sms($_GET['sms_verify']);
			break; 
		}
		case 'check_mobile_exist' : // 手机找回密码
		{
			$arr_return = fansclub_check_mobile_exist($_GET['mobile']);
			break; 
		}
		case 'register' : // 注册
		{
			break;
		}
	}
	echo json_encode($arr_return);
	exit;
}
elseif($_POST['regsubmit']) // 手机注册(POST)
{
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$mobile_real = trim($_POST['mobile_real']);
	$email = 'p_'.$mobile_real.'@example.com';
	
	include_once(DISCUZ_ROOT.'./config/config_ucenter.php');
	include_once(DISCUZ_ROOT.'./uc_client/client.php');
	$uid = uc_user_register(addslashes($username), $password, $email, '', '', $_G['clientip']);
	if($uid <= 0) {
		if($uid == -1) {
			echo lang('message', 'profile_username_illegal');
		} elseif($uid == -2) {
			echo lang('message', 'profile_username_protect');
		} elseif($uid == -3) {
			echo lang('message', 'profile_username_duplicate');
		} elseif($uid == -4) {
			echo lang('message', 'profile_email_illegal');
		} elseif($uid == -5) {
			echo lang('message', 'profile_email_domain_illegal');
		} elseif($uid == -6) {
			echo lang('message', 'profile_email_duplicate');
		} else {
			echo lang('message', 'undefined_action');
		}
	}
	else
	{
		// 注册UC成功
		$_G['username'] = $username;
		if(getuserbyuid($uid, 1)) {
			if(!$activation) {
				uc_user_delete($uid);
			}
			echo lang('message', 'profile_uid_duplicate');
		}
		else
		{
			$password = md5(random(10));
			
			$groupinfo = array();
			$groupinfo['groupid'] = 10;
			$emailstatus = 0;
			$profile = array();
			
			$init_arr = array('credits' => array(), 'profile'=>$profile, 'emailstatus' => $emailstatus);
			C::t('common_member')->insert($uid, $username, $password, $email, $_G['clientip'], $groupinfo['groupid'], $init_arr);
			require_once libfile('cache/userstats', 'function');
			build_cache_userstats();
			
			// 插入手机记录
			$data = array();
			$data['uid'] = $uid;
			$data['username'] = $username;
			$data['mobile'] = $mobile_real;
			C::t('#fansclub#plugin_fansclub_mobile')->insert($data);
			
			include_once libfile('function/member');
			setloginstatus(array(
				'uid' => $uid,
				'username' => $_G['username'],
				'password' => $password,
				'groupid' => $groupinfo['groupid'],
			), 0);
			include_once libfile('function/stat');
			updatestat('register');
			
			dsetcookie('loginuser', '');
			dsetcookie('activationauth', '');
			dsetcookie('invite_auth', '');
			
			$url_forward = dreferer();
			$refreshtime = 3000;

			$param = array('bbname' =>'', 'username' => $_G['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['uid']);
			$url_forward = 'forum.php';
			
			$message = 'register_succeed';
			$locationmessage = 'register_succeed_location';
			
			$href = str_replace("'", "\'", $url_forward);
			$extra = array(
				'showid' => 'succeedmessage',
				'extrajs' => '<script type="text/javascript">'.
					'setTimeout("window.location.href =\''.$href.'\';", '.$refreshtime.');'.
					'$(\'succeedmessage_href\').href = \''.$href.'\';'.
					'$(\'main_message\').style.display = \'none\';'.
					'$(\'main_succeed\').style.display = \'\';'.
					'$(\'succeedlocation\').innerHTML = \''.lang('message', $locationmessage).'\';'.
				'</script>',
				'striptags' => false,
			);
			include_once libfile('function/core');
			showmessage($message, $url_forward, $param, $extra);
		}
	}
	exit;
}