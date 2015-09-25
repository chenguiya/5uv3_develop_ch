<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$levelid = !empty($_GET['levelid']) ? intval($_GET['levelid']) : 0;
if(empty($levelid)) {
	$grouplevels = '';
	if(!submitcheck('grouplevelsubmit')) {
		$query = C::t('forum_grouplevel')->fetch_all_creditslower_order();
		foreach($query as $level) {
			$grouplevels .= showtablerow('', array('class="td25"', '', 'class="td28"', 'class=td28'), array(
					"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[$level[levelid]]\" value=\"$level[levelid]\">",
					"<input type=\"text\" class=\"txt\" size=\"12\" name=\"levelnew[$level[levelid]][leveltitle]\" value=\"$level[leveltitle]\">",
					"<input type=\"text\" class=\"txt\" size=\"6\" name=\"levelnew[$level[levelid]][creditshigher]\" value=\"$level[creditshigher]\" /> ~ <input type=\"text\" class=\"txt\" size=\"6\" name=\"levelnew[$level[levelid]][creditslower]\" value=\"$level[creditslower]\" disabled />",
					"<a href=\"".ADMINSCRIPT."?action=plugins&operation=config&do=14&identifier=fansclub&pmod=level&levelid=$level[levelid]\" class=\"act\">$lang[detail]</a>"
			), TRUE);
		}
		echo <<<EOT
<script type="text/JavaScript">
var rowtypedata = [
	[
		[1,'', 'td25'],
		[1,'<input type="text" class="txt" size="12" name="levelnewadd[leveltitle][]">'],
		[1,'<input type="text" class="txt" size="6" name="levelnewadd[creditshigher][]">', 'td28'],
		[4,'']
	],
	[
		[1,'', 'td25'],
		[1,'<input type="text" class="txt" size="12" name="leveltitlenewadd[]">'],
		[1,'<input type="text" class="txt" size="2" name="creditshighernewadd[]">', 'td28'],
		[4, '']
	]
];
</script>
EOT;
		shownav('group', 'nav_group_level');
		showsubmenu(lang('plugin/fansclub', 'fansclub_level'));
		showtips(lang('plugin/fansclub', 'fansclub_level_tips'));

		showformheader('plugins&operation=config&do=14&identifier=fansclub&pmod=level');
		showtableheader('group_level', 'fixpadding', 'id="grouplevel"');
		showsubtitle(array('del', 'group_level_title', 'group_level_creditsrange', ''));
		echo $grouplevels;
		echo '<tr><td>&nbsp;</td><td colspan="8"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['group_level_add'].'</a></div></td></tr>';
		showsubmit('grouplevelsubmit', 'submit');
		showtablefooter();
		showformfooter();
	} else {
		$levelnewadd = $levelnewkeys = $orderarray = array();
		$maxlevelid = 0;
		if(!empty($_GET['levelnewadd'])) {
			$levelnewadd = array_flip_keys($_GET['levelnewadd']);
			foreach($levelnewadd as $k => $v) {
				if(!$v['leveltitle'] || !$v['creditshigher']) {
					unset($levelnewadd[$k]);
				}
			}
		}
		if(!empty($_GET['levelnew'])) {
			$levelnewkeys = array_keys($_GET['levelnew']);
			$maxlevelid = max($levelnewkeys);
		}

		foreach($levelnewadd as $k=>$v) {
			$_GET['levelnew'][$k+$maxlevelid+1] = $v;
		}
		if(is_array($_GET['levelnew'])) {
			foreach($_GET['levelnew'] as $id => $level) {
				if((is_array($_GET['delete']) && in_array($id, $_GET['delete'])) || ($id == 0 && (!$level['grouptitle'] || $level['creditshigher'] == ''))) {
					unset($_GET['levelnew'][$id]);
				} else {
					$orderarray[$level['creditshigher']] = $id;
				}
			}
		}
		ksort($orderarray);
		$rangearray = array();
		$lowerlimit = array_keys($orderarray);
		for($i = 0; $i < count($lowerlimit); $i++) {
			$rangearray[$orderarray[$lowerlimit[$i]]] = array
			(
					'creditshigher' => isset($lowerlimit[$i - 1]) ? $lowerlimit[$i] : -999999999,
					'creditslower' => isset($lowerlimit[$i + 1]) ? $lowerlimit[$i + 1] : 999999999
			);
		}
		foreach($_GET['levelnew'] as $id => $level) {
			$creditshighernew = $rangearray[$id]['creditshigher'];
			$creditslowernew = $rangearray[$id]['creditslower'];
			if($creditshighernew == $creditslowernew) {
				cpmsg('group_level_update_credits_duplicate', '', 'error');
			}
			$data = array(
					'leveltitle' => $level['leveltitle'],
					'creditshigher' => $creditshighernew,
					'creditslower' => $creditslowernew,
			);
			if(in_array($id, $levelnewkeys)) {
				C::t('forum_grouplevel')->update($id, $data);
			} elseif($level['leveltitle'] && $level['creditshigher'] != '') {
				$data = array(
						'leveltitle' => $level['leveltitle'],
						'type' => 'default',
						'creditshigher' => $creditshighernew,
						'creditslower' => $creditslowernew,
				);
				$data['type'] = 'default';
				$newlevelid = C::t('forum_grouplevel')->insert($data, 1);
			}
		}
		if($ids = dimplode($_GET['delete'])) {
			$levelcount = C::t('forum_grouplevel')->fetch_count();
			if(count($_GET['delete']) == $levelcount) {
				updatecache('grouplevels');
				cpmsg('group_level_succeed_except_all_levels', 'action=group&operation=level', 'succeed');

			}
			C::t('forum_grouplevel')->delete($ids);
		}
		updatecache('grouplevels');
		cpmsg('group_level_update_succeed', 'action=plugins&operation=config&do=14&identifier=fansclub&pmod=level', 'succeed');
	}
} else {
	$clublevel = C::t('forum_grouplevel')->fetch($levelid);
	if(empty($clublevel)) {
		cpmsg(lang('plugin/fansclub', 'club_level_noexist'), 'plugins&operation=config&do=14&identifier=fansclub&pmod=level', 'error');
	}
	if (!($club_creditspolicy = dunserialize($clublevel['creditspolicy']))) {
		$club_creditspolicy = array(
				'post' => 0, 
				'post_credits' => 0, 
				'reply' => 0, 
				'reply_credits' => 0, 
				'digest' => 0, 
				'digest_credits' => 0, 
				'postattach' => 0, 
				'postattach_credits' => 0, 
				'getattach' => 0, 
				'getattach_credits' => 0, 
				'tradefinished' => 0, 
				'tradefinished_credits' => 0, 
				'joinpoll' => 0, 
				'joinpoll_credits' => 0);
	}
	if (!submitcheck('editgrouplevel')) {
		showsubmenu($clublevel['leveltitle'].'&nbsp;-&nbsp;'.lang('plugin/fansclub', 'fansclub_credits_rule'));
		showtips(lang('plugin/fansclub', 'fansclub_credits_rule_tips'));
		showformheader('plugins&operation=config&do=14&identifier=fansclub&pmod=level&levelid='.$levelid);
		showtableheader('', 'noborder');
		$creditspolicy = array('post', 'reply', 'digest', 'postattach', 'getattach', 'tradefinished', 'joinpoll');
		
		if (!empty($club_creditspolicy)) {
			foreach ($creditspolicy as $vo) {
				if ($club_creditspolicy[$vo]) {
					if (empty($club_creditspolicy[$vo.'_credits'])) $club_creditspolicy[$vo.'_credits'] = 0;
					showtablerow('', array('width="120px"'), array(
						'<input type="checkbox" name="levelnew[creditspolicy]['.$vo.']" value="1" checked>&nbsp;'.lang('plugin/fansclub', 'club_level_credits_'.$vo),
						'<input type="text" name="levelnew[creditspolicy]['.$vo.'_credits]" value="'.$club_creditspolicy[$vo.'_credits'].'">&nbsp;贡献',
		));
				} else {
					showtablerow('', array('width="120px"'), array(
						'<input type="checkbox" name="levelnew[creditspolicy]['.$vo.']" value="1">&nbsp;'.lang('plugin/fansclub', 'club_level_credits_'.$vo),
						'<input type="text" name="levelnew[creditspolicy]['.$vo.'_credits]" value="0">&nbsp;贡献',
		));
				}
				
			}
		}		
		showsubmit('editgrouplevel');
		showtablefooter();
		showformfooter();
	} else {
		$dataarr = array();
		$levelnew = $_POST['levelnew'];
		$default_creditspolicy = array(
				'post' => 0, 
				'post_credits' => 0, 
				'reply' => 0, 
				'reply_credits' => 0, 
				'digest' => 0, 
				'digest_credits' => 0, 
				'postattach' => 0, 
				'postattach_credits' => 0, 
				'getattach' => 0, 
				'getattach_credits' => 0, 
				'tradefinished' => 0, 
				'tradefinished_credits' => 0, 
				'joinpoll' => 0, 
				'joinpoll_credits' => 0);
		$levelnew['creditspolicy'] = empty($levelnew['creditspolicy']) ? $default_creditspolicy : array_merge($default_creditspolicy, $levelnew['creditspolicy']);
		$dataarr['creditspolicy'] = serialize($levelnew['creditspolicy']);
		
		C::t('forum_grouplevel')->update($levelid, $dataarr);
		updatecache('grouplevels');
		cpmsg('groups_setting_succeed', 'action=plugins&operation=config&do=14&identifier=fansclub&pmod=level', 'succeed');
	}
	
}
