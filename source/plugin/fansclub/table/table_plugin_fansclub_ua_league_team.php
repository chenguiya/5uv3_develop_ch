<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_ua_league_team extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_ua_league_team';
        $this->_pk = 'league_team_id';
        parent::__construct();
    }
}