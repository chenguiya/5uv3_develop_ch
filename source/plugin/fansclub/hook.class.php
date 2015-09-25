<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

// 这个是zhangjh测试用文件
class plugin_fansclub
{
	function deletethread($p)
	{
		/*
		// 删除帖子，可以给作者发站内信
		global $_G;
		$id = $_G['deletethreadtids'][0];
		$author = DB::result_first('SELECT author FROM %t WHERE tid = %d',
		array('forum_thread', $id));
		debug($author);
		debug($_G['deletethreadtids']);
		*/
		if($p['step'] == 'check')
		{
			// code todo
		} elseif($p['step'] == 'delete')
		{
			// code todo
		}
	}
	
	function common()
	{
		// 设置全局变量
		// global $_G;
		// $_G['ppc'] = 'test_common';
	}
	
	function global_cpnav_extra1()
	{
		// 嵌入点显示模版内容
		// include template('fans_club:group/apply_tips');
		// $str_return = '<a href="plugin.php?id=fans_club:apply" target="_self">球迷会申请</a>';
		// $str_return .= $tips; // tips是模板里的block
		// return $str_return;
	}
}

// 会员
class plugin_fansclub_member extends plugin_fansclub
{
	// 会员注册时，马上跳转到其他页面
	function register_top()
	{
		// header('Location:http://zc.qq.com/chs/index.html');
		// exit;
	}
}

// 群组
class plugin_fansclub_group extends plugin_fansclub
{
	function group_nav_extra()
	{
		global $_G;
		$mod = $_G['gp_mod'];
		$action = $_G['gp_action'];
		$op = $_G['gp_op'];
		$fid = intval($_G['gp_fid']);
		
		//return $mod."|".$action."|".$op."|".$fid;
		if($mod == 'group' && $action == 'manage' && $op == 'manageuser' && $fid > 0) // 群组 -> 管理群组 -> 成员管理
		{
			// print_r($_POST);
			// http://192.168.2.169/discuz/forum.php?mod=group&action=manage&op=manageuser&fid=66&manageuser=true
		}
		// return '<li><a href="plugin.php?id=fansclub&action=friendship&fid='.intval($_GET['fid']).'#friendship">友情链接</a></li>';
	}
	
	// 由 showmessage() 函数触发
	function index_test_message($p)
	{
		// debug($p);
		// 可以实现自己定制的页面
		// echo $p['param'][0];
		// exit;
	}
	
	function group_bottom()
	{
		global $_G;//全部变量
		$mod = $_G['gp_mod'];
		$action = $_G['gp_action'];
		$siteurl = $_G['siteurl'];
		$fupid = $_G['gp_fupid'] + 0;		// 48 群组分类 如 英超
		$groupid = $_G['gp_groupid'] + 0;	// 49 群组子分类 如 曼联
		
		//echo $fupid;
		//echo '|'.$groupid;
		// 如果是 forum.php?mod=group&action=create (创建群组)，则跳转自定义的创建页面
		// 创建页面使用自己定义的
		if($mod == 'group' && $action == 'create') // 用户点击后跳转
		{
		//	header("Location:".$siteurl.'plugin.php?id=fans_club:apply&fupid='.$fupid.'&groupid='.$groupid);
		//	exit;
		}
	}
	
	// 快速回帖
	function viewthread_fastpost_content_output()
	{
		//return plugin_fans_club_forum::viewthread_fastpost_content_output();
	}
	
}

// 论坛
class plugin_fansclub_forum extends plugin_fansclub
{
	// 由 showmessage() 函数触发
	function index_test_message()
	{
		// echo '你好';
		// exit;
	}
	
	// 帖子列表
	function forumdisplay_thread_subject()
	{
		// return array(
		// 0 => '第一个主题',
		// 1 => '第二个主题',
		// );
	}
	
	// 快速回帖(一个例子)
	function viewthread_fastpost_content_output()
	{
		return false;
		global $_G;//全部变量
		$config = $_G['cache']['plugin']['fans_club'];	//获取插件的变量信息
		if($_G['uid'] && $config['qr_enable'])				//用户是否登陆，快复回复是否禁用
		{
			$border_color = $config['qr_border_color'] ? $config['qr_border_color'] : ' #C2D5E3';
			$bg_color = $config['qr_bg_color'] ? $config['qr_bg_color'] : '#E5EDF2';
			$left_content = $config['qr_left_content'];
			$default_content = $config['qr_select_default'];
			
			//获取下拉框中的内容，并且定义以[br]分割所填的内容。
			$select_content = explode('[br]', str_replace(array("\n\r", "\t",), array('', ''), $config['qr_select_content']));
			$str = '<div style="border:'.$border_color.' 1px solid; background-color:'.$bg_color.'; height:24px; padding-top:2px;">&nbsp;&nbsp;'.$left_content.
				'&nbsp;&nbsp;<select id="quick_reply" style="height: 22px" onchange="quick_reply_z()" ><option value="">'.$default_content.'</option>';
			
			if($select_content)
			{
				foreach($select_content as $v)
				{
					if(empty($v))continue;
					$str .= '<option value="'.$v.'">'.$v.'</option>';
				}
			}
			
			$str .= '</select>&nbsp;&nbsp;</div>';
			$str .= '<script type="text/javascript">
function quick_reply_z() {
	var content = document.getElementById("quick_reply").value;
	document.getElementById("fastpostmessage").value = qr_replacehtml(content);
}
function qr_replacehtml(content) {
	content = content.replace(/<\/?.+?>/g,""); 
	content = content.replace(/[\r\n]/g, ""); 
	return content; 
}
</script>';
			return $str;
		}
	}
}
