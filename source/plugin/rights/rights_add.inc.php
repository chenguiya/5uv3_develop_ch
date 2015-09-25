<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');
$_setting = $_G['cache']['plugin']['rights'];

cpheader();
$operation = $operation ? $operation : 'admin';

//权益类型
$types = $ownertypes = array();
$type_title = array('通用', '会员', '球迷会', '版块', '商品');
$rights_type = dunserialize($_setting['rights_type']);
$ownertite = array('全部', '会员', '球迷会');
$ownertype = dunserialize($_setting['owner']);
foreach ($rights_type as $val) {
	$types[] = array($val, $type_title[$val]);
}
foreach ($ownertype as $_val) {
	$ownertypes[] = array($_val, $ownertite[$_val]);
}

if (!submitcheck('rightsaddsubmit')) {	
	showformheader('plugins&operation=config&do=25&identifier=rights&pmod=rights_add', '', 'rightsform', 'post');
	echo '<input type="hidden" name="ispacknew" value="0">';
	showtableheader();
	showtitle(lang('plugin/rights', 'rights_add'));
	showsetting(lang('plugin/rights', 'rights_name'), 'namenew', '', 'text');
	showsetting(lang('plugin/rights', 'rights_type'), array('typenew', $types), '', 'select');
	showsetting(lang('plugin/rights', 'rights_ownertype'), array('ownertypenew', $ownertypes), '', 'select');
	showsetting(lang('plugin/rights', 'rights_identifier'), 'identifiernew', '', 'text');
	showsetting(lang('plugin/rights', 'rights_description'), 'descriptionnew', '', 'textarea');
	showsubmit('rightsaddsubmit');	
	showtablefooter();	
	showformfooter();
} else {
	if (empty($_POST['namenew']) || empty($_POST['identifiernew'])) {
		cpmsg('权益名称和唯一标识不能为空', '', 'error');
	};
		
	$data = array(
		'name' => $_POST['namenew'],
		'ispack' => intval($_POST['ispacknew']),
		'typeid' => intval($_POST['typenew']),
		'owner' => intval($_POST['ownertypenew']),
		'identifier' => $_POST['identifiernew'],
		'description' => $_POST['descriptionnew'],
		'regdate' => TIMESTAMP,
		'canceldate' => TIMESTAMP + 30*24*60*60,
		'putawaytime' => TIMESTAMP + 24*60*60,
		'soldouttime' => TIMESTAMP + 7*24*60*60,
	);
	
	if (C::t('#rights#plugin_rights')->insert($data)) {
		cpmsg('添加成功！', 'action=plugins&operation=config&do=25&identifier=benefits&pmod=admincp', 'success');
	} else {
		cpmsg('添加失败！', '', 'error');
	}
}