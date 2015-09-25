<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class rights_postcreditup {
	
	var $version = '1.0';
	var $name = 'postscreditup_name';
	var $description = 'poststick_desc';
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
	
	function buy() {
		global $_G;
		$id = !empty($_GET['id']) ? dhtmlspecialchars($_GET['id']) : '';
		$idtype = !empty($_GET['idtype']) ? dhtmlspecialchars($_GET['idtype']) : '';
		if(!empty($id) && $_GET['idtype'] == 'pid') {
			list($id, $_G['tid']) = explode(':', $id);
			$post = getpostinfo(intval($id), 'pid', array('p.fid', 'p.authorid'));
			$this->_check($post);
		}
	}
	
	function _check($post) {
		global $_G;
		if (!checkrightperm($this->parameters['forum'], $post['fid'])) {
			showmessage(lang('magic/namepost', 'namepost_info_noperm'));
		}
		$member = getuserbyuid($post['authorid']);
		if (!checkrightperm($this->parameters['targetgroups'], $member['groupid'])) {
			showmessage(lang('magic/namepost', 'namepost_info_user_noperm'));
		}
	}
}