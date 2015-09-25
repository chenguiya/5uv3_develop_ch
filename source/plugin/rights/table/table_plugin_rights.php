<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_rights extends discuz_table {
	public function __construct() {
		$this->_table = 'plugin_rights';
		$this->_pk = 'rightsid';
		
		parent::__construct();
	}
	
// 	public function fetch_one($rightsid) {
// 		return DB::fetch_all("SELECT * FROM %t where rightsid=%d", array($this->_table, $rightsid));
// 	}	
	
	public function fetch_all_data() {
		return DB::fetch_all("SELECT * FROM %t", array($this->_table));
	}
	
	public function fetch_all_by_where($where, $start = 0, $limit = 10) {
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' WHERE '.$where.' ORDER BY rightsid DESC'.DB::limit($start, $limit));
	}

	public function fetch_rights_by_id($rightsid) {
		return DB::fetch_first("SELECT * FROM %t WHERE rightsid=%d", array($this->_table, $rightsid));
	}
	
	public function count_by_where($where) {
		return ($where = (string)$where) ? DB::result_first("SELECT COUNT(*) FROM ".DB::table($this->_table)." WHERE ".$where) : 0;
	}
}