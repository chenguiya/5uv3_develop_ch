<?php

	/*
		球迷会的友情链接
	*/
	
	if(!defined('IN_DISCUZ')) {  
        exit('Access Denied');  
	}  
	
	
	class plugin_grouplink_group {	//普通嵌入点
	
			function  group_side_bottom(){
				global $_G;
				$uid = $_G['uid'];
				$fid = $_GET['fid'];	
				$sql = "select * from ".DB::table('forum_groupuser')." where  level=1 and fid=".$fid." and uid=".$uid;		
				$arr = DB::fetch_all($sql);
				$listsql = "select linkgid from ".DB::table('plugin_grouplink')." where status=1 and gid=".$fid;		
				$list = DB::fetch_all($listsql);
				if($list){
					 foreach($list as $k =>$v){
						$sql1 = "select name,posts,fid from ".DB::table('forum_forum')." where fid=".$v['linkgid'];
						$forum = DB::fetch_first($sql1);
						$lists[$k]['name']=$forum['name'];
						$lists[$k]['posts']=$forum['posts'];
						$lists[$k]['fid']=$forum['fid'];
						$sql2 = "select count(fid) as usernum from ".DB::table('forum_groupuser')." where fid=".$v['linkgid']." group by fid";
						$num = DB::fetch_first($sql2);
						$sql3 = "select icon from ".DB::table('forum_forumfield')." where fid=".$v['linkgid'];
						$forumfield = DB::fetch_first($sql3);
						$lists[$k]['icon']=$this->get_groupimg($forumfield['icon'],'icon');
						$lists[$k]['usernum']=$num['usernum'];				
					}
				}
				include template("grouplink:showlink");
				return $html; 
			}
			
			function get_groupimg($imgname, $imgtype = '') {
				global $_G;
				$imgpath = $_G['setting']['attachurl'].'group/'.$imgname;
				if($imgname) {
					return $imgpath;
				} else {
					if($imgtype == 'icon') {
						return 'static/image/common/groupicon.gif';
					} else {
						return '';
					}
				}
			}
		function group_nav_extra(){
			return '<li><a href="plugin.php?id=grouplink:grouplink&fun=lists&fid='.$_GET['fid'].'">友情球迷会</a></li>';
		}
	}
	
	
?>