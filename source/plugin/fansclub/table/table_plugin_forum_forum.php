<?php
if (!defined('IN_DISCUZ')) {
	exit('Access denied');
}

class table_plugin_forum_forum extends discuz_table {
	public function __construct() {
		$this->_table = 'forum_forum';
		$this->_pk = 'fid';
		
		parent::__construct();
	}
	
	public function fetch_group_by_name($name, $type = '', $fup = 0) 
	{
		if($fup != 0 && $type != '')
		{
			return DB::fetch_all("SELECT fid, fup, type, name FROM %t WHERE name=%s AND status<>3 AND type=%s AND fup=%d", array($this->_table, $name, $type, $fup));
		}
		if($type != '')
		{
			return DB::fetch_all("SELECT fid, fup, type, name FROM %t WHERE name=%s AND status<>3 AND type=%s", array($this->_table, $name, $type));
		}
		else
		{
			return DB::fetch_all("SELECT fid, fup, type, name FROM %t WHERE name=%s AND status<>3", array($this->_table, $name));
		}
	}
}