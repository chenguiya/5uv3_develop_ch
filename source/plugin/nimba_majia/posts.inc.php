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
loadcache('plugin');
$langvars=lang('plugin/nimba_majia');
echo '<script type="text/javascript" src="static/js/calendar.js"></script>';
if($_GET['start']) $start=strtotime($_GET['start'].':00');
else $start=strtotime(date('Y-m',time()).'-1 00:00:00');
if($_GET['end']) $end=strtotime($_GET['end'].':00');
else $end=time();
$uid=intval($_GET['mainuid']);
showformheader("plugins&operation=config&identifier=nimba_majia&pmod=posts");
showtableheader($langvars['posts_title'], 'nobottom');
showsetting($langvars['mainuid'], 'mainuid',$_GET['mainuid'], 'text','',0,$langvars['mainuidinfo_2']);
showsetting($langvars['posts_start'], 'start',date('Y-m-d H:i',$start), 'calendar', '', 0,'', 1);	
showsetting($langvars['posts_end'], 'end',date('Y-m-d H:i',$end), 'calendar', '', 0,'', 1);	
showsubmit('editsubmit');
showtablefooter();
showformfooter();
$where="dateline>$start and dateline<$end";
$users=DB::fetch_all("select uid,useruid,username from ".DB::table("nimba_majia")." where uid='$uid'");
showtableheader($langvars['posts_title_2']);
showsubtitle(array($langvars['posts_uid'],$langvars['posts_majia'],$langvars['posts_thread'],$langvars['posts_post']));
if($uid){
	$posts=DB::result_first("select count(*) as num from ".DB::table("forum_post")." where $where and authorid='".$uid."' and first=0");
	$threads=DB::result_first("select count(*) as num from ".DB::table("forum_post")." where $where and authorid='".$uid."' and first=1");
	$username=DB::result_first("select username from ".DB::table('common_member')." where uid='$uid'");
	showtablerow('', array('class="td25"', 'class="td_k"', 'class="td_l"'), array(
		$uid,
		'<a href="home.php?mod=space&uid='.$uid.'" target="_blank">'.$username.'</a>',
		$threads,
		$posts
	));
}
foreach($users as $k=>$user) {
	$posts=DB::result_first("select count(*) as num from ".DB::table("forum_post")." where $where and authorid='".$user['useruid']."' and first=0");
	$threads=DB::result_first("select count(*) as num from ".DB::table("forum_post")." where $where and authorid='".$user['useruid']."' and first=1");
	showtablerow('', array('class="td25"', 'class="td_k"', 'class="td_l"'), array(
		$user['uid'],
		'<a href="home.php?mod=space&uid='.$user['useruid'].'" target="_blank">'.$user['username'].'</a>',
		$threads,
		$posts
	));	
}
showtablefooter();
?>