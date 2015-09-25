<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class table_plugin_forum_attachment extends discuz_table 
{
	public function __construct() {
		$this->_table = 'forum_attachment';
		$this->_pk    = 'aid';

		parent::__construct();
	}
	
	public function get_attachment_by_tid ($tid) {
		return DB::fetch_all("SELECT * FROM %t WHERE tid=%d", array($this->_table, $tid));
	}
	
	
}