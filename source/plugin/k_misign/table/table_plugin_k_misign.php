<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_k_misign extends discuz_table{
	public function __construct() {

		$this->_table = 'plugin_k_misign';
		$this->_pk    = 'uid';

		parent::__construct();
	}

	public function fetch_all_by_time($start, $limit, $tdtime) {
		return DB::fetch_all("SELECT * FROM %t q, ".DB::table('common_member')." m WHERE q.uid=m.uid AND q.time>%d ORDER BY q.time DESC LIMIT ".$start.",".$limit, array($this->table, $tdtime, $start, $limit), $this->_pk);;
	}
	
	public function fetch_all_cp($start, $limit) {
		return DB::fetch_all('SELECT * FROM %t ORDER BY prizeid DESC LIMIT '.$start.','.$limit, array($this->table), $start, $limit);;
	}
	
	public function fetch_by_prizeid($prizeid) {
		return DB::fetch_first('SELECT * FROM %t WHERE prizeid=%d', array($this->table, $prizeid));;
	}

	public function update_by_todaylast($prizeid, $todaylast) {
		return DB::query('UPDATE %t SET todaylast=%d WHERE prizeid=%d', array($this->table, $todaylast, $prizeid));;
	}
}

?>