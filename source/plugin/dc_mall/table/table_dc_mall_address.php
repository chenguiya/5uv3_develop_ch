<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_mall_address extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_mall_address';
		$this->_pk    = 'id';
		parent::__construct();
	}
	public function getbyuid($uid){
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('uid',$uid).' ORDER BY '.DB::order('default', 'DESC'),null,$this->_pk);
	}
	public function updatebyuid($uid,$data){
		if(isset($uid) && !empty($data) && is_array($data)) {
			$ret = DB::update($this->_table, $data, DB::field('uid', $uid));
			return $ret;
		}
		return false;
	}
}

?>