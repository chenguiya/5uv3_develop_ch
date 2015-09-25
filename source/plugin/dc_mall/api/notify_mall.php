<?php
define('IN_API', true);
define('CURSCRIPT', 'api');
require '../../../../source/class/class_core.php';
$discuz = C::app();
$discuz->init();
loadcache('plugin');
$PHP_SELF = $_SERVER['PHP_SELF'];
$_G['siteurl'] = dhtmlspecialchars('http://'.$_SERVER['HTTP_HOST'].preg_replace("/\/+(source\/plugin\/dc\_mall/api)?\/*$/i", '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))).'/');
$pay_type = empty($_GET['attach']) || !preg_match('/^[a-z0-9]+$/i', $_GET['attach']) ? 'alipay' : $_GET['attach'];
$modstr = 'api_'.$pay_type;
require_once DISCUZ_ROOT.'/source/plugin/dc_mall/api/'.$modstr.'.php';
$mobj = new $modstr();
$notifydata = $mobj->trade_notifycheck();
if($notifydata['validator']) {
	$orderid = $notifydata['order_no'];
	$price = $notifydata['price'];
	$order = C::t('#dc_mall#dc_mall_orders')->getbyorderid($orderid);
	if($order && !in_array($order['status'],array(0,1)) && floatval($price) == floatval($order['credit']) && ($pay_type == 'tenpay' || strtolower($_G['setting']['ec_account']) == strtolower($_REQUEST['seller_email']))) {
		$goods = C::t('#dc_mall#dc_mall_goods')->fetch($order['gid']);
		C::t('#dc_mall#dc_mall_goods')->update($order['gid'],array('sales'=>$goods['sales']+$order['count']));
		C::t('#dc_mall#dc_mall_orders')->update($order['id'],array('status'=>0,'paytime'=>TIMESTAMP,'payorderid'=>$pay_type.':'.$notifydata['trade_no']));
		$extend = C::t('#dc_mall#dc_mall_extend')->fetch($order['extid']);
		$order['extdata'] = dunserialize($order['extdata']);
		C::import('extend/goods','plugin/dc_mall');
		$identify = explode(':',$extend['identify']);
		if(count($identify)==2){
			$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
			C::import($identify[1].'_goods','plugin/'.$hook['directory'].'/dcmallextend',false);
			$modstr = $identify[1].'_goods';
		}else{
			C::import($extend['identify'].'/goods','plugin/dc_mall/extend',false);
			$modstr = $extend['identify'].'_goods';
		}
		if(class_exists($modstr,false)){
			$mobj = new $modstr($goods);
			$mobj->finish($order);
		}
	}
}
if($notifydata['location']) {
	dheader('location: '.$_G['siteurl'].'plugin.php?id=dc_mall:member&action=payfor&op=ok');
}else{
	exit($notifydata['notify']);
}
?>