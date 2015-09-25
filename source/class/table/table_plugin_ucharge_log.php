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
}
