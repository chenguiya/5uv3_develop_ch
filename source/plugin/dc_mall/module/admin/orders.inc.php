<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = $_GET['op'];
if($op == 'delete'){
	$oid = dintval($_GET['oid'],true);
	$orders = C::t('#dc_mall#dc_mall_orders')->fetch_all($oid);
	if(empty($orders))showmessage($_lang['admin_orders_noorder']);
	if(submitcheck('submitchk')){
		$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
		C::import('extend/adminorders','plugin/dc_mall');
		foreach($orders as $os){
			$os['extdata'] = dunserialize($os['extdata']);
			$identify = explode(':',$extends[$os['extid']]['identify']);
			if(count($identify)==2){
				$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
				C::import($identify[1].'_adminorders','plugin/'.$hook['directory'].'/dcmallextend',false);
				$modstr = $identify[1].'_adminorders';
			}else{
				C::import($extends[$os['extid']]['identify'].'/adminorders','plugin/dc_mall/extend',false);
				$modstr = $extends[$os['extid']]['identify'].'_adminorders';
			}
			if(class_exists($modstr,false)){
				$mobj = new $modstr($os);
				$mobj->delete();
			}
		}
		C::t('#dc_mall#dc_mall_orders')->delete($oid);
		$adminordersurl = getcookie('adminordersurl');
		if(!$adminordersurl)$adminordersurl='plugin.php?id=dc_mall:admin&action=orders';
		showmessage($_lang['delsucceed'],$adminordersurl);
	}
	if(!is_array($oid))$order = C::t('#dc_mall#dc_mall_orders')->fetch($oid);
	include template('dc_mall:admin/orders');
	exit();

}elseif($op == 'view'){
	$oid = dintval($_GET['oid']);
	$order = C::t('#dc_mall#dc_mall_orders')->fetch($oid);
	if(empty($order))showmessage($_lang['admin_orders_noorder']);
	$order['extdata'] = dunserialize($order['extdata']);
	$extend = C::t('#dc_mall#dc_mall_extend')->fetch($order['extid']);
	$hookstr='';
	if($extend){
		C::import('extend/adminorders','plugin/dc_mall');
		$identify = explode(':',$extend['identify']);
		if(count($identify)==2){
			$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
			C::import($identify[1].'_adminorders','plugin/'.$hook['directory'].'/dcmallextend',false);
			$modstr = $identify[1].'_adminorders';
		}else{
			C::import($extend['identify'].'/adminorders','plugin/dc_mall/extend',false);
			$modstr = $extend['identify'].'_adminorders';
		}
		if(class_exists($modstr,false)){
			$mobj = new $modstr($order);
			$hookstr = $mobj->view();
		}
	}
	if(submitcheck('submitchk')){
		if($mobj->finish($oid)!==false){
			$adminordersurl = getcookie('adminordersurl');
			if(!$adminordersurl)$adminordersurl='plugin.php?id=dc_mall:admin&action=orders';
			showmessage($_lang['succeed'],$adminordersurl);
		}else{
			showmessage($_lang['error']);
		}
	}
	include template('dc_mall:admin/orders');
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
			case 2:$wherearr['uid'] = dintval($searchkeyword);break;
			case 3:$wherearr['username'] = $searchkeyword;break;
			case 4:$wherearr['gname'] = $searchkeyword;break;
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
	$orders = C::t('#dc_mall#dc_mall_orders')->range($start,$perpage,$wherearr,'id');
	$count = C::t('#dc_mall#dc_mall_orders')->count($wherearr);
	$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
	foreach($extends as &$ev){
		$ev['data'] = dunserialize($ev['data']);
	}
	$where = "&searchtype=$searchtype&searchkeyword=$searchkeyword&sortid=$sortid&extid=$extid&status=$status";
	$multi = multi($count, $perpage, $page, 'plugin.php?id=dc_mall:admin&action=orders'.$where);
	dsetcookie('adminordersurl','plugin.php?id=dc_mall:admin&action=orders'.$where);
}
?>