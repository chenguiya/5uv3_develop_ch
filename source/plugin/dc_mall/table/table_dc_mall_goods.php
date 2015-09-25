<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_mall_goods extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_mall_goods';
		$this->_pk    = 'id';
		parent::__construct();
	}
	public function goodscheck($extid){
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('extid',$extid));
	}
	public function goodscheckbysortid($sortid){
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('sortid',$sortid));
	}
	public function range($start = 0, $limit = 0, $where = '',$item='', $sort = 'DESC') {
		if($sort) {
			$this->checkpk();
		}
		$gname ='';
		if($where['name']){
			$gname = $where['name'];
			unset($where['name']);
		}
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).($where||$gname ? ' WHERE '.($gname?DB::field('name','%'.$gname.'%','like'):'').($where&&$gname?' AND ':'').DB::implode($where, ' AND ') : '').($item ? ' ORDER BY '.DB::order($item, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}
	public function count($where = ''){
		$gname ='';
		if($where['name']){
			$gname = $where['name'];
			unset($where['name']);
		}
		$count = (int) DB::result_first("SELECT count(*) FROM ".DB::table($this->_table).($where||$gname ? ' WHERE '.($gname?DB::field('name','%'.$gname.'%','like'):'').($where&&$gname?' AND ':'').DB::implode($where, ' AND ') : ''));
		return $count;
	}
}

?>