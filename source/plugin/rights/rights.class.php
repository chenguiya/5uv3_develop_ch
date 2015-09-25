<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_rights {
	function __construct() {
		loadcache('plugin');
		global $_G;
		$_setting = $_G['plugin']['cache']['rights'];
		if ($_setting['open'] != 1) return false;
	}
	
// 	function global_header() {
// 		return '11111111';
// 	}
	function global_personal_pic() {
		global $_G;
		$uid = intval($_G['uid']);
		$rightsid = 26;
		include_once libfile('function/extends');
		if (userrightsperm($rightsid)) {
			return '<li><a href="plugin.php?id=rights:rightsrun&ac=setpersonalpic">个性顶图</a></li>';
		} 
	}
}

class plugin_rights_forum extends plugin_rights {
	function viewthread_vip_service_output() {
		loadcache('plugin');
		global $_G;
		$_setting = $_G['cache']['plugin']['rights'];
		if ($_setting['open'] != 1) return false;
		$packs = $rights = array();
		//获取两个权益包
		$packs = DB::fetch_all("SELECT * FROM ".DB::table('plugin_rights')." WHERE `ispack`=1 AND `putawaytime`<".TIMESTAMP." AND `soldouttime`>".TIMESTAMP." LIMIT 2");
		//获取3个单项权益
		$rights = DB::fetch_all("SELECT * FROM ".DB::table('plugin_rights')." WHERE `ispack`=0 AND `putawaytime`<".TIMESTAMP." AND `soldouttime`>".TIMESTAMP." LIMIT 3");
		
		include template('rights:service');
		return $return;
	}
}

class plugin_rights_group extends plugin_rights {
	function group_viewthread_vip_service() {
		return plugin_rights_forum::viewthread_vip_service_output();
	}
}