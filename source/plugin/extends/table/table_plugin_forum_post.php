<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_forum_post extends discuz_table {
	private static $_tableid_tablename = array();
	public function __construct() {
		$this->_table = 'forum_post';
		$this->_pk = 'pid';
		
		parent::__construct();
	}
	
	public static function get_tablename($tableid, $primary = 0) {
		list($type, $tid) = explode(':', $tableid);
		if(!isset(self::$_tableid_tablename[$tableid])) {
			if($type == 'tid') {
				self::$_tableid_tablename[$tableid] = self::getposttablebytid($tid, $primary);
			} else {
				self::$_tableid_tablename[$tableid] = self::getposttable($type);
			}
		}
		return self::$_tableid_tablename[$tableid];
	}
	
	public static function getposttablebytid($tids, $primary = 0) {
	
		$isstring = false;
		if(!is_array($tids)) {
			$thread = getglobal('thread');
			if(!empty($thread) && isset($thread['posttableid']) && $tids == $thread['tid']) {
				return 'forum_post'.(empty($thread['posttableid']) ? '' : '_'.$thread['posttableid']);
			}
			$tids = array(intval($tids));
			$isstring = true;
		}
		$tids = array_unique($tids);
		$tids = array_flip($tids);
		if(!$primary) {
			loadcache('threadtableids');
			$threadtableids = getglobal('threadtableids', 'cache');
			empty($threadtableids) && $threadtableids = array();
			if(!in_array(0, $threadtableids)) {
				$threadtableids = array_merge(array(0), $threadtableids);
			}
		} else {
			$threadtableids = array(0);
		}
		$tables = array();
		$posttable = '';
		foreach($threadtableids as $tableid) {
			$threadtable = $tableid ? "forum_thread_$tableid" : 'forum_thread';
			$query = DB::query("SELECT tid, posttableid FROM ".DB::table($threadtable)." WHERE tid IN(".dimplode(array_keys($tids)).")");
			while ($value = DB::fetch($query)) {
				$posttable = 'forum_post'.($value['posttableid'] ? "_$value[posttableid]" : '');
				$tables[$posttable][$value['tid']] = $value['tid'];
				unset($tids[$value['tid']]);
			}
			if(!count($tids)) {
				break;
			}
		}
		if(empty($posttable)) {
			$posttable = 'forum_post';
			$tables[$posttable] = array_flip($tids);
		}
		return $isstring ? $posttable : $tables;
	}
	
	public function fetch_post_by_tid($tableid, $tid, $outmsg = true) {
		$post = DB::fetch_first('SELECT * FROM %t WHERE tid=%d AND first=1', array(self::get_tablename($tableid), $tid));
		if(!$outmsg) {
			unset($post['message']);
		}
		return $post;
	}
	
	public static function getposttable($tableid = 0, $prefix = false) {
		global $_G;
		$tableid = intval($tableid);
		if($tableid) {
			loadcache('posttableids');
			$tableid = $_G['cache']['posttableids'] && in_array($tableid, $_G['cache']['posttableids']) ? $tableid : 0;
			$tablename = 'forum_post'.($tableid ? "_$tableid" : '');
		} else {
			$tablename = 'forum_post';
		}
		if($prefix) {
			$tablename = DB::table($tablename);
		}
		return $tablename;
	}
}