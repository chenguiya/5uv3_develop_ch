<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
$op = isset($_GET['op']) ? trim($_GET['op']) : 'list';
if ($op == 'delete') {
	;
} elseif ($op == 'view') {
	;
} elseif ($op == 'edit') {
	;
} else {
	
	$sqladd = creatsql();
	foreach ($_GET as $key => $val) {
		if (strpos($key, 'srch_') !== false && $val) {
			if (in_array($key, array('srch_username'))) {
				$val = rawurlencode($val);
			}
			$export_url[] = $key.'='.$val;
		}
	}
	
	$perpage = max(20, empty($_GET['perpage']) ? 20 : intval($_GET['perpage']));
	echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
	
	showtips('<li>点击编辑录入发货信息，包括快递信息，订单才会成为完成状态。</li>');
	//搜索框开始
	showformheader('order', '', 'orderform', 'get');
	showtableheader();
	showtablerow('', array(),
		array(
			'订单号', 
			'<input type="text" name="srch_id" class="txt" value="'.$_GET['srch_id'].'" />',
			'<input type="submit" name="srchbtn" class="btn" value="查询" />'
	));
	showtablefooter();
	showformfooter();
	//搜索框结束
	
	//订单列表开始
	showformheader('plugin.php?id=ucharge:orders&');
	showtableheader('用户订单列表');
	showsubtitle(array('', '订单号', '用户名', '商品名', '价格', '下单时间', '订单状态', '操作'));
	
	$start_limit = ($page - 1) * $perpage;
	$export_url[] = 'start='.$start_limit;
	foreach ($_GET as $key => $val) {
		if (strpos($key, 'srch_') !== false) {
			$url_add .= '&'.$key.'='.$val;
		}
	}
	$url = '';
	
	$count = (int)($sqladd ? C::t('#ucharge#plugin_ucharge_log')->count_by_where($sqladd) : C::t('common_card')->count());
	
	if ($count) {
		$multipage = multi($count, $perpage, $page, $url, 0, 3);
		foreach (C::t('#ucharge#plugin_ucharge_log')->fetch_by_where($sqladd, $start_limit, $perpage) as $key => $order) {
			$order['body'] = unserialize($order['body']);
			$orderlist[] = $order;
		}		

		foreach ($orderlist as $key => $val) {
			showtablerow('', array('class="smallefont"', '', '', '', ''), array(
			'<input class="checkbox" type="checkbox" name="delete[]" value="'.$val[id].'">',
			$val['orderid'],
			$val['body']['realname'],
			$val['body']['gname'],
			$val['price'].'&nbsp;元',
			dgmdate($val['log_time'], 'u'),
			'已付款',
			'编辑',
			));
		}
		echo '<input type="hidden" name="perpage" value="'.$perpage.'">';
		showsubmit('cardsubmit', 'submit', 'del', '<a href="'.ADMINSCRIPT.'?action=card&operation=export&'.implode('&', $export_url).'" title="导出订单">导出订单</a>', $multipage, false);
	}	
	showtablefooter();
	showformfooter();
	//订单列表结束
}

function creatsql() {
	$_GET = daddslashes($_GET);
	$_GET['orderid'] = trim($_GET['orderid']);
	$sqladd = '';
	if (!empty($_GET['orderid'])) {
		$sqladd .= " AND orderid LIKE '%{$_GET['orderid']}%'";
	}
	
	return $sqladd ? '1'.$sqladd.' AND bill_type=3' : '1 AND bill_type=3'; 
}
?>