<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_ucharge_log extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_ucharge_log';
		$this->_pk = 'orderid';
		parent::__construct();
	}
	
	public function fetch($uid, $fid) {
		return DB::fetch_first("SELECT * FROM ".DB::table($this->_table)." WHERE uid={$uid} AND fid={$fid}");
	}
	
	public function count($where = '1') {
		$data = DB::fetch_first("SELECT COUNT(*) num FROM %t WHERE ".$where, array($this->_table));
		return $data['num'];
	}
	
	public function fetch_by_where($where = '1', $start = 0, $num = 10) {
		return DB::fetch_all("SELECT * FROM %t WHERE ".$where." LIMIT %d,%d", array($this->_table, $start, $num));
	}
}
