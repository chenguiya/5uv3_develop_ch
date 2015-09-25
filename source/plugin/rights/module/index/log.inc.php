<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');
$_settings = $_G['cache']['plugin']['rights'];
$payment = array(
	'ALP' => '支付宝',
	'BLP' => '钱包支付'
);

if (!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}

$where = '1';
$where .= ' AND `uid`=' . intval($_G['uid']);
$time = isset($_GET['time']) ? intval($_GET['time']) : 0;
switch ($time) {
	case 1:
	$starttime = strtotime(date('Y-m-d', TIMESTAMP));
	$where .= ' AND `log_time`>' . $starttime;
	break;
	
	case 2:
	$starttime = strtotime(date('Y-m-d', TIMESTAMP-24*60*60));
	$endtime = strtotime(date('Y-m-d', TIMESTAMP));
	$where .= ' AND `log_time`>'.$starttime.' AND `log_time`<'.$endtime;
	break;
	
	case 3:
	$starttime = strtotime(date('Y-m-d', TIMESTAMP-2*24*60*60));
	$endtime = strtotime(date('Y-m-d', TIMESTAMP-24*60*60));
	$where .= ' AND `log_time`>'.$starttime.' AND `log_time`<'.$endtime;
	break;
	
	case 4:
	$starttime = strtotime(date('Y-m-d', TIMESTAMP-24*60*60));
	$endtime = strtotime(date('Y-m-d', TIMESTAMP));
	$where .= ' AND `log_time`>'.$starttime.' AND `log_time`<'.$endtime;
	break;
	
	default:
	$where .= '';
	break;
}

$count = C::t('#ucharge#plugin_ucharge_log')->count($where);

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 2;
$startlimit = ($page - 1)*$pagesize;

$lists = array();
$lists = C::t('#ucharge#plugin_ucharge_log')->fetch_by_where($where, $startlimit, $pagesize);
foreach ($lists as $key => $value) {
	$lists[$key]['dateline'] = date('Y-m-d H:i:s', $value['log_time']);
	$lists[$key]['payment'] = $payment[$value['operation']];
}

$multipage = multi($count, $pagesize, $page, 'plugin.php?id=rights:index&op=log&time='.$time);

// var_dump($lists);die;

?>