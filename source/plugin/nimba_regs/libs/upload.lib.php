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
if(submitcheck('submit')){
	loadcache('plugin');
	$vars=$_G['cache']['plugin']['nimba_regs'];	
	$group=empty($vars['regs_group'])? 10:$vars['regs_group'];
	$data= explode("/hhf/",str_replace(array("\r\n", "\n", "\r"), '/hhf/',$_POST['uploaddata']));
	foreach($data as $k=>$v){
		$v=trim($v);
		if($v){
			$load=explode(" # ",$v);
			$username=addslashes(trim($load[0]));
			$email=addslashes(trim($load[2]));
			$password=addslashes(make_password(8));
				//echo $v.'<br>';
			if($username&&$email&&$password){
				$uid=creat_new($username,$password,$email,$group);
				if($uid>1) C::t('#nimba_regs#nimba_member')->insert($uid, $username, $password, $email,randdomDate());
			}
		}
	}
	echo ishow($langvar['added'], ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=nimba_regs&pmod=pregs');
} else include template('nimba_regs:upload');

function randdomDate($begintime = '20151-1-1 00:00:00', $endtime = TIMESTAMP) {
	$begin = strtotime($begintime);
	$timestamp = rand($begin, $endtime);
	return $timestamp;
}

function make_password($length = 8) {
	// 密码字符集，可任意添加你需要的字符  
	$chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',  
'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',  
't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',  
'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',  
'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',  
'0', '1', '2', '3', '4', '5', '6', '7', '8', '9'); 
	//在chars中随机读取$length个数组元素键名
	$keys = array_rand($chars, $length);
	$password = '';
	for ($i = 0; $i < $length; $i++) {
		//将$length个数组元素连接生成字符串
		$password .= $chars[$keys[$i]];
	}
	return $password;
}

?>