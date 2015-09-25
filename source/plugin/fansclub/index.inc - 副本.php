<?php
	if (!defined('IN_DISCUZ')) {
		exit('Access Denied');
	}
	$ac = $_GET['ac'];
	include DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';
	$arr_forum_list = fansclub_get_forum_list();
	$page = $_GET['page'] ? $_GET['page']:1;
	$num = 20;
	$star = ($page-1)*$num;
	//debug($arr_forum_list);
	$sort=$_GET['sort'];
	if($sort=='new' || empty($sort)){
		$orderby='dateline desc';
	}elseif($sort=='hot'){
		$orderby='views desc';
	}elseif($sort=='stick'){
		$orderby='stickreply desc';
	}
	if($ac=='news'){
		$sql = "select * from ".DB::table('forum_thread')." where 1=1 order by {$orderby} limit {$star},{$num}";
		$arr = DB::fetch_all($sql);
		if($_GET['return']=='ajax'){
			include template('fansclub:index/index_news_ajax');
		}else{
			include template('fansclub:index/index_news');
		}
	}elseif($ac=='pics'){
		$sql = "select * from ".DB::table('forum_thread')." where attachment=2 order by {$orderby} limit {$star},{$num}";
		$arr = DB::fetch_all($sql);
		if($_GET['return']=='ajax'){
			foreach($arr as $key=>$val){
				$ajaxarr[$key]['subject']=mb_substr($val['subject'],0,16,'utf-8');
				$ajaxarr[$key]['url']='/forum.php?mod=viewthread&tid='.$val['tid'];
				$ajaxarr[$key]['views']=$val['views'];
				$ajaxarr[$key]['replies']=$val['replies'];
				$ajaxarr[$key]['memberurl']='/home.php?mod=follow&uid='.$val['authorid'].'&do=view&from=space';
				$ajaxarr[$key]['avatar']='uc_server/avatar.php?uid='.$val['authorid'].'&size=small';
				$ajaxarr[$key]['datetime']=date('Y-m-d',$val['dateline']);
				$ajaxarr[$key]['author']=$val['author'];
				$att = DB::fetch_first("select aid from ".DB::table('forum_attachment')." where tid=".$val['tid']);
				$ajaxarr[$key]['thumb']=getforumimg($att['aid']);
			}
			echo json_encode($ajaxarr);exit();
		}else{
			include template('fansclub:index/index_picture');
		}
	}



















?>