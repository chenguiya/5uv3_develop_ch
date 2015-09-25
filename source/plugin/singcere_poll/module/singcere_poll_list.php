<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$editors = explode(",", $_G['cache']['plugin']['singcere_poll']['editors']);
  $perm = true;
if ($_G['uid'] == 0 || !in_array($_G['uid'], $editors)) {
    $perm = false;
}
    $total = C::t('#singcere_poll#singcere_poll_theme')->count();

    $curpage = max(1, intval($_GET['page']));
    $perpage = 10;
    $url = "plugin.php?id=singcere_poll:singcere_poll&mod=list";
    $muti = multi($total, $perpage, $curpage, $url);
    list($navtitle, $metadescription, $metakeywords) = array(lang('plugin/singcere_poll', 'poll'), lang('plugin/singcere_poll', 'poll'), $_G['setting']["bbname"].lang('plugin/singcere_poll', 'poll'));
   $t_list = DB::fetch_all("SELECT t.*,a.attachment FROM %t t LEFT JOIN %t a ON t.pid=a.pid and a.type=%s ORDER BY pid DESC limit %d,%d",array("singcere_poll_theme","singcere_poll_attachment","head", ($curpage - 1) * $perpage, $perpage));

   include template('diy:singcere_poll_list', 0, 'source/plugin/singcere_poll/template');

