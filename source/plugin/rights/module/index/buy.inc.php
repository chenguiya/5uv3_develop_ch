<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');
$_settings = $_G['cache']['plugin']['rights'];

if (submitcheck('addfundssubmit')) {
	require_once libfile('function/extends');
	$paysum = !empty($_POST['paysum']) ? intval($_POST['paysum']) : showmessage('充值金额不能为空，请输入你要充值金额!');
	$data = array();
	$data['uid'] = $_G['uid'];
	$data['username'] = $_G['member']['username'];
	$data['price'] = $paysum;			//充值额度
	$data['subject'] = $_G['member']['username'].' - 钱包充值';		//记录操作
	$data['notify_url'] = $_G['siteurl'].'source/plugin/ucharge/api/notify_ucharge_vip.php';
	$data['return_url'] = $_G['siteurl'].'source/plugin/ucharge/api/notify_ucharge_vip.php';
	$data['show_url'] = $_G['siteurl'];
	$data['seller_email'] = $_G['setting']['ec_account'];
	$data['timestamp'] = TIMESTAMP;
	$data['clientip'] = $_G['clientip'];
	$data['email'] = $_G['member']['email'];
	
	paydirect($data);
	exit;
}