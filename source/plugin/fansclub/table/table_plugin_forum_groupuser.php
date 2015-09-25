<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_forum_groupuser extends discuz_table {
	public function __construct() {
		$this->_table = 'forum_groupuser';
		
		parent::__construct();
	}
	
	public function fetch_all_user_by_fid($fid, $level='') {
		if (empty($fid)) {
			return array();
		}
		if (!empty($level)) {
			$levelsql = ' AND level='.$level;
		} else {
			$levelsql = '';
		}
		
		return DB::fetch_all("SELECT * FROM %t WHERE fid=%d".$levelsql, array($this->_table, $fid));
	}
	
	public function fetch_new_member($where = '', $limit = 11) {
		return DB::fetch_all("SELECT DISTINCT uid FROM %t WHERE uid NOT IN (".$where.") ORDER BY joindateline DESC LIMIT %d", array($this->_table, $limit));
	}
	
	public function fetch_all_group_by_uid($uid)
	{
		if (empty($uid)) {
			return array();
		}
		return DB::fetch_all("SELECT * FROM %t WHERE uid=%d AND level > 0", array($this->_table, $uid));
	}
	
	
}