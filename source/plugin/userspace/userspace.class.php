<?php  
    if(!defined('IN_DISCUZ')) {  
        exit('Access Denied');  
    } 
	include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'; // 公共函数
	class plugin_userspace{   //全局潜入点
	
	}
	class plugin_userspace_home  extends plugin_userspace{   //全局潜入点
		public  function  follow_listgroup(){
				global $_G;
				$uid = $_GET['uid'] ? $_GET['uid'] : $_G['uid'];
				$sql = "select hf.fid,ff.name,ff.posts from ".DB::table('forum_groupuser')." as hf left join ".DB::table('forum_forum')." as ff on hf.fid=ff.fid where uid ={$uid} limit 0,4";
				$arr=DB::fetch_all($sql);
				if($arr){
					foreach($arr as $k=>$v){
						$sql1 = "select membernum,icon from ".DB::table('forum_forumfield')." where fid={$v[fid]}";
						$field=DB::fetch_first($sql1);
						$arr[$k]['membernum']=$field['membernum'];
						if($field['icon']){
							$icon = "data/attachment/group/".$field['icon'];
						}else{
							$icon = "static/image/common/groupicon.gif";
						}
						$arr[$k]['icon']=$icon;
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
					}
				}
				include template('userspace:listgroup');
				return $html;
			}
			
		public  function  follow_listfriend(){
				global $_G;
				if($_GET['uid']==$_G['uid'] || empty($_GET['uid'])){
				
					$uid = $_GET['uid'] ? $_GET['uid'] : $_G['uid'];
					$sql = "select * from ".DB::table('home_friend')." where uid={$uid} order by dateline desc limit 0,9";
					$arr=DB::fetch_all($sql);
					foreach($arr as $k=>$v){
						$arr[$k]['avatar']=avatar($v['fuid'],'small');
					}
					include template('userspace:listfriend');
					return $html;
				}else{
					return '';
				}
			}
	}
?>