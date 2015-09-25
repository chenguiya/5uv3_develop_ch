<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_user_hostclub extends discuz_table {
	function __construct() {
		$this->_table = 'plugin_user_hostclub';
		$this->_pk = 'uid';
		
		parent::__construct();
	}
}