<?php
/**
 * m1 zhangjh 2015-05-25 “后台审核”要改在“附议完成”之前吗？是 OK
 * m2 zhangjh 2015-05-25 会长申请后，后台要立即可以看到申请记录吗？最好 OK
 * m3 zhangjh 2015-05-25 后台审核不通过，前台要通知会长重新修改资料，然后再重新提交审核吗？好 还没有OK
 */
if(!defined('IN_DISCUZ')) exit('Access Denied');

if(!$_G['uid']) showmessage('not_loggedin','member.php?mod=logging&action=login'); // 要先登录

if(!$_G['group']['allowbuildgroup']) showmessage('group_create_usergroup_failed', "plugin.php?id=fansclub:fansclub"); // 抱歉，您所在的用户组不能建立群组

$op = trim($_GET['op']);

if($op == 'create')
{
	if(submitcheck('apply_submit'))
	{
		fansclub_apply();
	}
	exit();
}

// 记录积分(例子)
// include_once libfile('function/credit');
// credit_log($member['uid'], $logtype, $member['uid'], $log);

// 缓存机制(例子)
/*
// 写缓存
require_once libfile('function/cache');
function build_cache_example()
{
	$data = array();
	$data[] = 'Hello World';
	$data[] = 'Hello Discuz!';
	save_syscache('example', $data);
}
updatecache('example');

// 其它页面读缓存
loadcache('example');
print_r($_G['cache']['example']);
*/

// 球迷会类别论坛版块数组
$arr_forum_list = fansclub_get_forum_list();

// 球迷会范围的省市联动
$fansclubcityhtml = fansclub_showdistrict(array(0, 0, 0, 0), 
	array('fansclubprovince', 'fansclubcity', 'fansclubdistrict', 'fansclubcommunity'), 'fansclubcitybox', null, 'fansclub');

$formhash = FORMHASH;

$msg_need_credit_user = $_G['username'];
$msg_need_credit = msg_need_credit();


// 显示需要的积分信息
function msg_need_credit()
{
	global $_G;
	$str_return = '，你好！';
	
	$creditstransextra = $_G['setting']['creditstransextra']['12'] ? $_G['setting']['creditstransextra']['12'] : $_G['setting']['creditstrans'];
	if($_G['group']['buildgroupcredits'])
	{
		if(empty($creditstransextra))
		{
			$_G['group']['buildgroupcredits'] = 0;
			$str_return .= '创建球迷会将消耗 0 积分';
		}
		else
		{
			getuserprofile('extcredits'.$creditstransextra);
			if($_G['member']['extcredits'.$creditstransextra] < $_G['group']['buildgroupcredits'])
			{
				$str_return .= '创建球迷会将消耗 '.$_G['group']['buildgroupcredits'].' '.
					$_G['setting']['extcredits'][$creditstransextra]['unit'].
					$_G['setting']['extcredits'][$creditstransextra]['title'].
					'，你现在有 '.$_G['member']['extcredits'.$creditstransextra].' '.
					$_G['setting']['extcredits'][$creditstransextra]['unit'].
					$_G['setting']['extcredits'][$creditstransextra]['title'].
					'，积分不足';
			}
			else
			{
				$str_return .= '创建球迷会将消耗 '.$_G['group']['buildgroupcredits'].' '.
					$_G['setting']['extcredits'][$creditstransextra]['unit'].
					$_G['setting']['extcredits'][$creditstransextra]['title'].
					'，你现在有 '.$_G['member']['extcredits'.$creditstransextra].' '.
					$_G['setting']['extcredits'][$creditstransextra]['unit'].
					$_G['setting']['extcredits'][$creditstransextra]['title'];
			}
		}
	}
	else
	{
		$str_return .= '创建球迷会将消耗 0 积分';
	}
	
	return $str_return."。";
}

// 球迷会申请 copy from ./source/module/forum/forum_group.php action=create
function fansclub_apply()
{
	global $_G, $config;
	
	// 1、用户组是否可以建立群组
	if(!$_G['group']['allowbuildgroup'])
	{
		fansclub_showerror('submit', '暂不可以申请');
	}
	
	// 2、申请是否需要积分(extcredits2/金钱)
	$creditstransextra = $_G['setting']['creditstransextra']['12'] ? $_G['setting']['creditstransextra']['12'] : $_G['setting']['creditstrans'];
	if($_G['group']['buildgroupcredits'])
	{
		if(empty($creditstransextra))
		{
			$_G['group']['buildgroupcredits'] = 0;
		}
		else
		{
			getuserprofile('extcredits'.$creditstransextra);
			if($_G['member']['extcredits'.$creditstransextra] < $_G['group']['buildgroupcredits'])
			{
				fansclub_showerror('submit', '不足'.$_G['group']['buildgroupcredits'].
					$_G['setting']['extcredits'][$creditstransextra]['unit'].
					$_G['setting']['extcredits'][$creditstransextra]['title']);
			}
		}
	}
	
	// 3、用户是否已经超过可以创始的群组数
	$groupnum = C::t('forum_forumfield')->fetch_groupnum_by_founderuid($_G['uid']);
	$groupnum_apply = C::t('#fansclub#plugin_fansclub_apply_log')->fetch_groupnum_by_uid($_G['uid']);
	$allowbuildgroup = $_G['group']['allowbuildgroup'] - $groupnum - $groupnum_apply;
	if($allowbuildgroup < 1)
	{
		fansclub_showerror('submit', '已经超过可以申请的球迷会数目');
	}
	
	// 处理传递的一些参数
	$uid = intval($_G['uid']);
	$username = censor(dhtmlspecialchars(trim($_G['username'])));
	$need_support = intval($config['apply_need_support']);
	$fansclub_name = censor(dhtmlspecialchars(cutstr(trim($_GET['fansclub_name']), 40, '')));
	
	$league_id = intval($_GET['league_id']);
	$club_id = intval($_GET['club_id']);
	$star_id = intval($_GET['star_id']);
	
	$province_id = intval($_GET['province_id']);
	$city_id = intval($_GET['city_id']);
	$district_id = intval($_GET['district_id']);
	$community_id = intval($_GET['community_id']);
	$fansclub_brief = censor(dhtmlspecialchars(cutstr(trim($_GET['fansclub_brief']), 1000, '')));
	$mobile = censor(dhtmlspecialchars(trim($_GET['mobile'])));
	$qq = censor(dhtmlspecialchars(trim($_GET['qq'])));
	$email = censor(dhtmlspecialchars(trim($_GET['email'])));
	
	// 条件检查
	if($league_id == 0) fansclub_showerror('league_id', '没有选择联赛');
	if($club_id == 0) fansclub_showerror('league_id', '没有选择球会');
	if($fansclub_name == '' || $fansclub_name == '请输入球迷会名称') fansclub_showerror('fansclub_name', '没有填写球迷会名称');
	if(C::t('forum_forum')->fetch_fid_by_name($fansclub_name)) fansclub_showerror('fansclub_name', '该球迷会名称已经存在');
	if(C::t('#fansclub#plugin_fansclub_apply_log')->fetch_apply_id_by_name($fansclub_name)) fansclub_showerror('fansclub_name', '该球迷会名称已经申请');
	if($province_id == 0) fansclub_showerror('province_id', '没有选择省份');
	if($city_id == 0) fansclub_showerror('province_id', '没有选择城市');
	
	if($fansclub_brief == '' || $fansclub_brief == '请输入球迷会简介') fansclub_showerror('fansclub_brief', '没有填写简介');
	// 组织要插入的数据
	require_once libfile('function/discuzcode');
	$fansclub_rules = discuzcode(dhtmlspecialchars(censor(trim($_GET['fansclub_rules']))), 0, 0, 0, 0, 1, 1, 0, 0, 1);
	$censormod = censormod($fansclub_rules);
	if($fansclub_rules == '' || $fansclub_rules == '请输入管理章程') fansclub_showerror('sendmessage', '没有填写管理章程');
	
	if($mobile == '' || $mobile == '请输入手机') fansclub_showerror('mobile', '没有填写手机');
	if(!is_mobile($mobile)) fansclub_showerror('mobile', '手机号码不正确');
	if($qq == '' || $mobile == '请输入QQ') fansclub_showerror('qq', '没有填写QQ');
	if(!is_qq($qq)) fansclub_showerror('qq', 'QQ不正确');
	if($email == '' || $mobile == '请输入Email') fansclub_showerror('email', '没有填写Email');
	if(!is_email($email)) fansclub_showerror('email', 'Email不正确');
	
	$data = array(
		'uid' => $uid,
		'username' => $username,
		'log_time' => TIMESTAMP,
		'status' => '0',
		'need_support' => $need_support,
		'have_support' => '0',
		'confirm_uid' => '',
		'confirm_time' => '',
		'fansclub_name' => $fansclub_name,
		'relation_fid' => $star_id > 0 ? $star_id : ($club_id > 0 ? $club_id : $league_id),
		'league_id' => $league_id,
		'club_id' => $club_id,
		'star_id' => $star_id,
		'range_id' => $community_id > 0 ? $community_id : ($district_id > 0 ? $district_id : ($city_id > 0 ? $city_id : $province_id)),
		'province_id' => $province_id,
		'city_id' => $city_id,
		'district_id' => $district_id,
		'community_id' => $community_id,
		'fansclub_logo' => '',
		'fansclub_brief' => $fansclub_brief,
		'fansclub_rules' => $fansclub_rules,
		'mobile' => $mobile,
		'qq' => $qq,
		'email' => $email,
		'credit_type' => 'extcredits'.$creditstransextra,
		'credit_num' => $_G['group']['buildgroupcredits'],
		'credit_unit' => $_G['setting']['extcredits'][$creditstransextra]['unit'].$_G['setting']['extcredits'][$creditstransextra]['title'],
		'ip' => $_G['clientip']
	);
	
	if($_FILES['fansclub_logo'])
	{
		//require_once libfile('function/home');
		/*
		if($files = pic_upload($_FILES['fansclub_logo'], 'forum', 225, 180, 2))
		{
			$data['fansclub_logo'] = "data/attachment/forum/".$files['pic'];
		}
		*/

		
		// 2015-04-29 zhangjh 修改同论坛的一样
		$upload = new discuz_upload();
		$eid = time();
		$upload->init($_FILES['fansclub_logo'], 'group', $eid, $uid);
		$upload->save();
		//echo $upload->errorcode;
		//die('测试服务器上传图片');
		
		//require_once libfile('class/image');
		//$image = new image();
		//$image->Thumb($upload->attach['target'], '', 48, 48, 'fixwr');
		//$data['fansclub_logo'] = $upload->attach['attachment'].'.thumb.jpg'; // 取缩略图
		$data['fansclub_logo'] = $upload->attach['attachment']; // 不取缩略图
	}
	
	$apply_id = C::t('#fansclub#plugin_fansclub_apply_log')->insert($data, TRUE);
	
	if($apply_id + 0 > 0) // 插入成功，跳转附议页面
	{
		// m1 相关修改，申请人自动附议
		$support_result = fansclub_apply_support($apply_id, FALSE);
		if($support_result === TRUE)
		{
			fansclub_showsuccess('申请成功，正在跳转', 'plugin.php?id=fansclub&ac=apply_support&apply_id='.$apply_id);
		}
		else
		{
			fansclub_showsuccess($support_result);
		}
	}
	else
	{
		fansclub_showerror('submit', '数据库异常');
	}
}

