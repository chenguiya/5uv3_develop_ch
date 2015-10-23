<?php
include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function/function_home.php');
// 一些通用函数

// 先检查是否为wap代理，准确度高
function fansclub_check_wap()
{
    if(stristr($_SERVER['HTTP_VIA'],"wap"))
    {
        return true;
    }
    // 检查浏览器是否接受 WML.
    elseif(strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0){
        return true;
    }
    //检查USER_AGENT
    elseif(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])){
        return true;
    }
    else{
        return false;
    }
}

// 用户行为记录 by zhangjh 2015-10-23
function fansclub_use_log($log_type = '')
{
    global $_G;
    $is_wap = fansclub_check_wap();
    
    /*
    require_once libfile('function/cache');
    save_syscache('qudao_fid', 113);
    updatecache('qudao_fid');
    save_syscache('qudao_from', 'weixin');
    updatecache('qudao_from');
    */
    
    loadcache('qudao_fid');
    loadcache('qudao_from');
    $qudao_fid = intval(trim($_G['cache']['qudao_fid']));
    $qudao_from = trim($_G['cache']['qudao_from']);
    
    // echo "<pre>";
    // print_r($_G);
    $arr_data = array();
    $arr_data['log_time'] = TIMESTAMP;
    $arr_data['is_wap'] = $is_wap ? 1 : 0;
    $arr_data['qudao_from'] = $qudao_from;
    $arr_data['qudao_fid'] = $qudao_fid;
    $arr_data['log_type'] = $log_type;
    
    $arr_data['g_uid'] = $_G['uid'];
    $arr_data['g_username'] = $_G['username'];
    $arr_data['g_clientip'] = $_G['clientip'];
    $arr_data['g_basescript'] = $_G['basescript'];
    $arr_data['g_mod'] = $_G['mod'];
    $arr_data['g_fid'] = $_G['fid'];
    $arr_data['g_tid'] = $_G['tid'];
    $arr_data['g_action_action'] = $_G['action']['action'];
    $arr_data['g_action_fid'] = $_G['action']['fid'];
    $arr_data['g_action_tid'] = $_G['action']['tid'];
    $arr_data['g_sessoin_sid'] = $_G['session']['sid'];
    $arr_data['g_sessoin_lastactivity'] = $_G['session']['lastactivity'];
    $arr_data['g_cookie_saltkey'] = $_G['cookie']['saltkey'];
    $arr_data['g_cookie_lastvisit'] = $_G['cookie']['lastvisit'];
    
    $arr_data['s_server_addr'] = $_SERVER['SERVER_ADDR'];
    $arr_data['s_server_name'] = $_SERVER['SERVER_NAME'];
    $arr_data['s_http_host'] = $_SERVER['HTTP_HOST'];
    $arr_data['s_request_uri'] = $_SERVER['REQUEST_URI'];
    $arr_data['s_php_self'] = $_SERVER['PHP_SELF'];
    $arr_data['s_remote_addr'] = $_SERVER['REMOTE_ADDR'];
    $arr_data['s_http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $arr_data['s_http_referer'] = $_SERVER['HTTP_REFERER'];
    //echo TIMESTAMP;
    //$_G['session']['qudiaoid'] = 119;
    // print_r($arr_data);
    //print_r($_G['session']);
    //print_r($_G['cookie']);
    

    
/*
CREATE TABLE `group_plugin_fansclub_user_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志时间',
  `is_wap` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否wap访问',
  `log_type` char(20) NOT NULL DEFAULT '' COMMENT '日志类型',
  `qudao_from` char(10) NOT NULL DEFAULT '' COMMENT '渠道来源',
  `qudao_fid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '渠道ID',
  `g_uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '_G的uid',
  `g_username` char(25) NOT NULL DEFAULT '',
  `g_clientip` char(25) NOT NULL DEFAULT '',
  `g_basescript` char(35) NOT NULL DEFAULT '',
  `g_mod` char(35) NOT NULL DEFAULT '',
  `g_fid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `g_tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `g_action_action` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `g_action_fid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `g_action_tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `g_sessoin_sid` char(10) NOT NULL DEFAULT '',
  `g_sessoin_lastactivity` int(11) unsigned NOT NULL DEFAULT '0',
  `g_cookie_saltkey` char(10) NOT NULL DEFAULT '',
  `g_cookie_lastvisit` int(11) unsigned NOT NULL DEFAULT '0',
  `s_server_addr` char(100) NOT NULL DEFAULT '' COMMENT '_SERVER的server_addr',
  `s_server_name` char(100) NOT NULL DEFAULT '',
  `s_http_host` char(100) NOT NULL DEFAULT '',
  `s_request_uri` char(255) NOT NULL DEFAULT '',
  `s_php_self` char(100) NOT NULL DEFAULT '',
  `s_remote_addr` char(25) NOT NULL DEFAULT '',
  `s_http_user_agent` char(255) NOT NULL DEFAULT '',
  `s_http_referer` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球迷会用户行为记录表';
*/
C::t('#fansclub#plugin_fansclub_user_log')->insert($arr_data);
  
}

/**
 * touch端上传头像处理
 */
function fansclub_avatar_upload($uid, $data = '', $url = '')
{
    global $_G;
    $arr_return = array('success' => FALSE, 'message' => 'init');
    
    //define('UC_DATADIR', DISCUZ_ROOT.'./uc_server/data/');
    //echo DISCUZ_ROOT;
    //echo "\n";
    include_once(DISCUZ_ROOT.'./source/plugin/fansclub/extend/avatar.php');
    
    if(intval($_G['uid']) > 0 && $uid == intval($_G['uid']))
    {
        $avatar = new Avatar();
        if($url != '')
        {
            $img = $avatar->myGetImageSize($url);
            if(count($img) > 0)
            {
                $data = $img['code'];
            }
            
        }
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $data, $result))
        {
            $bln_upload = $avatar->onuploadavatar_touch($uid, $data);
            if($bln_upload === TRUE)
            {
                $bln_rect = $avatar->onrectavatar_touch($uid);
                if($bln_rect === TRUE)
                {
                    $arr_return['success'] = TRUE;
                    $arr_return['message'] = '成功';
                }
                else
                {
                    $arr_return['message'] = '保存缩放文件失败';
                }
            }
            else
            {
                $arr_return['message'] = '保存临时文件失败';
            }
        }
        else
        {
            //print_r($result);
            
            $arr_return['message'] = '不是一个有效的图片编码';
        }
    }
    else
    {
        $arr_return['message'] = '不能修改别人的头像';
    }
    return $arr_return;
}

/**
 * eg: http://www.usport.com.cn/plugin.php?id=fansclub:api&ac=get_data&method=leaguescore&league_id=1
 * 根据参数，返回api的URL和sign值
 */
function fansclub_get_api_url3($arr_param, $srt_method)
{
    //$data_domain = 'http://www.usport.com.cn'; //内网
    $data_domain = 'http://www.5usport.com/'; //外网
    /*
    $arr_sign = array();
    foreach($arr_param as $key => $value)
    {
        $arr_sign[] = $key.'='.$value;
    }
    $arr_sign[] = 'token='.md5("5usport:+" + $arr_param['time']);
    asort($arr_sign);
    $str_param = implode('&', $arr_sign);
     */
    //echo $data_domain.'/'.$srt_method.'&method='.$_GET['ac'].'&league_id='.$arr_param['league_id'];exit;
    if($_GET['ac'] == 'scorer'){
        $gets ='shooter';
    }
    if($_GET['ac'] == 'standings'){
        $gets ='leaguescore';
    }
    //$gets = $_GET['ac'] == 'scorer' ? 'shooter' : $_GET['ac'] ;
    //echo $gets;exit;
    return $data_domain.'/'.$srt_method.'&method='.$gets.'&league_id='.$arr_param['league_id'];
}

/**
 * 根据参数，返回api的URL或sign值(取爬虫工程师提供接口)
 */
function fansclub_get_api_url2($arr_param, $srt_method)
{
    $data_domain = 'http://120.24.163.54/api/v1';
    $arr_sign = array();
    foreach($arr_param as $key => $value)
    {
        $arr_sign[] = $key.'='.$value;
    }
    $arr_sign[] = 'token='.md5("5usport:+" + $arr_param['time']);
    asort($arr_sign);
    
    $str_param = implode('&', $arr_sign);
    
    return $data_domain.'/'.$srt_method.'?'.$str_param;
    
    /* 
    射手榜
    1、http://120.24.163.54/api/v1/shooter?leagueid=1&type=ios&time=12345&version=12234&token=HHDJHDK&season=2015-2016
    2、参数说明
    leagueid：联赛ID
    type:请求来源类型
    time：服务器时间
    version：数据版本
    token：验证码 md5("5usport:+"+ time)
    season：联赛赛季

    3、返回数据格式
    {
    "dataversion":"11222",
    "data":[{
    "ranking":1,	#排名	
    "player":"马赫雷斯",	#球员	
    "country":"",	#国籍	
    "team":"",	#球队	
    "total":"",	#总进球(点球)	
    "home":"",	#主场	
    "guest":""	#客场
    "penalty":""	#点球	

    },{}]

    }
    */

    /*
    积分榜
    1、http://120.24.163.54/api/v1/leaguescore?leagueid=123&type=ios&time=12345&version=12234&token=HHDJHDK&season=2015-2016
    2、参数说明
    leagueid：联赛ID
    type:请求来源类型
    time：服务器时间
    version：数据版本
    token：验证码 md5("5usport:+"+ time)
    season：联赛赛季

    3、返回数据格式
    {
    "dataversion":"12233",
    "data":[{"ranking":1, 排名
    "team_name":"車路士", #球队
    "total":10,总比赛场数
    "win":6, 赢
    "fail":2, 输
    "draw":2, 平局
    "get":8, 得球
    "lose":0, 失球
    "netbar":8 净得
    "score":9	积分
    "nearly_six_rounds":"" 近六轮

    },{}]
    }
    */
}

/**
 * 根据参数，返回api的URL或sign值
 */
function fansclub_get_api_url($arr_param, $srt_method = 'passport/login', $bln_get_sign = FALSE)
{       
	global $_G;
	$data_domain = $_G['config']['playerdata']['domian'];
	$data_domain = 'http://api.5usport.com';
	if(strpos($_SERVER['HTTP_HOST'], '5usport.com') !== FALSE || $srt_method == 'api/liaoqiu/get_match_integral_shooter' ||
		$srt_method == 'api/liaoqiu/get_match_list2')
	{
		$sn_key = '3#u29As9Fj23';
	}
	else
	{
		$sn_key = 'a@39e8a53Qs';    // 测试key:a@39e8a53Qs 正式key:3#u29As9Fj23
	}
	
	$arr_sign = array();
	foreach($arr_param as $key => $value)
	{
		$arr_sign[] = $key.'='.$value;
	}
	asort($arr_sign);
	
	$str_param = implode('&', $arr_sign);
	$str_sign = $str_param.'||'.$sn_key;
	$my_sign = md5($str_sign);
        //echo $data_domain.'/'.$srt_method.'?'.$str_param.'&sign='.$my_sign;exit;
	return $bln_get_sign ? $my_sign : $data_domain.'/'.$srt_method.'?'.$str_param.'&sign='.$my_sign;
}


// 根据uid查已经浏览的的频道
function fansclub_get_haveview($uid, $num = 10)
{
	global $_G;
	$mem_check = memory('check'); // 先检查缓存是否生效
	
	$arr_return = FALSE;
	if($mem_check != '') // 缓存可以用
	{
		$arr_return = memory('get', 'fansclub_arr_user_haveview_'.$uid);
		
		if($arr_return === FALSE || is_array($arr_return))
		{
			if($_G['forum']['type'] == 'forum' && $_G['forum']['status'] != 3 && $num > 0)
			{
				$_arr_tmp = array();
				$_arr_tmp['fid'] = $_G['forum']['fid'];
				$_arr_tmp['name'] = $_G['forum']['name'];
				$_arr_tmp['icon'] = $_G['setting']['attachurl'].'common/'.$_G['forum']['icon'];
				
				if(strpos($_SERVER['QUERY_STRING'], 'mod=viewthread') !== FALSE) // 如果是帖子详细，icon要修改路径
				{
					$arr_return[$i]['icon'] = $_G['setting']['attachurl'].'common/'.$_G['forum']['icon'];
				}
				
				if(is_array($arr_return))
				{
					$_arr_tmp_new = array();
					$_arr_tmp_new[] = $_arr_tmp;
					
					$_arr_tmp_have = array();
					$_arr_tmp_have[] = $_arr_tmp['fid'];
					
					for($i = 0; $i < count($arr_return); $i++)
					{
						if(count($_arr_tmp_new) >= $num)
							break;
						
						if(!in_array($arr_return[$i]['fid'], $_arr_tmp_have))
						{
							$_arr_tmp_have[] = $arr_return[$i]['fid'];
							$_arr_tmp_new[] = $arr_return[$i];
						}
					}
					
					$arr_return = $_arr_tmp_new;
				}
				else
				{
					$arr_return[] = $_arr_tmp;
				}
				memory('set', 'fansclub_arr_user_haveview_'.$uid, $arr_return, 60*60*24*30); // 缓存一月
				$arr_return = memory('get', 'fansclub_arr_user_haveview_'.$uid);
			}
		}
	}
	
	return $arr_return === FALSE ? array() : $arr_return;
}

// 根据uid查已经加入的球迷会
function fansclub_get_havejoin($uid, $num = 10)
{
	global $_G;
	$arr_return = array();
	if(intval($uid) == 0)
	{
		return $arr_return;
	}
	
	$mem_check = memory('check'); // 先检查缓存是否生效
	
	$arr_return = FALSE;
	if($mem_check != '') // 缓存可以用
	{
		//echo "缓存可以用";
		$arr_return = memory('get', 'fansclub_arr_user_havejoin_'.$uid);
	}
	
	if($arr_return === FALSE)
	{
		$arr_return = array();
		$arr = C::t('#fansclub#plugin_forum_groupuser')->fetch_all_group_by_uid(intval($uid));
		for($i = 0; $i < count($arr); $i++)
		{
			if($i >= $num )
				break;
			
			$field = C::t('forum_forumfield')->fetch($arr[$i]['fid']);
			$forum = C::t('forum_forum')->fetch($arr[$i]['fid']);
			
			// 不显示版块的
			if($forum['status'] != 3)
				continue;
			
			$arr_return[$i]['fid'] = $arr[$i]['fid'];
			if($field['icon']){
				$arr_return[$i]['icon'] = $_G['setting']['attachurl'].'group/'.$field['icon'];
			}
			else
			{
				$arr_return[$i]['icon'] =  $_G['siteurl']."static/image/common/groupicon.gif";
			}
			$arr_return[$i]['name'] = $forum['name'];
		}
		
		if($mem_check != '') // 缓存可以用
		{
			memory('set', 'fansclub_arr_user_havejoin_'.$uid, $arr_return, 60*60*24); // 缓存一天
			$arr_return = memory('get', 'fansclub_arr_user_havejoin_'.$uid);
		}
	}
	
	return $arr_return;
}

// 伪静态分页
// $page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
function fansclub_multi($num, $perpage, $curpage, $mpurl, $add_middle = '', $add_tail = '')
{
	$all_page = ceil($num/$perpage);
	$multipage = '';
	$pre_point = '';
	$next_point = '';
	
	for($i = 1; $i <= $all_page; $i++)
	{
		if($i == 1 && $curpage > 1)
		{
			if($curpage == 2)
			{
				$multipage .= '<a class="prev" href="'.$mpurl.$add_tail.'">上一页</a>';
			}
			else
			{
				$multipage .= '<a class="prev" href="'.$mpurl.$add_middle.($curpage-1).'.html">上一页</a>';
			}
		}
		if($curpage == $i)
		{
			$multipage .= '<strong>'.$i.'</strong>';
		}
		else
		{
			if($curpage - $i > 3 || $i - $curpage > 3 )
			{
				if($i == 1)
				{
					$multipage .= '<a href="'.$mpurl.$add_tail.'">'.$i.'</a>';
				}
				if($curpage - $i > 3 && $pre_point == '')
				{
					$pre_point = '...';
					$multipage .= $pre_point;
				}
				
				if($i - $curpage > 3 && $next_point == '')
				{
					$next_point = '...';
					$multipage .= $next_point;
				}
				
				if($i == $all_page)
				{
					if($i == 1)
					{
						$multipage .= '<a href="'.$mpurl.$add_tail.'">'.$i.'</a>';
					}
					else
					{
						$multipage .= '<a href="'.$mpurl.$add_middle.$i.'.html">'.$i.'</a>';
					}
				}
			}
			else
			{
				if($i == 1)
				{
					$multipage .= '<a href="'.$mpurl.$add_tail.'">'.$i.'</a>';
				}
				else
				{
					$multipage .= '<a href="'.$mpurl.$add_middle.$i.'.html">'.$i.'</a>';
				}
			}
		}
		if($i == $all_page && $i > $curpage)
		{
			$multipage .= '<a class="nxt" href="'.$mpurl.$add_middle.($curpage+1).'.html">下一页</a>';
		}
	}
	
	$multipage = $multipage ? '<div class="pg common_pages">'.$multipage.'</div>' : '';
	if($all_page == 1)
	{
		$multipage = '';
	}
	return $multipage;
}

// 取联赛排名
function fansclub_get_league_rank($fid)
{
	global $_G;
	$data_domain = $_G['config']['playerdata']['domian'];
	$fid = intval($fid);
	$team_id = fansclub_get_team_player_id($fid);
	$url = $data_domain."/v3/v3_api/get_info/getTeamById/SDsFJO4dS3D4dF64SDF46?id=".$team_id;

	$result = curl_access($url);
	$arr_result = json_decode($result, TRUE);

	$url = $data_domain."/v3/v3_api/get_info/getScoreboardByTeamidLeagueid/SDsFJO4dS3D4dF64SDF46?team_id=".$team_id."&league_id=".$arr_result['content']['league_id'];
	$result = curl_access($url);
	$arr_result = json_decode($result, TRUE);
	
	$rank = intval($arr_result['content']['ranking']);
	return $rank;
}

// 球迷会加入按钮html
function fansclub_joinin_button($fid)
{
	global $_G;
	$html_return = '';
	$fid = intval($fid);
	/*
		原版的
	<!-- join 表加入， join_audit 表审核中， join_on 表已加入，join_out表退出-->
	<span class="z ch_joins">
		<!--{if $status != 2 && $status != 3 && $status != 5}-->
			<!--{if helper_access::check_module('group') && $status != 'isgroupuser'}-->
			<a class="join" href="forum.php?mod=group&action=join&fid=$_GET[fid]">&nbsp;</a>
			<!--{/if}-->
			<!--{if CURMODULE == 'group'}--><!--{hook/group_navlink}--><!--{else}--><!--{hook/forumdisplay_navlink}--><!--{/if}-->
		<!--{/if}-->

		<!--{if $action == 'index' && ($status == 2 || $status == 3 || $status == 5)}-->
			<p class="mtm xi1">
			<!--{if $status == 3 || $status == 5}-->
				<!-- {lang group_has_joined} -->
				<a class="join_audit" href="javascript:void(0);">&nbsp;</a>
			<!--{elseif helper_access::check_module('group')}-->
				<a class="join" href="forum.php?mod=group&action=join&fid=$_GET[fid]">&nbsp;</a>
			<!--{/if}-->
			</p>
		<!--{/if}-->
		
		<!--{if $status == 'isgroupuser'}-->
		<a class="join_on" style="float:left;" onclick="showDialog('您确定要退出该球迷会吗？', 'confirm', '', function(){location.href='forum.php?mod=group&action=out&fid=$_GET[fid]'})" href="javascript:;" onmouseover="this.className='join_out'" onmouseout="this.className='join_on'">&nbsp;</a>
		<!--{/if}-->
   </span>
   */
	$html_return = '<span class="z ch_joins">';
	if(intval($_G['uid']) == 0) // 没有登录的
	{
		$html_return .= '<a style="text-decoration:none;" class="join" href="forum.php?mod=group&action=join&fid='.$fid.'">&nbsp;</a>';
	}
	else
	{
		// 用户状态
		$groupuser = C::t('forum_groupuser')->fetch_userinfo($_G['uid'], $fid);
		if(count($groupuser) > 0) // 有记录
		{
			if(intval($groupuser['level']) == 0) // 审核中
			{
				$html_return .= '<a style="text-decoration:none;" class="join_audit" href="javascript:void(0);">&nbsp;</a>';
			}
			else
			{
				$html_return .= "<a style='text-decoration:none;' class='join_on' style='float:left;' onclick='showDialog(\"您确定要退出该球迷会吗？\", \"confirm\", \"\", function(){location.href=\"forum.php?mod=group&action=out&fid=".$fid."\"})' href='javascript:;' onmouseover='this.className=\"join_out\"' onmouseout='this.className=\"join_on\"'>&nbsp;</a>";
			}
		}
		else
		{
			$html_return .= '<a style="text-decoration:none;" class="join" href="forum.php?mod=group&action=join&fid='.$fid.'">&nbsp;</a>';
		}
	
	}
	$html_return .= '</span>';
	return $html_return;
}

// 取群组的认证图标html
function fansclub_get_level_img($fid)
{
	global $_G;
	$html_return = '';
	
	// 普通等级
	$forum_info = C::t('forum_forum')->fetch(intval($fid));
	$level = intval($forum_info['level']);
	$arr_img = array(1 => 'certify_tong.png', 2 => 'certify_tong.png', 3 => 'certify_yin.png', 4 => 'certify_jin.png');
	$html_return .= '<img src="'.$_G['style']['tpldir'].'/common/images/'.$arr_img[($level+1)].'">';
	
	// 特殊等级
	$apply_status = fansclub_get_level_apply_status($fid);
	
	if($apply_status['verify_org'] == 3)
	{
		$html_return .= '<img src="'.$_G['style']['tpldir'].'/common/images/certify_jigou.png">';
	}
	
	if($apply_status['verify_5u'] == 3)
	{
		$html_return .= '<img src="'.$_G['style']['tpldir'].'/common/images/certify_5u.png">';
	}
	return $html_return;
}

// 更新群组的成员数目
function fansclub_update_membernum($fid)
{
	$data = array();
	$count = C::t('forum_groupuser')->fetch_count_by_fid($fid);
	$data = array('membernum' => intval($count));
	C::t('forum_forumfield')->update($fid, $data);
}

// 重新数版块下面的球迷会
function fansclub_forum_recount($fid)
{
	$arr_return = array();
	$fansclubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch_all_fansclub_info_by_relation_fid(intval($fid));
	for($i = 0; $i < count($fansclubinfo); $i++)
	{
		$arr_return[] = $fansclubinfo[$i]['fid'];
	}
	return $arr_return;
}

// 群组页面top，这个可以代替 <!--{hook/group_hd_top}-->
function fansclub_group_hd_top()
{
		global $_G;
		// $gid = isset($_GET['fid']) ? intval($_GET['fid']) : return;
		$gid = intval($_GET['fid']);
		if($gid == 0)
			return '';
		$fansclubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_fid($gid);
		$clubinfo = C::t('forum_forum')->fetch_info_by_fid($fansclubinfo['relation_fid']);
		$branchids = explode(',', $clubinfo['relatedgroup']);		
		$fansnum = $postsnum = 0;
		$clubinfo['branchnum'] = count($branchids);
		foreach ($branchids as $id) {
			$branchinfo = C::t('forum_forum')->fetch_info_by_fid($id);
			$clubinfo['fansnum'] += $branchinfo['membernum'];
			$clubinfo['postsnum'] += $branchinfo['posts'];
		}
		
		// 2015-05-15 zhangjh 增加默认icon
		if($clubinfo['icon'] == '')
			$clubinfo['icon'] = $_G['siteurl'].'template/usportstyle/common/images/default_icon.jpg';
		else
			$clubinfo['icon'] = $_G['siteurl'].'data/attachment/common/'.$clubinfo['icon'];
					
		include template('fansclub:index/head_top');		
		return $return;
}


function fansclub_get_post_pic($tid)
{
	$thumb = '';
	$tid = intval($tid);
	// 查旧文章记录表
	$thumb_info = DB::fetch_first("select thumb from ".DB::table('plugin_fansclub_old_article_log')." where new_tid = ".$tid);
	$thumb = $thumb_info['thumb'];
	
	// 取附件的图片
	if(trim($thumb) == '')
	{
		$arr = DB::fetch_first("select aid from ".DB::table('forum_attachment')." where tid = ".$tid);
		if(trim($arr['aid']) == '')
		{
			// 取内容的第一张图片
			$post = DB::fetch_first("select message from ".DB::table('forum_post')." where tid = ".$tid." AND `first` = 1");

			if($post['message'] != '')
			{
				$regex = "|\[img(.*)\](.*)\[\/img\]|U";
				preg_match_all($regex, $post['message'], $_tmp_arr, PREG_PATTERN_ORDER);
				$thumb = $_tmp_arr[2][0];
			}
		}
		else
		{
			$thumb = getforumimg($arr['aid'], 0, 235, 0, 1);
		}
	}
	return $thumb;
}

// 处理视频列表显示，传入已排序好帖子列表
function fansclub_get_video_list($arr_threadlist)
{
	$arr_return = array();
	
	if(count($arr_threadlist) > 0)
	{
		foreach($arr_threadlist as $key => $val)
		{
			
			$ajaxarr[$key]['tid'] = $val['tid'];
			// $ajaxarr[$key]['subject'] = mb_substr($val['subject'], 0, 16, 'utf-8');
			$ajaxarr[$key]['subject'] = $val['subject'];
			$ajaxarr[$key]['digest'] = $val['digest'];
			//$ajaxarr[$key]['url'] = 'forum.php?mod=viewthread&tid='.$val['tid'];
			$ajaxarr[$key]['url'] = 'thread-'.$val['tid'].'.html';
			$ajaxarr[$key]['views'] = $val['views'];
			$ajaxarr[$key]['replies'] = $val['replies'];
			$ajaxarr[$key]['memberurl'] = 'home.php?mod=follow&uid='.$val['authorid'].'&do=view&from=space';
			$ajaxarr[$key]['authorid'] = $val['authorid'];
			$ajaxarr[$key]['avatar'] = 'uc_server/avatar.php?uid='.$val['authorid'].'&size=small';
			if(intval($val['dbdateline']) > 0)
			{
				$ajaxarr[$key]['dateline'] = date('Y-m-d', $val['dbdateline']);
				$ajaxarr[$key]['datetime'] = date('Y-m-d' ,$val['dbdateline']);
			}
			else
			{
				$ajaxarr[$key]['dateline'] = date('Y-m-d', $val['dateline']);
				$ajaxarr[$key]['datetime'] = date('Y-m-d', $val['dateline']);
			}
			
			$ajaxarr[$key]['author'] = $val['author'];
			
			$ajaxarr[$key]['thumb'] = fansclub_get_post_pic($val['tid']);
			
			// 如果没有旧记录的缩略图
			if(trim($ajaxarr[$key]['thumb']) == '')
			{
				$ajaxarr[$key]['video'] = '';
				
				$video_html = create_video_html($val['tid']);
				if($video_html != '')
				{
					$ajaxarr[$key]['video'] = '<embed width="235" height="200" allownetworking="internal" allowscriptaccess="never" src="'.$video_html.'" quality="high" bgcolor="#ffffff" wmode="transparent" allowfullscreen="true" type="application/x-shockwave-flash">';
				}
			}
		}
	}
	
	$arr_return = $ajaxarr;
	return $arr_return;
}

// 从CI接口取腾讯的team/player id
function fansclub_get_team_player_id($fid)
{
	global $_G;
	$arr_sign = array();
	$sn_key = '3#u29As9Fj23';
	
	if(count($data) > 0)
	{
		foreach($data as $key => $value)
		{
			$arr_sign[] = $key.'='.urldecode($value);
		}
	}
	$arr_sign[] = 'fid='.$fid;
	
	asort($arr_sign);
	$str_param = implode('&', $arr_sign);
	$str_sign = $str_param.'||'.$sn_key;
	
	$sign = md5($str_sign);
	
	// 根据fid查上级各名字
	
	$league_name = $team_name = $player_name = '';
	$fid = intval($fid);
	$arr_forum_info = C::t('forum_forum')->fetch($fid);
	if(count($arr_forum_info) > 0)
	{
		if($status == 3) // 如果是群组的，不处理
		{
			return 0;
		}
		
		if($arr_forum_info['type'] == 'forum') // 这个是球队
		{
			$team_name = $arr_forum_info['name'];
			$arr_forum_info_0 = C::t('forum_forum')->fetch($arr_forum_info['fup']);
			$league_name = $arr_forum_info_0['name'];
		}
		
		if($arr_forum_info['type'] == 'sub') // 这个是球员
		{
			$player_name = $arr_forum_info['name'];
			$arr_forum_info_1 = C::t('forum_forum')->fetch($arr_forum_info['fup']);
			$team_name = $arr_forum_info_1['name'];
			$arr_forum_info_0 = C::t('forum_forum')->fetch($arr_forum_info_1['fup']);
			$league_name = $arr_forum_info_0['name'];
		}
	}
	
	$url = 'http://api.5usport.com/v3/to_v3/phpcms/get_team_player_id?league_name='.urlencode($league_name).'&team_name='.urlencode($team_name).'&player_name='.urlencode($player_name).'&sign='.$sign;
	// $url = 'http://zhangjh.dev.usport.cc/v3/to_v3/phpcms/get_team_player_id?league_name='.$league_name.'&team_name='.$team_name.'&player_name='.$player_name.'&sign='.$sign;
	$result = curl_access($url);
	$arr_result = json_decode($result, TRUE);
	
	if($arr_result['success'] === TRUE)
	{
		return intval($arr_result['id']);
	}
	else
	{
		return 0;
	}
}

// 球迷会显示和排序(返回总数)
function fansclub_list_total($data)
{
	return fansclub_list($data, TRUE);
}

// 球迷会显示和排序
function fansclub_list($data, $show_total = FALSE)
{
	global $_G;
	require_once libfile('function/extends');
	$_rightsdettings = $_G['cache']['plugin']['rights'];
	
	$arr_group_show = array();
	$city_id = intval($data['city_id']);	// 城市ID
	$fid = intval($data['fid']); 			// 版块ID
	$sort = trim($data['sort']);			// contribution 按贡献值, fansnum 粉丝数, level 认证等级
	$limit = intval($data['limit']);		// 显示多少个
	$start = intval($data['start']);		// 从哪个开始显示
	
	$conditions = '1 = 1';
	if($city_id != 0)
	{
		$conditions .= ' AND f.city_id = '.$city_id;
	}
	if($fid != 0)
	{
		$conditions .= ' AND f.relation_fid = '.$fid;
	}
		
	$arr_group_list = C::t('#fansclub#plugin_fansclub_info')->fetch_all_for_search($conditions);
		
	if($show_total)
	{
		$total = count($arr_group_list);
		return $total;
	}
	
	$have_show = 0;
	for($i = 0; $i < count($arr_group_list); $i++)
	{
		//echo $have_show."|".$limit."|".$i."<br>";
		if($limit > 0 && $have_show >= $limit)
			break;
		
		if($i >= $start)
		{
			$arr_group_show[$have_show] = get_fansclub_info($arr_group_list[$i]['fid']);
			$arr_group_show[$have_show]['description_short'] = str_intercept($arr_group_show[$i]['description'], 0, 17); // 简介缩短
			//判断球迷会是否有球迷会特权
			if ($_rightsdettings['open'] == 1 && checkmemberrights(35, $arr_group_list[$i]['fid'], 'clubid')) {
				$arr_group_show[$have_show]['specialclub'] = TRUE;
			} else {
				$arr_group_show[$have_show]['specialclub'] = FALSE;
			}
			$have_show++;
		}
	}
	
	if(count($arr_group_show) > 0)
	{
		if($sort == 'contribution' || $sort == '') // 按贡献值
		{
			$contributions = array();
			foreach ($arr_group_show as $arr_gshow)
			{
				$contributions[] = $arr_gshow['contribution']; // 按贡献值从高到低
			}
			array_multisort($contributions, SORT_DESC, $arr_group_show);
		}
		
		if($sort == 'fansnum')
		{
			$members = array();
			foreach ($arr_group_show as $arr_gshow)
			{
				$members[] = $arr_gshow['members']; // 粉丝数
			}
			array_multisort($members, SORT_DESC, $arr_group_show);
		}
		
		if($sort == 'level')
		{
			$levels = array();
			foreach ($arr_group_show as $arr_gshow)
			{
				$levels[] = $arr_gshow['level']; // 认证等级
			}
			array_multisort($levels, SORT_DESC, $arr_group_show);
		}
	}
	
	return $arr_group_show;
}

// 根据名字取版块
function fansclub_get_forum_info($name, $type, $fup)
{
	return C::t('#fansclub#plugin_forum_forum')->fetch_group_by_name($name, $type, $fup);
}

// 增加后台编辑账号，用来收录旧文章用 2015-05-26
function fansclub_add_ucmember_for_author($author)
{
	$arr_return = array('author' => '5Uadmin', 'authorid' => 1); // 默认作者
	
	$author = trim($author);
	if($author == '')
	{
		return $arr_return;
	}
	
	$newusername = $author;
	$newpassword = $author.'abc123';
	$newemail = strtolower($author.'@example.com');
	
	$id1 = C::t('common_member')->fetch_uid_by_username($newusername);
	//$id2 = C::t('common_member_archive')->fetch_uid_by_username($newusername);
	
	if($id1 + 0 > 0) // 已经有这个账号，不用再插入
	{
		$arr_return = array('author' => $newusername, 'authorid' => $id1 + 0);
		return $arr_return;
	}
	
	loaducenter();
	$uid = uc_user_register(addslashes($newusername), $newpassword, $newemail);
	
	if($uid <= 0) {
		return $arr_return;
	}
	
	$group = C::t('common_usergroup')->fetch(17);
	$newadminid = in_array($group['radminid'], array(1, 2, 3)) ? $group['radminid'] : ($group['type'] == 'special' ? -1 : 0);
	
	$profile = $verifyarr = array();
	loadcache('fields_register');
	$init_arr = explode(',', $_G['setting']['initcredits']);
	$password = md5(random(10));
	C::t('common_member')->insert($uid, $newusername, $password, $newemail, 'Manual Acting', 17, '', $newadminid);
	
	$arr_return = array('author' => $newusername, 'authorid' => $uid);
	return $arr_return;
}

/**
 * 取球迷会等级申请信息
 * @param unknown $fid
 * @return array $arr_return verify_org -1:默认未认证； verify_5u -1：未认证。
 */
function fansclub_get_level_apply_status($fid)
{
	$arr_return = array('verify_org' => '-1', 'verify_5u' => '-1'); // verify_org 机构认证，verify_5u 5U认证
	$fid = intval($fid);
	
	$data = array();
	$all_level_apply_log = C::t('#fansclub#plugin_fansclub_level_apply_log')->fetch_all_by_fid($fid);
	//echo "<pre>"; print_r($all_level_apply_log);
	
	if(count($all_level_apply_log) > 0)
	{
		for($i = 0; $i < count($all_level_apply_log); $i++)
		{
			if($all_level_apply_log[$i]['level_type'] == 0 && $arr_return['verify_org'] == '-1') // 机构认证
			{
				$arr_return['verify_org'] = $all_level_apply_log[$i]['status'];
				continue;
			}
			
			if($all_level_apply_log[$i]['level_type'] == 1 && $arr_return['verify_5u'] == '-1') // 5U认证
			{
				$arr_return['verify_5u'] = $all_level_apply_log[$i]['status'];
				continue;
			}
		}
	}
	
	return $arr_return;
}

// 取球迷会有效时间的设置
function fansclub_get_group_active_time_setting()
{
	$arr_group_time = array();
	$time = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_all_by_name('group_active_time');
	if(count($time) > 0 && $time[0]['policy'] != '')
	{
		$arr_policy = unserialize($time[0]['policy']);
		
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
				$arr_group_time[] = $value;
			}
		}
	}
	return $arr_group_time;
}

// 取球迷会特殊等级的设置
function fansclub_get_group_level_special_setting()
{
	$arr_group_special = array();
	$special = C::t('#fansclub#plugin_fansclub_setting_ex')->fetch_all_by_name('group_level_special');
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
			foreach($_tmp as $key => $value)
			{
				$arr_group_special[] = $value;
			}
		}
		// print_r($arr_group_special); // 话题数 成员数 活跃度 贡献值
	}
	return $arr_group_special;
}

// 迷会等级申请 
function fansclub_level_apply($fid, $uid, $level_type, $file_pic = array())
{
	global $_G;
	$fid = intval($fid);
	$uid = intval($uid);
	$level_type = intval($level_type); // 等级类型0机构认证，1官方认证
	$type = ($level_type == 1) ? '5u' : 'org';
	
	if($fid > 0 && $uid > 0)
	{
		// 1、不是会长权限不可以申请：“您还不是球迷会会长，无法申请哦”
		$arr_userinfo =  C::t('forum_groupuser')->fetch_userinfo($uid, $fid);
		if(count($arr_userinfo) > 0)
		{
			if($arr_userinfo['level'] != 1)
			{
				fansclub_showsuccess('您还不是本球迷会会长，无法申请哦', '', 'notice');
			}
		}
		else
		{
			fansclub_showsuccess('不存在用户信息', '', 'notice');
		}
		
		// 2、单个球星/俱乐部同一时间只有1个官方认证球迷会，要判断这个会长来自哪个球迷会，才能判断是否已经被申请：“已被申请”
		$arr_fansclubifo = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_fid($fid);
		if(count($arr_fansclubifo) > 0)
		{
			$relation_fid = intval($arr_fansclubifo['relation_fid']); // 关联版块的ID
		}
		else
		{
			fansclub_showsuccess('不存在球迷会信息', '', 'notice');
		}
		
		
		// 查询是否已申请过或正在生效
		$apply_status = fansclub_get_level_apply_status($fid);
		$verify_pic = '';
		if($level_type == 1) // 官方认证
		{
			if($apply_status['verify_5u'] == 0)
			{
				fansclub_showsuccess('5U认证审核中', '', 'notice');
			}
			
			if($apply_status['verify_5u'] == 3)
			{
				fansclub_showsuccess('5U认证已成功', '', 'notice');
			}
			
			$can_apply = C::t('#fansclub#plugin_fansclub_level_apply_log')->relation_fid_can_apply($relation_fid, $level_type);
			if($can_apply === TRUE)
			{}
			else
			{
				fansclub_showsuccess('【'.fansclub_get_forum_name($relation_fid).'】已被申请5U认证', '', 'notice');
			}
			
			// 3、话题数不够：“话题数不够，无法申请哦”
			// 4、成员数不够：“成员数不够，无法申请哦”
			// 5、活跃度不够：“活跃度不够，无法申请哦”
			// 6、贡献值不够：“贡献值不够，无法申请哦”
			
			$fansclub_info = get_fansclub_info($fid);
			
			$members = $fansclub_info['members'];				// 成员数
			$posts = $fansclub_info['posts'];					// 话题数
			$contribution = $fansclub_info['contribution'];		// 贡献值
			$activity = $fansclub_info['commoncredits'];		// 活跃度(=公共积分)
			
			// 查询后台设置的数目限制
			$arr_group_special = fansclub_get_group_level_special_setting();
			
			if(count($arr_group_special) == 0)
			{
				fansclub_showsuccess('后台没有设置认证条件', '', 'notice');
			}
			else
			{
				$specil_posts = $specil_members = $specil_activity = $specil_contribution = 999999999;
				foreach($arr_group_special as $key => $value)
				{
					if($value['levelid'] == $level_type)
					{
						$specil_posts = $value['specil_posts'];
						$specil_members = $value['specil_members'];
						$specil_activity = $value['specil_activity'];
						$specil_contribution = $value['specil_contribution'];
					}
				}
				
				if($posts < $specil_posts)
				{
					fansclub_showsuccess('话题数不够，无法申请哦', '', 'notice');
				}
				
				if($members < $specil_members)
				{
					fansclub_showsuccess('成员数不够，无法申请哦', '', 'notice');
				}
				
				if($activity < $specil_activity)
				{
					fansclub_showsuccess('活跃度不够，无法申请哦', '', 'notice');
				}
				
				if($contribution < $specil_contribution)
				{
					fansclub_showsuccess('贡献值不够，无法申请哦', '', 'notice');
				}
			}
			
			// 7、有周期限制，系统应该会私信通知会长：“xxx球迷会”5U认证即将（201X-XX-XX）过期，请再次申请 
			// 这个第二期改版再加吧 by zhangjh 2015-06-03
			
			// 查询过期时间
			$active_month = 0;
			$expired_time = 0;
			$arr_group_time = fansclub_get_group_active_time_setting();
			if(count($arr_group_time) > 0)
			{
				foreach($arr_group_time as $key => $value)
				{
					if($value['levelid'] == $level_type)
					{
						$active_month = $value['specil_time'];
						$expired_time = mktime(date('H', TIMESTAMP), date('i', TIMESTAMP), date('s', TIMESTAMP), date('m', TIMESTAMP) + $active_month, date('d', TIMESTAMP), date('Y', TIMESTAMP));
					}
				}
			}
		}
		elseif($level_type == 0) // 机构认证
		{
			if($apply_status['verify_org'] == 0)
			{
				fansclub_showsuccess('机构认证审核中', '', 'notice');
			}
			
			if($apply_status['verify_org'] == 3)
			{
				fansclub_showsuccess('机构认证已成功', '', 'notice');
			}
			
			if($file_pic['error'] != 0)
			{
				fansclub_showsuccess('没有提交证明图片，提交失败', '', 'notice');
			}
			else
			{
				$upload = new discuz_upload();
				$eid = time();
				$upload->init($file_pic, 'group', $eid, $uid);
				$upload->save();
				$verify_pic = $upload->attach['attachment'];
			}
		}
		
		// 写申请记录
		$data = array(	'level_type' => $level_type,
						'fid' => $fid,
						'relation_fid' => $relation_fid,
						'uid' => $uid,
						'username' => $_G['username'],
						'log_time' => TIMESTAMP,
						'verify_pic' => $verify_pic,
						'ip' => $_G['clientip'],
						'status' => 0,
						'active_month' => $active_month,
						'expired_time' => $expired_time
						);
		$apply_id = C::t('#fansclub#plugin_fansclub_level_apply_log')->insert($data, TRUE);
		fansclub_showsuccess('提交成功，等待审核');
		
	}
	else
	{
		fansclub_showsuccess('请要先登录', 'member.php?mod=logging&action=login', 'notice');
	}
}

// 申请附议
function fansclub_apply_support($apply_id, $show_tip = TRUE)
{
	global $_G;
	
	$apply_id = intval($apply_id);
	$uid = intval($_G['uid']);
	$username = censor(dhtmlspecialchars(trim($_G['username'])));
	
	$apply = C::t('#fansclub#plugin_fansclub_apply_log')->fetch($apply_id);

	if(count($apply) > 0) // 如果有申请记录
	{
		if(($apply['status'] == 0 || $apply['status'] == 1) && ($apply['have_support'] + 0) < ($apply['need_support'] + 0)) // 如果可以附议
		{
			$support = C::t('#fansclub#plugin_fansclub_apply_support')->fetch_all_by_ids($apply_id, $uid);
			if(count($support) > 0) // 已经附议过
			{
				if($show_tip)
					fansclub_showsuccess('你已经附议过了', '', 'notice');
				else
					return '你已经附议过了';
			}
			else
			{
				$bln_last_support = ($apply['have_support'] + 1 == $apply['need_support']) ? TRUE : FALSE; // 是否最后一个附议
				// $bln_first_support = ($apply['have_support'] + 0 == 0) ? TRUE : FALSE; // 是否第一个附议
				// 
				$all_support = C::t('#fansclub#plugin_fansclub_apply_support')->fetch_all_by_ids($apply_id);
				$bln_first_support = (count($all_support) == 0) ? TRUE : FALSE;
				
				// 检查申请人的积分是否足够
				if($apply['credit_num'] > 0 && $bln_last_support) // 如果需要积分并且是最后一个附议
				{
					$apply_member = C::t('common_member_count')->fetch($apply['uid']);
					if($apply_member[$apply['credit_type']] < $apply['credit_num']) // 积分不足
					{
						if($show_tip)
							fansclub_showsuccess('申请人积分不足，附议不成功', '', 'notice');
						else
							return '申请人积分不足，附议不成功';
					}
				}
				
				// 写附议记录
				$data = array('apply_id' => $apply_id, 'uid' => $uid, 'username' => $username, 'support_time' => TIMESTAMP, 'ip' => $_G['clientip']);
				$support_id = C::t('#fansclub#plugin_fansclub_apply_support')->insert($data, TRUE);
				
				if(intval($support_id) > 0)
				{
					$all_support = C::t('#fansclub#plugin_fansclub_apply_support')->fetch_all_by_ids($apply_id);
					$new_status = (count($all_support) >= $apply['need_support']) ? 2 : 1;
					
					// 记录成功后，修改申请记录表
					$data = array('status' => $new_status, 'have_support' => count($all_support));
					C::t('#fansclub#plugin_fansclub_apply_log')->update($apply_id, $data);
					
					// 附议成功，把附议加入该群组
					if(intval($apply['fid']) > 0) // 如果这个存在，
					{
						// $groupuser = ($all_support[0]['uid'] == $apply['uid']) ? 1 : 4; //  成员等级 (0:待审核 1:群主 2:副群主 3:明星成员 4:普通成员)
						// 第一个是群主
						$groupuser = $bln_first_support ? 1 : 4;
						C::t('forum_groupuser')->insert($apply['fid'], $uid, $username, $groupuser, TIMESTAMP);
					}
					
					// if(!$bln_last_support)
					if($bln_first_support) // 不是第一个附议的，直接要返回吧
					{
						
					}else{
						if($show_tip)
							fansclub_showsuccess('附议成功', '', 'right');
						else
							return TRUE;
					}
				}
				else
				{
					if($show_tip)
						fansclub_showsuccess('附议失败', '', 'notice');
					else
						return '附议失败';
				}
				
				//if($bln_last_support) // zhangjh 2015-05-25 从最后一个附议就提交后台审核，改成第一个附议就提交后台审核
				if($bln_first_support)
				{
					// 新建群组分类，层级同版块一致(除第一层)
					$fansclub_fid = fansclub_add_group_type($apply);
					
					// 新建群组
					$newfid = C::t('forum_forum')->insert_group($fansclub_fid, 'sub', $apply['fansclub_name'], '3', -1);
					if($newfid) {
						$fieldarray = array(
							'fid' => $newfid,
							'description' => $apply['fansclub_brief'],
							'jointype' => 2,
							'gviewperm' => 1,
							'dateline' => TIMESTAMP,
							'founderuid' => $apply['uid'],
							'foundername' => $apply['username'],
							'membernum' => count($all_support));
						C::t('forum_forumfield')->insert($fieldarray);
						C::t('forum_forumfield')->update_groupnum($apply['relation_fid'], 1);
						
						// zhangjh 2015-05-25 更新申请记录的newfid
						$data = array('fid' => $newfid);
						C::t('#fansclub#plugin_fansclub_apply_log')->update($apply_id, $data);
						
						// 插入附议人为群组成员（这里只有申请人）
						
						//for($i = 0; $i < count($all_support); $i++)
						//{
							$groupuser = ($all_support[0]['uid'] == $apply['uid']) ? 1 : 4; //  成员等级 (0:待审核 1:群主 2:副群主 3:明星成员 4:普通成员)
							C::t('forum_groupuser')->insert($newfid, $all_support[0]['uid'], $all_support[0]['username'], $groupuser, TIMESTAMP);
						//}
						
						
						//require_once libfile('function/cache');
						//updatecache('grouptype');
					}
					else
					{
						if($show_tip)
							fansclub_showsuccess('新建群组失败', '', 'notice');
						else
							return '新建群组失败';
					}
					
					// 扣申请人积分 
					if($apply['credit_num'] > 0)
					{
						$extcredits = $apply['credit_type'];
						$_tmp = explode('extcredits', $extcredits);
						updatemembercount($apply['uid'], array($_tmp[1] => -$apply['credit_num']), 1, 'BGR', $newfid);
					}
					
					// 自动加入关联版块
					fansclub_add_forum_relation($apply['relation_fid'], $newfid);
					if($show_tip)
						fansclub_showsuccess('附议成功', '', 'right'); //fansclub_showsuccess('附议成功，后台审核中...', '', 'right');
					else
						return TRUE;
				}
			}
		}
		else
		{
			if($show_tip)
				fansclub_showsuccess('附议已结束了', '', 'notice');
			else
				return '附议已结束了';
		}
	}
	else
	{
		if($show_tip)
			fansclub_showsuccess('没有记录', '', 'notice');
		else
			return '没有记录';
	}
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
	$arr_return = array('level' => 0, 'commoncredits' => 0); // 返回等级和积分
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
			C::t('forum_forum')->update_group_level($levelid, $fid); // 更新等级
			$arr_return = array('level' => $levelid, 'commoncredits' => $credits); 
			// 暂时没有UPDATE记录 commoncredits
		}
	}
	return $arr_return;
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
	$data['return_msg'] = $sms->msg_post($mobile, trim($content)); // 发短信
	//$data['return_msg'] = '测试没有发';
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
function fansclub_send_sms($mobile, $is_change = 0, $geetest_challenge = '', $geetest_validate = '', $geetest_seccode = '')
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
	
	// zhangjh 2015-06-18 加验证码
	include_once(DISCUZ_ROOT.'./source/plugin/fansclub/extend/geetestlib.php');
	$geetestlib = new Geetestlib();
	$validate_response = $geetestlib->geetest_validate ( $geetest_challenge, $geetest_validate, $geetest_seccode);
	if(!$validate_response)
	{
		$arr_return['message'] = '验证码不通过';
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
	// zhangjh 2015-06-18 加验证码
	if($verify_sms_code != '' && strtolower($verify_sms_code) != 'null' && $sms_verify === $verify_sms_code)
	{
		$arr_return['success'] = TRUE;
	}
	else
	{
		//$arr_return['message'] = '验证失败，正确的是'.$verify_sms_code;
		$arr_return['message'] = '验证失败';
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
	
	// $url = 'http://zhangjh.dev.usport.cc/api/liaoqiu/get_match_list?'.$str_param.'&sign='.$sign; // 暂时用内网的数据
	$url = 'http://api.5usport.com/api/liaoqiu/get_match_list?'.$str_param.'&sign='.$sign; // 外网的数据
	
	$result = curl_access($url);
	$arr_result = json_decode($result, TRUE);
	
	// zhangjh 2015-06-25 加更新比赛结果
	$url2 = 'http://api.5usport.com/v3/to_v3/phpcms/dealstatus?sign='.$sign;
	$result2 = curl_access($url2);
	
	if($arr_result['state_code'] == '0')
	{
		return $arr_result['game_array'];
	}
	else
	{
		return array();
	}
}

// 2015-08-25 zhangjh 新规则修改
function fansclub_get_live2($data = array())
{

	$data['league_id'] = trim($data['league_id']) == '' ? '0' : trim($data['league_id']);
                   
        //未开始比赛
	$data['match_status'] = 'not_end'; // or 'have_end'
	$url0 = fansclub_get_api_url($data, 'api/liaoqiu/get_match_list2');
	$result0 = curl_access($url0);
	$arr_result['not_end'] = json_decode($result0, TRUE);
        
        //已结束比赛
	$data['match_status'] = 'have_end'; // or 'not_end'
	$url1 = fansclub_get_api_url($data, 'api/liaoqiu/get_match_list2');
	$result1 = curl_access($url1);
	$arr_result['have_end'] = json_decode($result1, TRUE);

        // zhangjh 2015-09-02 更新比赛状态
        $url2 = fansclub_get_api_url(array(), 'v3/to_v3/phpcms/dealstatus');
        $result2 = curl_access($url2);
        
        //射手
        $url3 = fansclub_get_api_url3($data, 'plugin.php?id=fansclub:api&ac=get_data');
        $result3 = curl_access($url3);
        $arr_result['shooter'] = json_decode($result3, TRUE);
        
        //积分
        $url4 = fansclub_get_api_url3($data, 'plugin.php?id=fansclub:api&ac=get_data');
        $result4 = curl_access($url4);
        $arr_result['leaguescore'] = json_decode($result4, TRUE);
	//echo "<pre>";
	//print_r($arr_result);exit;
        
	return $arr_result;
}

function fansclub_get_live($data = array())
{
	if(count($data) == 0)
	{
		$data['all_event'] = '1';
	}
	$data['all_event'] = '1';
	
	$game_count_real = $data['game_count'];
	$data['game_count'] = 100; // 强制取100条
	
	//$data['last_id'] = date('Y-m-d');
	$arr_game = fansclub_get_live_from_ci($data);

	$game_count = $data['game_count']; // 要返回的比赛条数
	$have_count = 0;
	
	//echo "<pre>";
	//print_r($arr_game);

	for($i = 0; $i < count($arr_game); $i++)
	{
		if($have_count >= $game_count)
			break;
		
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
		
		$_arr_games = array();
		for($j = 0; $j < count($arr_game[$i]['games']); $j++)
		{
			if($have_count >= $game_count)
				break;
			$_tmp = explode(' ', $arr_game[$i]['games'][$j]['game_time']);
			$_tmp2 = explode(':', $_tmp[1]);
			$arr_game[$i]['games'][$j]['game_time'] = $_tmp2[0].':'.$_tmp2[1];
			
			$arr_game[$i]['games'][$j]['image1_url'] = $arr_game[$i]['games'][$j]['image1_url'];
			$arr_game[$i]['games'][$j]['image2_url'] = $arr_game[$i]['games'][$j]['image2_url'];
			$_arr_games[] = $arr_game[$i]['games'][$j];
			$have_count++;
		}
		$arr_game[$i]['games'] = $_arr_games;
		
	}
	
	$arr_game2 = array();
	if($game_count > $have_count)
	{
		$data['top_id'] = trim($arr_game[0]['date']) != '' ? $arr_game[0]['date'] : date('Y-m-d'); // 取上面第一天显示的时间
		$data['page_size'] = 10; // 过去10天内
		$arr_game2 = fansclub_get_live_from_ci($data);
		
		$arr_game_tmp = array();
		
		for($i = count($arr_game2) - 1; $i >= 0; $i--)
		{
			if($have_count >= $game_count)
				break;
			if($arr_game2[$i]['date'] == date('Y-m-d', time()-60*60*24))
			{
				$arr_game2[$i]['date_tips'] = '昨天';
			}
			elseif($arr_game2[$i]['date'] == date('Y-m-d', time()-60*60*24*2))
			{
				$arr_game2[$i]['date_tips'] = '前天';
			}
			elseif($arr_game2[$i]['date'] == date('Y-m-d', time()-60*60*24*3))
			{
				$arr_game2[$i]['date_tips'] = '大前天';
			}
			else
			{
				$arr_game2[$i]['date_tips'] = '';
			}
			
			$_arr_games = array();
			for($j = 0; $j < count($arr_game2[$i]['games']); $j++)
			{
				if($have_count >= $game_count)
					break;
				$_tmp = explode(' ', $arr_game2[$i]['games'][$j]['game_time']);
				$_tmp2 = explode(':', $_tmp[1]);
				$arr_game2[$i]['games'][$j]['game_time'] = $_tmp2[0].':'.$_tmp2[1];
				
				$arr_game2[$i]['games'][$j]['image1_url'] = $arr_game2[$i]['games'][$j]['image1_url'];
				$arr_game2[$i]['games'][$j]['image2_url'] = $arr_game2[$i]['games'][$j]['image2_url'];
				$_arr_games[] = $arr_game2[$i]['games'][$j];
				$have_count++;
			}
			$arr_game2[$i]['games'] = $_arr_games;
			if(count($_arr_games) > 0)
			$arr_game_tmp[] = $arr_game2[$i];
		}
		
		$arr_game = array_merge ($arr_game, $arr_game_tmp);
	}
	
	// 2015-06-17 重新排序
	// 按顺序加入
	if(count($arr_game) > 0)
	{
		$times = array();
		foreach ($arr_game as $arr_games)
		{
			$times[] = $arr_games['date'];
		}
		array_multisort($times, SORT_ASC, $arr_game);
	}

	$arr_game_new = array();
	$arr_game_old = array();
	//echo "<pre>";
	for($i = 0; $i < count($arr_game); $i++)
	{
		$today = date('Y-m-d');
		if($arr_game[$i]['date'] >= $today)
		{
			if($arr_game[$i]['date'] == $today)
			{
				// 当天直播中放最上面
				if(count($arr_game[$i]['games']) > 0)
				{
					$game_states = array();
					foreach ($arr_game[$i]['games'] as $arr_games)
					{
						$game_states[] = $arr_games['game_state'];
					}
					array_multisort($game_states, SORT_DESC, $arr_game[$i]['games']);
				}
			}
			
			$arr_game_new[] = $arr_game[$i];
		}
		else
		{
			$arr_game_old[] = $arr_game[$i];
		}
	}

	// old的倒序
	if(count($arr_game_old) > 0)
	{
		$times = array();
		foreach($arr_game_old as $arr_game_olds)
		{
			$times[] = $arr_game_olds['date'];
		}
		array_multisort($times, SORT_DESC, $arr_game_old);
	}
	
	$arr_game = $_tmp_arr_game = array();
	$_tmp_arr_game = array_merge($arr_game_new, $arr_game_old);

	$have_in = 0;
	
	for($i = 0; $i < count($_tmp_arr_game); $i++)
	{
		$arr_game[$i] = $_tmp_arr_game[$i];
		for($j = 0; $j < count($_tmp_arr_game[$i]['games']); $j++)
		{
			if($have_in >= $game_count_real)
			{
				unset($_tmp_arr_game[$i]['games'][$j]);
				continue;
			}
			$have_in++;
		}
		if($have_in >= $game_count_real)
			break;
	}
	//echo "<pre>";
	//print_r($arr_game);
	
	return $arr_game;
}

function get_live_fixture($data = array()) {
	if (count($data) == 0) $data['all_event'] = '1';
	$data['game_count'] = 15;
	$gamesarr = $newgamearr = array();
	$gamesarr = fansclub_get_live($data);
	
	foreach ($gamesarr as $key => $val) {
		foreach ($val['games'] as $_key => $_val) {
			$newgamearr[$_val['game_id']] = $_val;
			$newgamearr[$_val['game_id']]['date'] = date('m/d', strtotime($val['date']));
			$newgamearr[$_val['game_id']]['time'] = date('H:i', strtotime($_val['game_time']));
		}
	}
	return $newgamearr;
}

// 处理视频截图 by zhangjh 2015-09-08 使用api
function fansclub_videoscreenshot3()
{
    include_once(DISCUZ_ROOT.'./source/plugin/fansclub/extend/videoapi.php');
    $url = 'http://player.youku.com/player.php/sid/XMjU0NjI2Njg4/v.swf'; // OK
    $url = 'http://www.tudou.com/a/_93DHcFhgf4/&iid=132528782&rpid=801544571&resourceId=801544571_04_05_99/v.swf'; // OK
    $url = 'http://www.tudou.com/v/siuBXDL5nGs/v.swf'; // OK
    $url = 'http://i7.imgs.letv.com/player/swfPlayer.swf?autoPlay=0&id=23452267';
    $video = new VideoApi();
    $data = $video->parse($url);
    print_r($data);
}

// 处理视频截图 by zhangjh 2015-09-06
function fansclub_videoscreenshot2()
{
    global $_G;
    
    include_once(DISCUZ_ROOT.'./source/plugin/fansclub/extend/videourlparser.php');
    
    //$url = "http://v.youku.com/v_show/id_XMjI4MDM4NDc2.html"; // OK
    //$url = "http://v.youku.com/v_show/id_XMTMyOTEzODkwOA==_ev_1.html?from=y1.3-idx-uhome-1519-20887.205805-205902.1-1";
    //$url = 'http://v.youku.com/v_show/id_XNTc3OTAzNDc2.html?from=y1.6-96.1.1.cc1446ca962411de83b1';
    //$url = 'http://player.youku.com/player.php/sid/XNTc3OTAzNDc2/v.swf'; // 直接分享的
    //$url = 'http://v.youku.com/v_show/id_XMTMyODE2NzE2MA==_ev_5.html?from=y1.3-idx-uhome-1519-20887.205805-205902.8-1';
    //$url = 'http://v.youku.com/v_show/id_XMjE2MTc0ODE2.html?from=y1.3-idx-uhome-1519-20887.212790-212949.2-1.1-8-1-2-0';
    //$url = 'http://v.youku.com/v_show/id_XMTMyOTYzNzgwNA==.html?f=26023883&from=y1.3-idx-uhome-1519-20887.205908-205909-205916.1-3';
    
    $url = 'http://www.tudou.com/programs/view/s4E4Cc0AvMA/'; // OK
    //$url = 'http://www.tudou.com/listplay/Q0MiBE2DPCs/RhTCg-sHcTY.html'; // false
    //$url = 'http://www.tudou.com/albumplay/dIoKgU-j9O4.html'; // false
    //$url = 'http://www.tudou.com/a/G_k9GiGsfzc/&iid=132519374&resourceId=0_04_05_99/v.swf'; // 直接分享的
    $obj = new VideoUrlparser();
    $data = $obj->parse($url);
    print_r($data);
    $arr_img_info = getimagesize($data['img']);
    if(trim(@$data['swf']) != '' && !is_array($arr_img_info)) // 可以分析出swf地址
    {
        $arr_param = array();
        $arr_param['video_url'] = $data['swf'];
        $arr_param['flvcd'] = $data['flvcd'];
        $arr_result = fansclub_videoscreenshot($arr_param);
        print_r($arr_result);
    }
}

// 处理视频截图
function fansclub_videoscreenshot($data = array())
{
	global $_G;
	
	$arr_return = array('success' => FALSE, 'message' => '', 'attachment' => '', 'img_url' => '');
	// return $arr_return; // 功能不完善，暂不处理
	
	$video_url = $data['video_url']; // flash地址
	
	$arr_query = array();
	$arr_query['action'] = 'videoscreenshot';
	$arr_query['pid'] = '5usport'; // 暂时写死
	$arr_query['data'] = urlencode($video_url);
	$arr_query['time'] = time();
    $arr_query['flvcd'] = intval($data['flvcd']);
	$arr_query['sign'] = md5(http_build_query($arr_query).'&key='.'12345678'); // 暂时写死
	
    if(strpos($_SERVER['HTTP_HOST'], '5usport.com') !== FALSE)
	{
		$api_heard = 'http://192.168.2.169'; // 外网
	}
	else
	{
		$api_heard = 'http://192.168.2.169'; // 内网
	}
    
    $api_url = $api_heard.'/api/index.php'.'?'.http_build_query($arr_query);
    
	$result = curl_access($api_url);
    echo $api_url."\n";
    exit;
    //echo $result."\n";
	$arr_result = json_decode($result, TRUE);
    //print_r($arr_result);
	
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
				
				$message = $arr_result['message'];
				$arr_return['success'] = TRUE;
				$arr_return['attachment'] = $attachment;
                $arr_return['img_url'] = $_G['siteurl'].'data/attachment/forum/'.$attachment;
                
                // 写数据库记录
                if(intval($data['pid']) > 0) // 有帖子id才要记录
                {
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
	return str_replace(array('市', '省', '自治区', '特别行政区', '回族', '壮族', '维吾尔', '土家族', '苗族', '自治州'), '', $str);
}

// 取球迷会信息
function get_fansclub_info($fid)
{
	global $_G;
	$arr_return = array();
	$fid = $fid + 0;
	$arr = C::t('#fansclub#plugin_fansclub_info')->fetch($fid); // table_plugin_fansclub_apply_log
	if(count($arr) > 0) // 如果有数据
	{
		$_arr_forum = C::t('forum_forum')->fetch($fid);
		$_arr_forumfield = C::t('forum_forumfield')->fetch($fid);
		$_arr_balance = C::t('#fansclub#plugin_fansclub_balance')->fetch_first($fid);
			
		$arr_return['fid'] = $_arr_forum['fid'];
		$arr_return['relation_fid'] = $arr['relation_fid'];
		$arr_return['fup'] = $_arr_forum['fup'];
		$arr_return['type'] = $_arr_forum['type'];
		$arr_return['name'] = $_arr_forum['name'];
		$arr_return['status'] = $_arr_forum['status'];
		
		if($_arr_forum['status'] <> '3')
		{
			$arr_return['banner'] = $_G['setting']['attachurl'].'common/'.$_arr_forumfield['banner'];
			$arr_return['icon'] = $_G['setting']['attachurl'].'common/'.$_arr_forumfield['icon'];
			
			// 联赛名称
			if($arr_return['type'] == 'forum')
			{
				$_arr_league = C::t('forum_forum')->fetch($arr_return['fup']);
				$arr_return['league_name'] = $_arr_league['name'];
				$arr_return['fup_name'] = $_arr_league['name'];
			}
			elseif($arr_return['type'] == 'sub')
			{
				$_arr_league_culb = C::t('forum_forum')->fetch($arr_return['fup']);
				$_arr_league = C::t('forum_forum')->fetch($_arr_league_culb['fup']);
				$arr_return['league_name'] = $_arr_league['name'];
				$arr_return['fup_name'] = $_arr_league_culb['name']; 
			}
		}
		else // 群组
		{
			$arr_return['banner'] = $_G['setting']['attachurl'].'group/'.$_arr_forumfield['banner'];
			$arr_return['icon'] = $_G['setting']['attachurl'].'group/'.$_arr_forumfield['icon'];
			$_arr_league = C::t('forum_forum')->fetch($arr_return['fup']);
			$arr_return['fup_name'] = $_arr_league['name']; 
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
		
		// zhangjh 2015-05-21 更新积分并升级
		if($_arr_forum['status'] == '3') // 只作用群组
		{
			$data = array();
			$data['contribution'] = $arr_return['contribution'];
			$data['members'] = $arr_return['members'];
			$data['posts'] = $arr_return['posts'];
			$level_credits = fansclub_update_level($fid, $data);
			$arr_return['level'] = $level_credits['level'];
			$arr_return['commoncredits'] = $level_credits['commoncredits'];
			
			// 2015-06-13 更新成员数量
			fansclub_update_membernum($fid); 
			
			// 2015-06-13 取等级图标
			$arr_return['level_img'] = fansclub_get_level_img($fid);
			$arr_return['joinin_button'] = fansclub_joinin_button($fid);
		}
	
		// ===================== 取频道的信息 =====================
		$search_info = C::t('forum_forum')->fetch(intval($arr_return['relation_fid']));
		
// 		$search_info = array_merge($search_info, $search_info2);
// 		var_dump($search_info);die;
		if($search_info['type'] == 'sub') // 如果是球星版块
		{
			$channel_fid = $search_info['fup']; // 频道ID
			
			$search_info2 = C::t('forum_forumfield')->fetch(intval($channel_fid));
			$extra = dunserialize($search_info2['extra']);
			$arr_return['color'] = $extra['namecolor'];
		}
		elseif($search_info['type'] == 'forum') // 如果是俱乐部版块
		{
			$channel_fid = $search_info['fup'];
			
			$search_info2 = C::t('forum_forumfield')->fetch(intval($arr_return['relation_fid']));
			$extra = dunserialize($search_info2['extra']);
			$arr_return['color'] = $extra['namecolor'];
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
		
		// zhangjh 2015-06-18 如果是球星版块，league_name取上上级
		if($_tmp_forum['type'] == 'sub')
		{
			$_tmp_forum_league_real = C::t('forum_forum')->fetch($_tmp_forum_league['fup']);
			$_tmp['league_name'] = $_tmp_forum_league_real['name'];
			$arr_return['league_name'] = $_tmp['league_name'];
		}
		
		
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

/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password)
{
    // 密码长度8~16位，数字、字母、字符至少包含两种
    $strlen = strlen($password);
    if($strlen >= 8 && $strlen <= 16)
    {
        $arr_hit = array(0, 0, 0);
        $arr_str = array('0123456789', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ' ~!@#$%^&*()_+`-={}[]|\?/<>,.;:"'."'");

        for($i = 0; $i< $strlen; $i++)
        {
            $char = chr(ord($password[$i]));

            $r0 = strpos($arr_str[0], $char);
            $r1 = strpos($arr_str[1], $char);
            $r2 = strpos($arr_str[2], $char);

            if($r0 !== FALSE) $arr_hit[0] = 1;
            if($r1 !== FALSE) $arr_hit[1] = 1;
            if($r2 !== FALSE) $arr_hit[2] = 1;
        }

        return ($arr_hit[0] + $arr_hit[1] + $arr_hit[2] >= 2) ? TRUE : FALSE;
    }
    else
    {
        return FALSE;
    }
}

// 取地区的名称
function fansclub_get_district_name($id)
{
	$district = C::t('common_district')->fetch($id);
	return count($district) > 0 ? $district['name'] : '';
}

// 显示省市
function fansclub_get_province_city($only_province = FALSE, $full_name = FALSE)
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
		$arr_return[$i]['name'] = !$full_name ? replace_province_city($province_name) : $province_name;
		$arr_return[$i]['list'] = array();
		if(!$only_province)
		{
			$_tmp_sub = C::t('common_district')->fetch_all_by_upid($province_id, 'displayorder', 'ASC');
			$j = 0;
			foreach($_tmp_sub as $value2)
			{
				$city_id = intval($value2['id']);
				$city_name = $value2['name'];
				$arr_return[$i]['list'][$j]['id'] = $city_id;
				$arr_return[$i]['list'][$j]['name'] = !$full_name ? replace_province_city($city_name) : $city_name;
				$j++;
			}
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
			if( $forum['name'] == '')
					continue;
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
	
	
	$arr_league_icon = array('英超' => 'a', 
							 '西甲' => 'b',
							 'NBA' => 'c',
							 'CBA' => 'd',
							 '意甲' => 'e',
							 '德甲' => 'f',
							 '法甲' => 'g',
							 '中超' => 'h',
							 '亚洲' => 'i',
							 '其他' => 'j',
							 '综合' => 'k'
							 );

	if(count($arr_return) > 0)
	{
		foreach($arr_return as $key => $value)
		{
			$arr_return[$key]['icon'] = $_G['style']['tpldir'].'/common/images/nav'.$arr_league_icon[$value['name']].'.png';
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

function http_header($str_url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $str_url);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);  //表示需要response header
	curl_setopt($ch, CURLOPT_NOBODY, FALSE); //表示需要response body
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 2000);

	$result = curl_exec($ch);

	// if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200')
	return $result;
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
    curl_setopt($obj_ch, CURLOPT_TIMEOUT, 2000);
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

/**
 * 生成设置主球迷会按钮
 * @param int $uid
 * @param int $fid
 * @return string
 */
function set_hostclub_button($uid, $fid) {
	global $_G;
	if ($uid != intval($_G['uid'])) return '';		//登录用户非当前访问页面的用户，不现实设置主球迷会按钮
	$html .= '<span class="z ch_joins">';
	$hostclub = DB::fetch_first("SELECT * FROM ".DB::table('plugin_user_hostclub')." WHERE uid=".$uid);
	if ($hostclub && $hostclub['gid'] == $fid) {	//设置过主球迷会,当前球迷会为主球迷会，显示取消按钮
		$html .= '<a class="join_on" onclick="showDialog(\'你确定要取消主球迷会吗？\', \'confirm\', \'\', function(){location.href=\'plugin.php?id=userspace:sethostclub&action=cancel&uid='.$uid.'\'})" href="javascript:void(0);" onmouseover="this.className=\'join_out\'" onmouseout="this.className=\'join_on\'">&nbsp;</a>';
	} elseif ($hostclub && $hostclub['gid'] != $fid) { 	//设置过主球迷会，当前球迷会非主球迷会，不现实按钮
		return '';
	} else {		//未设置过主球迷会，则全显示设置按钮
		$html .= '<a class="join" href="plugin.php?id=userspace:sethostclub&action=set&fid='.$fid.'">&nbsp;</a>';
	}
	$html .= '</span>';
	return $html;
}
/**
 * 合并所有数组元素值
 * @return multitype:|multitype:mixed
 */
function array_values_merge() {
	$argc = func_num_args();
	if ($argc == 0) {
		return array();
	} elseif ($argc == 1) {
		$arg1 = func_get_arg(0);
		if (is_array($arg1)) {
			return array_values(array_unique($arg1));
		} else {
			return array($arg1);
		}
	} else {
		$arg_list = func_get_args();
		$arr = array();
		for ($i = 0; $i < $argc; $i++) {
			$arr = array_merge($arr, $arg_list[$i]);
		}
		return array_values(array_unique($arr));
	}
}
/**
 * 判断用户是否已加入球迷会
 * @param number $uid
 * @param number $fid
 * @return boolean
 */
function check_joined_fansclub($fid, $uid = '') {
	global $_G;
	$uid = isset($uid) ? $_G['uid'] : $uid;
	$groupuser = C::t('forum_groupuser')->fetch_userinfo($uid, $fid);
	if (count($groupuser) > 0) {//有记录
		return true;
	} else {
		return false;
	}
}
