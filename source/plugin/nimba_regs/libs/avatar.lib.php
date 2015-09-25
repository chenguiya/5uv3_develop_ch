<?php
/*
 * 主页：http://addon.discuz.com/?@ailab
 * 人工智能实验室：Discuz!应用中心十大优秀开发者！
 * 插件定制 联系QQ594941227
 * From www.ailab.cn
 */
 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$avatars=unserialize($_G['setting']['defaultavatar']);
if(!$avatars||!count($avatars)){
	cpmsg(lang('plugin/nimba_regs','defaultavatar_error'),'action=plugins&operation=config&identifier=nimba_regs&pmod=status','succeed');
}else{
	if($_GET['start']||submitcheck('addsubmit')){
		$start=max(1,intval($_GET['start']));
		$avatar=$avatars[array_rand($avatars,1)];
		$uid=DB::result_first("SELECT uid  FROM ".DB::table('common_member')." where avatarstatus=0 and status=0 order by uid desc");
		if($uid){
			$dir=sprintf("%09d",$uid);
			$root=checkAvatarDir($dir);
			createAvatar(DISCUZ_ROOT.'./source/plugin/defaultavatar/avatar/'.$avatar['name'],$root.substr($dir,-2).'_avatar_big.jpg','big');
			createAvatar(DISCUZ_ROOT.'./source/plugin/defaultavatar/avatar/'.$avatar['name'],$root.substr($dir,-2).'_avatar_middle.jpg','middle');
			createAvatar(DISCUZ_ROOT.'./source/plugin/defaultavatar/avatar/'.$avatar['name'],$root.substr($dir,-2).'_avatar_small.jpg','small');	
			DB::update('common_member',array('avatarstatus'=>1),array('uid'=>$uid));
			$start++;
			echo lang('plugin/nimba_regs','avatar_tip_3').$start.lang('plugin/nimba_regs','avatar_tip_4').$uid.lang('plugin/nimba_regs','avatar_tip_5');
			echo "<script>window.location.href='".ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=nimba_regs&pmod=avatar&start=".$start."';</script>";
		}else{
			cpmsg(lang('plugin/nimba_regs','avatar_tip_6'),'action=plugins&operation=config&identifier=nimba_regs&pmod=avatar', 'succeed');
		}
	}else{
		$count=DB::result_first("SELECT count(*)  FROM ".DB::table('common_member')." where avatarstatus=0 and status=0");
		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=nimba_regs&pmod=avatar');	
		showtableheader(lang('plugin/nimba_regs','avatar_title'),'nobottom');
		showsetting(lang('plugin/nimba_regs','avatar_tip'), 'avatar','',lang('plugin/nimba_regs','avatar_tip_1').$count.lang('plugin/nimba_regs','avatar_tip_2'),'',0,'');
		showsubmit('addsubmit',lang('plugin/nimba_regs','avatar_submit'));	
		showtablefooter();
		showformfooter();
	}
}

function createAvatar($img,$dir,$size){
	$image_size = getimagesize($img);
	$width=array('big'=>200,'middle'=>120,'small'=>48);
	$from=imagecreatefromjpeg($img);
	if($image_size[0]>$image_size[1]){
		$w=$width[$size]*$image_size[1]/$image_size[0];
		$h=$width[$size];
	}elseif($image_size[0]<$image_size[1]){
		$w=$width[$size];
		$h=$width[$size]*$image_size[0]/$image_size[1];
	}else{
		$w=$width[$size];
		$h=$width[$size];	
	}
	$new=imagecreatetruecolor($w,$h);
	imagecopyresized($new,$from,0,0,0,0,$w,$h,$image_size[0],$image_size[1]);
	imagejpeg($new,$dir);
}

function checkAvatarDir($dir){
	$root=DISCUZ_ROOT.'/uc_server/data/avatar/';
	$dir1 = substr($dir, 0, 3);
	$dir2 = substr($dir, 3, 2);
	$dir3 = substr($dir, 5, 2);
	$root.=$dir1.'/';
	if(!is_dir($root)) mkdir($root);
	$root.=$dir2.'/';
	if(!is_dir($root)) mkdir($root);
	$root.=$dir3.'/';
	if(!is_dir($root)) mkdir($root);
	return $root;
}
?>