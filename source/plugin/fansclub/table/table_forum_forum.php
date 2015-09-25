<?php
if (!defined('IN_DISCUZ')) {
	exit('Access denied');
}

class table_forum_forum extends discuz_table {
	public function __construct() {
		$this->_table = 'forum_forum';
		$this->_pk = 'fid';
		
		parent::__construct();
	}
	
	public function fetch_relation_forum_by_gid($gid) {
		return DB::fetch_first("SELECT fid FORM ".$this->_table." WHERE relatedgroup LIKE '%,$gid,%'");
	}
}