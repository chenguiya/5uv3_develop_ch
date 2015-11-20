<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_ua_match extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_ua_match';
        $this->_pk = 'match_id';
        parent::__construct();
    }
     //球迷会赛程web
    /*
     * $lid  赛制id
     * $mid 轮次id
     */
    public function fetchAllMatchData($lid,$mid)
    {
          if(intval($lid) <= 0 || intval($mid) <= 0) return array();
        $row = DB::fetch_all("select * from %t where league_id=".$lid." and num_round=".$mid, array($this->_table));
        return $row;
    }
    //球迷会赛程wap
        public function fetchAllMatchDataWap()
    {
        $row = DB::fetch_all("select * from %t ",array($this->_table));
        return $row;
    }
}