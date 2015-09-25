<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_mall_sort extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_mall_sort';
		$this->_pk    = 'id';
		parent::__construct();
	}
	public function getdata(){
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' ORDER BY '.DB::order('order', 'ASC'),null,$this->_pk);
	}
}

?>