<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_setting_ex extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_setting_ex';
		$this->_pk = 'ex_id';
		parent::__construct();
	}
	
	public function fetch_all_by_name($name)
	{
		if(empty($name)) return array();
		return DB::fetch_all("SELECT * FROM %t WHERE `name`=%s", array($this->_table, $name));
	}
	
	public function fetch_id_by_name($name)
	{
		if(empty($name)) return 0;
		$row = DB::fetch_all("SELECT ex_id FROM %t WHERE `name`=%s", array($this->_table, $name));
		return (count($row) == 1) ? intval($row[0]['ex_id']) : 0;
	}
}
