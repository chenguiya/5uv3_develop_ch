<?php
/*
 * 主页：http://addon.discuz.com/?@ailab
 * 人工智能实验室：Discuz!应用中心十大优秀开发者！
 * 插件定制 联系QQ594941227
 * From www.ailab.cn
 */
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$pagenum=20;
$page=max(1,intval($_GET['page']));
$uasrname=array();
$count=DB::result_first("select count(*) from ".DB::table("nimba_majia")." ");
$data=DB::fetch_all("select * from ".DB::table("nimba_majia")." order by uid asc limit ".($page-1)*$pagenum.",$pagenum");
showtableheader(lang('plugin/nimba_majia', 'appname'));
showsubtitle(array('',lang('plugin/nimba_majia', 'mainuser'),lang('plugin/nimba_majia', 'repeat'),''));
foreach($data as $item) {
	if(!isset($uasrname[$item['uid']])) $uasrname[$item['uid']]=DB::result_first("SELECT username FROM ".DB::table('common_member')." WHERE uid='".intval($item['uid'])."'");
	showtablerow('', array('class="td25"', 'class="td_k"', 'class="td_l"'), array(
		'',
		'<a href="home.php?mod=space&uid='.$item['uid'].'" target="_blank">'.$uasrname[$item['uid']].'</a>',
		'<a href="home.php?mod=space&uid='.$item['useruid'].'" target="_blank">'.$item['username'].'</a>',
		'',
	));
			
}
showtablefooter();
echo multi($count,$pagenum,$page,ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=nimba_majia&pmod=data");

?>