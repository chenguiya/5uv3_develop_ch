<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_mobile extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_mobile';
		$this->_pk = 'uid';
		parent::__construct();
	}
	
	public function fetch_uid_by_mobile($mobile)
	{
		if(empty($mobile)) return array();
		return DB::fetch_all("SELECT uid, username, mobile FROM %t WHERE `mobile`=%s", array($this->_table, $mobile));
	}
	
	public function fetch_all_by_uid($uid)
	{
		if(empty($uid)) return array();
		$uid = intval($uid);
		return DB::fetch_all("SELECT * FROM %t WHERE `uid`=%d", array($this->_table, $uid));
	}
}
