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
loadcache('plugin');
$langvars=lang('plugin/nimba_majia');

if(submitcheck('addsubmit')){
	$mainuid=intval($_POST['mainuid']);
	if($mainuid){
		$uids= explode(",",str_replace(' ', '',$_POST['uids']));
		$users=C::t('common_member')->fetch_all_username_by_uid($uids);
		foreach($users as $useruid=>$username){
			if($useruid&&$username){
				$count=DB::result_first("select count(*) from ".DB::table('nimba_majia')." where uid='$mainuid' and useruid='$useruid'");
				if(!$count) DB::insert('nimba_majia',array('uid'=>$mainuid,'username'=>$username,'useruid'=>$useruid));
			}
		}
		cpmsg($langvars['updateuser_succeed'],'action=plugins&operation=config&identifier=nimba_majia', 'succeed');
	}else{
		cpmsg($langvars['updateuser_error']);
	}
}else{
	showformheader("plugins&operation=config&identifier=nimba_majia&pmod=add");
	showtableheader($langvars['addcontent'], 'nobottom');	
	showsetting($langvars['mainuid'], 'mainuid','', 'text','',0,$langvars['mainuidinfo']);
	showsetting($langvars['uidlist'],'uids','','textarea','', 0,$langvars['uidlistinfo']);
	showsubmit('addsubmit');
	showtablefooter();
	showformfooter();
}

?>