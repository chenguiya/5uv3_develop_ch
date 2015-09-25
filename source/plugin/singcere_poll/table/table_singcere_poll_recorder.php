<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_singcere_poll_recorder extends discuz_table {

    public function __construct() {
        $this->_table = 'singcere_poll_recorder';
        $this->_pk = 'rid';
        parent::__construct();
    }




    function fetch_by_condition($con, $ordersql, $start = 0, $pnum = 0) {
        $sql = "SELECT * FROM %t ";
        if (!empty($con)) {
            $sql.="WHERE ";
            foreach ($con as $key => $value) {
                if (!is_array($value)) {
                    $sql.=$key . "=" . $value . " AND ";
                } else {
                    $sql.=DB::field($key, $value) . " AND ";
                }
            }
            $sql = substr($sql, 0, strlen($sql) - 4);
        }
        if (!empty($ordersql)) {
            $sql.=$ordersql;
        }
        if ($pnum != 0) {
            $sql.=" LIMIT $start,$pnum";
        }
        return DB::fetch_all($sql, array($this->_table));
    }

	public function count_by_where($where) {
		return ($where = (string)$where) ? DB::result_first('SELECT COUNT(*) FROM '.DB::table('singcere_poll_recorder').' WHERE '.$where) : 0;
	}
}
