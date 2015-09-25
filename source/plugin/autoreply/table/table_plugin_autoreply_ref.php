<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_autoreply_ref extends discuz_table
{
	public function __construct()
	{
		$this->_table = 'plugin_autoreply_ref';
		$this->_pk = '';
		$this->_pre_cache_key = $this->_table.'_';	

		parent::__construct();
	}

	public function fetch_by_tid($tid)
	{
		return DB::fetch_first('SELECT * FROM %t WHERE '.DB::field('tid', $tid).' '.DB::limit(0, 1), array($this->_table));
	}

	public function fetch_by_tid_uid($tid, $uid)
	{
		return DB::fetch_first('SELECT * FROM %t WHERE '.DB::field('tid', $tid).' AND '.DB::field('uid', $uid).' '.DB::limit(0, 1), array($this->_table));
	}

	public function count_by_tid($tid)
	{
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE '.DB::field('tid', $tid), array($this->_table));
	}

	public function fetch_all_uid_by_tid($tid)
	{
		return DB::fetch_all('SELECT uid FROM %t WHERE '.DB::field('tid', $tid), array($this->_table));
	}

	public function fetch_last_by_tid($tid)
	{
		return DB::fetch_first('SELECT * FROM %t WHERE '.DB::field('tid', $tid).' ORDER BY `insert_time` DESC '.DB::limit(0, 1), array($this->_table));
	}

	public function fetch_newest()
	{
		return DB::fetch_first('SELECT * FROM %t ORDER BY `insert_time` DESC '.DB::limit(0, 1), array($this->_table));
	}

	public function distinct_count()
	{
		return DB::result_first('SELECT COUNT(DISTINCT(tid)) FROM %t', array($this->_table));
	}
}
