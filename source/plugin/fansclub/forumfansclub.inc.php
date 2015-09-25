<?php
	if(!defined('IN_DISCUZ')) exit('Access Denied');
	
	$province=DB::fetch_all("select * from ".DB::table('common_district')." where level=1");
	require './source/function/function_forum.php';
	include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';
	loadforum();
	$fid = $_G['forum']['fid'];
    
    $arr_forum_add = DB::fetch_all("select * from ".DB::table('forum_forumfield')." where fid = ".intval($_G['fid']));
    $_G['forum']['banner'] = $_G['setting']['attachurl'].'common/'.$arr_forum_add[0]['banner'];
    
	// $_G['forum'] = array_merge($_G['forum'], get_fansclub_info($_G['fid']));
	$page=isset($_GET['page']) ? $_GET['page']:1; //当前页数
	$ac=$_GET['ac'];
	if($ac=='ajax'){
		$city=DB::fetch_all("select * from ".DB::table('common_district')." where upid=".$_GET['proid']);
		echo json_encode($city);exit();
	}
	$data = array();
	if($ac=='contribute'){
		$sort='contribution';
	}elseif($ac=='membernum'){
		$sort='fansnum';
	}elseif($ac=='level'){
		$sort='level ';
	}elseif($ac=='address'){
		$data['city_id'] = $_GET['cityid'];
	}else{
		$sort='contribution';
	}
	 // 城市ID
	$data['fid'] = $fid; // 版块ID
	$data['sort'] = $sort; // contribution 按贡献值(默认), fansnum 粉丝数, level 认证等级
	$data['limit'] = 9; // 显示多少个
	$starnum=($page-1)*$data['limit'];
	$data['start'] = $starnum; // 从哪个开始显示
	$arr= fansclub_list($data);
	$total= fansclub_list_total($data);
	if($ac=='address'){
		$mpurl="plugin.php?id=fansclub:forumfansclub&fid={$fid}&ac={$ac}&cityid={$_GET['cityid']}";
	}else{
		$mpurl="plugin.php?id=fansclub:forumfansclub&fid={$fid}&ac={$ac}";
	}
	//$str_page = multi($total, $data['limit'], $page, $mpurl, $maxpages = 0, $page = 10);
	
	// zhangjh 2015-06-25 伪静态分页 fansclub/25/2.html
	$str_page = fansclub_multi($total, $data['limit'], $page, 'fansclub/'.$fid.'/');
	
	// zhangjh 2015-06-24
	$forum_is_open = $_G['forum']['status'] != 0 ? TRUE : FALSE;
	
	$nobbname = TRUE;
	$navtitle = $_G['forum']['name'].'球迷建立的球迷会_'.$_G['setting']['bbname'];
	$metakeywords = $_G['forum']['name'].'球迷会';
	$metadescription = '5u体育'.$_G['forum']['name'].'球迷会汇集了所有热爱'.$_G['forum']['name'].'的球迷而建立的球迷会。';
	
	include template('fansclub:forum/channel_clubs');
?>