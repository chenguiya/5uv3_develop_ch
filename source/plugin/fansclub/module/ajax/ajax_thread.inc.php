<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;

$fid = isset($_GET['fid']) ? intval($_GET['fid']) : showmessage('球迷会不存在');
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'lastpost';
$orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : 'lastpost';

$count = C::t('#fansclub#plugin_forum_thread')->count_by_fid_fileter($fid);
$page = isset($_GET['page']) ? intval($_GET['page']) : 2;
$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;

if ($pagesize < $count) {
	echo json_encode(array('flag' => -1));
}  elseif (($count%$pagesize) != 0 && $page > intval($count/$pagesize)+1) {
	echo json_encode(array('flag' => -1));
} elseif (($count%$pagesize) == 0 && $page >= intval($count/$pagesize)) {
	echo json_encode(array('flag' => -1));
} else {
	$limit = ($page-1) * $pagesize . ',' . $pagesize;
	$threadlist = array();
	include_once DISCUZ_ROOT.'./source/plugin/fansclub/function/function_home.php';
	foreach (($threadlist = C::t('#fansclub#plugin_forum_thread')->fetch_thread_by_fid_filter($fid)) as $key => $val) {
		$attachment = get_attachment($val['tid']);
		$threadlist[$key]['attachment'] = $attachment;
		$threadlist[$key]['avatar'] = avatar($val['authorid'], 'small', 1);
	}
	echo json_encode($threadlist);
}