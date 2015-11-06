<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_ua_match extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_ua_match';
        $this->_pk = 'match_id';
        parent::__construct();
    }
}