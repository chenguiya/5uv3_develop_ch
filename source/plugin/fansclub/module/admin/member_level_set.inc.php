<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$formhash = FORMHASH;

if(submitcheck('grouplevelsubmit'))
{
	$ex_id = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_id_by_name('user_level');
	
	$arr_policy = array();
	foreach($_POST as $key => $value)
	{
		if($key == 'formhash' || $key == 'grouplevelsubmit') {} 
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
			cpmsg('【会员等级设置】修改成功', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=member_level_set', 'succeed');
		}
		else
		{
			cpmsg('【会员等级设置】没有要修改的', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=member_level_set', 'succeed');
		}
	}
	cpmsg('【会员等级设置】没有要修改的', 'action=plugins&operation=config&do=24&identifier=fansclub&pmod=admin&ac=member_level_set', 'succeed');
	
}
else
{
	
	$arr_group_user_level = fansclub_get_member_level();
	$arr_group_user_right = $config['group_user_right'];
	//print_r($arr_group_user_right);
}
