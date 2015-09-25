<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_plugin_common_block_item extends discuz_table {
	public function __construct() {

		$this->_table = 'common_block_item';
		$this->_pk    = 'itemid';

		parent::__construct();
	}
	
	public function fetch_all_by_bid($bids, $page = 1, $pagesize = 10, $sort = false) {
		$start = ($page - 1) * $pagesize;
		$limit = ' LIMIT '.$start.','.$pagesize;
		return ($bids = dintval($bids, true)) ? DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('bid', $bids).($sort ? ' ORDER BY displayorder, itemtype DESC' : '').$limit, null, $this->_pk) : array();
	}
}