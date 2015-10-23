<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_user_log extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_user_log';
        $this->_pk = 'id';
        parent::__construct();
    }
    
    public function fetch_all_by_uid($uid)
    {
        if(empty($uid)) return array();
        $uid = intval($uid);
        return DB::fetch_all("SELECT id, logitime, event, page FROM %t WHERE `uid`=%d ORDER BY ".DB::order($this->_pk, 'DESC').DB::limit(0, 50), array($this->_table, $uid));
    }
    

}