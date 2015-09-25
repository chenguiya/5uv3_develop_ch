<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');
$_setting = $_G['cache']['plugin']['rights'];

//权益类型
$types = array();
$type_title = array('通用', '权益包', '版块', '群组', '商品', '会员');
$rights_type = dunserialize($_setting['rights_type']);
foreach ($rights_type as $val) {
	$types[] = array($val, $type_title[$val]);
}
$agingtitle = array('无时效', '时段型', '时长型', '计次型');
$pluginlang = lang('plugin/rights');

$op = isset($_GET['op']) ? $_GET['op'] : 'lists';
if ($op == 'lists') {
	//进如权益打包页面
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
// 	var_dump($ownertypes);
	if (submitcheck('packsubmit')) {
		// 		var_dump($_POST);die;
		if (is_array($_POST['delete'])) {			
			$price = 0;
			foreach ($_POST['delete'] as $rightsid) {
				$query = C::t('#rights#plugin_rights')->fetch_rights_by_id($rightsid);
				$price += $query['price'];
				$branch[$rightsid] = array(
					'rightsid' => $rightsid,
					'name' => $query['name'],
				);
			}
			$packinfo = serialize($branch);
			showformheader('plugins&operation=config&do=25&identifier=rights&pmod=admincp');
			echo '<input type="hidden" name="ispacknew" value="1">';
			showtableheader();
			showsetting('权益包名称', 'namenew', '', 'text');
			showsetting('权益包价格', 'pricenew', $price, 'text');
			showsetting(lang('plugin/rights', 'rights_type'), array('typenew', $types), '', 'select');
			showsetting(lang('plugin/rights', 'rights_ownertype'), array('ownertypenew', $ownertypes), '', 'select');
			echo "<input type='hidden' name='packinfo' value='$packinfo'>";			
			showsubmit('createpacksubmit');
			showtablefooter();
			showformfooter();
		}
		exit;
	}
	//添加权益包
	if (submitcheck('createpacksubmit')) {
		$data = array(
			'name' => $_POST['namenew'],
			'ispack' => intval($_POST['ispacknew']),
			'available' => 1,
			'identifier' => 'rightspack_'.TIMESTAMP,
			'typeid' => 1,
			'packinfo' => $_POST['packinfo'],
			'price' => $_POST['pricenew'],
			'regdate' => TIMESTAMP,
			'canceldate' => TIMESTAMP + 30*24*60*60,
			'putawaytime' => TIMESTAMP + 24*60*60,
			'soldouttime' => TIMESTAMP + 7*24*60*60,
		);
		if (C::t('#rights#plugin_rights')->insert($data)) {
			cpmsg('添加成功！', 'action=plugins&operation=config&do=25&identifier=rights&pmod=admincp', 'success');
		} else {
			cpmsg('添加失败！', '', 'error');
		}
	}
	if (!submitcheck('rightssubmit')) {
		showtips('magics_tips');
		
		showformheader('plugins&operation=config&do=25&identifier=rights&pmod=admincp');
		showtableheader('权益列表', 'fixpadding');
		showsubtitle(array('', 'display_order', '<input type="checkbox" onclick="checkAll(\'prefix\', this.form, \'available\', \'availablechk\')" class="checkbox" id="availablechk" name="availablechk">'.cplang('available'), $pluginlang['rights_name'], $pluginlang['rights_type'], $pluginlang['price'], $pluginlang['rightsnum']));
		foreach (C::t('#rights#plugin_rights')->fetch_all_data() as $rights) {
			$rights['typeid'] = $rights['typeid'] ? $rights['typeid'] : 0;
			$setting_type = dunserialize($_setting['rights_type']);
			$typelist = '<select name="type['.$rights['typeid'].']">';
			foreach ($setting_type as $typeid) {
				$typelist .= '<option value="'.$typeid.'" '.($typeid == $rights['typeid'] ? 'selected' : '').'>'.$type_title[$typeid].'</option>';
			}
			$typelist .= '</select>';
// 			echo $types;die;
			$rights['credit'] = $rights['credit'] ? $rights['credit'] : $_G['setting']['creditstransextra'][3];
			$credits = '<select name="credit['.$rights['rightsid'].']">';
			foreach ($_G['setting']['extcredits'] as $i => $extcredit) {
				$credits .= '<option value="'.$i.'" '.($i == $rights['credit'] ? 'selected' : '').'>'.$extcredit['title'].'</option>';
			}
			$credits .= '</select>';
			$rightstype = $pluginlang['rights_type'.$rights['type']];
			$eidentifier = explode(':', $rights['identifier']);
			showtablerow('', array('class="td25"', 'class="td25"', 'class="td25"', 'class="td28"', 'class="td28"', 'class="td28"', 'class="td28"', '', ''), array(
			"<input type=\"checkbox\" class=\"checkbox\" name=\"delete[]\" value=\"$rights[rightsid]\">",
			"<input type=\"text\" class=\"txt\" name=\"displayorder[$rights[rightsid]]\" value=\"$rights[displayorder]\">",
			"<input type=\"checkbox\" class=\"checkbox\" name=\"available[$rights[rightsid]]\" value=\"1\" ".($rights['available'] ? 'checked' : '').">",
			"<input type=\"text\" class=\"txt\" style=\"width:80px\" name=\"name[$rights[rightsid]]\" value=\"$rights[name]\">".(count($eidentifier) > 1 ? (file_exists(DISCUZ_ROOT.'./source/plugin/'.$eidentifier[0].'/magic/magic_'.$eidentifier[1].'.small.gif') ? '<img class="vmiddle" src="source/plugin/'.$eidentifier[0].'/magic/magic_'.$eidentifier[1].'.small.gif" />' : '')
			: (file_exists(DISCUZ_ROOT.'./source/plugin/rights/static/image/'.$rights['identifier'].'.small.gif') ? '<img class="vmiddle" src="source/plugin/rights/static/image/'.$rights['identifier'].'.small.gif" />' : '')),
			$typelist,
			"<input type=\"text\" class=\"txt\" name=\"price[$rights[rightsid]]\" value=\"$rights[price]\">".$credits,
			"<input type=\"text\" class=\"txt\" name=\"num[$rights[rightsid]]\" value=\"$rights[num]\">".
			($rights['supplytype'] ? '/ '.$rights['supplynum'].' / '.$pluginlang['magic_suppytype_'.$rights['supplytype']] : ''),
			"<input type=\"hidden\" name=\"ridentifier[$rights[rightsid]]\" value=\"$rights[identifier]\">",
			"<a href=\"".ADMINSCRIPT."?action=plugins&operation=config&do=25&identifier=rights&pmod=admincp&op=edit&rightsid=$rights[rightsid]\" class=\"act\">$pluginlang[detail]</a>"
			));
			unset($newrights[$rights['identifier']]);
		}
		showsubmit('delsubmit', 'submit', 'select_all', '&nbsp;&nbsp;<input type="submit" class="btn" id=packsubmit" name="packsubmit" title="" value="权益打包">&nbsp;&nbsp;&nbsp;<input type="checkbox" onclick="checkAll(\'prefix\', this.form, \'available\', \'availablechk1\')" class="checkbox" id="availablechk1" name="availablechk1">'.cplang('available'));
		showtablefooter();
		showformfooter();
	}	
} elseif ($op == 'edit') {
	$rightsid = intval($_GET['rightsid']);
	$rights = C::t('#rights#plugin_rights')->fetch($rightsid);
	if (!submitcheck('rightseditsubmit')) {
		$credits = array();
		foreach($_G['setting']['extcredits'] as $i => $extcredit) {
			$credits[] = array($i, $extcredit['title']);
		}
		if ($rights['ispack'] == 1) {
			
			echo '<div class="colorbox"><h4>'.$rights['name'].'</h4>'.
					'<table cellspacing="0" cellpadding="3"><tr><td>'.
					(!empty($rights['icon']) ? '<img src="data/attachment/forum/'.$rights['icon'].'" width=45 height=45/>' : '<img src="source/plugin/rights/static/image/default.png" width=45 height=45 />').
					'</td><td valign="top">'.$rights['description'].'</td></tr></table>'.
					'<div style="width:95%" align="right"><a href="./" target="_blank">5U体育</a></div></div>';
			
			showformheader('plugins&operation=config&do=25&identifier=rights&pmod=admincp&op=edit&rightsid='.$rightsid.'&typeid=1', 'enctype="multipart/form-data"');
			showtableheader();
			showtitle('编辑 - '.$rights['name']);
			showsetting($pluginlang['rights_name'], 'namenew', $rights['name'], 'text');
			showsetting($pluginlang['rights_icon'], 'iconnew', $rights['icon'], 'file');
			showsetting($pluginlang['rights_available'], 'availablenew', $rights['available'], 'radio');
			showsetting($pluginlang['rights_credit'], array('creditnew', $credits), $rights['credit'], 'select');
			showsetting($pluginlang['price'], 'pricenew', $rights['price'], 'text');
			showsetting($pluginlang['rights_num'], 'numnew', $rights['num'], 'text');
			
			$branch_rights = dunserialize($rights['packinfo']);
			showtableheader('', 'noborder');
			showtitle('权益包包含权益列表');
			foreach ($branch_rights as $val) {
				showtablerow('','',array(
				$val['name'],
				'<a href="">删除</a>',
				));
			}
			showtablefooter();
			echo '<div style="height:15px; clear:both;"></div>';
			showsetting('生效时间', 'regdatenew', date('Y-m-d', $rights['regdate']), 'calendar');
			showsetting('失效时间', 'canceldatenew', date('Y-m-d', $rights['canceldate']), 'calendar');
			echo "<br>";
			showsetting('上架时间', 'putawaytimenew', date('Y-m-d', $rights['putawaytime']), 'calendar');
			showsetting('下架时间', 'soldouttimenew', date('Y-m-d', $rights['soldouttime']), 'calendar');
			
			showsubmit('rightseditsubmit');
			showtablefooter();
			showformfooter();
		} else {
			$rightsperm = dunserialize($rights['rightsperm']);
			$groups = $forums = array();
			foreach (C::t('common_usergroup')->range() as $group) {
				$groups[$group['groupid']] = $group['grouptitle'];
			}
			
			$typeselect = array($rights['type'] => 'selected');
			include_once DISCUZ_ROOT.'./source/plugin/rights/class/rights_'.$rights['identifier'].'.php';
			$rightsclass = 'rights_'.$rights['identifier'];
			$rightsclass = new $rightsclass;
			$rightssetting = $rightsclass->getsetting($rightsperm);	

			echo '<div class="colorbox"><h4>'.$rights['name'].'</h4>'.
					'<table cellspacing="0" cellpadding="3"><tr><td>'.
					(!empty($rights['icon']) ? '<img src="data/attachment/forum/'.$rights['icon'].'" width=45 height=45/>' : '<img src="source/plugin/rights/static/image/default.png" width=45 height=45 />').
							'</td><td valign="top">'.$rights['description'].'</td></tr></table>'.
							'<div style="width:95%" align="right"><a href="./" target="_blank">5U体育</a></div></div>';			
			
			
			showformheader('plugins&operation=config&do=25&identifier=rights&pmod=admincp&op=edit&rightsid='.$rightsid, 'enctype="multipart/form-data"');
			showtableheader();
			showtitle('编辑 - '.$rights['name'].'权益('.$rights['identifier'].')');
			showsetting($pluginlang['rights_name'], 'namenew', $rights['name'], 'text');
			if (empty($rights['icon'])) {
				showsetting($pluginlang['rights_icon'], 'iconnew', $rights['icon'], 'file');
			} else {
				echo '<input type="hidden" name="iconnew" value="'.$rights['icon'].'">';
			}			
			showsetting($pluginlang['rights_available'], 'availablenew', $rights['available'], 'radio');
			showsetting($pluginlang['rights_type'], array('typenew', $types), $rights['typeid'], 'select');
			showsetting($pluginlang['rights_credit'], array('creditnew', $credits), $rights['credit'], 'select');
			showsetting($pluginlang['price'], 'pricenew', $rights['price'], 'text');
			showsetting($pluginlang['discount'], 'discountnew', $rights['discount'], 'text');
			showsetting($pluginlang['rights_num'], 'numnew', $rights['num'], 'text');
			showsetting($pluginlang['rights_supplynum'], 'supplynumnew', $rights['supplynum'], 'text');
			showsetting($pluginlang['rights_supplytype'], array('supplytypenew', array(
				array(0, $pluginlang['rights_goods_stack_none']),
				array(1, $pluginlang['rights_goods_stack_day']),
				array(2, $pluginlang['rights_goods_stack_week']),
				array(3, $pluginlang['rights_goods_stack_month']),
			)), $rights['supplytype'], 'mradio');
			showsetting($pluginlang['rights_useperoid'], array('useperoidnew', array(
				array(0, $pluginlang['rights_useperoid_none']),
				array(1, $pluginlang['rights_useperoid_day']),
				array(4, $pluginlang['rights_useperoid_24hr']),
				array(2, $pluginlang['rights_useperoid_week']),
				array(3, $pluginlang['rights_useperoid_month']),
			)), $rights['useperoid'], 'mradio');
			showsetting($pluginlang['rights_usenum'], 'usenumnew', $rights['usenum'], 'text');
			showsetting($pluginlang['rights_description'], 'descriptionnew', $rights['description'], 'textarea');
			showsetting('生效时间', 'regdatenew', date('Y-m-d', $rights['regdate']), 'calendar');
			showsetting('失效时间', 'canceldatenew', date('Y-m-d', $rights['canceldate']), 'calendar');
			showsetting('上架时间', 'putawaytimenew', date('Y-m-d', $rights['putawaytime']), 'calendar');
			showsetting('下架时间', 'soldouttimenew', date('Y-m-d', $rights['soldouttime']), 'calendar');
			
			if(is_array($rightssetting)) {
				foreach($rightssetting as $settingvar => $setting) {
					if(!empty($setting['value']) && is_array($setting['value'])) {
						foreach($setting['value'] as $k => $v) {
							$setting['value'][$k][1] = lang('magic/'.$rights['identifier'], $setting['value'][$k][1]);
						}
					}
					$varname = in_array($setting['type'], array('mradio', 'mcheckbox', 'select', 'mselect')) ?
					($setting['type'] == 'mselect' ? array('perm['.$settingvar.'][]', $setting['value']) : array('perm['.$settingvar.']', $setting['value']))
					: 'perm['.$settingvar.']';
					$value = $rightsperm[$settingvar] != '' ? $rightsperm[$settingvar] : $setting['default'];
					$comment = lang('magic/'.$rights['identifier'], $setting['title'].'_comment');
					$comment = $comment != $setting['title'].'_comment' ? $comment : '';
					showsetting($pluginlang['rights_prem_forum'].':', $varname, $value, $setting['type'], '', 0, $comment);
				}
			}
			
			showtitle($pluginlang['rights_perm']);
			showtablerow('', 'colspan="2" class="td27"', $pluginlang['rights_usergroupperm'].':<input class="checkbox" type="checkbox" name="chkall1" onclick="checkAll(\'prefix\', this.form, \'usergroupsperm\', \'chkall1\', true)" id="chkall1" /><label for="chkall1"> '.cplang('select_all').'</label>');
			showtablerow('', 'colspan="2"', mcheckbox('usergroupsperm', $groups, explode("\t", $rightsperm['usergroups'])));
			
			showsubmit('rightseditsubmit');
			showtablefooter();
			showformfooter();
		}
echo <<<EOF
<script type="text/javascript" src="static/js/calendar.js"></script>
EOF;
	} else {
// 		var_dump($_GET);die;
		if ($_GET['typeid'] == 1) {
			$namenew = dhtmlspecialchars(trim($_GET['namenew']));
			$availablenew = intval($_GET['availablenew']);			
			$creditnew = intval($_GET['creditnew']);
			$regdatenew = strtotime($_GET['regdatenew']);
			$canceldatenew = strtotime($_GET['canceldatenew']);
			$putawaytimenew = strtotime($_GET['putawaytimenew']);
			$soldouttimenew = strtotime($_GET['soldouttimenew']);
			
			if(!$namenew) {
				cpmsg($pluginlang['rights_parameter_invalid'], '', 'error');
			}
			
			if (!empty($_GET['icon'])) {
				$iconnew = $_GET['icon'];
			} else {
				//图标处理
				if ($_FILES['iconnew']) {
					$upload = new discuz_upload();
					$iconid = TIMESTAMP;
					$upload->init($_FILES['iconnew'], 'forum', $iconid, $_G['uid']);
					$upload->save();
					
					$iconnew = $upload->attach['attachment'];		//取图片原件
				}
			}
			
			C::t('#rights#plugin_rights')->update($rightsid, array(
				'name' => $namenew,
				'icon' => $iconnew,
				'available' => $availablenew,
				'price' => $_GET['pricenew'],
				'num' => $_GET['numnew'],
				'credit' => $creditnew,
				'regdate' => $regdatenew,
				'canceldate' => $canceldatenew,
				'putawaytime' => $putawaytimenew,
				'soldouttime' => $soldouttimenew,
			));
		} else {
			$namenew = dhtmlspecialchars(trim($_GET['namenew']));
			$typenew = isset($_GET['typenew']) ? intval($_GET['typenew']) : 0;
			$availablenew = intval($_GET['availablenew']);
			$identifiernew	= dhtmlspecialchars(trim(strtoupper($_GET['identifiernew'])));
			$descriptionnew	= dhtmlspecialchars($_GET['descriptionnew']);
			
			$rightsperm['usergroups'] = is_array($_GET['usergroupsperm']) && !empty($_GET['usergroupsperm']) ? "\t".implode("\t", $_GET['usergroupsperm'])."\t" : '';
			$rightsperm['targetgroups'] = is_array($_GET['targetgroupsperm']) && !empty($_GET['targetgroupsperm']) ? "\t".implode("\t",$_GET['targetgroupsperm'])."\t" : '';
			
			$eidentifier = explode(':', $rights['identifier']);
			include_once DISCUZ_ROOT.'./source/plugin/rights/class/rights_'.$rights['identifier'].'.php';
			$rightsclass = 'rights_'.$rights['identifier'];
			$rightsclass = new $rightsclass;
			$rightsclass->setsetting($rightsperm, $_GET['perm']);
			$rightspermnew = addslashes(serialize($rightsperm)); 
			
			$supplytypenew = intval($_GET['supplytypenew']);
			$supplynumnew = $_GET['supplytypenew'] ? intval($_GET['supplynumnew']) : 0;
			$usenumnew = intval($_GET['usenumnew']);
			$useperoidnew = $_GET['useperoidnew'] ? intval($_GET['useperoidnew']) : 0;
			$creditnew = intval($_GET['creditnew']);
			$regdatenew = strtotime($_GET['regdatenew']);
			$canceldatenew = strtotime($_GET['canceldatenew']);
			$putawaytimenew = strtotime($_GET['putawaytimenew']);
			$soldouttimenew = strtotime($_GET['soldouttimenew']);
			
			if(!$namenew) {
				cpmsg($pluginlang['rights_parameter_invalid'], '', 'error');
			}
			if (!empty($_GET['iconnew'])) {
				$iconnew = $_GET['iconnew'];
			} else {
				//图标处理
				if ($_FILES['iconnew']) {
					$upload = new discuz_upload();
					$iconid = TIMESTAMP;
					$upload->init($_FILES['iconnew'], 'forum', $iconid, $_G['uid']);
					$upload->save();
					
					$iconnew = $upload->attach['attachment'];		//取图片原件
				}
			}
						
			C::t('#rights#plugin_rights')->update($rightsid, array(
				'name' => $namenew,
				'icon' => $iconnew,
				'available' => $availablenew,
				'typeid' => $typenew,
				'description' => $descriptionnew,
				'price' => $_GET['pricenew'],
				'discount' => $_GET['discountnew'],
				'num' => $_GET['numnew'],
				'supplytype' => $supplytypenew,
				'supplynum' => $supplynumnew,
				'useperoid' => $useperoidnew,
				'usenum' => $usenumnew,
				'rightsperm' => $rightspermnew,
				'credit' => $creditnew,
				'regdate' => $regdatenew,
				'canceldate' => $canceldatenew,
				'putawaytime' => $putawaytimenew,
				'soldouttime' => $soldouttimenew,
			));
		}
		updatecache(array('setting', 'rights'));
		cpmsg('magics_data_succeed', 'action=plugins&operation=config&do=25&identifier=rights&pmod=admincp', 'succeed');
	}
} 
?>