<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

loadcache('plugin');
global $_G;

$action = isset($_GET['action']) ? trim($_GET['action']) : '';
$action_arr = array('rss');
if (!in_array($action, $action_arr)) showmessage('undefined_action');

$filepath = DISCUZ_ROOT.'./source/plugin/extends/module/index/'.$action.'.inc.php';
if (!file_exists($filepath)) showmessage('undefined_action');

include $filepath;

?>