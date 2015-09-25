<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_rights_personal_banner extends discuz_table {
	public function __construct() {
		$this->_table = 'plugin_rights_personal_banner';
		$this->_pk = 'id';
		
		parent::__construct();
	}
	
	public function fetch($uid) {
		return DB::fetch_all("SELECT * FROM %t WHERE uid=%d", array($this->_table, $uid));
	}
	
	public function update($uid, $banner = '') {
		DB::update($this->_table, array('banner' => $banner), array('uid' => $uid));
	}
}