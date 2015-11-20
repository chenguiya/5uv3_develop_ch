<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_ua_league_integral_shooter extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_ua_league_integral_shooter';
        $this->_pk = 'league_id';
        parent::__construct();
    }
    //积分射手数据
    public function fetchAllIntegralShooterData($lid, $start = 0, $limit = 0, $sort = '')
    {
            if(intval($lid) <= 0) return array();

            return DB::fetch_all("SELECT * FROM %t WHERE `league_id`=%d".
                    ($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').
                    DB::limit($start, $limit), array($this->_table, intval($lid)), '');
    }
}