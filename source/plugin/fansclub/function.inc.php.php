<?php
// 一些通用函数

// 根据名字取版块
function fansclub_get_forum_info($name, $type, $fup)
{
	return C::t('#fansclub#plugin_forum_forum')->fetch_group_by_name($name, $type, $fup);
}

// 插入版块(通过名称和类)，返回版块ID
function fansclub_insert_forum($league_name, $club_name, $star_name)
{
	if($league_name == '' || $club_name == '')
		return 0;
	
	$league_info = fansclub_get_forum_info($league_name, 'group', 0);
	if(count($league_info) > 0)
	{
		$league_id = $league_info[0]['fid'];
		$club_info = fansclub_get_forum_info($club_name, 'forum', $league_id);
		
		if(count($club_info) > 0)
		{
			$club_id = $club_info[0]['fid'];
			if($star_name == '')
			{
				return $club_id;
			}
			else
			{
				$star_info = fansclub_get_forum_info($star_name, 'sub', $club_id);
				if(count($star_info) > 0)
				{
					return $star_info[0]['fid'];
				}
				else
				{
					//echo "没有【".$star_info."】，要插入\n";
					$fid = C::t('forum_forum')->insert(array('fup' => $club_id, 'type' => 'sub', 'name' => $star_name, 'status' => 1, 'displayorder' => 0), 1);
					C::t('forum_forumfield')->insert(array('fid' => $fid));
					return $fid;
				}
			}
		}
		else
		{
			$fid = C::t('forum_forum')->insert(array('fup' => $league_id, 'type' => 'forum', 'name' => $club_name, 'status' => 1, 'displayorder' => 0), 1);
			C::t('forum_forumfield')->insert(array('fid' => $fid));
			if($star_name == '') // 没有选择star
			{
				return $fid;
			}
			else
			{
				return fansclub_insert_forum($league_name, $club_name, $star_name);
			}
		}
	}
	else
	{
		echo "没有【".$league_name."】，要插入\n";
		$fid = C::t('forum_forum')->insert(array('fup' => 0, 'type' => 'group', 'name' => $league_name, 'status' => 1, 'displayorder' => 0), 1);
		C::t('forum_forumfield')->insert(array('fid' => $fid));
		return fansclub_insert_forum($league_name, $club_name, $star_name);
	}
}

 
// 更新积分并升级
function fansclub_update_level($fid, $data)
{
	global $G;
	$group = array();
	$group['contribution'] = $data['contribution'];
	$group['members'] = $data['members'];
	$group['posts'] = $data['posts'];
	
	/* 要屏蔽的地方
	discuz/source/function/function_grouplog.php:                     C::t('forum_forum')->update_commoncredits($fid);
	discuz/source/include/post/post_newthread.php:                    C::t('forum_forum')->update_commoncredits(intval($mygroupid[0]));
	*/
	// 取积分规则
	$normal = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_all_by_name('group_level_normal');
	if(count($normal) > 0 && $normal[0]['policy'] != '')
	{
		$arr_policy = unserialize($normal[0]['policy']);
		$creditsformula = $arr_policy['creditsformula'];
		if($creditsformula != '')
		{
			$svalue = $creditsformula;
			$svalue = preg_replace("/(contribution|members|friends|doings|blogs|albums|polls|sharings|digestposts|posts|threads|oltime|extcredits[1-8])/", "\$group['\\1']", $svalue);
			
			eval("\$credits = round(".$svalue.");");
			$levelinfo = C::t('forum_grouplevel')->fetch_by_credits($credits);
			$_G['forum']['commoncredits'] = $credits;
			$levelid = $levelinfo['levelid'];
			$_G['forum']['level'] = $levelid;
			C::t('forum_forum')->update_group_level($levelid, $fid); 
			
			// 暂时没有UPDATE记录 credits
			
		}
	}
}
		
// 会员中心短信日志
function fansclub_write_sms_log($mobile, $content)
{
	global $_G;
	include_once(DISCUZ_ROOT.'./source/plugin/fansclub/extend/sms.php');
	$ip = $_G['clientip'];
	
	$sms = new sms();
	
	$posttime = time();
	
	// 先检查同一个手机号码是否大于1分钟
	$arr_log = C::t('#fansclub#plugin_fansclub_sms_log')->fetch_all_by_mobile($mobile);
	
	$last_post_time = (count($arr_log) > 0) ? ($arr_log[0]['posttime'] + 0) : 0;
	
	$pass_time = $posttime - $last_post_time;
	if($pass_time < 60)
	{
		return '请在60秒后再试';
	}
	$data = array();
	$data['mobile'] = $mobile + 0;
	$data['posttime'] = $posttime;
	$data['content'] = trim($content);
	$data['return_msg'] = '注册发短信了'; // 测试不发$sms->msg_post($mobile, trim($content));
	$data['ip'] = $ip;
	$data['user_name'] = $_G['username'];
	$data['money'] = $sms->find_money();  // 这个要大于30S调用1次
	
	$id = C::t('#fansclub#plugin_fansclub_sms_log')->insert($data);
	if($id + 0 > 0) {
		return TRUE;
	} else {
		return '记录失败，请稍候再试';
	}
}

// 生成手机注册验证码并发短信
function fansclub_mobile_create_verify_code($mobile)
{
	$arr_return = array('success' => FALSE, 'message' => '', 'verify_sms_code' => '');
	// 生成校验码
	$how = 6;
	// $alpha = 'abcdefghijkmnpqrstuvwxyz';
	$number = '1234567890';
	$verify_sms_code = '';
	
	for($i = 0; $i < $how; $i++)  
	{     
		// $alpha_or_number = mt_rand(0, 1);
		// $str = $alpha_or_number ? $alpha : $number;
		$str = $number;
		$which = mt_rand(0, strlen($str)-1);
		$code = substr($str, $which, 1);
		$j = !$i ? 4 : $j+15;
		$verify_sms_code .= $code;
	}
	
	if(is_mobile($mobile))
	{
		$content = '手机注册验证，您的短信验证码是：'.$verify_sms_code;
		$send_seccess = fansclub_write_sms_log($mobile, $content);
		if($send_seccess === TRUE)
		{
			$arr_return['success'] = TRUE;
			$arr_return['message'] = '发送成功，请输入短信验证码';
			$arr_return['verify_sms_code'] = $verify_sms_code;
		}
		else
		{
			$arr_return['message'] = $send_seccess;
		}
	}
	else
	{
		$arr_return['message'] = '不是一个有效的手机号码，请重新填写';
	}
	return $arr_return;
}


// 发短信
function fansclub_send_sms($mobile, $is_change = 0)
{
	global $_G;
	$arr_return = array('success' => FALSE, 'message' => '');
	$mobile = trim($mobile);
	$is_change = trim($is_change) + 0; // 是否是修改手机的
	
	if($is_change > 0)
	{
		$userid = $_G['uid'] + 0;
	
		if($userid == 0)
		{
			$arr_return['message'] = '请先登录...';
		}
		else
		{
			// todo 暂时没有需求
		}
	}
	
	if($arr_return['message'] == '')
	{
		$return = fansclub_mobile_create_verify_code($mobile);
		if($return['success'] === TRUE)
		{
			include_once(libfile('function/cache'));
			save_syscache('verify_sms_code', $return['verify_sms_code']);
			updatecache('verify_sms_code');
		}
		$arr_return['success'] = $return['success'];
		$arr_return['message'] = $return['message'];
	}
	
	return $arr_return;
}

// 检查短信
function fansclub_check_sms($sms_verify)
{
	global $_G;
	$arr_return = array('success' => FALSE, 'message' => '');
	loadcache('verify_sms_code');
	$verify_sms_code = trim($_G['cache']['verify_sms_code']);
	if($verify_sms_code != '' && strtolower($verify_sms_code) != 'null' && $sms_verify === $verify_sms_code)
	{
		$arr_return['success'] = TRUE;
	}
	else
	{
		$arr_return['message'] = '验证失败，正确的是'.$verify_sms_code;
	}
	return $arr_return;
}

// 找回手机号码时检查手机是否存在
function fansclub_check_mobile_exist($mobile)
{
	$arr_return = array('success' => FALSE, 'message' => '');
	$mobile = trim($mobile);
	$mobilelen = dstrlen($mobile);
	if($mobilelen != 11)
	{
		$arr_return['message'] = '手机号码长度不正确';
	}
	
	if($arr_return['message'] == '')
	{
		$have_record = C::t('#fansclub#plugin_fansclub_mobile')->fetch_uid_by_mobile($mobile);
		if(count($have_record) > 0)
		{
			
		}
		else
		{
			$arr_return['message'] = '手机号码不存在';
		}
	}
	if($arr_return['message'] == '')
	{
		$arr_return['success'] = TRUE;
	}
	
	return $arr_return;
}

// 注册检查手机号码
function fansclub_check_mobile($mobile)
{
	// C::t('#fansclub#plugin_fansclub_balance_log')->insert($fansclubcredits_log_data);
	$arr_return = array('success' => FALSE, 'message' => '');
	$mobile = trim($mobile);
	
	$mobilelen = dstrlen($mobile);
	if($mobilelen != 11)
	{
		$arr_return['message'] = '手机号码长度不正确';
	}
	
	if($arr_return['message'] == '')
	{
		$have_record = C::t('#fansclub#plugin_fansclub_mobile')->fetch_uid_by_mobile($mobile);
		if(count($have_record) > 0)
		{
			$arr_return['message'] = '手机号码已存在';
		}
	}
	
	// 多检查是否有这个手机号码的用户名
	if($arr_return['message'] == '')
	{
		$username = $mobile;
		loaducenter();
		$ucresult = uc_user_checkname($username);

		if($ucresult == -1) {
			$arr_return['message'] = '手机号码已存在';
		} elseif($ucresult == -2) {
			$arr_return['message'] = '手机号码已存在';
		} elseif($ucresult == -3) {
			if(C::t('common_member')->fetch_by_username($username) || C::t('common_member_archive')->fetch_by_username($username)) {
				$arr_return['message'] = '手机号码已存在';
			} else {
				$arr_return['message'] = '手机号码已存在';
			}
		}
	}
	
	if($arr_return['message'] == '')
	{
		$arr_return['success'] = TRUE;
	}
	return $arr_return;
	
}

// 注册检查用户名
function fansclub_check_username($username)
{
	global $_G;
	
	$arr_return = array('success' => FALSE, 'message' => '');
	$username = trim($username);
	include_once(DISCUZ_ROOT.'./config/config_ucenter.php');
	include_once(DISCUZ_ROOT.'./uc_client/client.php');
	
	$usernamelen = dstrlen($username);
	if($usernamelen < 3) {
		$arr_return['message'] = lang('message', 'profile_username_tooshort');
	} elseif($usernamelen > 15) {
		$arr_return['message'] = lang('message', 'profile_username_toolong');
	}
	
	if($arr_return['message'] == '')
	{
		loaducenter();
		$ucresult = uc_user_checkname($username);

		if($ucresult == -1) {
			$arr_return['message'] = lang('message', 'profile_username_illegal');
		} elseif($ucresult == -2) {
			$arr_return['message'] = lang('message', 'profile_username_protect');
		} elseif($ucresult == -3) {
			if(C::t('common_member')->fetch_by_username($username) || C::t('common_member_archive')->fetch_by_username($username)) {
				$arr_return['message'] = lang('message', 'register_check_found');
			} else {
				$arr_return['message'] = lang('message', 'register_activation');
			}
		}
	}
	
	if($arr_return['message'] == '')
	{
		$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($_G['setting']['censoruser'] = trim($_G['setting']['censoruser'])), '/')).')$/i';
		if($_G['setting']['censoruser'] && @preg_match($censorexp, $username)) {
			$arr_return['message'] = lang('message', 'profile_username_protect');
		}
	}
	
	// 多检查是否有这个用户名的手机号码
	if($arr_return['message'] == '')
	{
		$have_record = C::t('#fansclub#plugin_fansclub_mobile')->fetch_uid_by_mobile($mobile);
		if(count($have_record) > 0)
		{
			$arr_return['message'] = '用户名已存在';
		}
	}
	
	if($arr_return['message'] == '')
	{
		$arr_return['success'] = TRUE;
	}
	return $arr_return;
}

// 从CI接口取比赛数据
function fansclub_get_live_from_ci($data = array())
{
	$arr_sign = array();
	$sn_key = '3#u29As9Fj23';    // 测试key:a@39e8a53Qs 正式key:3#u29As9Fj23
	
	if(count($data) > 0)
	{
		foreach($data as $key => $value)
		{
			$arr_sign[] = $key.'='.urldecode($value);
		}
	}
	
	asort($arr_sign);
	$str_param = implode('&', $arr_sign);
	$str_sign = $str_param.'||'.$sn_key;
	
	$sign = md5($str_sign);
	
	//$url = 'http://zhangjh.dev.usport.cc/api/liaoqiu/get_match_list?'.$str_param.'&sign='.$sign; // 暂时用内网的数据
	$url = 'http://www.5usport.com/api/liaoqiu/get_match_list?'.$str_param.'&sign='.$sign; // 外网的数据
	
	$result = curl_access($url);
	$arr_result = json_decode($result, TRUE);
	
	if($arr_result['state_code'] == '0')
	{
		return $arr_result['game_array'];
	}
	else
	{
		return array();
	}
}

function fansclub_get_live($data = array())
{
	if(count($data) == 0)
	{
		$data['all_event'] = '1';
	}
	
	$arr_game = fansclub_get_live_from_ci($data);

	for($i = 0; $i < count($arr_game); $i++)
	{
		if($arr_game[$i]['date'] == date('Y-m-d'))
		{
			$arr_game[$i]['date_tips'] = '今天';
		}
		elseif($arr_game[$i]['date'] == date('Y-m-d', time()+60*60*24))
		{
			$arr_game[$i]['date_tips'] = '眀天';
		}
		elseif($arr_game[$i]['date'] == date('Y-m-d', time()+60*60*24*2))
		{
			$arr_game[$i]['date_tips'] = '后天';
		}
		else
		{
			$arr_game[$i]['date_tips'] = '';
		}
		
		for($j = 0; $j < count($arr_game[$i]['games']); $j++)
		{
			$_tmp = explode(' ', $arr_game[$i]['games'][$j]['game_time']);
			$_tmp2 = explode(':', $_tmp[1]);
			$arr_game[$i]['games'][$j]['game_time'] = $_tmp2[0].':'.$_tmp2[1];
			
			$arr_game[$i]['games'][$j]['image1_url'] = 'http://192.168.2.169/api/index.php?action=showimg&data='.urlencode($arr_game[$i]['games'][$j]['image1_url']);
			$arr_game[$i]['games'][$j]['image2_url'] = 'http://192.168.2.169/api/index.php?action=showimg&data='.urlencode($arr_game[$i]['games'][$j]['image2_url']);
			
			// $_tmp3 = explode('-', $arr_game[$i]['games'][$j]['round']);
			// $arr_game[$i]['games'][$j]['round'] = $_tmp3[0];
		}
	}
	
	return $arr_game;
}
// 处理视频截图
function fansclub_videoscreenshot($data = array())
{
	global $_G;
	
	$arr_return = array('success' => FALSE, 'message' => '', 'attachment' => '');
	$video_url = $data['video_url']; // flash地址
	
	$arr_query = array();
	$arr_query['action'] = 'videoscreenshot';
	$arr_query['pid'] = '5usport'; // 暂时写死
	$arr_query['data'] = urlencode($video_url);
	$arr_query['time'] = time();
	$arr_query['sign'] = md5(http_build_query($arr_query).'&key='.'12345678'); // 暂时写死
	
	$api_url = 'http://192.168.2.169/api/index.php'.'?'.http_build_query($arr_query); // 暂时写死
	$result = curl_access($api_url);
	$arr_result = json_decode($result, TRUE);
	
	if($arr_result['success'] === TRUE)
	{
		$base64_image_content = $arr_result['img_code'];
		if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result))
		{
			$type = $result[2];
			$source = getglobal('setting/attachdir').'./temp/'.'shipin_'.md5($video_url).".{$type}";
			if(file_put_contents($source, base64_decode(str_replace($result[1], '', $base64_image_content))))
			{
				$upload = new discuz_upload();
				$extid = md5($video_url);
				$extension = $type;
				$attachdir = $upload->get_target_dir('forum', $extid);
				$attachment = $attachdir.$extid.'.'.$extension;
				$target = getglobal('setting/attachdir').'./forum/'.$attachment;
				@copy($source, $target);
				@chmod($target, 0644);
				@unlink($source);
				
				$message = '新文件保存成功';
				$arr_return['success'] = TRUE;
				$arr_return['attachment'] = $attachment;
				
				// 写数据库记录
				$data['pic_path'] = $attachment;
				
				$arr_info = C::t('#fansclub#plugin_fansclub_videoscreenshot')->fetch($data['pid']);
				if(count($arr_info) == 0) // 没有记录
				{
					C::t('#fansclub#plugin_fansclub_videoscreenshot')->insert($data);
				}
				else
				{
					C::t('#fansclub#plugin_fansclub_videoscreenshot')->update($data['pid'], $data);
				}
			}
			else
			{
				$message = '新文件保存失败';
			}
		}
		else
		{
			$message = '编码验证错误';
		}
	}
	else
	{
		$message = $arr_result['message'];
	}
	
	$arr_return['message'] = $message;
	
	return $arr_return;
}

// 取群组用户等级
function fansclub_get_member_level()
{
	// $cvar = $_G['cache']['plugin']['fansclub']; 
	$config = include DISCUZ_ROOT.'./source/plugin/fansclub/data/config.php';
	// $config = array_merge($cvar, $config); // 合并配置
	
	$user_level = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_all_by_name('user_level');
	
	$arr_group_user_level = array();
	if(count($user_level) > 0 && $user_level[0]['policy'] != '')
	{
		$arr_policy = unserialize($user_level[0]['policy']);
		$_tmp = array();

		foreach($arr_policy as $key => $value)
		{
			$_arr_tmp = explode('_', $key);
			$_tmp[$_arr_tmp[1]][$key] = $value;
			
			
			if($_arr_tmp[0] == 'title')
			{
				$_tmp[$_arr_tmp[1]]['id'] = $_arr_tmp[1];
				$_tmp[$_arr_tmp[1]]['title'] = $value;
			}
			else
			{
				$_tmp[$_arr_tmp[1]][$_arr_tmp[0]] = $value;
			}
			unset($_tmp[$_arr_tmp[1]][$key]);
		}
		
		if(count($_tmp) > 0)
		{
			foreach($_tmp as $key => $value)
			{
				$arr_group_user_level[] = $value;
			}
		}
		
	}
	else // 如果没有记录，取默认的值
	{
		foreach($config['group_user_level'] as $key => $value)
		{
			if($key == '0') continue;
			
			$_tmp = array();
			$_tmp['id'] = $key;
			$_tmp['title'] = $value;
			$_tmp['moderator'] = in_array($key, $config['group_user_level_moderator']) ? '1' : '0';
			$arr_group_user_level[] = $_tmp;
		}
	}
	
	// 2015-05-15 设置管理员显示在前面
	if(count($arr_group_user_level) > 0)
	{
		$moderators = array();
		foreach ($arr_group_user_level as $key => $row)
		{
			$moderators[$key] = $row['moderator'];
		}
		array_multisort($moderators, SORT_DESC, $arr_group_user_level);
	}
	return $arr_group_user_level;
}

// 自动添加群组分类，返回上级fid
function fansclub_add_group_type($apply)
{
	global $_G;
	$return_fid = 0;
	
	$league_id = intval($apply['league_id']);
	$club_id = intval($apply['club_id']);
	$star_id = intval($apply['star_id']);
	
	$league_name = fansclub_get_forum_name($league_id);
	$club_name = fansclub_get_forum_name($club_id);
	
	if($star_id > 0) // 有选球星的
	{
		$star_name = fansclub_get_forum_name($star_id);
	}
	
	$have_club = $have_star = FALSE; 		// 查询群组分类是否有 club_name star_name
	$club_fup = $star_fup = $fans_fup = 0; 	// club、star、fans的上级fid
	
	$row = C::t('forum_forum')->fetch_all_group_type();
	for($i = 0; $i < count($row); $i++)
	{
		$have_club = ($row[$i]['type'] == 'group' && $row[$i]['name'] == $club_name) ? TRUE : $have_club;
		$star_fup = ($have_club && $star_fup == 0) ? $row[$i]['fid'] : $star_fup; // 如果有相同名字的club，取排前面的ID
		
		if($star_id > 0)
		{
			$have_star = ($row[$i]['type'] == 'forum' && $row[$i]['name'] == $star_name) ? TRUE : $have_star;
			$fans_fup = ($have_star && $fans_fup == 0) ? $row[$i]['fid'] : $fans_fup;
		}
	}
	
	if(!$have_club) // 分类不存在的
	{
		$star_fup = C::t('forum_forum')->insert_group($club_fup, 'group', $club_name, 3, 0);
		C::t('forum_forumfield')->insert(array('fid' => $star_fup)); // 同时插入扩展表
	}
	$return_fid = $star_fup;
	
	if($star_id > 0) // 有选球星的
	{
		if(!$have_star) 
		{
			$fans_fup = C::t('forum_forum')->insert_group($star_fup, 'forum', $star_name, 3, 0);
			C::t('forum_forumfield')->insert(array('fid' => $fans_fup));
		}
		$return_fid = $fans_fup;
	}
	
	return $return_fid;
}

// 自动移除关联版块，群组fansclub_fid 从 版块 fid中移除
function fansclub_remove_forum_relation($fid, $fansclub_fid)
{
	// 取已级关联的数据
	$row = C::t('forum_forumfield')->fetch($fid);
	$relatedgroup = trim($row['relatedgroup']);
	$add_relatedgroup = ','.$relatedgroup.',';
	$add_fansclub_fid = ','.$fansclub_fid.',';
	
	$new_relatedgroup = str_replace($add_fansclub_fid, ',', $add_relatedgroup);
	$real_relatedgroup = substr($new_relatedgroup, 1, strlen($new_relatedgroup) - 2); 
	
	C::t('forum_forumfield')->update($fid, $real_relatedgroup);
}

// 自动加入关联版块  版块 fid中添加 群组fansclub_fid
function fansclub_add_forum_relation($fid, $fansclub_fid)
{
	// 取已级关联的数据
	$row = C::t('forum_forumfield')->fetch($fid);
	$relatedgroup = trim($row['relatedgroup']);
	
	if($relatedgroup == '')
	{
		$forumfielddata = array('relatedgroup' => $fansclub_fid);
	}
	else
	{
		$forumfielddata = array('relatedgroup' => $relatedgroup.','.$fansclub_fid);
	}
	
	if($forumfielddata)
	{
		C::t('forum_forumfield')->update($fid, $forumfielddata);
	}
}

// 不显示省市
function replace_province_city($str)
{
	return str_replace(array('市', '省', '自治区', '特别行政区', '回族', '壮族', '维吾尔'), '', $str);
}
// 取球迷会信息
function get_fansclub_info($fid)
{
	global $_G;
	$arr_return = array();
	$fid = $fid + 0;
	$arr = C::t('#fansclub#plugin_fansclub_info')->fetch($fid); // table_plugin_fansclub_apply_log
	if(count(arr) > 0) // 如果有数据
	{
		$_arr_forum = C::t('forum_forum')->fetch($fid);
		$_arr_forumfield = C::t('forum_forumfield')->fetch($fid);
		$_arr_balance = C::t('#fansclub#plugin_fansclub_balance')->fetch_first($fid);
			
		$arr_return['fid'] = $_arr_forum['fid'];
		$arr_return['relation_fid'] = $arr['relation_fid'];
		$arr_return['fup'] = $_arr_forum['fup'];
		$arr_return['type'] = $_arr_forum['type'];
		$arr_return['name'] = $_arr_forum['name'];
		
		if($_arr_forum['status'] <> '3')
		{
			$arr_return['banner'] = $_G['siteurl'].'data/attachment/common/'.$_arr_forumfield['banner'];
			$arr_return['icon'] = $_G['siteurl'].'data/attachment/common/'.$_arr_forumfield['icon'];
		}
		else // 群组
		{
			$arr_return['banner'] = $_G['siteurl'].'data/attachment/group/'.$_arr_forumfield['banner'];
			$arr_return['icon'] = $_G['siteurl'].'data/attachment/group/'.$_arr_forumfield['icon'];
		}
		
		// 默认 banner 和 icon
		if($_arr_forumfield['banner'] == '')
			$arr_return['banner'] = $_G['siteurl'].'template/usportstyle/common/images/default_banner.jpg';
		if($_arr_forumfield['icon'] == '')
			$arr_return['icon'] = $_G['siteurl'].'template/usportstyle/common/images/default_icon.jpg';
		
		$_tmp = explode(' ', trim($arr['province_city']));
		$arr_return['province_city'] = $_tmp[0].' | '.$_tmp[1];
		$arr_return['province_city'] = replace_province_city($arr_return['province_city']);
		
		$arr_return['members'] = $_arr_forumfield['membernum'];
		$arr_return['posts'] = $_arr_forum['posts'];
		$arr_return['contribution'] =  $_arr_balance['extendcredits3']+0;
		$arr_return['description'] = $_arr_forumfield['description'];
		$arr_return['league_id'] = $arr['league_id'];
		$arr_return['club_id'] = $arr['club_id'];
		$arr_return['star_id'] = $arr['star_id'];
		$arr_return['province_id'] = $arr['province_id'];
		$arr_return['city_id'] = $arr['city_id'];
		$arr_return['district_id'] = $arr['district_id'];
		$arr_return['community_id'] = $arr['community_id'];
		$arr_return['province_name'] = replace_province_city(fansclub_get_district_name($arr['province_id']));
		$arr_return['city_name'] = replace_province_city(fansclub_get_district_name($arr['city_id']));
		$arr_return['district_name'] = fansclub_get_district_name($arr['district_id']);
		$arr_return['community_name'] = fansclub_get_district_name($arr['community_id']);
		
		// ===================== 取频道的信息 =====================
		$search_info = C::t('forum_forum')->fetch(intval($arr_return['relation_fid']));
		if($search_info['type'] == 'sub') // 如果是球星版块
		{
			$channel_fid = $search_info['fup']; // 频道ID
		}
		elseif($search_info['type'] == 'forum') // 如果是俱乐部版块
		{
			$channel_fid = $search_info['fup'];
		}
		
		$_tmp_forum = C::t('forum_forum')->fetch($channel_fid);
		$_tmp_forumfield = C::t('forum_forumfield')->fetch($channel_fid);
		$_tmp = array();
		$_tmp['fid'] = $channel_fid;
		$_tmp['name'] = $_tmp_forum['name'];
		$_tmp['rank'] = $_tmp_forum['rank'];
		$_tmp['icon'] = $_G['siteurl'].'data/attachment/common/'.$_tmp_forumfield['icon'];
		
		$_tmp_forum_league = C::t('forum_forum')->fetch($_tmp_forum['fup']);
		$_tmp['league_name'] = $_tmp_forum_league['name'];
		
		$related_group = trim($_tmp_forumfield['relatedgroup']);
	
		// 下级版块关联的
		$arr_forum_list = fansclub_get_forum_list(); // 左版块
		$arr_sub_forum = array(); // 子版块
		foreach($arr_forum_list as $key => $value)
		{
			foreach($value['list'] as $key2 => $value2)
			{
				foreach($value2['list'] as $key3 => $value3)
				{
					$arr_sub_forum[$key2][] = $key3;
				}
			}
		}
		
		for($j = 0; $j < count($arr_sub_forum[$channel_fid]); $j++)
		{
			$sub_fid = intval($arr_sub_forum[$channel_fid][$j]);
			$sub_forumfield = C::t('forum_forumfield')->fetch($sub_fid);
			$sub_related_group = trim($sub_forumfield['relatedgroup']);
			if(trim($sub_related_group) != '')
				$related_group = $related_group.','.$sub_related_group;
		}
		
		$_tmp['members'] = $_tmp['posts'] = $_tmp['contribution'] = 0;
		if($related_group != '') // 如果有关联的球迷会
		{
			$_arr = explode(',', $related_group);
			$_tmp['clubs'] = count($_arr);
			foreach($_arr as $key => $value)
			{
				$group_fid = intval($value);
				$_arr_forum = C::t('forum_forum')->fetch($group_fid);
				$_arr_forumfield = C::t('forum_forumfield')->fetch($group_fid);
				$_arr_balance = C::t('#fansclub#plugin_fansclub_balance')->fetch_first($group_fid);
				
				$_tmp['members'] += $_arr_forumfield['membernum'];
				$_tmp['posts'] += $_arr_forum['posts'];
				$_tmp['contribution'] += $_arr_balance['extendcredits3'];
			}
		}
		$arr_return['channel'] = $_tmp;
		//写入球迷会动态		2015/5/12 by Daming
		$feeds = $tody_feeds = 0;
		foreach ($userlist = C::t('#fansclub#plugin_forum_groupuser')->fetch_all_user_by_fid($fid) as $value) {
			$arr_return['feed_num'] += C::t('#fansclub#plugin_home_feed')->count_feed_num($value['uid']);
			$arr_return['todayfeed_num'] += C::t('#fansclub#plugin_home_feed')->count_today_feed_num($value['uid']);
		}		
	}
// 	var_dump($arr_return);die;
	return $arr_return;
}

function is_mobile($mobile) {
	return preg_match("/1[3458]{1}\d{9}$/", $mobile);
}

function is_qq($qq) {
	return strlen($qq) > 3 && preg_match("/\d{5,12}$/", $qq);
}

function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

// 取地区的名称
function fansclub_get_district_name($id)
{
	$district = C::t('common_district')->fetch($id);
	return count($district) > 0 ? $district['name'] : '';
}

// 显示省市
function fansclub_get_province_city()
{
	$arr_return = array();
	$_tmp = C::t('common_district')->fetch_all_by_upid(array(0), 'displayorder', 'ASC');
	$i = 0;
	foreach($_tmp as $value)
	{
		
		// Provides: <body text='black'>
		$province_id = intval($value['id']);
		$province_name = $value['name'];
		$arr_return[$i]['id'] = $province_id;
		$arr_return[$i]['name'] = replace_province_city($province_name);
		$arr_return[$i]['list'] = array();
		$_tmp_sub = C::t('common_district')->fetch_all_by_upid($province_id, 'displayorder', 'ASC');
		$j = 0;
		foreach($_tmp_sub as $value2)
		{
			$city_id = intval($value2['id']);
			$city_name = $value2['name'];
			$arr_return[$i]['list'][$j]['id'] = $city_id;
			$arr_return[$i]['list'][$j]['name'] = replace_province_city($city_name);
			$j++;
		}
		$i++;
	}
	return $arr_return;
}

// 取版块的名称
function fansclub_get_forum_name($fid)
{
	global $_G;
	if(!isset($_G['cache']['forums']))
	{
		loadcache('forums');
	}
	
	$forums_cache = $_G['cache']['forums'];
	return $forums_cache[$fid]['name'];
}

// 取论坛版块数组，联动显示用
function fansclub_get_forum_list($charge_forums = array())
{
	global $_G;
	$arr_return = array();
	
	if(!isset($_G['cache']['forums']))
	{
		loadcache('forums');
	}
	
	$forums_cache = $_G['cache']['forums'];
	foreach($forums_cache as $fid => $forum)
	{
		if($forum['status'] == 1 && (!$forum['viewperm'] && $_G['group']['readaccess']) || ($forum['viewperm'] && forumperm($forum['viewperm'])))
		{
			if($forum['type'] == 'group')
			{
				$arr_return[$fid]['name'] = $forum['name'];
			}
			elseif($forum['type'] == 'forum')
			{
				$arr_return[$forum['fup']]['list'][$fid] = array('name' => $forum['name']);
				$_tmp[$fid] = $forum['fup'];
			}
			elseif($forum['type'] == 'sub') // todo 这里显示不是很完美
			{
				$arr_return[$_tmp[$forum['fup']]]['list'][$forum['fup']]['list'][$fid] = array('name' => $forum['name']);
			}
		}
	}
	return $arr_return;
}

// 提示表单错误
function fansclub_showerror($key, $extrainfo)
{
	echo '<script>';
	echo 'parent.show_error("'.$key.'", "'.$extrainfo.'");';
	echo '</script>';
	exit();
}

// 提示表单成功
function fansclub_showsuccess($message = '', $jump_url = '', $type = '')
{
	echo '<script type="text/javascript">';
	echo "parent.show_success('$message', '$jump_url', '$type');";
	echo '</script>';
	exit();
}

// 省市联动 copy from .source/function/function_profile.php showdistrict
function fansclub_showdistrict($values, $elems = array(), $container = 'districtbox', $showlevel = null, $containertype = 'birth')
{
	$html = '';
	if(!preg_match("/^[A-Za-z0-9_]+$/", $container)) {
		return $html;
	}
	$showlevel = !empty($showlevel) ? intval($showlevel) : count($values);
	$showlevel = $showlevel <= 4 ? $showlevel : 4;
	$upids = array(0);
	for($i=0;$i<$showlevel;$i++) {
		if(!empty($values[$i])) {
			$upids[] = intval($values[$i]);
		} else {
			for($j=$i; $j<$showlevel; $j++) {
				$values[$j] = '';
			}
			break;
		}
	}
	$options = array(1=>array(), 2=>array(), 3=>array(), 4=>array());
	if($upids && is_array($upids)) {
		foreach(C::t('common_district')->fetch_all_by_upid($upids, 'displayorder', 'ASC') as $value) {
			if($value['level'] == 1 && ($value['id'] != $values[0] && ($value['usetype'] == 0 || !((($containertype == 'birth' || $containertype == 'fansclub') && in_array($value['usetype'], array(1, 3))) || ($containertype != 'birth' && in_array($value['usetype'], array(2, 3))))))) {
				continue;
			}
			$options[$value['level']][] = array($value['id'], replace_province_city($value['name']));
		}
	}
	$names = array('province', 'city', 'district', 'community');
	for($i=0; $i<4;$i++) {
		if(!empty($elems[$i])) {
			$elems[$i] = dhtmlspecialchars(preg_replace("/[^\[A-Za-z0-9_\]]/", '', $elems[$i]));
		} else {
			$elems[$i] = ($containertype == 'birth' ? 'birth' : ($containertype == 'reside' ? 'reside' : 'fansclub')).$names[$i];
		}
	}

	for($i=0;$i<$showlevel;$i++) {
		$level = $i+1;
		if($level > 2)
			break;
		if(!empty($options[$level])) {
			$jscall = "fansclub_showdistrict('$container', ['$elems[0]', '$elems[1]', '$elems[2]', '$elems[3]'], $showlevel, $level, '$containertype')";
			$html .= '<select name="'.$elems[$i].'" id="'.$elems[$i].'" class="ps" onchange="'.$jscall.'" tabindex="1">';
			$html .= '<option value="">'.lang('spacecp', 'district_level_'.$level).'</option>';
			foreach($options[$level] as $option) {
				$selected = $option[0] == $values[$i] ? ' selected="selected"' : '';
				$html .= '<option did="'.$option[0].'" value="'.$option[1].'"'.$selected.'>'.$option[1].'</option>';
			}
			$html .= '</select>';
			$html .= '&nbsp;';
		}
	}
	return $html;
}

/**
 * 字符串截取函数
 *
 * @param   $str        要处理的字符串
 * @param   $start      开始位置
 * @param   $length     长度
 * @param   $charater   字符串编码
 * @param   $ppp        多加的后缀
 * @return  string
 */
function str_intercept($str, $start, $length, $charater='UTF-8', $ppp = "...")
{
    $len = mb_strlen($str, $charater);
    if($start >= $length)
    {
        $return = $str;
    }
    else
    {
        $return = mb_substr($str, $start, $length, $charater);
    }
    if(mb_strlen($return,$charater) < ($len-$start))
        $return .= $ppp;
    return $return;
}

function is_gbk($str)
{
    if(!preg_match("/^[".chr(0xa1)."-".chr(0xff)."a-za-z0-9_]+$/", $str))   // gb2312汉字字母数字下划线正则表达式 
    {
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

function is_utf8($str)
{
    if(!preg_match("/^[x{4e00}-x{9fa5}a-za-z0-9_]+$/u", $str))  // utf-8汉字字母数字下划线正则表达式 
    {
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

function arr_to_gbk($arr) 
{
    if (is_array($arr))
    {
        foreach($arr as $k => $v)
        {
            $_k = arr_to_gbk($k);
            $arr[$_k] = arr_to_gbk($v);
            
            if ($k != $_k)
                unset($arr[$k]);
        }
    }
    else
    {
        $arr = iconv('UTF-8', 'GBK', $arr);
    }
    return $arr;
}

function arr_to_utf8($arr) 
{
    if (is_array($arr))
    {
        foreach($arr as $k => $v)
        {
            $_k = arr_to_utf8($k);
            $arr[$_k] = arr_to_utf8($v);
            
            if ($k != $_k)
                unset($arr[$k]);
        }
    }
    else
    {
        $arr = iconv('GBK', 'UTF-8', $arr);
    }
    return $arr;
}

/**
 * curl请求
 *
 * @access  public
 * @param   $url            请求地址地址
 * @param   $str_query      请求的参数
 * @param   $method         请求的方式
 * @param   $str_referer    伪造请求来源地址
 * @param   $cookie_file    请求cookie信息
 * @return  string
 */
function curl_access($str_url, $str_query = '', $method = '', $str_referer = '', $cookie_file = '')
{
    $obj_ch = curl_init();
    curl_setopt($obj_ch, CURLOPT_TIMEOUT, 200);
    curl_setopt($obj_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');

    if ($cookie_file != '')
    {
        if(file_exists($cookie_file))
        {
            curl_setopt($obj_ch, CURLOPT_COOKIEFILE, $cookie_file);
        }
        curl_setopt($obj_ch, CURLOPT_COOKIEJAR, $cookie_file);
    }

    if ($str_referer != '')
    {
        curl_setopt($obj_ch, CURLOPT_REFERER, $str_referer);
    }

    if ($method == 'post')
    {
        curl_setopt($obj_ch, CURLOPT_URL, $str_url);
        curl_setopt($obj_ch, CURLOPT_POST, 1);
        curl_setopt($obj_ch, CURLOPT_POSTFIELDS, $str_query); 
    }
    else
    {
        curl_setopt($obj_ch, CURLOPT_URL, $str_url.($str_query?'?'.$str_query:''));
        curl_setopt($obj_ch, CURLOPT_HTTPGET, 1);
    }

    if (strpos($str_url, 'https') !== false)
    {
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }

    @curl_setopt($obj_ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
    $str = curl_exec($obj_ch);
    curl_close($obj_ch);

    return trim($str);
}

function get_group_info($fid) {
// 	$cache_file = DISCUZ_ROOT.'./data/sysdata/cache_'.$fid.'_groupifo.php';
// 	if (($_G['timestamp'] - @filetime($cache_file)) >$it618['cachetime']*60) {
		$data = C::t('forum_forum')->fetch_info_by_fid($fid);
		$data['banner'] = 'data/attachment/group/'.$data['banner'];
		$data['icon'] = 'data/attachment/group/'.$data['icon'];
// 		$cacheArray .= "\$data=".arrayeval($data).";\n";
// 		writetocache($fid.'_groupifo', $cacheArray);
// 	} else {
// 		include_once DISCUZ_ROOT.'./data/sysdata/cache_'.$fid.'_groupifo.php';
// 		$data = $data[0];
// 	}
	return $data;
}
