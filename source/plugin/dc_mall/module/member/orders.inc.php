<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = $_GET['op'];
if($op == 'view'){
	$oid = dintval($_GET['oid']);
	$order = C::t('#dc_mall#dc_mall_orders')->fetch($oid);
	if(empty($order))showmessage($_lang['noorder']);
	$order['extdata'] = dunserialize($order['extdata']);
	$extend = C::t('#dc_mall#dc_mall_extend')->fetch($order['extid']);
	$hookstr='';
	if($extend){
		C::import('extend/memberorders','plugin/dc_mall');
		$identify = explode(':',$extend['identify']);
		if(count($identify)==2){
			$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
			C::import($identify[1].'_memberorders','plugin/'.$hook['directory'].'/dcmallextend',false);
			$modstr = $identify[1].'_memberorders';
		}else{
			C::import($extend['identify'].'/memberorders','plugin/dc_mall/extend',false);
			$modstr = $extend['identify'].'_memberorders';
		}
		if(class_exists($modstr,false)){
			$mobj = new $modstr($order);
			$hookstr = $mobj->view();
		}
	}
	include template('dc_mall:member/orders');
	exit();
}else{
	$page = dintval($_GET['page']);
	$page = $page?$page:1;
	$perpage = 20;
	$start = ($page-1)*$perpage;
	$wherearr = array();
	$searchkeyword = trim(daddslashes($_GET['searchkeyword']));
	$sid = dintval($_GET['sortid']);
	$extid = dintval($_GET['extid']);
	if($searchkeyword){
		switch(dintval($_GET['searchtype'])){
			case 1:$wherearr['orderid'] = $searchkeyword;break;
			case 2:$wherearr['gname'] = $searchkeyword;break;
			default:break;
		}
	}
	if($sid){
		$wherearr['sortid'] = $sid;
	}
	if($extid){
		$wherearr['extid'] = $extid;
	}
	$status = dintval($_GET['status']);
	if($status){
		if($status==9)
			$wherearr['status'] = 0;
		else
			$wherearr['status'] = $status;
	}
	$wherearr['uid']=$_G['uid'];
	$orders = C::t('#dc_mall#dc_mall_orders')->range($start,$perpage,$wherearr,'id');
	$count = C::t('#dc_mall#dc_mall_orders')->count($wherearr);
	$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
	foreach($extends as &$ev){
		$ev['data'] = dunserialize($ev['data']);
	}
	$where = "&searchtype=$searchtype&searchkeyword=$searchkeyword&sortid=$sortid&extid=$extid&status=$status";
	$multi = multi($count, $perpage, $page, 'plugin.php?id=dc_mall:member&action=orders'.$where);
}
?>