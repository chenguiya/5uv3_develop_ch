<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_mall_extend extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_mall_extend';
		$this->_pk    = 'id';
		parent::__construct();
	}
	public function getdata($identify){
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).($identify ? ' WHERE '.DB::field('identify', $identify) : '').' ORDER BY '.DB::order($this->_pk, 'ASC'), null, $this->_pk ? $this->_pk : '');
	}
}

?>