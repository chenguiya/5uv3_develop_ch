<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_fansclub_balance extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_balance';
		$this->_pk    = 'balanceid';

		parent::__construct();
	}
	
	public function update_fansclub_balance($gid, $credits = 1, $type = 3) {
		if (!intval($gid)) {
			return false;
		};
		DB::query("UPDATE ".DB::table($this->_table)." SET extendcredits3 = extendcredits3+".$credits." WHERE relation_fid={$gid}");
	}
	
	public function fetch_first($gid) {
		return DB::fetch_first("SELECT * FROM %t WHERE relation_fid=%d", array($this->_table, $gid));
	}
	
	public function fetch_club_ban($filed, $limit) {
		return DB::fetch_all("SELECT * FROM %t ORDER BY %w DESC LIMIT %d", array($this->_table, $filed, $limit));
	}
}