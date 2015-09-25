<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');
$_settings = $_G['cache']['plugin']['rights'];

$rtypes = array();
$plugin = C::t('common_plugin')->fetch_by_identifier('rights');
$pluginvar = C::t('common_pluginvar')->fetch_all_by_pluginid($plugin['pluginid']);
$arr1 = dunserialize($_settings['rights_type']);
$arr2 = explode(chr(13).chr(10), $pluginvar[1]['extra']);
foreach ($arr2 as $k => $v) {
	$arr3 = explode(' = ', $v);
	$arr2[$k] = $arr3[1];
}
foreach ( $arr1 as $vo) {
	$rtypes[$vo] = $arr2[$vo];
}

$typeid = isset($_GET['typeid']) ? intval($_GET['typeid']) : 0;
$where = '1';
if ($typeid != 0) {
	$where .= " AND `typeid`=" . $typeid;
}
$count = C::t('#rights#plugin_rights')->count_by_where($where);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 6;

$start = ($page - 1)*$pagesize;
$rlists = array();
$rlists = C::t('#rights#plugin_rights')->fetch_all_by_where($where, $start, $pagesize);

$multipage = multi($count, $pagesize, $page, 'plugin.php?id=rights:index&op=mall&typeid='.$typeid);