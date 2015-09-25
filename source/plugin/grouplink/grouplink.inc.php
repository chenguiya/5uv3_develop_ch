<?php


	/*
		球迷会的友情链接
	*/
	
	if(!defined('IN_DISCUZ')) {  
        exit('Access Denied');  
	}  
	if(!$_G['uid']){ //判断是否登录
		showmessage('请先登录!');
	}else{
		$uid = $_G['uid'];	
	}
	global $_G;
	$_G['fid']=$fid = htmlspecialchars($_GET['fid']);
	$fun =  $_GET['fun'];
	if($fun=="add"){   //申请
		if(submitcheck('subaddlink') || $_POST['autosub']){
			$info =array();
			$info['gid'] = htmlspecialchars($_POST['mygid']);//用户自己球迷会的ID
			$info['linkgid'] =  $fid;   //要成为友情球迷会的ID
			if($info['gid']==$info['linkid']){
				showmessage('不能添加自己的球迷会为友情球迷会!','forum.php?mod=group&fid='.$info['linkgid']);
			}
			$sql = "select * from ".DB::table('plugin_grouplink')." where gid=".$info['gid']." and linkgid=".$info['linkgid'] ;
			$arr = DB::fetch_all($sql);
			if(!empty($arr)){
				showmessage('已申请过!','forum.php?mod=group&fid='.$info['linkgid']);
			}
			$info['createtime'] = time(); 
			DB::insert('plugin_grouplink',$info);
			showmessage('申请成功，待对方审核！','forum.php?mod=group&fid='.$info['linkgid'],'',array('alert'=>'right'));
		}else{
			$formhash = FORMHASH;			
			$sql = "select fg.fid,ff.name from ".DB::table('forum_groupuser')." as fg left join ".DB::table('forum_forum')." as ff on fg.fid=ff.fid where  fg.level=1 and fg.uid=".$uid; 
			$arr = DB::fetch_all($sql);
			if(count($arr)==1){
				$flag = 1;
			}
			if(empty($arr)){
				showmessage('没有此权限!');
			}
			include template("grouplink:addlink");
		}
	}elseif($fun=="pass"){  //审核通过
		$linkgid = htmlspecialchars($_GET['linkgid']);//对方的球迷会id
		$checksql = "select * from ".DB::table('forum_groupuser')." where level=1 and uid=".$uid." and fid=".$fid; 
		$check = DB::fetch_all($checksql);
		if(empty($check)){   //是否为这个球迷会的会长
			showmessage('没有此权限!');
		}
		$sql="select * from ".DB::table("plugin_grouplink")." where linkgid=".$fid." and status=0 and gid=".$linkgid;//判断是否有这条申请记录
		$arr = DB::fetch_all($sql);
		if(empty($arr)){
			showmessage('没有这条申请记录!');
		}
		DB::update('plugin_grouplink',array('status'=>1),array('gid'=>$linkgid,'linkgid'=>$fid));
		DB::insert('plugin_grouplink',array('gid'=>$fid,'linkgid'=>$linkgid,'createtime'=>time(),'status'=>1));
		showmessage("已通过！",'','',array('alert'=>'right'));
	}elseif($fun=="reject"){ //拒绝
		$linkgid = htmlspecialchars($_GET['linkgid']);//对方的球迷会id
		$checksql = "select * from ".DB::table('forum_groupuser')." where level=1 and uid=".$uid." and fid=".$fid; 
		$check = DB::fetch_all($checksql);
		if(empty($check)){   //是否为这个球迷会的会长
			showmessage('没有此权限!');
		}
		DB::update('plugin_grouplink',array('status'=>2),array('gid'=>$linkgid,'linkgid'=>$fid));
		showmessage("已拒绝！",'','',array('alert'=>'right'));
	}elseif($fun =="del"){ //解除
		$linkgid = htmlspecialchars($_GET['linkgid']);//对方的球迷会id
		$checksql = "select * from ".DB::table('forum_groupuser')." where level=1 and uid=".$uid." and fid=".$fid; 
		$check = DB::fetch_all($checksql);
		if(empty($check)){   //是否为这个球迷会的会长
			showmessage('没有此权限!');
		}
		DB::delete('plugin_grouplink',array('linkgid'=>$fid,'gid'=>$linkgid,'status'=>1),1);
		DB::delete('plugin_grouplink',array('linkgid'=>$linkgid,'gid'=>$fid,'status'=>1),1);
		showmessage("已删除！",'','',array('alert'=>'right'));
	}elseif($fun=='lists'){
		if(checklevel($uid,$fid) || $_G['adminid'] == 1) {
			$sql="select pg.gid,ff.name from ".DB::table('plugin_grouplink')." as pg left join ".DB::table('forum_forum')." as ff on pg.gid=ff.fid where  pg.linkgid=".$_G['fid']." and pg.status=0";
			$applink = DB::fetch_all($sql);
			$sql1 = "select pg.linkgid,ff.name from ".DB::table('plugin_grouplink')." as pg left join ".DB::table('forum_forum')." as ff on pg.linkgid=ff.fid where  pg.gid=".$_G['fid']." and pg.status=1";
			$friendlink = DB::fetch_all($sql1);
			include template("grouplink:listslink");
		}else{
			showmessage('您不是群主！');
		}
	}else{
		showmessage('无操作!');
	}
	
	function checklevel($uid,$fid){
		$checksql = "select * from ".DB::table('forum_groupuser')." where level=1 and uid=".$uid." and fid=".$fid; 
		$check = DB::fetch_all($checksql);
		if($check){
			return 1;
		}else{
			return 0;
		}
	}
	
?>