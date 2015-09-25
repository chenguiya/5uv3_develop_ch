<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_videoscreenshot extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_videoscreenshot';
		$this->_pk = 'pid';
		parent::__construct();
	}
	
	public function fetch_all_by_fid($fid)
	{
		if(intval($fid) == 0) return array();
		return DB::fetch_all("SELECT `pid`, `fid`, `tid`, FROM_UNIXTIME(`dateline`) as dateline, `pic_path`, `video_url` FROM %t WHERE `fid`=%s ORDER BY dateline DESC", array($this->_table, $fid));
	}
	
	public function fetch_all_by_tid($tid)
	{
		if(intval($tid) == 0) return array();
		return DB::fetch_all("SELECT `pid`, `fid`, `tid`, FROM_UNIXTIME(`dateline`) as dateline, `pic_path`, `video_url` FROM %t WHERE `tid`=%s ORDER BY dateline DESC", array($this->_table, $tid));
	}
}