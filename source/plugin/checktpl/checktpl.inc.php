<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');

$file = isset($_GET['tpl']) ? trim($_GET['tpl']) : 'index';

include template('common/header');
include template('extend/desktop/'.$file);
include template('common/footer');