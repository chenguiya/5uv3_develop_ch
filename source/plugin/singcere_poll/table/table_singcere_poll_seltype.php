<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_singcere_poll_seltype extends discuz_table {

    public function __construct() {
        $this->_table = 'singcere_poll_seltype';
        $this->_pk = 'stid';
        parent::__construct();
    }

   
     function fetch_by_condition($con) {
        
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
        }
        $sql = substr($sql, 0, strlen($sql) - 4);
        return DB::fetch_all($sql, array($this->_table),"stid");
    }

}
