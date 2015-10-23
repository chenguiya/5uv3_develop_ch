<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perpage = 10;
$start = ($page -1) * $perpage;

ckstart($start, $perpage);

$orderlist = array();
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : $_G['uid'];
$where = 'uid='.$uid;
$where .= ' AND status=2 AND bill_type=3';
$orderlist = C::t('#ucharge#plugin_ucharge_log')->fetch_by_where($where, $start, $perpage);

foreach ($orderlist as $key => $order) {
	$orderlist[$key]['log_time'] = dgmdate($order['log_time'], 'u');
	$orderlist[$key]['body'] = unserialize($order['body']);
}

include_once template("diy:home/space_orders");