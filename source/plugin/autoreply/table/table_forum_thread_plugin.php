<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_thread_plugin extends discuz_table
{
	public function __construct()
	{
		$this->_table = 'forum_thread';
		$this->_pk    = 'tid';
		$this->_pre_cache_key = $this->_table.'_';	
		parent::__construct();
	}

	public function fetch($new = true, $dateline = 0, $forums = array(), $closed = 0, $limit = 1) 
	{
		$tableid = 0;
		if ($new) {
			if (mt_rand()/mt_getrandmax() <= 0.6) {
				$sql = sprintf(
				//	"SELECT * FROM %s WHERE `fid` IN (".implode(',', $forums).") AND displayorder>=0 AND `isgroup`='0' AND `replies`='0' AND `closed`='%s' %s ORDER BY `views`,`dateline` DESC LIMIT %s", 
					"SELECT * FROM %s WHERE `fid` IN (".implode(',', $forums).") AND displayorder>=0 AND `isgroup`='0' AND `replies`='0' AND `closed`='%s' %s LIMIT %s", 
					DB::table($this->get_table_name($tableid)), 
					$closed, 
					$dateline>0?"AND `dateline`>=$dateline":'', 
					$limit
				);
			} else {
				if (DB::result_first('SELECT COUNT(*) FROM '.DB::table('forum_thread')) > 10000) {
					$tid = DB::result_first('SELECT FLOOR(MAX(tid)*RAND()) FROM %t', array($this->_table));
					if ($tid > 0) {
						$sql = sprintf(
							"SELECT * FROM %s WHERE `tid`>='%s' AND `fid` IN (".implode(',', $forums).") AND displayorder>=0 AND `isgroup`='0' AND `closed`='%s' %s LIMIT %s", 
							DB::table($this->get_table_name($tableid)), 
							$tid, 
							$closed, 
							$dateline>0?"AND `dateline`>=$dateline":'', 
							$limit
						);
					} else {
						return null;
					}
				} else {
					$sql = sprintf(
						"SELECT * FROM %s WHERE `fid` IN (".implode(',', $forums).") AND displayorder>=0 AND `isgroup`='0' AND `closed`='%s' %s ORDER BY RAND() LIMIT %s", 
						DB::table($this->get_table_name($tableid)),
						$closed, 
						$dateline>0?"AND `dateline`>=$dateline":'', 
						$limit
					);
				}
			}
		} else {
			if (mt_rand()/mt_getrandmax() <= 0.6) {
				if (DB::result_first('SELECT COUNT(*) FROM '.DB::table('forum_thread')) > 10000) {
					$tid = DB::result_first('SELECT FLOOR(MAX(tid)*RAND()) FROM %t', array($this->_table));
					if ($tid > 0) {
						$sql = sprintf(
							"SELECT * FROM %s WHERE `tid`>='%s' AND `fid` IN (".implode(',', $forums).") AND displayorder>=0 AND `isgroup`='0' AND `closed`='%s' %s LIMIT %s", 
							DB::table($this->get_table_name($tableid)), 
							$tid, 
							$closed, 
							$dateline>0?"AND `dateline`>=$dateline":'', 
							$limit
						);
					} else {
						return null;
					}
				} else {
					$sql = sprintf(
						"SELECT * FROM %s WHERE `fid` IN (".implode(',', $forums).") AND displayorder>=0 AND `isgroup`='0' AND `closed`='%s' %s ORDER BY RAND() LIMIT %s", 
						DB::table($this->get_table_name($tableid)),
						$closed, 
						$dateline>0?"AND `dateline`>=$dateline":'', 
						$limit
					);
				}
			} else {
				$sql = sprintf(
				//	"SELECT * FROM %s WHERE `fid` IN (".implode(',', $forums).") AND displayorder>=0 AND `isgroup`='0' AND `replies`='0' AND `closed`='%s' %s ORDER BY `views`,`dateline` DESC LIMIT %s", 
					"SELECT * FROM %s WHERE `fid` IN (".implode(',', $forums).") AND displayorder>=0 AND `isgroup`='0' AND `replies`='0' AND `closed`='%s' %s LIMIT %s", 
					DB::table($this->get_table_name($tableid)), 
					$closed, 
					$dateline>0?"AND `dateline`>=$dateline":'', 
					$limit
				);
			}
		}

		return DB::fetch_all($sql);
	}

	public function fetch_by_tid($tid)
	{
		$sql = "SELECT * FROM ".DB::table($this->_table)." WHERE tid=$tid AND displayorder>=0 AND `closed`=0";
		return DB::fetch_all($sql);	
	}

	public function get_table_name($tableid = 0)
	{
		$tableid = intval($tableid);
		return $tableid ? $this->_table."_$tableid" : $this->_table;
	}
}
