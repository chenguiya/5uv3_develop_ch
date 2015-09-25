<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_mall_kami extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_mall_kami';
		$this->_pk    = 'id';
		parent::__construct();
	}
	public function deletebygid($val){
		$ret = false;
		if(isset($val)) {
			$ret = DB::delete($this->_table, DB::field('gid', $val));
		}
		return $ret;
	}
	public function deleteids($val,$gid=0){
		return DB::query('DELETE FROM %t where `gid`=%d AND '.DB::field('id', $val),array($this->_table,$gid));
	}
	public function fetchbykey($key,$gid,$yn = false){
		return DB::fetch_first('SELECT * FROM %t WHERE `key`=%s AND `gid`=%d AND `use`=%d',array($this->_table,$key,$gid,$yn?1:0));
	}
	public function fetchbyone($gid,$yn = false){
		return DB::fetch_first('SELECT * FROM %t WHERE `gid`=%d AND `use`=%d',array($this->_table,$gid,$yn?1:0));
	}
	public function getrange($condition,$start = 0, $limit = 0,$sort = 'DESC'){
		$where='1 ';
		if($sort) {
			$this->checkpk();
		}
		if(is_array($condition)){
			$where = DB::implode_field_value($condition, ' AND ');
		}
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' WHERE '.$where.($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}
	public function getcount($condition){
		$where='1 ';
		if(is_array($condition)){
			$where = DB::implode_field_value($condition, ' AND ');
		}
		return DB::result_first('SELECT count(*) FROM '.DB::table($this->_table).' WHERE '.$where);
	}
	
}

?>