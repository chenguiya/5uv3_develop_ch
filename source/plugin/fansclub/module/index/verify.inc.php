<?php
// 取球迷会相关信息
//echo "<pre>";
//print_r($_G['setting']['discuzurl']);
// $http://192.168.2.169/discuz/plugin.php?id=fansclub&ac=verify&fid=6

$fid = intval($_GET['fid']);
$op = trim($_GET['op']);

if($op == 'apply') // 点击申请
{
	if(!$_G['uid']) fansclub_showsuccess('请要先登录', 'member.php?mod=logging&action=login', 'notice'); // 要先登录
	$uid = intval($_G['uid']);
	$level_type = intval($_GET['level_type']);
	$file_pic = @$_FILES['file_pic'];
	
	if(submitcheck('apply_level_submit')) fansclub_level_apply($fid, $uid, $level_type, $file_pic);
	exit();
}
else
{
	$formhash = FORMHASH;
	$arr_left_url = array(
						'boots' => 'plugin.php?id=fansclub&ac=verify&type=boots&fid='.$fid,
						'5u' => 'plugin.php?id=fansclub&ac=verify&type=5u&fid='.$fid,
						'org' => 'plugin.php?id=fansclub&ac=verify&type=org&fid='.$fid,
						'faq' => 'forum.php?mod=viewthread&tid=33152&extra=page%3D1'
					);
	
	// 普通等级积分公式
	$normal = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_all_by_name('group_level_normal');
	if(count($normal) > 0 && $normal[0]['policy'] != '')
	{
		$arr_policy = unserialize($normal[0]['policy']);
		$creditsformula = $arr_policy['creditsformula']; // 积分公式
	}
	
	// 普通等级名称
	$level_name =  C::t('forum_grouplevel')->fetch_all_creditslower_order();
	$show_level = array();
	$j = 0;
	for($i = count($level_name) - 1; $i > 0; $i--)
	{
		$show_level[$j]['leveltitle'] = $level_name[$i]['leveltitle'];
		$show_level[$j]['creditshigher'] = $level_name[$i]['creditshigher'];
		$show_level[$j]['id'] = $j+1;
		$j++;
	}
	
	// 取特殊等级信息
	$special = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_all_by_name('group_level_special');
	$arr_verify_org = array(); // 机构认证
	$arr_verify_5u = array(); // 5U认证
	
	if(count($special) > 0 && $special[0]['policy'] != '')
	{
		$arr_policy = unserialize($special[0]['policy']);
		
		$_tmp = array();
		foreach($arr_policy as $key => $value)
		{
			$_arr_tmp = explode('_', $key);
			$_tmp[$_arr_tmp[1]][$key] = $value;
			if($_arr_tmp[0] == 'leveltitle')
			{
				$_tmp[$_arr_tmp[1]]['levelid'] = $_arr_tmp[1];
				$_tmp[$_arr_tmp[1]]['leveltitle'] = $value;
			}
			else
			{
				$_tmp[$_arr_tmp[1]][$_arr_tmp[0].'_'.$_arr_tmp[2]] = $value;
			}
			unset($_tmp[$_arr_tmp[1]][$key]);
		}
		
		if(count($_tmp) > 0)
		{
			$apply_status = fansclub_get_level_apply_status($fid);
			// echo "<pre>"; print_r($apply_status);
			
			foreach($_tmp as $key => $value)
			{
				$arr_group_special[] = $value;
				if($value['levelid'] == 0) // 机构认证
				{
					$arr_verify_org['leveltitle'] = $value['leveltitle'];
					$arr_verify_org['specil_posts'] = $value['specil_posts'];
					$arr_verify_org['specil_members'] = $value['specil_members'];
					$arr_verify_org['specil_activity'] = $value['specil_activity'];
					$arr_verify_org['specil_contribution'] = $value['specil_contribution'];
					$arr_verify_org['apply_status'] = $apply_status['verify_org'];
				}
				
				if($value['levelid'] == 1) // 官方认证
				{
					$arr_verify_5u['leveltitle'] = $value['leveltitle'];
					$arr_verify_5u['specil_posts'] = $value['specil_posts'];
					$arr_verify_5u['specil_members'] = $value['specil_members'];
					$arr_verify_5u['specil_activity'] = $value['specil_activity'];
					$arr_verify_5u['specil_contribution'] = $value['specil_contribution'];
					$arr_verify_5u['apply_status'] = $apply_status['verify_5u'];
					
					// 是否已被申请
					$can_apply = TRUE;
					if($apply_status['verify_5u'] == '-1')
					{
						// 2015-09-21 修改这个取值
						$arr = C::t('#fansclub#plugin_fansclub_info')->fetch($_G['forum']['fid']);
						$_G['forum']['relation_fid'] = $arr['relation_fid'];
						
						$can_apply = C::t('#fansclub#plugin_fansclub_level_apply_log')->relation_fid_can_apply($_G['forum']['relation_fid'], $value['levelid']);
					}
				}
			}
		}
	}
}
