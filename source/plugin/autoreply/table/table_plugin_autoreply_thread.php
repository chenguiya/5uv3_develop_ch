<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_autoreply_thread extends discuz_table
{
	public function __construct()
	{
		$this->_table = 'plugin_autoreply_thread';
		$this->_pk = 'tid';
		$this->_pre_cache_key = $this->_table.'_';	

		parent::__construct();
	}
}
