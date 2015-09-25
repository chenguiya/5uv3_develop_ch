<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_forum_forum extends discuz_table {
	public function __construct() {
		$this->_table = 'forum_forum';
		$this->_pk = 'fid';
		
		parent::__construct();
	}
}