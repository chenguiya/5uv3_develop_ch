<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_extends_column extends discuz_table {
	public function __construct() {
		$this->_table = 'plugin_extends_column';
		$this->_pk ='id';
		
		parent::__construct();
	}
	
}