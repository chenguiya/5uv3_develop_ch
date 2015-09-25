<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = trim($_GET['op']);
if($op == 'delete'){
	$sortid = dintval($_GET['sortid']);
	$sort = C::t('#dc_mall#dc_mall_sort')->fetch($sortid);
	if(empty($sort))showmessage($_lang['admin_sort_nosort']);
	if(submitcheck('submitcheck')){
		C::t('#dc_mall#dc_mall_sort')->delete($sortid);
		showmessage($lang['delsucceed'],'plugin.php?id=dc_mall:admin&action=sort');
	}
	include template('dc_mall:admin/sort');
	exit();
}
if(submitcheck('submitcheck')){
	$sortids = dintval($_GET['sortid'],true);
	$name = daddslashes($_GET['name']);
	$order = dintval($_GET['order'],true);
	$isnav = dintval($_GET['isnav'],true);
	foreach($sortids as $sid){
		$d = array(
			'name'=>trim($name[$sid]),
			'order'=>$order[$sid],
			'isnav'=>$isnav[$sid]?1:0,
		);
		C::t('#dc_mall#dc_mall_sort')->update($sid,$d);
	}
	$newname = trim(daddslashes($_GET['newname']));
	$neworder = dintval($_GET['neworder']);
	$newisnav = dintval($_GET['newisnav']);
	if(!empty($newname)){
		$d = array(
			'name'=>$newname,
			'order'=>$neworder,
			'isnav'=>$newisnav?1:0,
		);
		C::t('#dc_mall#dc_mall_sort')->insert($d);
	}
	showmessage($_lang['succeed'],'plugin.php?id=dc_mall:admin&action=sort');
}
?>