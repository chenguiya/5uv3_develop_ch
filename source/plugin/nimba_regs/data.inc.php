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
@require_once DISCUZ_ROOT.'./source/discuz_version.php';
@require_once DISCUZ_ROOT.'./source/plugin/nimba_regs/function/regs.fun.php';
$langvar=lang('plugin/nimba_regs');
$pagenum=20;
$page=max(1,intval($_GET['page']));
$count=C::t('#nimba_regs#nimba_member')->count();
showtableheader();
showsubtitle(array('UID',$langvar['username'],$langvar['email'],$langvar['pw1'],$langvar['time']));
$data=C::t('#nimba_regs#nimba_member')->fetch_all_by_range(($page - 1)*$pagenum,$pagenum);
foreach($data as $user) {
	showtablerow('', array('class="td25"', 'class="td_k"', 'class="td_l"'), array(
		$user['uid'],
		'<a href="home.php?mod=space&uid='.$user['uid'].'" target="_blank">'.$user['username'].'</a>',
		$user['email'],		
		$user['password'],
		date('Y-m-d H:i:s',$user['dateline']),
	));
			
}
showtablefooter();
echo multi($count,$pagenum,$page,ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=nimba_regs&pmod=data");
?>