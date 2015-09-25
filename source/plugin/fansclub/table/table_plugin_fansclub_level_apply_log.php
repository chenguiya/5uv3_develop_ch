<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_level_apply_log extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_level_apply_log';
		$this->_pk = 'apply_id';
		parent::__construct();
	}
	
	public function fetch_all_by_fid($fid)
	{
		if(empty($fid))
		{
			return FALSE;
		}
		return DB::fetch_all("SELECT * FROM %t WHERE `fid`=%d ORDER BY ".DB::order($this->_pk, 'DESC'), array($this->_table, $fid));
	}
	
	public function fetch_all_by_relation_fid($relation_fid)
	{
		if(empty($relation_fid))
		{
			return FALSE;
		}
		return DB::fetch_all("SELECT * FROM %t WHERE `relation_fid`=%d ORDER BY ".DB::order($this->_pk, 'DESC'), array($this->_table, $relation_fid));
	}
	
	// relation_fid 
	public function relation_fid_can_apply($relation_fid, $level_type = 0)
	{
		if(empty($relation_fid))
		{
			return FALSE;
		}
		
		if($level_type == 1) // 官方(5u)认证只能一个版块生效一个，其他认证暂时不做检查
		{
			$arr = $this->fetch_all_by_relation_fid($relation_fid);
			if(count($arr) == 0) // 没有申请记录
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		return TRUE;
		
	}
	
	// 后台管理员审核用，返回要审核的记录数目
	public function validate_level_num()
	{
		return DB::result_first("SELECT count(*) FROM ".DB::table($this->_table)." WHERE status=0");
	}
	
	// 后台管理员审核用，返回要审核的记录
	public function fetch_all_validate($start, $limit)
	{
		$sql =	"SELECT a.*, b.name as c_name, c.name as f_name ".
					//"FROM_UNIXTIME(a.log_time, '%Y-%m-%d %H:%i:%s') as log_time, ".
					//"FROM_UNIXTIME(a.expired_time, '%Y-%m-%d %H:%i:%s') as expired_time ".
				"FROM ".DB::table($this->_table)." a ".
					"LEFT JOIN ".DB::table('forum_forum')." b ON a.fid = b.fid ".
					"LEFT JOIN ".DB::table('forum_forum')." c ON a.relation_fid = c.fid ".
				"WHERE a.status = 0 ".
				"ORDER BY a.apply_id DESC ".
				"LIMIT ".intval($start).', '.intval($limit);
		return DB::fetch_all($sql);
	}
	
	// 通过审核
	public function validate_level_for_group($apply_ids, $confirm_uid = 0, $confirm_time = TIMESTAMP, $confirm_remark = '')
	{
		if(empty($apply_ids)) {
			return false;
		}
		//申请状态：0新建，3审核通过，4审核不通过，5认证过期',
		//a.`status`,a.confirm_uid,a.confirm_time,a.confirm_remark,
		DB::query("UPDATE ".DB::table($this->_table)." SET `status` = 3, confirm_uid = '".$confirm_uid."', confirm_time = '".$confirm_time."', confirm_remark = '".$confirm_remark."' WHERE `status` = 0 AND %i", array(DB::field($this->_pk, $apply_ids)));
	}
	
	// 审核不通过
	public function not_validate_level_for_group($apply_ids, $confirm_uid = 0, $confirm_time = TIMESTAMP, $confirm_remark = '')
	{
		if(empty($apply_ids)) {
			return false;
		}
		DB::query("UPDATE ".DB::table($this->_table)." SET `status` = 4, confirm_uid = '".$confirm_uid."', confirm_time = '".$confirm_time."', confirm_remark = '".$confirm_remark."' WHERE `status` = 0 AND %i", array(DB::field($this->_pk, $apply_ids)));
	}
}
