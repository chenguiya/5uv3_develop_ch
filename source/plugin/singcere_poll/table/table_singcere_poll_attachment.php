<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_singcere_poll_attachment extends discuz_table {

    public function __construct() {
        $this->_table = 'singcere_poll_attachment';
        $this->_pk = 'aid';
        parent::__construct();
    }

    function delete_batch($aids) {

        $sql = "DELETE FROM %t WHERE " . DB::field("aid", $aids);
        DB::query($sql, array($this->_table));
    }

    function update_batch($aids, $value) {

        $sql = "UPDATE %t SET $value[field]=%d WHERE  " . DB::field("aid", $aids);
        DB::query($sql, array($this->_table, $value[value]));
    }

    function fetch_all_by_sids($sids) {

        $sql = "SELECT attachment,sid FROM %t WHERE " . DB::field("sid", $sids);
        $temp = DB::fetch_all($sql, array($this->_table));
        
        foreach ($temp as $key => $value) {
            $return[$value[sid]][] = $value;
        }
        return $return;
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
        return DB::fetch_all($sql, array($this->_table), "aid");
    }

}
