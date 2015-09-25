<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
loadcache('plugin');
$setting = $_G['cache']['plugin']['k_misign'];
$setting['pextend'] = unserialize($setting['pextend']);
if(submitcheck('pluginsubmit')) {
	if(file_exists(DISCUZ_ROOT.'./source/plugin/tie/index.inc.php')){
		$data['value']['tie_button']['status'] = intval($_GET['tie_button']['status']);
		$data['value']['tie_button']['width'] = intval($_GET['tie_button']['width']);
	}
	$data['value'] = serialize($data['value']);
	C::t("common_pluginvar")->update_by_variable($do, 'pextend', $data);
	updatecache(array('plugin'));
	cpmsg(lang('plugin/k_misign', 'success'), "action=plugins&operation=config&do=".$do."&identifier=k_misign&pmod=pluginextendcp", 'succeed');
}else{
	
	showformheader('plugins&operation=config&do='.$pluginid.'&identifier=k_misign&pmod=pluginextendcp', 'enctype');
	showtableheader();
	if(file_exists(DISCUZ_ROOT.'./source/plugin/tie/index.inc.php')){
		showtableheader(lang('plugin/tie', 'plugintitle'));
		showsetting(lang('plugin/k_misign', 'button_status'), 'tie_button[status]', (($setting['pextend']['tie_button']['status'] == 1) ? 1 : 0), 'radio');
		showsetting(lang('plugin/k_misign', 'button_width'), 'tie_button[width]', $setting['pextend']['tie_button']['width'], 'text');
		showtablefooter();
	}
	showsubmit('pluginsubmit', 'submit');
	showtablefooter();
	showformfooter();
}
?>