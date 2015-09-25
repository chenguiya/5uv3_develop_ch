<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = $_GET['op'];
if($op == 'ok'){
	showmessage($_lang['member_order_payok'],'plugin.php?id=dc_mall:member&action=orders');
}
$oid = dintval($_GET['oid']);
$order = C::t('#dc_mall#dc_mall_orders')->fetch($oid);
if(!$order||$order['status']!=2||$order['uid']!=$_G['uid']||$order['creditid']!=99)showmessage($_lang['member_order_error']);
if($op == 'check'){
	if(!in_array($order['status'],array(0,1)))showmessage($_lang['member_order_paynotok']);
	showmessage($_lang['member_order_payok'],'plugin.php?id=dc_mall:member&action=orders');
	exit();
}else{
	if(submitcheck('submitchk')){
		$paytype = $_GET['paytype']=='tenpay'?'tenpay':'alipay';
		$modstr = 'api_'.$paytype;
		require_once DISCUZ_ROOT.'./source/plugin/dc_mall/api/'.$modstr.'.php';
		if (class_exists($modstr,false)){
			$mobj = new $modstr();
			if(in_array('create_payurl',get_class_methods($mobj))){
				$payurl = $mobj->create_payurl($order['orderid'],$order['credit'],$order['gname'],$_G['siteurl'].'plugin.php?id=dc_mall&action=goods&gid='.$order['gid']);
			}
		}
		if($payurl)
			showmessage($_lang['member_pay_waiting'],$payurl);
		else
			showmessage($_lang['error']);
	}
}
?>