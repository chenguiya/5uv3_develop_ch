<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_event_log extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_event_log';
		$this->_pk = 'log_id';
		parent::__construct();
	}
	
	public function fetch_all_by_fid($fid, $start = 0, $limit = 0, $sort = '')
	{
		if(intval($fid) <= 0) return array();

		return DB::fetch_all("SELECT * FROM %t WHERE `fid`=%d".
			($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').
			DB::limit($start, $limit), array($this->_table, intval($fid)), '');
	}
	
	public function fetch_relation_id_by_fid_type($fid, $type)
	{
		if(intval($fid) <= 0 || intval($type) <= 0 ) return 0;
		$row = DB::fetch_all("SELECT relation_id FROM %t WHERE `fid`=%d AND `type`=%d", array($this->_table, intval($fid), intval($type)), '');
		if(count($row) == 1)
			return $row[0]['relation_id'];
		else
			return 0;
	}
}