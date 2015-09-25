<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class table_plugin_member_rights extends discuz_table 
{
	public function __construct() {
		$this->_table = 'plugin_member_rights';
		$this->_pk = '';
		
		parent::__construct();
	}
	
	public function fetch_rightsid_by_uid($uid, $start = 0, $num = 10) {
		return DB::fetch_all("SELECT `rightsid` FROM %t WHERE `idtype`='uid' AND `uid`=%d LIMIT %d,%d", array($this->_table, $uid, $start, $num));
	}
	
	public function delete($uid = null, $rightsid = null) {
		$para = array();
		if ($uid) {
			$para[] = DB::field('uid', $uid);
		}
		if ($rightsid) {
			$para[] = DB::field('rightsid', $rightsid);
		}
		if (!$where = $para ? implode(' AND ', $para) : '') {
			return null;
		}
		return DB::delete($this->_table, $where);
	}
	
	public function increase($uid, $rightsid, $setarr, $slient = false, $unbuffered = false) {
		$para = array();
		$setsql = array();
		$allowkey = array('num');
		foreach ($setarr as $key => $value) {
			if (($value = intval($value)) && in_array($key, $allowkey)) {
				$setsql[] = "`$key`=`$key`+'$value'";
			}
		}
		if ($uid) {
			$para[] = DB::field('uid', $uid);
		}
		if ($rightsid) {
			$para[] = DB::field('rightsid', $rightsid);
		}
		if (!count($para) || !count($setsql)) {
			return null;
		}
		$sql = implode(' AND ', $para);
		return DB::query('UPDATE %t SET %i WHERE %i', array($this->_table, implode(',', $setsql), $sql), $slient, $unbuffered);
	}
	
	public function fetch_rights_buy($uid, $rightsid) {
		return DB::fetch_first("SELECT * FROM %t WHERE uid=%d AND rightsid=%d", array($this->_table, $uid, $rightsid));
	}
	
	public function fetch_all_fid_by_rightsids($rightsids) {
		if(empty($rightsids)) {
			return array();
		}
		$data = array();
		$query = DB::query("SELECT uid FROM %t WHERE %i", array($this->_table, DB::field('rightsid', $rightsids)));
		while($row = DB::fetch($query)) {
			$data[] = $row['uid'];
		}
		return $data;
	}
	
	public function count($uid) {
		return ($uid = (string)$uid) ? DB::result_first("SELECT COUNT(*) FROM ".DB::table($this->_table)." WHERE uid=".$uid) : 0;
	}
}