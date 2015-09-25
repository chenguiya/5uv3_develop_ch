<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_fans_credit_log extends discuz_table {
	public function __construct() {
		$this->_table = 'plugin_fans_credit_log';
		$this->_pk = 'id';
		
		parent::__construct();
	}
	
	public function fetch_weekcredits_ban($fid, $dateline) {
		return DB::fetch_all("SELECT uid,username,SUM(credits) weekcredits FROM %t WHERE fid=%d  AND dateline>%d GROUP BY uid ORDER BY weekcredits DESC LIMIT 0,7", array($this->_table, $fid, $dateline));
	}
}