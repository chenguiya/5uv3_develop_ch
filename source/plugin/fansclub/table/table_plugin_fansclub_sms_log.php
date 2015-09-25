<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_sms_log extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_sms_log';
		$this->_pk = 'id';
		parent::__construct();
	}
	
	public function fetch_all_by_mobile($mobile)
	{
		if(empty($mobile)) return array();
		return DB::fetch_all("SELECT id, posttime, mobile FROM %t WHERE `mobile`=%s ORDER BY ".DB::order($this->_pk, 'DESC').DB::limit(0, 50), array($this->_table, $mobile));
	}
	
	public function fetch_all_by_uid($uid)
	{
		if(empty($uid)) return array();
		$uid = intval($uid);
		return DB::fetch_all("SELECT * FROM %t WHERE `uid`=%d", array($this->_table, $uid));
	}
}
