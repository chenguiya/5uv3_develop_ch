<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_ucharge_log extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_ucharge_log';
		$this->_pk = 'orderid';
		parent::__construct();
	}
	
	public function fetch($uid, $fid) {
		return DB::fetch_first("SELECT * FROM ".DB::table($this->_table)." WHERE uid={$uid} AND fid={$fid}");
	}
	
	public function count($where = '1') {
		$data = DB::fetch_first("SELECT COUNT(*) num FROM %t WHERE ".$where, array($this->_table));
		return $data['num'];
	}
	
	public function fetch_by_where($where = '1', $start = 0, $num = 10) {
		return DB::fetch_all("SELECT * FROM %t WHERE ".$where." ORDER BY log_id DESC LIMIT %d,%d", array($this->_table, $start, $num));
	}
    
    public function count_by_where($where = '1')
    {
        $arr_result = DB::fetch_first("SELECT count(*) as totle FROM %t WHERE ".$where, array($this->_table));
        return $arr_result['totle'];
    }
    
    public function stat_by_where($where = '1')
    {
        $query = DB::query("SELECT FROM_UNIXTIME(a.log_time,'%Y-%m-%d') as time, count(a.log_id) as num, sum(a.amount*a.price) as amount, ".
                                "a.`status`, a.api_type, a.bill_type ".
                            "FROM ".DB::table($this->_table)." a WHERE ".$where." ".
                            "GROUP BY time, a.`status`, a.api_type, a.bill_type ".
                            "ORDER BY time DESC, a.api_type ASC");
        $arr_result = array();
        $i = 0;
        
        while($row = DB::fetch($query))
        {
            $arr_result[$i] = $row;
            $i++;
        }
        
        return $arr_result;
    }
    
    public function range($start = 0, $limit = 0, $where = '', $item = '', $sort = 'DESC') {
    	if ($sort) $this->checkpk();
    	$gname = '';
//     	if ($where['gname']) {
//     		$gname = $where['gname'];
//     		unset($where['gname']);
//     	}
    	return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).($where ? ' WHERE '.DB::implode($where, ' AND ') : '').($item ? ' ORDER BY '.DB::order($item, $sort) : '').DB::limit($start, $limit), null, 'log_id');
    }
    
    public function countorders($where = '') {
    	$count = (int) DB::result_first("SELECT COUNT(*) FROM ".DB::table($this->_table).($where ? ' WHERE '.DB::implode($where, ' AND ') : ''));
    	return $count;
    }
}
