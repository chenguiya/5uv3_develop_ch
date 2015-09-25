<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_forum_post extends discuz_table {
	public function __construct() {
		$this->_table = 'forum_post';
		$this->_pk    = 'pid';
		
		parent::__construct();
	}
	
	public function fetch_postinfo_by_tid($tid) {
// 		$sql = "SELECT * FROM ".DB::table($this->_table)." WHERE tid=".$tid;
// 		echo $sql;
		$query =  DB::fetch_first("SELECT * FROM ".DB::table($this->_table)." WHERE tid=".$tid." AND first=1");
		
		var_dump($query);die;
	}
	
	public function fetch_message_by_tid($tid) {
		return DB::fetch_first("SELECT message FROM %t WHERE tid=%d AND first=1", array($this->_table, $tid));
	}
}