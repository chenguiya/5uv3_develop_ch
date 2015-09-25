<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_autoreply_member extends discuz_table
{
	public function __construct()
	{
		$this->_table = 'plugin_autoreply_member';
		$this->_pk = 'uid';
		$this->_pre_cache_key = $this->_table.'_';	

		parent::__construct();
	}

	public function delete_by_uid($uid)
	{
		return $uid ? DB::delete($this->_table, DB::field('uid', $uid)) : false;
	}

	public function fetch_all_uid($no_admin = false)
	{
		$uids = array();
		if ($no_admin) {
			$query = DB::query("SELECT `uid` FROM ".DB::table($this->_table)." WHERE uid!=1 ORDER BY uid DESC");
		} else {
			$query = DB::query("SELECT `uid` FROM ".DB::table($this->_table)." ORDER BY uid DESC");
		}
		while($value = DB::fetch($query)) {
			$uids[] = intval($value['uid']);
		}
		return $uids;
	}
}
