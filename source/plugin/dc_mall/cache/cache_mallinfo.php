<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_plugin_mallinfo() {
	$data = array();
	$data['goodstotal'] = DB::result_first('SELECT count(id) FROM '.DB::table('dc_mall_goods').' WHERE '.DB::field('status',1));
	$data['orderstotal'] = DB::result_first('SELECT count(id) FROM '.DB::table('dc_mall_orders'));
	savecache('dcmallinfo', $data);
}


?>