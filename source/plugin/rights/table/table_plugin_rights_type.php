<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_rights_type extends discuz_table {
	public function __construct() {
		$this->_table = 'plugin_rights_type';
		$this->_pk = 'id';
		parent::__construct();
	}

	public function fetch_all() {
		$data = array(
				array(
						'id' => 0,
						'typename' => '通用'
				));

		$query = DB::query('SELECT * FROM '.DB::table($this->_table));
		while($value = DB::fetch($query)) {
			$data[$value[$this->_pk]] = $value;
			// 			$this->store_cache($value[$this->_pk], $value);
		}

		return $data;
	}
}