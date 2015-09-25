<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_fans_credit extends discuz_table {
	public function __construct() {
		$this->_table = 'plugin_fans_credit';
		$this->_pk = 'id';
		parent::__construct();
	}
	
	public function update_credit($uid, $fid, $credits, $lastdate) {
		if (!intval($fid)) return false;
		if ((TIMESTAMP - $lastdate) > 1800) {
			DB::query("UPDATE %t SET weekcredits = weekcredits+%d, credits = credits+%d, lastdate = %d WHERE uid=%d AND gid=%d", array($this->_table, $credits, $credits, TIMESTAMP, $uid, $fid));;
		} else {
			DB::query("UPDATE %t SET weekcredits = weekcredits+%d, credits = credits+%d WHERE uid=%d AND gid=%d", array($this->_table, $credits, $credits, $uid, $fid));
		}
	}
	
	public function fetch_credit_by_fid_uid($uid, $fid) {
		if (!intval($fid)) return false;
		
		return DB::fetch_first("SELECT * FROM %t WHERE `uid`=%d AND `gid`=%d", array($this->_table, $uid, $fid));
	}
	
	public function fetch_credits_ban_by_fid($fid) {
		return DB::fetch_all("SELECT username,credits FROM %t WHERE `gid`=%d ORDER BY credits DESC LIMIT 0,7",array($this->_table, $fid));
	}
	
	public  function fetch_sum_credits_ban() {
		return DB::fetch_all("SELECT `uid`,`username`,SUM(credits) total FROM %t GROUP BY `uid` ORDER BY total DESC LIMIT 0,7", array($this->_table));
	}
	
	public function fetch_max_credits_fansclub($uid) {
		return DB::fetch_all("SELECT * FROM %t WHERE uid=%d ORDER BY credits DESC LIMIT 1", array($this->_table, $uid));
	}
	
	public function fetch_active_member() {
		return DB::fetch_all("SELECT SUM(credits) total,uid FROM %t GROUP BY uid ORDER BY total DESC LIMIT 11", array($this->_table));
	}
}