<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

class table_plugin_fansclub_ua_player extends discuz_table {

    public function __construct() {
        $this->_table = 'plugin_fansclub_ua_player';
        $this->_pk = 'player_id';
        parent::__construct();
    }
    
    public function fetch_by_fid($fid = 0)
    {
        if(intval($fid) <= 0)
            return array();
        
        $row_team = DB::fetch_all("SELECT * FROM ".DB::table('plugin_fansclub_ua_team')." WHERE fid = %d", array($fid));
        if(count($row_team) == 1)
        {
            $team_id = $row_team[0]['team_id'];
            $row_player = DB::fetch_all("SELECT a.team_id,a.num, a.position, b.player_id,  b.player_name, b.logo, b.birthday, b.weight, ".
                    "b.height ".
                    "FROM ".DB::table('plugin_fansclub_ua_team_player')." a, ".DB::table('plugin_fansclub_ua_player')." b ".
                    "WHERE a.team_id = %d AND a.player_id = b.player_id", array($team_id));
            return $row_player;
        }
        else
        {
            return array();
        }
    }
}
