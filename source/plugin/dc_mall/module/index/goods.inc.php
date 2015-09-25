<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$gid = dintval($_GET['gid']);
$goods = C::t('#dc_mall#dc_mall_goods')->fetch($gid);
if(empty($goods)||!$goods['status'])showmessage($_lang['nogoods']);
$goods['des'] = dstripslashes($goods['des']);
require_once libfile('function/blog');
$goods['des'] = blog_bbcode($goods['des']);
$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
if(empty($extends[$goods['extid']]))showmessage($_lang['error']);
$extend = $extends[$goods['extid']];
$sortid = $goods['sortid'];
$hookcheck = true;
$hookstr = '';
C::import('extend/goods','plugin/dc_mall');
$identify = explode(':',$extend['identify']);
if(count($identify)==2){
	$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
	if(empty($hook)||!$hook['available'])showmessage($_lang['error']);
	C::import($identify[1].'_goods','plugin/'.$hook['directory'].'/dcmallextend',false);
	$modstr = $identify[1].'_goods';
}else{
	C::import($extend['identify'].'/goods','plugin/dc_mall/extend',false);
	$modstr = $extend['identify'].'_goods';
}
if(class_exists($modstr,false)){
	$mobj = new $modstr($goods);
	$hookcheck = $mobj->check();
	$hookstr = $mobj->view();
}
$orders = C::t('#dc_mall#dc_mall_orders')->range(0,10,array('gid'=>$gid),'id');
$goodshot = C::t('#dc_mall#dc_mall_goods')->range(0,6,array('status' => 1),'hot');
foreach($goodshot as &$g){
	$g['thumb'] = $g['pic'].'.thumb.jpg';
}
$ordersnew = C::t('#dc_mall#dc_mall_orders')->range(0,20,array(),'id');
loadcache('dcmallinfo');
C::t('#dc_mall#dc_mall_goods')->update($gid,array('views'=>$goods['views']+1));
$navtitle = $goods['name'].' - '.$mallnav[$sortid]['name'].' - '.$cvar['title'];
?>