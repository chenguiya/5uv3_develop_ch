<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_home_feed extends discuz_table {
	public function __construct() {
		$this->_table = 'home_feed';
		$this->_pk = 'feedid';
		
		parent::__construct();
	}
	
	public function fetch_feedlist($where = '', $page = 1, $pagesize = 20, $order = '') {
		$sql = "SELECT * FROM " . DB::table($this->_table);
		if (!empty($where)) $sql .= " WHERE " . $where;		
		if (!empty($order)) $sql .= " ORDER BY " . $order . " DESC";
		$start = ($page - 1)* $pagesize;
		$sql .= " LIMIT " . $start . "," . $pagesize;
		$query = DB::fetch_all($sql);
		
		return $query;
	}
	
	public function count_feed_num($uid) {
		$uid = intval($uid);
		$count = (int) DB::result_first("SELECT COUNT(*) FROM %t WHERE uid=%s", array($this->_table, $uid));
		return $count;		
	}
	
	public function count_today_feed_num($uid) {
		$uid = intval($uid);
		//获取日期
		$nowdate = strtotime(date('Y-m-d', time()));
		$count = (int) DB::result_first("SELECT COUNT(*) FROM %t WHERE uid=%d AND dateline>%d", array($this->_table, $uid, $nowdate));
		return $count;
	}
	
	public function count_by_where($where = '1') {
		$query = DB::fetch_first("SELECT COUNT(*) num FROM %t WHERE ".$where, array($this->_table));		
		return (int)$query['num'];
	}
}