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
if(file_exists(DISCUZ_ROOT.'./source/plugin/nimba_regs/libs/output.lib.php')){
	@require_once DISCUZ_ROOT . './source/plugin/nimba_regs/libs/output.lib.php';
}else{
	cpmsg(lang('plugin/nimba_regs','output_error'),'action=plugins&operation=config&identifier=nimba_regs&pmod=status','succeed');
}
?>