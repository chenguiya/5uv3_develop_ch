<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_ua_player extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_ua_player';
        $this->_pk = 'player_id';
        parent::__construct();
    }
    
    public function fetch_by_fid()
    {
        $filed = 'player_id';
        $limit = 10;
        return DB::fetch_all("SELECT * FROM %t ORDER BY %w ASC LIMIT %d", array($this->_table, $filed, $limit));
    }
}