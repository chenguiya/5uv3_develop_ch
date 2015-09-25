<?php
	
	if(!defined('IN_DISCUZ')) {  
        exit('Access Denied');  
    } 
    
    global $_G;	
	$uid = $_GET['uid'] ? $_GET['uid']: $_G['uid'];
	$space = getuserbyuid($uid, 1);	
	$do = 'club';
	require_once libfile('function/spacecp');
	space_merge($space, 'count');
	space_merge($space, 'field_home');
	space_merge($space, 'field_forum');
	space_merge($space, 'profile');
	space_merge($space, 'status');
	
	//获取用户栏目信息
	$user_profiles = C::t('common_member_profile')->fetch_all($space['uid']);
	$_G['cache']['usergroups'] = C::t('common_usergroup')->fetch_all_by_type();
	
	$sql = "select hf.fid,ff.name,ff.posts from ".DB::table('forum_groupuser')." as hf left join ".DB::table('forum_forum')." as ff on hf.fid=ff.fid where uid ={$uid} limit 0,100";
	include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'; // 公共函数
				$arr=DB::fetch_all($sql);
				if($arr){
					foreach($arr as $k=>$v){
						$sql1 = "select membernum,icon,description from ".DB::table('forum_forumfield')." where fid={$v[fid]}";
						$field=DB::fetch_first($sql1);
						$arr[$k]['membernum']=$field['membernum'];
						$arr[$k]['description']=mb_substr($field['description'],0,50,'utf-8');
						if($field['icon']){
							$icon = "data/attachment/group/".$field['icon'];
						}else{
							$icon = "static/image/common/groupicon.gif";
						}
						$arr[$k]['icon']=$icon;
						$arr[$k]['info']= get_fansclub_info($v['fid']);
						if($_G['uid']==$uid){
							$isjoin = 1;
						}else{
							$sql2 = " select * from ".DB::table('forum_groupuser')." where fid={$v[fid]} and uid={$_G[uid]}";
							$join=DB::fetch_first($sql2);
							if($join){
								$isjoin = 1;
							}else{
								$isjoin = 0;
							}
						}
						$arr[$k]['isjoin']=$isjoin;
						
						// zhangjh 2015-06-13 加图标和是否加入
						$arr[$k]['level_img'] = fansclub_get_level_img($v['fid']);
						$arr[$k]['joinin_button'] = fansclub_joinin_button($v['fid']);
						//add by Daming 2015/06/17	设置主球迷会
// 						$arr[$k]['set_hostclub_button'] = set_hostclub_button($uid, $v['fid']);
					}
				}
// 				var_dump($arr);die;
	$num = count($arr);
	include template('userspace:home_clubs');

?>