<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_autoreply_kv extends discuz_table
{
	public function __construct()
	{
		$this->_table = 'plugin_autoreply_kv';
		$this->_pk = 'key';
		$this->_pre_cache_key = $this->_table.'_';	

		parent::__construct();
	}

	public function fetch_by_key($key)
	{
		return DB::fetch_first('SELECT * FROM %t WHERE '.DB::field('key', $key).' '.DB::limit(0, 1), array($this->_table), $this->_pk);
	}
}
