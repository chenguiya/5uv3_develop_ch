<?php
if(!defined('IN_DISCUZ')) exit('Access Denied'); 

class table_plugin_fansclub_info extends discuz_table {

	public function __construct() {
		$this->_table = 'plugin_fansclub_info';
		$this->_pk = 'fid';
		parent::__construct();
	}
	
	public function fetch_all_for_search($conditions, $start = 0, $limit = 20, $sort = '')
	{
		$arr_return = array();
		
		if(empty($conditions)) return array();
		
		if($start == -1)
		{
			$arr_tmp = DB::result_first("SELECT count(*) FROM ".DB::table($this->_table)." f WHERE %i AND f.displayorder >= 0 ", array($conditions));
		}
		else
		{
			$sql = "SELECT f.fid, f.name, f.relation_fid, f.relation_name, f.province_city, f.league_id, f.club_id, ".
						"f.star_id, f.province_id, f.city_id, f.district_id, f.community_id, f.logo, f.brief, f.rules, ".
						"f.members, f.contribution, f.posts, f.level ".
				   "FROM ".DB::table($this->_table)." f ".
				   "WHERE %i AND f.displayorder >= 0 ";
			$sql .= (!$sort ? ' ORDER BY '.DB::order($this->_pk, 'DESC') : ' ORDER BY '.DB::order($sort, 'DESC')).DB::limit($start, $limit);
			$arr_tmp = DB::fetch_all($sql, array($conditions));
		}
		
		$arr_return = $arr_tmp;
		return $arr_return;
	}
	
	public function update_info_from_forum()
	{
		$sql = "SELECT f.fid, f.members, f.contribution, f.posts, f.level FROM ".DB::table($this->_table)." f WHERE f.displayorder >= 0";
		$arr_tmp = DB::fetch_all($sql);
		for($i = 0; $i < count($arr_tmp); $i++)
		{
			$group_fid = intval($arr_tmp[$i]['fid']);
			$_arr_forum = C::t('forum_forum')->fetch($group_fid);
			$_arr_forumfield = C::t('forum_forumfield')->fetch($group_fid);
			$_arr_balance = C::t('#fansclub#plugin_fansclub_balance')->fetch_first($group_fid);
			DB::query("UPDATE ".DB::table($this->_table)." SET members = ".intval($_arr_forumfield['membernum']).", contribution = ".intval($_arr_balance['extendcredits3']).", posts = ".intval($_arr_forum['posts']).", level = ".intval($_arr_forum['level']).", displayorder = ".intval($_arr_forum['displayorder'])." WHERE fid = ".$group_fid);
		}
	}
	
	/**
	 * 查询和版块对应的分类id
	 * @param int $fid
	 * @return array
	 */
	public function fetch_fansclub_info_by_relation_fid($relation_id) {
		return DB::fetch_first("SELECT * FROM %t WHERE relation_fid=%d", array($this->_table, $relation_id));
	}
	
	public function fetch_fansclub_info_by_fid($fid) {
		return DB::fetch_first("SELECT * FROM %t WHERE fid=%d", array($this->_table, $fid));
	}
	
	public function fetch_all_fansclub_info_by_relation_fid($relation_id) {
		return DB::fetch_all("SELECT * FROM %t WHERE relation_fid=%d", array($this->_table, $relation_id));
	}
	
	public function fetch_fansclub_credits($fid, $type = 'forum') {
		$credits = 0;
		$query = DB::fetch_all("SELECT fid FROM %t WHERE relation_fid=%d", array($this->_table, $fid));
		
		if (!empty($query)) {
			foreach ($query as $vo) {
// 				var_dump($vo);
				$balance = DB::fetch_first("SELECT * FROM ".DB::table('plugin_fansclub_balance')." WHERE relation_fid=".$vo['fid']);
				$credits += $balance['extendcredits3'];
			}
		}
		return $credits;
	}
}
