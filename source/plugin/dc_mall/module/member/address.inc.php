<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = $_GET['op'];
if($op=='setdefault'){
	$addrid = dintval($_GET['addrid']);
	$address = C::t('#dc_mall#dc_mall_address')->getbyuid($_G['uid']);
	if(!$address[$addrid])showmessage($_lang['member_address_noaddr']);
	$addr = $address[$addrid];
	if(submitcheck('submitchk')){
		C::t('#dc_mall#dc_mall_address')->updatebyuid($_G['uid'],array('default'=>0));
		C::t('#dc_mall#dc_mall_address')->update($addrid,array('default'=>1));
		showmessage($_lang['succeed'],'plugin.php?id=dc_mall:member&action=address');
	}
	include template('dc_mall:member/address');
	exit();
	
}elseif($op =='delete'){
	$addrid = dintval($_GET['addrid']);
	$addr = C::t('#dc_mall#dc_mall_address')->fetch($addrid);
	if(!$addr||$addr['uid']!=$_G['uid'])showmessage($_lang['member_address_noaddr']);
	if(submitcheck('submitchk')){
		 C::t('#dc_mall#dc_mall_address')->delete($addrid);
		 showmessage($_lang['delsucceed'],'plugin.php?id=dc_mall:member&action=address');
	}
	include template('dc_mall:member/address');
	exit();
}elseif($op=='add'){
	if(submitcheck('submitchk')){
		$strAddress  = trim($_GET['resideprovince']).trim($_GET['residecity']).trim($_GET['residedist']).trim($_GET['residecommunity']).trim($_GET['txt_address']);
		$strRealname  = trim($_GET['txt_realname']);
		$strTel  = trim($_GET['txt_tel']);
		if(!$strAddress||!$strRealname||!$strTel)showmessage($_lang['member_address_error']);
		$setarr = array(
				'uid' => $_G['uid'],
				'realname' => $strRealname,
				'address' => $strAddress,
				'tel' => $strTel,
				'default'=>0,
		);
		DB::insert('dc_mall_address', $setarr);
		showmessage($_lang['succeed'],'plugin.php?id=dc_mall:member&action=address');
	}
	require_once libfile('function/profile');
	$values = array(0,0,0,0);
	$elems = array('resideprovince', 'residecity', 'residedist', 'residecommunity');
	$reside = '<p id="residedistrictbox">'.showdistrict($values, $elems, 'residedistrictbox', 1, 'reside').'</p>';
	include template('dc_mall:member/address');
	exit();
	
}else{
	$address = C::t('#dc_mall#dc_mall_address')->getbyuid($_G['uid']);
}
?>