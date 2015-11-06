<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$ac = $_GET['ac'] ? $_GET['ac'] : 'index';
if($_GET['ac']=='scorer'){
    $ac = 'shooter';
}
if($_GET['ac']=='standings'){
    $ac = 'leaguescore';
}
switch ($_G['gp_league_id']) {
    case 'pl':
        $_G['gp_league_id'] = 1;
        break;
    case 'laliga':
        $_G['gp_league_id'] = 15;
        break;
    case 'csl':
        $_G['gp_league_id'] = 13;
        break;
    case 'afccl':
        $_G['gp_league_id'] = 41;
        break;
    case 'seriea':
        $_G['gp_league_id'] = 21;
        break;
    case 'bundesliga':
        $_G['gp_league_id'] = 2;
        break;
    case 'ligue':
        $_G['gp_league_id'] = 3;
        break;
    case 'nba':
        $_G['gp_league_id'] = 7;
        break;
    case 'cba':
        $_G['gp_league_id'] = 8;
        break;
    case 'others':
        $_G['gp_league_id'] = 100;
        break;
}
$cvar = $_G['cache']['plugin']['fansclub']; 
$config = include DISCUZ_ROOT.'./source/plugin/fansclub/data/config.php';
$config = array_merge($cvar, $config); // 合并配置



include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'); // 公共函数
$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
if($fid > 0)
{
	require_once libfile('function/forum');
	loadforum($fid); // 装载版块信息
	
	$_G['forum'] = array_merge($_G['forum'], get_fansclub_info($_G['fid']));
	require_once libfile('function/group');
	$nav = get_groupnav($_G['forum']);
	$groupnav = $nav['nav']; // 面包屑导航
	
	// 用户状态
	$groupuser = C::t('forum_groupuser')->fetch_userinfo($_G['uid'], $fid);
	$action = getgpc('action') && in_array($_GET['action'], $actionarray) ? $_GET['action'] : 'index';
	$status = groupperm($_G['forum'], $_G['uid'], $action, $groupuser);
	
	// 2015-06-24 zhangjh
	$forum_is_open = $_G['forum']['status'] != 0 ? TRUE : FALSE;
	
}

if(defined('IN_MOBILE'))	{
        foreach (C::t('forum_forum')->my_fetch_all_name_by_fid($fid) as $key => $val){           
            foreach (C::t('forum_forum')->my_fetch_all_name_by_fid($val['fup']) as $k=>$v){
                $fup = C::t('forum_forum')->my_fetch_forum_fup_by_name($v['name']);
            }   
        }
        //var_dump($test);exit;
        switch ($fup) {
            case '1':
                $_G['gp_league_id'] = 1;
                break;
            case '54':
                $_G['gp_league_id'] = 15;
                break;
            case '185':
                $_G['gp_league_id'] = 13;
                break;
            case '81':
                $_G['gp_league_id'] = 21;
                break;
            case '64':
                $_G['gp_league_id'] = 2;
                break;
            case '82':
                $_G['gp_league_id'] = 3;
                break;
        }
        //echo $_G['gp_league_id'];exit;
 }

$arr = array('rights', 'index', 'apply','shooter','leaguescore', 'apply_support', 'ajax', 'friendship', 'lists', 'ajax_thread', 'home', 'event', 'live', 'live2', 'mobile_register', 'verify', '404', 'about','index2'); // 只允许的action
if(!in_array($ac, $arr)) showmessage('undefined_action');

$file = DISCUZ_ROOT.'./source/plugin/fansclub/module/index/'.$ac.'.inc.php'; // 检查模块是否存在
// if(!file_exists($file) || !$config['fansclub_enable']) showmessage('undefined_action');
include $file;

// if($_G['style']['styleid'] == 2) // 如果后台选择了第二个模版

// zhangjh 2015-06-29 加 navtitle metakeywords 和 metadescription
$nobbname = TRUE;
$navtitle = $_G['setting']['bbname'].'_承载自由体育文化，传递不一样的体育声音';
$metakeywords = '足球明星,篮球明星,体育明星,球队';
$metadescription = $_G['setting']['bbname'].'是以体育新闻、NBA、CBA、英超、西甲、中超、中国足球等的垂直体育社群门户，体育明星在线互动，独特的体育观点与草根体育专栏，尽在你我的体育社区。';
if($ac == 'index' || $ac == 'index2') // 广场
{
	$navtitle = '5U球迷会_因球星而聚，为球迷而生_'.$_G['setting']['bbname'];
	$metakeywords = '篮球球迷会,足球球迷会';
	$metadescription = $_G['setting']['bbname'].'球迷会以共同的体育爱好为桥梁，通过球迷会为枢纽，让更多爱好足球、篮球的球迷在球迷会表达出自己的想法，发出不一样的属于球迷自己的体育声音。';
}
elseif($ac == 'live' || $ac == 'live2')
{
	$navtitle = '足球赛事，赛程视频比分直播_NBA直播_'.$_G['setting']['bbname'];
	$metakeywords = '赛程,比分,足球赛事直播,NBA直播视频,篮球赛事直播';
	$metadescription = '最全，最新的各种足球赛事、篮球赛事直播信息。';

	$arr_league_name = trim($config['arr_league'][intval($_G['gp_league_id'])]);
	if($arr_league_name != '')
	{
		$navtitle = $arr_league_name.'视频比分直播_'.$_G['setting']['bbname'];
		$metakeywords = $arr_league_name.'视频比分直播';
		$metadescription = $arr_league_name.'视频比分直播';
	}
}
elseif($ac == 'shooter')
{
                        $arr_league_name = trim($config['arr_league'][intval($_G['gp_league_id'])]);
                         if($arr_league_name != '')
                         {
                                     $navtitle = $arr_league_name.'射手榜'.$_G['setting']['bbname'];
                                     $metakeywords = $arr_league_name.',射手榜';
                                     $metadescription = $arr_league_name.'射手榜，最新联赛射手榜总排名。';
                         }else{
                                    $navtitle = '射手榜_足球联赛射手榜_'.$_G['setting']['bbname'];
                                    $metakeywords = '射手榜,足球联赛';
                                    $metadescription = '五大足球联赛射手榜，顶级甲级足球联赛射手榜。';		
                         }
}
elseif($ac == 'leaguescore')
{
                        $arr_league_name = trim($config['arr_league'][intval($_G['gp_league_id'])]);
                        if($arr_league_name != '')
                        {
                                    $navtitle = $arr_league_name.'积分榜_'.$_G['setting']['bbname'];
                                    $metakeywords = $arr_league_name.',积分榜';
                                    $metadescription = $arr_league_name.'积分榜，最新联赛积分榜排名。';

                        }else{
                                    $navtitle = '积分榜_足球联赛积分榜_'.$_G['setting']['bbname'];
                                    $metakeywords = '积分榜,足球联赛';
                                    $metadescription = '五大足球联赛积分榜，顶级甲级足球联赛积分榜。';
                        }	
}
elseif($ac == '404')
{
	$navtitle = '您所访问的页面不存在或已删除_'.$_G['setting']['bbname'];
	$metakeywords = '页面不存在';
	$metadescription = '您所访问的页面不存在或已删除';
}

if($_G['style']['tplname'] == '5U体育模版套系')
{
	if($ac == 'lists')
	{
		if(trim($_GET['type']) == 'pic')
		{
			$navtitle = $_G['forum']['name'].'图片_'.$_G['setting']['bbname'].'球迷会';
			$metakeywords = $_G['forum']['name'].'图片';
			$metadescription = '汇聚了'.$_G['forum']['name'].'每一个球迷所发的图片都将在这里呈现，通过这里就可以一览'.$_G['forum']['name'].'各种信息资源。';
		}
		elseif(trim($_GET['type']) == 'video')
		{
			$navtitle = $_G['forum']['name'].'视频_'.$_G['setting']['bbname'].'球迷会';
			$metakeywords = $_G['forum']['name'].'视频';
			$metadescription = '汇聚了'.$_G['forum']['name'].'每一个球迷所发的视频都将在这里呈现，通过这里就可以一览'.$_G['forum']['name'].'各种信息资源。';
		}
                		elseif(trim($_GET['type']) == 'activity')
		{
			$navtitle = $_G['forum']['name'].'球迷活动_'.$_G['forum']['name'].'活动交流平台_'.$_G['setting']['bbname'].'球迷会';
			$metakeywords = $_G['forum']['name'].'，球迷，活动';
			$metadescription = $_G['forum']['name'].'球迷活动交流平台，为'.$_G['forum']['name'].'球迷争取福利的平台，提供各种线上线下有趣的球迷会活动，大家赶快来参加吧。';
		}
	}
	elseif($ac == 'event')
	{                                                                         
		$navtitle = $_G['forum']['name'].'大事记_'.$_G['setting']['bbname'].'球迷会';
		$metakeywords = $_G['forum']['name'].'大事记';
		$metadescription = '记录了'.$_G['forum']['name'].'自创建以来的历程，球迷通过时间轴就能了解'.$_G['forum']['name'].'的发展情况。';
	}
	
	//if($ac  != 'about') // about 不用这个header
                       include template('common/header');

	if($ac == 'apply') // 球迷会申请
	{
		include template('extend/desktop/create_club');
	}
	elseif($ac == 'apply_support') // 球迷会申请附议
	{
		include template('extend/desktop/create_state');
	}
	elseif($ac == 'event') // 大事记
	{		
                                                if(defined('IN_MOBILE')){
                                                        include template('touch/group/group_event');
                                                }elseif($_GET['type'] == 'edit' ){
                                                            include template('group/group_event');
                                                }elseif($_GET['type'] == 'read'){
                                                    dheader("Location:http://".$_SERVER['HTTP_HOST']."/fans/event/".$fid);
                                                 }else{
                                                        $html_group_hd_top = fansclub_group_hd_top();
                                                        include template('group/group_event');
                                                }		
	}
	elseif ($ac == 'lists')
	{
		$html_group_hd_top = fansclub_group_hd_top();
                                            if($_GET['type'] == 'activity'){
                                                include template('group/group_activity');
                                            }else{
                                                include template('fansclub:index/lists_' . $_GET['type']);
                                            }
		
	}
	elseif ($ac == 'home') {
		include template('fansclub:index/home');
	}
	elseif($ac == 'index')
	{
		// include template('extend/desktop/club_square');
		// 2015-08-04 zhangjh 球迷联盟社区1.1
		include template('extend/desktop/club_page');
	}
    elseif($ac == 'index2')
    {
        include template('extend/desktop/club_square');
    }
       elseif($ac == 'live')
	{	if(defined('IN_MOBILE'))	{
                                                    include template('touch/live/live');
                                             }else{
                                                 include template('live/live');
                                             }
		
	}
        elseif($ac == 'shooter')
	{	if(defined('IN_MOBILE'))	{
                                                    include template('touch/live/live');
                                             }else{
                                                     include template('live/live');
                                             }
	}
        elseif($ac == 'leaguescore')
	{	if(defined('IN_MOBILE'))	{
                                                    include template('touch/live/live');
                                             }else{
                                                    include template('live/live');
                                             }
		
	}
	elseif($ac == 'live2')
	{

		include template('live/live');
	}
    elseif($ac == 'rights')
    {
        $type = $_GET['type'];
        if(defined('IN_MOBILE'))
        {
            include template('touch/extend/rights/rights_'.$type);
        }
        else
        {
            die('notthing');
        }
    }
    elseif($ac == 'mobile_register')
    {
        include template('extend/desktop/mobile_register');
    }
	elseif($ac == 'topside') // 还不知道这个要不要做
	{
		$type = $_GET['type'] ? $_GET['type'] : 'index';
		if($type == 'news')
		{
			include template('extend/desktop/index_news');
		}
		elseif($type == 'picture')
		{
			include template('extend/desktop/index_picture');
		}
		elseif($type == 'video')
		{
			include template('extend/desktop/index_video');
		}
		else
		{
			include template('extend/desktop/club_square');
		}
	}
	elseif($ac == 'verify') // 球迷会申请
	{
		$type = $_GET['type'] ? $_GET['type'] : 'boots';
		if($type == '5u') // 5u认证
		{
			include template('extend/desktop/verify_5u');
		}
		elseif($type == 'org') // 机构认证
		{
			include template('extend/desktop/verify_org');
		}
		elseif($type == 'boots') // 金银铜靴认证
		{
			include template('extend/desktop/verify_boots');
		}
	}
	elseif($ac == '404')
	{
		include template('extend/desktop/404');
	}
	elseif($ac == 'about') // 网站关于
	{
		$type = $_GET['type'] ? $_GET['type'] : 'aboutus'; 
		include template('extend/about/'.$type);
	}
	
	include template('common/footer');
}
else
{
	include template('fansclub:index/'.$ac);
}
