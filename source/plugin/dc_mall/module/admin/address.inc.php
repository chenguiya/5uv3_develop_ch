<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(submitcheck('submitcheck')){
	$aid = dintval($_GET['aid'],true);
	C::t('#dc_mall#dc_mall_address')->delete($aid);
	showmessage($_lang['succeed'],'plugin.php?id=dc_mall:admin&action=address');
	
}
$page = dintval($_GET['page']);
$page = $page?$page:1;
$perpage = 20;
$start = ($page-1)*$perpage;
$address = C::t('#dc_mall#dc_mall_address')->range($start,$perpage);
$uids = array();
foreach($address as $v){
	$uids[] = $v['uid'];
}
$users = C::t('common_member')->fetch_all($uids);
$count = C::t('#dc_mall#dc_mall_address')->count();
$multi = multi($count, $perpage, $page, 'plugin.php?id=dc_mall:admin&action=address');
?>