<?php
/*
 * 主页：http://addon.discuz.com/?@ailab
 * 人工智能实验室：Discuz!应用中心十大优秀开发者！
 * 插件定制 联系QQ594941227
 * From www.ailab.cn
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
error_reporting(0);
loadcache('plugin');
$groups = unserialize($_G['cache']['plugin']['nimba_majia']['groups']);
if(!$_G['uid']||!in_array($_G['groupid'], $groups)) {
	showmessage('nimba_majia:usergroup_disabled');
}
if($_GET['pluginop'] == 'add' && submitcheck('adduser')) {
	loaducenter();
	$_GET['usernamenew']=addslashes($_GET['usernamenew']);
	$_GET['passwordnew']=addslashes($_GET['passwordnew']);
	if($_GET['questionidnew']) $_GET['questionidnew']=daddslashes($_GET['passwordnew']);
	$ucresult = $_GET['questionidnew']? uc_user_login($_GET['usernamenew'], $_GET['passwordnew']):uc_user_login($_GET['usernamenew'], $_GET['passwordnew'],0,1,$_GET['questionidnew'],$_GET['answernew']);
	if(empty($_GET['passwordnew']) || ($_GET['questionidnew'] && empty($_GET['answernew'])) || $ucresult[0]<=0) {
		showmessage('nimba_majia:adduser_fail',"javascript:history.back()");
	}
	$useruid=intval(DB::result_first("SELECT uid FROM ".DB::table('common_member')." WHERE username='".$_GET['usernamenew']."'"));
	$usernamenew = strip_tags($_GET['usernamenew']);
	DB::insert('nimba_majia',array('uid'=>$_G['uid'],'username'=>$usernamenew,'useruid'=>$useruid));
	$usernamenew = stripslashes($usernamenew);
	showmessage('nimba_majia:adduser_succeed', 'home.php?mod=spacecp&ac=plugin&id=nimba_majia:admincp', array('usernamenew' => $usernamenew));
	
}elseif(!empty($_GET['delete'])&&$_GET['formhash'] == FORMHASH) {
	$_GET['delete']=daddslashes($_GET['delete']);
	$uids=implode(',',$_GET['delete']);
	//echo "DELETE FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]' AND useruid in($uids)";
	DB::query("DELETE FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]' AND useruid in($uids)");
	showmessage('nimba_majia:updateuser_succeed', 'home.php?mod=spacecp&ac=plugin&id=nimba_majia:admincp');
}
//马甲列表
$repeatusers=DB::fetch_all("SELECT * FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]'");
?>