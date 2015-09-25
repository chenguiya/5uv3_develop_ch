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
DB::query("DROP TABLE IF EXISTS ".DB::table('nimba_majia')."");
DB::query("DROP TABLE IF EXISTS ".DB::table('nimba_forumupdate')."");
DB::query("DROP TABLE IF EXISTS ".DB::table('nimba_noticeupdate')."");
$finish = TRUE;
?>