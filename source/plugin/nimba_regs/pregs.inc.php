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
if(file_exists(DISCUZ_ROOT.'./source/plugin/nimba_regs/libs/upload.lib.php')){
	$uploadon=1;
	@require_once DISCUZ_ROOT . './source/plugin/nimba_regs/libs/upload.lib.php';
}else{
 	$uploadon=0; 
	if(submitcheck('submit')){
		echo ishow($langvar['error3'], ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=nimba_regs&pmod=pregs');
	}else include template('nimba_regs:upload'); 
}
?>