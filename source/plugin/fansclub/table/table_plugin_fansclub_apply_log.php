<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_apply_log extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_apply_log';
		$this->_pk = 'apply_id';
		parent::__construct();
	}
	
	public function fetch_all_by_fid($fid)
	{
		if(empty($fid))
		{
			return FALSE;
		}
		return DB::fetch_all("SELECT * FROM %t WHERE `fid`=%d", array($this->_table, $fid));
	}
	
	public function fetch_apply_id_by_name($name)
	{
		return DB::result_first("SELECT `apply_id` FROM %t WHERE `fansclub_name`=%s", array($this->_table, $name));
	}
	
	public function fetch_groupnum_by_uid($uid)
	{
		if(empty($uid))
		{
			return FALSE;
		}
		return DB::result_first("SELECT COUNT(*) FROM ".DB::table($this->_table)." WHERE `uid`=%d", array($uid));
	}
	
	public function fetch_all_by_page($start = 0, $limit = 1000, $sort = 'DESC')
	{
		global $config;
		$arr_return = FALSE;
		
		$arr_data = $this->range($start, $limit, $sort);
		if(count($arr_data) > 0)
		{
			$i = 0;
			foreach($arr_data as $key => $value)
			{
				$arr_return[$i]['apply_id'] = $value['apply_id'];
				$arr_return[$i]['username'] = $value['username'];
				$arr_return[$i]['log_time'] = date('Y-m-d H:i:s', $value['log_time']);
				$arr_return[$i]['fansclub_name'] = $value['fansclub_name'];
				$arr_return[$i]['status'] = $config['apply_status'][$value['status']];
				$arr_return[$i]['need_support'] = $value['need_support'];
				$arr_return[$i]['have_support'] = $value['have_support'];
				$arr_return[$i]['fid'] = $value['fid'];
				$i++;
			}
		}
		return $arr_return;
	}
}
