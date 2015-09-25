<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_forum_thread extends discuz_table {
	public function __construct() {
		$this->_table = 'forum_thread';
		$this->_pk    = 'tid';
		$this->_pre_cache_key = 'forum_thread_';
		parent::__construct();
	}
	
	
	public function fetch_list_attachment($fid, $limit = '20', $attachment = 0, $start = 0, $orderby = 'tid') {
		$data = array();
		if(intval($limit) == 0) // ·µ»ØÌõÊý
		{
			return DB::fetch_first("SELECT count(*) as num FROM %t WHERE fid=%d AND attachment=%d", array($this->_table, $fid, $attachment));
		}
		else
		{
			return DB::fetch_all("SELECT * FROM %t WHERE fid=%d AND attachment=%d ORDER BY ".$orderby." DESC LIMIT ".$start.",".$limit, array($this->_table, $fid, $attachment));
		}
	}
	
	public function count($fid, $attachment = 0) {
		$where = ' fid='.$fid.' AND attachment='.$attachment;
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE".$where, array($this->_table));
	}
	
	public function count_by_fid_fileter($fid, $filter = '') {
		$where = ' WHERE fid=' . $fid;
// 		if ($filter == 'hot') $where .= ' AND ';
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE fid=%d", array($this->_table, $fid));
	}
	
	public function fetch_thread_by_fid_filter($fid, $filter = 'lastpost', $orderby = 'lastpost', $limit = '') {
		$extendwhere = '';
		switch ($filter) {
			case 'lastpost':
			;
			break;
			
			case 'heat':
			;
			break;
			
			case 'hot':
			;
			break;
			
			case 'digest':
			;
			break;
			
			case 'specialtype':
			;
			break;
			
			default:
				;
			break;
		}
		return DB::fetch_all("SELECT * FROM %t WHERE fid=%d ORDER BY ".$orderby." DESC LIMIT ".$limit, array($this->_table, $fid));
	}
}