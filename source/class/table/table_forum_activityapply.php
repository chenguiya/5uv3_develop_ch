<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_forum_activityapply.php 28709 2012-03-08 08:53:48Z liulanbo $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_activityapply extends discuz_table
{
	public function __construct() {

		$this->_table = 'forum_activityapply';
		$this->_pk    = 'applyid';

		parent::__construct();
	}
                        //add by xurui 2015-09-16
                      public function fetch_info_by_uid($uid){
                               return DB::fetch_all("SELECT * FROM %t WHERE  uid=%d and verified = 1 ORDER BY dateline DESC", array($this->_table, $uid));
                      }
                      	public function fetch_info_by_tid($tid) {
		return DB::fetch_all("SELECT * FROM %t WHERE tid=%d ORDER BY dateline DESC", array($this->_table, $tid));
	}
	public function fetch_info_for_user($uid, $tid) {
		return DB::fetch_first("SELECT * FROM %t WHERE tid=%d AND uid=%d", array($this->_table, $tid, $uid));
	}
	public function delete_for_user($uid, $tid) {
		DB::query("DELETE FROM %t WHERE tid=%d AND uid=%d", array($this->_table, $tid, $uid));
	}
	public function delete_by_tid($tids) {
		return DB::delete($this->_table, DB::field('tid', $tids));
	}
	public function delete_for_thread($tid, $applyids = array()) {
		if($applyids) {
			$pksql = " AND ".DB::field('applyid', $applyids);
		}
		DB::query("DELETE FROM %t WHERE tid=%d $pksql", array($this->_table, $tid));
	}
	public function fetch_count_for_thread($tid) {
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE tid=%d AND verified='1'", array($this->_table, $tid));
	}
	public function fetch_all_for_thread($tid, $start = 0, $limit = 100, $uid = 0, $master = 0) {
		$verifiedsql = empty($master) ? ' AND verified=1' : '';
		if(intval($uid)) {
			$verifiedsql .= ' AND uid='.intval($uid);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE tid=%d $verifiedsql ORDER BY dateline DESC".DB::limit($start, $limit), array($this->_table, $tid));
	}
	public function update_verified_for_thread($verified, $tid, $applyid) {
		DB::query("UPDATE %t SET verified=%d WHERE tid=%d AND applyid IN (%n)", array($this->_table, $verified, $tid, $applyid));
	}
	
	//add by Daming 2015-9-6 for wap
	public function fetch_all_unaudited_for_thread($tid, $start = 0, $limit = 100, $uid = 0, $master = 0) {
		$verifiedsql = ' AND verified=0';
		if(intval($uid)) {
			$verifiedsql .= ' AND uid='.intval($uid);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE tid=%d $verifiedsql ORDER BY dateline DESC".DB::limit($start, $limit), array($this->_table, $tid));
	}
	
	public function count_apply_by_tid($tid, $status = 0) {
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE tid=%d AND verified=%d", array($this->_table, $tid, $status));
	}
	
	public function update($val, $data) {
		$condition = '1';
		foreach ($val as $key => $vo) {
			$condition .= ' AND '.DB::field($key, $vo);
		}
// 		var_dump($condition);die;		
		return DB::query('UPDATE %t SET `registration`=1,`sign_time`=' . TIMESTAMP . ' WHERE '.$condition, array($this->_table));
	}
	
	public function count_signed_by_tid_issign($tid, $issign = 1) {
		return intval(DB::result_first("SELECT COUNT(*) FROM %t WHERE tid=%d AND registration=%d", array($this->_table, $tid, $issign)));
	}
	
	public function fetch_all_user_by_issign($tid, $start = 0, $limit = 100, $issign = 1) {
		$signsql = ' AND verified=1 AND registration='.$issign;
		return DB::fetch_all("SELECT * FROM %t WHERE tid=%d $signsql ORDER BY sign_time DESC".DB::limit($start, $limit), array($this->_table, $tid));
	}
}

?>