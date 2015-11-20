<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_ua_team extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_ua_team';
        $this->_pk = 'team_id';
        parent::__construct();
    }
    //球队类别七人和五人
    public function fetchAllTeamByLeagueId($lid, $start = 0, $limit = 0, $sort = '')
    {
            if(intval($lid) <= 0) return array();

            return DB::fetch_all("SELECT fid,team_name,logo,team_intro FROM %t WHERE `league_id`=%d".
                    ($sort ? ' ORDER BY '.DB::order($this->_pk, $sort) : '').
                    DB::limit($start, $limit), array($this->_table, intval($lid)), '');
    }
    
    
    // 首页显示的全部队伍
    public function fetchAllTeamForIndex()
    {
        $row = DB::fetch_all("select fid,team_name,logo from %t group by fid,team_name,logo order by fid asc", array($this->_table));
        return $row;
    }
    
    //球队头数据
    public function fetchAllTeamDataByFid($fid){
            if(intval($fid) <= 0) return array();
            $row = DB::fetch_first("select fid,team_name,logo,team_intro from %t  where fid=".$fid, array($this->_table));
            return $row;
    }
    
}