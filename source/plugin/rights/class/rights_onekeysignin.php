<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class rights_onekeysignin {
	
	var $version = '1.0';
	var $name = 'onekeysignin_name';
	var $description = 'onekeysignin_desc';
	var $price = '10';
	var $targetgroupperm = true;
	var $copyright = '<a href="http://www.5usport.com" target="_blank">5U体育</a>';
	var $rights = array();
	var $parameters = array();
	
	function getsetting(&$rights) {
		global $_G;
		$settings = array(
			'fids' => array(
				'title' => 'postscreditup_forum',
				'type' => 'mselect',
				'value' => array(),
			),
		);
		loadcache('forums');
		$settings['fids']['value'][] = array(0, '&nbsp;');
		if(empty($_G['cache']['forums'])) $_G['cache']['forums'] = array();
		foreach($_G['cache']['forums'] as $fid => $forum) {
			$settings['fids']['value'][] = array($fid, ($forum['type'] == 'forum' ? str_repeat('&nbsp;', 4) : ($forum['type'] == 'sub' ? str_repeat('&nbsp;', 8) : '')).$forum['name']);
		}
		$rights['fids'] = explode("\t", $rights['forum']);
		
		return $settings;
	}
	
	function setsetting(&$rightsnew, &$parameters) {
		global $_G;
		$rightsnew['forum'] = is_array($parameters['fids']) && !empty($parameters['fids']) ? implode("\t",$parameters['fids']) : '';
	}
}