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
$libs=array(
	0=>'majia',
	1=>'output',
	2=>'upload',
	3=>'avatar',
);

showtableheader(lang('plugin/nimba_regs','tips'));
showsubtitle(array(lang('plugin/nimba_regs','name'),lang('plugin/nimba_regs','info'),lang('plugin/nimba_regs','status'),lang('plugin/nimba_regs','down')));
foreach($libs as $k=>$lib) {
	if(file_exists(DISCUZ_ROOT.'./source/plugin/nimba_regs/libs/'.$lib.'.lib.php')) $status='<font color="green">'.lang('plugin/nimba_regs','status_1').'</font>';
	else $status='<font color="red">'.lang('plugin/nimba_regs','status_2').'</font>';
	showtablerow('', array('class="td_k"', 'class="td_k"', 'class="td_l"'), array(
		lang('plugin/nimba_regs',$lib),
		lang('plugin/nimba_regs',$lib.'info'),	
		$status,
		'<a href="'.ADMINSCRIPT.'?action=cloudaddons&id=nimba_regs.plugin">'.lang('plugin/nimba_regs','down').'</a>',
	));
}
showtablefooter();
?>