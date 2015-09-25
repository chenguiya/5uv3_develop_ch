<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_mall_orders extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_mall_orders';
		$this->_pk    = 'id';
		parent::__construct();
	}
	public function range($start = 0, $limit = 0, $where = '',$item='', $sort = 'DESC') {
		if($sort) {
			$this->checkpk();
		}
		$gname ='';
		if($where['gname']){
			$gname = $where['gname'];
			unset($where['gname']);
		}
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).($where||$gname ? ' WHERE '.($gname?DB::field('gname','%'.$gname.'%','like'):'').($where&&$gname?' AND ':'').DB::implode($where, ' AND ') : '').($item ? ' ORDER BY '.DB::order($item, $sort) : '').DB::limit($start, $limit), null, $this->_pk ? $this->_pk : '');
	}
	public function count($where = ''){
		$gname ='';
		if($where['gname']){
			$gname = $where['gname'];
			unset($where['gname']);
		}
		$count = (int) DB::result_first("SELECT count(*) FROM ".DB::table($this->_table).($where||$gname ? ' WHERE '.($gname?DB::field('gname','%'.$gname.'%','like'):'').($where&&$gname?' AND ':'').DB::implode($where, ' AND ') : ''));
		return $count;
	}
	public function getbyorderid($orderid){
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('orderid',$orderid));
	}
}

?>