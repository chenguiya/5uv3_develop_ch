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
$xing=explode(',',lang('plugin/nimba_regs','xing'));
$ming=explode(',',lang('plugin/nimba_regs','ming'));
$word5000=explode(',',lang('plugin/nimba_regs','word5000'));
function creatname($sj){
	global $_G,$xing,$ming,$word5000;
	//四种用户名 5随机
	//1 纯汉字
	//2 纯英语
	//3 汉字+数字
	//4 英语+数字
	if($sj==5) $sj=rand(1,4);
	$zm=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

	if($sj==1){
		$t=time()%3;
		if($t==0) return $xing[rand(0,count($xing)-1)].$ming[rand(0,count($ming)-1)];
		elseif($t==1) return $word5000[rand(0,count($word5000)-1)].$word5000[rand(0,count($word5000)-1)];
		else return $word5000[rand(0,count($word5000)-1)].$word5000[rand(0,count($word5000)-1)].$word5000[rand(0,count($word5000)-1)];
	}elseif($sj==2) return $zm[rand(0,25)].$zm[rand(0,25)].$zm[rand(0,25)].$zm[rand(0,25)].$zm[rand(0,25)].$zm[rand(0,25)].$zm[rand(0,25)].$zm[rand(0,25)].$zm[rand(0,25)].$zm[rand(0,25)];
	elseif($sj==3){
		$t=time()%3;
		if($t==0) return $ming[rand(0,count($ming)-1)].rand(100,9999);
		elseif($t==1) return $xing[rand(0,count($xing)-1)].rand(100,9999);
		else $word5000[rand(0,count($word5000)-1)].rand(100,9999);
	}
	if($sj==4){
		$re='';
		for($i=0;$i<10;$i++){
			$k=rand(0,1);
			if($k) $re.=$zm[rand(0,25)];
			else $re.=rand(0,9);
		}
		return $re;
	}
}
function createmail(){
	$t=time()%2;
	if($t==0) return time().rand(0,9).'@139.com';
	else return time().rand(0,9).'@qq.com';
}
function creatuser($sj,$group=10,$pw=''){
	$user=array();
	$user['username']=creatname($sj);
	$username=$user['username'];
	$password=empty($pw)? substr(md5(creatname(4)),0,8):$pw;
	$email=(string)createmail();
	//$group=10;	
	$uid=creat_new($username,$password,$email,$group);
 	if($uid>1){	
		C::t('#nimba_regs#nimba_member')->insert($uid, $username, $password, $email,time());
		return $uid;
	}else return '';
}
function creat_new($username,$password,$email,$group){
	global $_G;
	loaducenter();
	$uid=0;
	$uid = uc_user_register($username, $password, $email);
	/*
	-1 : 用户名不合法
	-2 : 包含不允许注册的词语
	-3 : 用户名已经存在
	-4 : email 格式有误
	-5 : email 不允许注册
	-6 : 该 email 已经被注册
	>1 : 表示成功，数值为 UID
	*/
	if($uid > 0) {
		$pwd=md5(random(10));
		$profile=array('profile'=>array('gender'=>rand(1,2)));
		C::t('common_member')->insert($uid,$username,$pwd,$email,creatip(),$group,$profile);//$_G['clientip']
		loadcache('plugin');
		$var=$_G['cache']['plugin']['nimba_regs'];
		if($var['credit']&&$var['creditnum']){
			updatemembercount($uid, array($var['credit']=>$var['creditnum']));
		}
	}
	require_once libfile('cache/userstats', 'function');
	build_cache_userstats();
	return $uid;
}

function creatip(){
	global $_G;
	loadcache('plugin');
	$ips=explode("/hhf/",str_replace(array("\r\n", "\n", "\r"), '/hhf/',trim($_G['cache']['plugin']['nimba_regs']['ips'])));
	$newips=array();
	foreach($ips as $k=>$ipStr){
		if($ipStr&&substr_count($ipStr,'.')==3) $newips[]=$ipStr;
	}
	$ipLen=count($newips);
	if($ipLen){
		$ipArr=explode('.',$newips[rand(0,($ipLen-1))]);
		$ip='';
		for($i=0;$i<4;$i++){
			$ipArr[$i]=intval($ipArr[$i]);
			if($ipArr[$i]) $ip.=empty($ip)? $ipArr[$i]:'.'.$ipArr[$i];
			else $ip.=empty($ip)? rand(0,255):'.'.rand(0,255);
		}
		return $ip;
	}else{
		$first=array('113','222','119','121','58','122','125','219','61','116','123','218','59','221','124','27','60','183','202','114','120','112','115','117','180','210','220','74','111','165','182','72','110','211','14','159','161','169','175','187','207','81','86');
		return $first[rand(0,count($first)-1)].'.'.rand(0,255).'.'.rand(0,255).'.'.rand(0,255);
	}
}
function auto_charset($fContents,$from='gbk',$to='utf-8'){
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}

function ishow($message,$url){
	return "<script>alert('$message');window.location.href='$url';</script>";
}
?>