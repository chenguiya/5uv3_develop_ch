<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(!submitcheck('submitchk'))showmessage($_lang['feifa']);
if (!$_G['uid']) showmessage('not_loggedin','member.php?mod=logging&action=login');
$gid = dintval($_GET['gid']);
$count = abs(dintval($_GET['txt_count']));
$op = $_GET['op'];
if(!$count)$count = 1;
$goods = C::t('#dc_mall#dc_mall_goods')->fetch($_GET['gid']);
if(empty($goods)||!$goods['status'])showmessage($_lang['nogoods']);
$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
if(empty($extends[$goods['extid']]))showmessage($_lang['error']);
$extend = $extends[$goods['extid']];
$sortid = $goods['sortid'];
if($goods['vipcredit']==null)$goods['vipcredit'] = $goods['credit'];
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
	if($hookcheck!==true)showmessage($_lang['index_pay_donotpay']);
	if($count>$goods['count'])$count = $goods['count'];
	if($count>$goods['maxbuy'])$count = $goods['maxbuy'];
	$hookstr = $mobj->payview();
}
if($op=='save'){
	$credit = 0;
	if($_G['dc_mall']['vip']['user'])
		$credit = $goods['vipcredit'] * $count;
	else
		$credit = $goods['credit'] * $count;
	if($goods['creditid']!=99){
		$usercredit = getuserprofile('extcredits'.$goods['creditid']);
		if($credit>$usercredit)showmessage($_lang['index_pay_nocredit']);
	}
	$extdata = $mobj->paycheck();
	$data = array(
		'orderid'=>dgmdate(TIMESTAMP, 'YmdHis').random(6),
		'gid'=>$gid,
		'gname'=>$goods['name'],
		'creditid'=>$goods['creditid'],
		'credit'=>$credit,
		'count'=>$count,
		'uid'=>$_G['uid'],
		'username'=>$_G['username'],
		'dateline'=>TIMESTAMP,
		'status'=>$goods['creditid']!=99?0:2,
		'sortid'=>$goods['sortid'],
		'extid'=>$goods['extid'],
	);
	if($extdata&&is_array($extdata)){
		$data['extdata'] = serialize($extdata);
	}
	$oid = C::t('#dc_mall#dc_mall_orders')->insert($data,true);
	$goodsdata = array();
	$goodsdata['count'] = $goods['count']-$count;
	if($goods['creditid']!=99)$goodsdata['sales'] = $goods['sales']+$count;
	C::t('#dc_mall#dc_mall_goods')->update($gid,$goodsdata);
	$mobj->payfinish($oid);
	if($goods['creditid']!=99||!$credit){
		updatemembercount($_G['uid'], array('extcredits'.$goods['creditid'] => "-".$credit), true, '', 0, '',$_lang['index_pay_credittitle'],'<a href="plugin.php?id=dc_mall&action=goods&gid='.$gid.'">'.$goods['name'].'</a>');
		$data['id'] = $oid;
		$data['extdata'] = $extdata;
		$mobj->finish($data);
		showmessage($_lang['index_pay_succeed'],'plugin.php?id=dc_mall:member&action=orders');
	}
	showmessage($_lang['member_pay_waiting'],'plugin.php?id=dc_mall:member&action=payfor&oid='.$oid,array('waiting'=>'yes'));
	exit();
}
?>