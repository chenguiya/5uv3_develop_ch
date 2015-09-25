<?php
if (!defined('IN_DISCUZ')) exit('Access Denied');

$operation = $_GET['operation'] ? $_GET['operation'] : 'subject';
// var_dump($subjects);
if ($operation == 'subject') {
	showtableheader('话题列表', '');
	showsubtitle();

	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$perpage = max(20, empty($_GET['perpage'])) ? 20 : intval($_GET['perpage']);
	$offset = ($page -1)*$perpage;
	$url = ADMINSCRIPT . '?action=plugins&operation=config&do=33&identifier=singcere_poll&pmod=vote_manage&perpage='.$perpage.'&page='.$page;
	$count = C::t('#singcere_poll#singcere_poll_theme')->count();
	if ($count) {
		$multipage = multi($count, $perpage, $page, $url, 0, 3);
		foreach ((C::t('#singcere_poll#singcere_poll_theme')->fetch_by_condition('', '', $offset, $perpage)) as $v) {
			showtablerow('', '', '<a href="?action=plugins&operation=config&do=33&identifier=singcere_poll&pmod=vote_manage&operation=option&pid='.$v['pid'].'">'.$v['subject'].'</a>');
		}
	}
	showtablerow('','',$multipage) ;
	showtablefooter();
} elseif ($operation == 'option') {
	$pid = isset($_GET['pid']) ? intval($_GET['pid']) : exit;
	$theme = DB::fetch_first('SELECT * FROM '.DB::table('singcere_poll_theme').' WHERE pid='.$pid);

	showtableheader($theme['subject'].'&nbsp;&nbsp;-&nbsp;&nbsp;投票详细数据列表', '');
	showsubtitle(array('投票ID','支持选项','投票人','投票时间'));
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$perpage = max(20, empty($_GET['perpage'])) ? 20 : intval($_GET['perpage']);
	$offset = ($page -1)*$perpage;
	$url = ADMINSCRIPT . '?action=plugins&operation=config&do=33&identifier=singcere_poll&pmod=vote_manage&perpage='.$perpage.'&page='.$page.'&operation=option&pid='.$pid;
	$count = C::t('#singcere_poll#singcere_poll_recorder')->count_by_where("pid={$pid}");

	$selitem = DB::fetch_all('SELECT * FROM '.DB::table('singcere_poll_selitem').' WHERE pid='.$pid, '', 'sid');

	if ($count) {
		$multipage = multi($count, $perpage, $page, $url, 0, 3);
		foreach ((C::t('#singcere_poll#singcere_poll_recorder')->fetch_by_condition('', '', $offset, $perpage)) as $v) {
			showtablerow('', '', array('id'=>$v['rid'], 'option'=>$selitem[$v['sid']]['name'], 'username'=>fetch_username($v['uid']), 'dateline'=>date('Y-m-d H:s', $v['dateline'])));
		}
	} else {
		showmessage('此话题暂无投票');
	}
	showtablerow('','',$multipage) ;
	showtablefooter();

}

//获取用户名
function fetch_username($uid)  {
	$uid = isset($uid) ? intval($uid) : exit;
	$result = DB::fetch_first('SELECT username FROM '.DB::table('common_member').' WHERE uid='.$uid);
	if ($result) {
		return $result['username'];
	} else {
		showmessage('用户不存在');
	}
}
