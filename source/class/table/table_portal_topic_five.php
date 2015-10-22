<?php

/**
 *
 *      $Id: table_portal_topic_five.php  2015-10-21 add rui $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_portal_topic_five extends discuz_table
{
	public function __construct() {

		$this->_table = 'portal_topic_five';
		$this->_pk    = 'id';

		parent::__construct();
	}

	public function fetch_all_by_uid($uid) {
		return $uid ? DB::fetch_all('SELECT * FROM %t WHERE uid=%d ', array($this->_table, $uid)) : array();
	}
                      public function insert_type_by_uid($uid,$type){
                          if($type == 1){
                                    if(intval($uid) <= 0) return 0;
                                    $data = "(uid,mylike) values ($uid,$type)";
                                    $table =DB::table($this->_table) ;
                                    $query = "insert into  ".$table. " ".$data;
                                    $result = DB::query($query);
                                    if($result)
			return $result;
		else
			return 0;
                          }elseif($type == 0){
                                    if(intval($uid) <= 0) return 0;
                                    $type_a = 1 ;
                                    $data = "(uid,unlike) values ($uid,$type_a)";
                                    $table =DB::table($this->_table) ;
                                    $query = "insert into  ".$table. " ".$data;
                                    $result = DB::query($query);
                                    if($result)
                                                                   return $result;
		else
			return 0;
                          }
                                    
                      }
                      public function count_ding_cai($action){
                          if(!$action) return 0;
                          if($action == 'like'){
                              return DB::fetch_all('SELECT count(*) FROM %t WHERE mylike=%d ', array($this->_table, 1));
                          }elseif($action == 'unlike'){
                              return DB::fetch_all('SELECT count(*) FROM %t WHERE unlike=%d ', array($this->_table, 1));
                          }
                      }

}

?>

