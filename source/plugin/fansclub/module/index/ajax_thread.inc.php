<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$type = isset($_GET['type']) ? trim($_GET['type']) : 'thread';
if ($type == 'pic') {	
	$gid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
	//获取主题数
	$count = C::t('#fansclub#plugin_forum_thread')->count($gid, 2);
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
	
	if ($pagesize > $count) {
		echo json_encode(array('flag' => -1));
	} elseif (($count%$pagesize) != 0 && $page > intval($count/$pagesize)+1) {
		echo json_encode(array('flag' => -1));
	} elseif (($count%$pagesize) == 0 && $page >= intval($count/$pagesize)) {
		echo json_encode(array('flag' => -1));
	} else {
		$limit = ($page-1) * $pagesize . ',' . $pagesize;
		$threadlist = array();
		include_once DISCUZ_ROOT.'./source/plugin/fansclub/function/function_home.php';
		foreach (($threadlist = C::t('#fansclub#plugin_forum_thread')->fetch_list_attachment($gid, $limit, 2)) as $key => $vo) {
			$attachment_list = get_attachment($vo['tid']);
			$threadlist[$key]['cover'] = 'data/attachment/forum/'.$attachment_list[0]['attachment'];
			$threadlist[$key]['avatar'] = avatar($vo['authorid'], 'small', 1);
		}
		echo json_encode($threadlist);
	}	
	exit;
}
?>

