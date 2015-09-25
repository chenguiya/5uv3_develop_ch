<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$formhash = FORMHASH;

$arr_group = array(); // 普通等级
$arr_group_special = array(); // 特殊等级
$arr_group_time = array(); // 有效时间

if(submitcheck('grouplevelsubmit')) // 修改普通等级
{
	global $_G;
	$ex_id = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_id_by_name('group_level_normal');
	
	//$svalue = 'contribution*1+members*5+posts*2';
	//$svalue = preg_replace("/(contribution|members|friends|doings|blogs|albums|polls|sharings|digestposts|posts|threads|oltime|extcredits[1-8])/", "\$group['\\1']", $svalue);
	
	//eval("\$credits = round(".$svalue.");");
	//update $credits 
	
	$arr_policy = array();
	foreach($_POST as $key => $value)
	{
		if($key == 'formhash' || $key == 'grouplevelsubmit') {} 
		else
		{
			$arr_policy[$key] = $value;
			/* 这个是之前的
			$arr_policy[$key] = $value;
			// 更新原来表的名称
			$arr_tmp = explode('_', $key);
			if($arr_tmp[0] == 'leveltitle')
			{
				$grouplevel_data = array('leveltitle' => $value);
				C::t('forum_grouplevel')->update(intval($arr_tmp[1]), $grouplevel_data); 
			}
			*/
		}
	}
	
	$policy = serialize($arr_policy);
	$forumfielddata = array('policy' => $policy);
	if($forumfielddata)
	{
		$result = C::t('#fansclub#plugin_fansclub_setting_ex')->update($ex_id, $forumfielddata); // 记录其他配置信息
		if($result)
		{
			cpmsg('【球迷会等级设置 - 普通等级】修改成功', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
		}
		else
		{
			cpmsg('【球迷会等级设置 - 普通等级】没有要修改的', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
		}
	}
	cpmsg('【球迷会等级设置 - 普通等级】没有要修改的', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
}
elseif(submitcheck('grouplevelsubmit_special')) // 修改特殊等级
{
	$ex_id = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_id_by_name('group_level_special');
	
	$arr_policy = array();
	
	foreach($_POST as $key => $value)
	{
		if($key == 'formhash' || $key == 'grouplevelsubmit_special') {} 
		else
		{
			$arr_policy[$key] = $value;
		}
	}
	
	$policy = serialize($arr_policy);
	$forumfielddata = array('policy' => $policy);
	if($forumfielddata)
	{
		$result = C::t('#fansclub#plugin_fansclub_setting_ex')->update($ex_id, $forumfielddata); // 记录其他配置信息
		if($result)
		{
			cpmsg('【球迷会等级设置 - 特殊等级】修改成功', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
		}
		else
		{
			cpmsg('【球迷会等级设置 - 特殊等级】没有要修改的', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
		}
	}
	cpmsg('【球迷会等级设置 - 特殊等级】没有要修改的', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
}
elseif(submitcheck('grouplevelsubmit_time')) // 修改有效时间
{
	$ex_id = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_id_by_name('group_active_time');
	
	$arr_policy = array();
	foreach($_POST as $key => $value)
	{
		if($key == 'formhash' || $key == 'grouplevelsubmit_time') {} 
		else
		{
			$arr_policy[$key] = $value;
		}
	}
	
	$policy = serialize($arr_policy);
	$forumfielddata = array('policy' => $policy);
	if($forumfielddata)
	{
		$result = C::t('#fansclub#plugin_fansclub_setting_ex')->update($ex_id, $forumfielddata); // 记录其他配置信息
		if($result)
		{
			cpmsg('【球迷会等级设置 - 有效时间】修改成功', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
		}
		else
		{
			cpmsg('【球迷会等级设置 - 有效时间】没有要修改的', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
		}
	}
	cpmsg('【球迷会等级设置 - 有效时间】没有要修改的', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=club_level_set', 'succeed');
}
else // 显示设置
{
	// ================================= 普通等级 =================================
	$normal = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_all_by_name('group_level_normal');
	if(count($normal) > 0 && $normal[0]['policy'] != '')
	{
		$arr_policy = unserialize($normal[0]['policy']);
		// print_r($arr_policy);
		$creditsformula = $arr_policy['creditsformula'];
		
		/* 这个是之前的
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
			foreach($_tmp as $key => $value)
			{
				$arr_group[] = $value;
			}
		}
		*/
	}
	else // 如果没有记录，取默认的值
	{
		$query = C::t('forum_grouplevel')->fetch_all_creditslower_order();
		for($i = 0; $i < count($query); $i++)
		{
			$_tmp = array();
			$_tmp['levelid'] = $query[$i]['levelid'];
			$_tmp['leveltitle'] = $query[$i]['leveltitle'];
			$arr_group[] = $_tmp;
		}
	}
	
	// ================================= 特殊等级 =================================
	$arr_group_special = fansclub_get_group_level_special_setting();
	if(count($arr_group_special) == 0)
	{
		foreach($config['group_level_special'] as $key => $value)
		{
			$_tmp = array();
			$_tmp['levelid'] = $key;
			$_tmp['leveltitle'] = $value;
			$arr_group_special[] = $_tmp;
		}
	}
	//print_r($arr_group_special);
	
	// ================================= 有效时间(结构同上) =================================
	$arr_group_time = fansclub_get_group_active_time_setting();
	
	if(count($arr_group_time) == 0)
	{
		foreach($config['group_level_special'] as $key => $value)
		{
			$_tmp = array();
			$_tmp['levelid'] = $key;
			$_tmp['leveltitle'] = $value;
			$arr_group_time[] = $_tmp;
		}
	}
}