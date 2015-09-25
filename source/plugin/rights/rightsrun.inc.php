<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');

$ac = isset($_GET['ac']) ? trim($_GET['ac']) : '';
include_once DISCUZ_ROOT.'./source/plugin/rights/module/index/'.$ac.'.inc.php';
include template('rights:index/'.$ac);