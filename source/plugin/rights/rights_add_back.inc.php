<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');
$_setting = $_G['cache']['plugin']['rights'];

cpheader();
$operation = $operation ? $operation : 'admin';

if (!submitcheck('rightsaddsubmit')) {
	//用户组列表
	$groups = $forums = array();
	foreach(C::t('common_usergroup')->range() as $group) {
		$groups[$group['groupid']] = $group['grouptitle'];
	}	
	//消耗货币
	$credits = array();
	foreach($_G['setting']['extcredits'] as $i => $extcredit) {
		$credits[] = array($i, $extcredit['title']);
	}	
	//权益类型
	$types = array();
	$type_title = array('通用', '版块', '群组', '商品');
	$rights_type = dunserialize($_setting['rights_type']);
	foreach ($rights_type as $val) {
		$types[] = array($val, $type_title[$val]);
	}
	//时效类型
	$agings = array();
	$aging_title = array('无时效', '时段型', '时长型', '计次型');
	$aging_type = dunserialize($_setting['aging_type']);
	foreach ($rights_type as $val) {
		$aging_option .= '<option value="'.$val.'">'.$aging_title[$val].'</option>';
	}
	require_once libfile('function_common', 'plugin/rights/function');
	$rightssetting = getsetting();
	
// 	var_dump($rightssetting);die;
	
	showformheader('plugins&operation=config&do=25&identifier=rights&pmod=rights_add', '', 'rightsform', 'post');
	showtableheader();
	showtitle(lang('plugin/rights', 'rights_add'));
	showsetting(lang('plugin/rights', 'rights_name'), 'namenew', '', 'text');
	showsetting(lang('plugin/rights', 'rights_indentifer'), 'indentifernew', '', 'text');
	showsetting(lang('plugin/rights', 'rights_credit'), array('creditnew', $credits), '', 'select');
	showsetting(lang('plugin/rights', 'rights_price'), 'pricenew', 0, 'text');
	showsetting(lang('plugin/rights', 'rights_num'), 'numnew', 10, 'text');
	showsetting(lang('plugin/rights', 'rights_supplynum'), 'supplynumnew', 10, 'text');
	showsetting(lang('plugin/rights', 'rights_supplytype'), array('supplytypenew', array(
		array(0, lang('plugin/rights', 'rights_goods_stack_none')),
		array(1, lang('plugin/rights','rights_goods_stack_day')),
		array(2, lang('plugin/rights','rights_goods_stack_week')),
		array(3, lang('plugin/rights','rights_goods_stack_month')),
	)), 0, 'mradio');
	showsetting(lang('plugin/rights', 'rights_useperoid'), array('useperoidnew', array(
		array(0, lang('plugin/rights','rights_useperoid_none')),
		array(1, lang('plugin/rights','rights_useperoid_day')),
		array(4, lang('plugin/rights','rights_useperoid_24hr')),
		array(2, lang('plugin/rights','rights_useperoid_week')),
		array(3, lang('plugin/rights','rights_useperoid_month')),
	)), 0, 'mradio');
	showsetting(lang('plugin/rights', 'rights_usenum'), 'usenumnew', 1, 'text');
	
	showsetting(lang('plugin/rights', 'rights_type'), array('typenew', $types), '', 'select', '', 0, '', '', 'typenew');
	if(is_array($rightssetting)) {
		foreach($rightssetting as $settingvar => $setting) {
			if(!empty($setting['value']) && is_array($setting['value'])) {
				foreach($setting['value'] as $k => $v) {
					$setting['value'][$k][1] = lang('plugin/rights', $setting['value'][$k][1]);
				}
			}
			$varname = in_array($setting['type'], array('mradio', 'mcheckbox', 'select', 'mselect')) ?
			($setting['type'] == 'mselect' ? array('perm['.$settingvar.'][]', $setting['value']) : array('perm['.$settingvar.']', $setting['value']))
			: 'perm['.$settingvar.']';
			$comment = lang('plugin/rights', $setting['title'].'_comment');
			$comment = $comment != $setting['title'].'_comment' ? $comment : '';
// 			var_dump($setting['default']);die;
			$forumstr = showsetting(lang('plugin/rights', $setting['title']).':', $varname, 0, $setting['type'], '', 0, $comment);
		}
	}
	
	showtablerow('onmouseover="setfaq(this, \'faq7106\')"', array('colspan="2" class="td27" s="1"', 'class="td25"', 'class="td28"'), array('时效类型：'));	
	showtablerow('class="noborder" onmouseover="setfaq(this, \'faqtypenew\')"', array('class="vtop rowform"'), array(
			'<select name="agingnew" id="agingnew" onChange="change(this);">'.$aging_option.'</select>',
			'时效类型，权益时间段由权益的起始和终止时间构成，时长为权益的持续时长，小时/天/月/年',
		));
	echo '<tr class="noborder" onmouseover="setfaq(this, \'faqtypenew\')" id="time"></tr>';
	showsetting(lang('plugin/rights', 'rights_description'), 'descriptionnew', '', 'textarea');
	
	showsetting('上架时间', 'puawaytimenew', '', 'calendar');
	showsetting('下架时间', 'soldouttimenew', '', 'calendar');
	
	showtitle(lang('plugin/rights', 'rights_perm'));
	showtablerow('', 'colspan="2" class="td27"', lang('plugin/rights', 'rights_usergroupperm').':<input class="checkbox" type="checkbox" name="chkall1" onclick="checkAll(\'prefix\', this.form, \'usergroupsperm\', \'chkall1\', true)" id="chkall1" /><label for="chkall1"> '.cplang('select_all').'</label>');
	showtablerow('', 'colspan="2"', mcheckbox('usergroupsperm', $groups, explode("\t", '')));	
	showsubmit('rightsaddsubmit');	
	showtablefooter();	
	showformfooter();
echo <<<EOF
<script type="text/javascript" src="static/js/calendar.js"></script>
<script type="text/JavaScript">
var agingstypeEle = document.getElementsByName('agingnew');
function change (obj) {
	if (obj.value == 1) {
		document.getElementById('time').innerHTML = '<td class="vtop rowform"><span>开始日期：</span><input type="text" name="regdate" id="regdate" size="25" value="" onclick="showcalendar(event, this)"></td></tr><tr><td class="vtop rowform"><span>结束日期：</span><input type="text" name="canceldate" id="canceldate" size="25" value="" onclick="showcalendar(event, this)"></td>';
	} else if (obj.value == 2) {
		document.getElementById('time').innerHTML = '<td class="vtop rowform"><input name="duration" id="duration" value="0"></td><td class="td25"><select name="time_unit" id="time_unit"><option value="0">小时</option><option value="1">天</option><option value="2">月</option><option value="2">年</option></select></td>';
	} else  {
		document.getElementById('time').innerHTML = '';
	}
}
function changeText (id) {
	id.innerHTML();
}
</script>
EOF;
} else {
	if (empty($_POST['namenew']) || empty($_POST['indentifernew'])) {
		cpmsg('权益名称和唯一标识不能为空', '', 'error');
	};
	
	var_dump($_POST);die;
	
	$data = array(
			'name' => $_POST['name'],
			''
	);
}