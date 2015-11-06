<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_ua_league_integral_shooter extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_ua_league_integral_shooter';
        $this->_pk = 'league_id';
        parent::__construct();
    }
}