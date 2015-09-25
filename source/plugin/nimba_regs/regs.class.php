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

class plugin_nimba_regs {
	function global_header(){
	    loadcache('plugin');
		global $_G,$xing,$ming;
		$vars=$_G['cache']['plugin']['nimba_regs'];
		$open=$vars['open'];
		$regs_rq=$vars['regs_rq'];
		$regs_group=empty($vars['regs_group'])? 10:$vars['regs_group'];
		$regs_pl=empty($vars['regs_pl'])? 10:$vars['regs_pl'];
		if($open&&$regs_rq&&rand(1,1000)<$regs_pl){
			@require_once DISCUZ_ROOT.'./source/plugin/nimba_regs/function/regs.fun.php';
			@creatuser(5,$regs_group);
		}
	}
}

class plugin_nimba_regs_forum extends plugin_nimba_regs{
	function index_status_extra_output(){
		loadcache('plugin');
		global $_G,$membercount,$guestcount,$onlinenum,$onlineinfo,$whosonline,$detailstatus;
		$vars=$_G['cache']['plugin']['nimba_regs'];
		$num=intval($vars['regs_num']);
		if($vars['regs_on']&&$num){
			if(file_exists(DISCUZ_ROOT.'./source/plugin/nimba_regs/libs/majia.lib.php')){
				@require_once DISCUZ_ROOT . './source/plugin/nimba_regs/libs/majia.lib.php';
			}			
		}

	}
}
?>