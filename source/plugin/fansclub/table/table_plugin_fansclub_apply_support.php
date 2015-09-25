<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_apply_support extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_apply_support';
		$this->_pk = 'support_id';
		parent::__construct();
	}
	
	public function fetch_all_by_ids($apply_id, $uid = 0)
	{
		if($uid == 0)
		{
			return DB::fetch_all("SELECT `uid`, `username`, FROM_UNIXTIME(`support_time`) as support_time FROM %t WHERE `apply_id`=%s", array($this->_table, $apply_id));
		}
		else
		{
			return DB::fetch_all("SELECT `uid`, `username`, FROM_UNIXTIME(`support_time`) as support_time FROM %t WHERE `apply_id`=%s AND `uid`=%s", array($this->_table, $apply_id, $uid));
		}
	}
}
