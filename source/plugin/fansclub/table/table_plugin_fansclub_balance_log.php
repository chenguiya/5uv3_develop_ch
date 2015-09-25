<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_fansclub_balance_log extends discuz_table {
	public function __construct() {
		$this->_table = 'plugin_fansclub_balance_log';
		$this->_pk    = 'log_id';

		parent::__construct();
	}
}
?>