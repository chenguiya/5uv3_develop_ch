<?php
/*
 * 主页：http://addon.discuz.com/?@ailab
 * 人工智能实验室：Discuz!应用中心十大优秀开发者！
 * 插件定制 联系QQ594941227
 * From www.ailab.cn
 */
 
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_nimba_member extends discuz_table_archive{
	public function __construct() {

		$this->_table = 'nimba_member';
		$this->_pk    = 'uid';
		$this->_pre_cache_key = 'nimba_member_';

		parent::__construct();
	}

	public function insert($uid, $username, $password, $email,$dateline){
		if(($uid = dintval($uid))) {
			$base = array(
				'uid' => $uid,
				'username' => (string)$username,
				'password' => (string)$password,
				'email' => (string)$email,
				'dateline' => intval($dateline)
			);
			parent::insert($base, false, true);
		}
	}
	public function count(){
		$count = DB::result_first('SELECT COUNT(*) FROM %t', array($this->_table));
		return $count;
	}
	public function max_uid() {
		return DB::result_first('SELECT MAX(uid) FROM %t', array($this->_table));
	}

	public function fetch_all_by_range($start,$end) {
		return DB::fetch_all('SELECT * FROM %t ORDER BY uid DESC LIMIT %d,%d', array($this->_table,$start,$end), $this->_pk);
	}
	
	public function fetch_all() {
		return DB::fetch_all('SELECT * FROM %t ORDER BY uid DESC', array($this->_table), $this->_pk);
	}
	
	public function drop() {
		return DB::query('DROP TABLE IF EXISTS %t',array($this->_table));
	}
}

?>