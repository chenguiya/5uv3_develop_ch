<?php
//m2: libo 2015-05-19
$os = check_mobile_system();
$array_download_url = array(
	"ios" => "https://itunes.apple.com/us/app/liao-qiu/id987830580?l=zh&ls=1&mt=8",
	"android" => "http://static2.5usport.com/download/apps/5usport_liaoqiu.0619.apk",
	"download_url" => "http://static2.5usport.com/download/apps/5usport_liaoqiu.0619.apk",
);

switch($os) {
	case "ios":
		$array_download_url["download_url"] = $array_download_url["ios"];
		include_once("./wap.php");
		break;
	case 'android':
		$array_download_url["download_url"] = $array_download_url["android"];
		include_once("./wap.php");
		break;
	default:
		$array_download_url["download_url"] = $array_download_url["android"];
		include_once("./app.php");
		break;
}

/**
 * 根据HTTP_USER_AGENT判断设备系统
 */
function check_mobile_system()
{
    $useragent  = strtolower($_SERVER["HTTP_USER_AGENT"]);
    $system = 'unknown';
    // iphone
    $is_iphone  = strripos($useragent,'iphone');
    if($is_iphone){
        $system = 'ios';
    }
    // android
    $is_android    = strripos($useragent,'android');
    if($is_android){
        $system =  'android';
    }
    /*// 微信
    $is_weixin  = strripos($useragent,'micromessenger');
    if($is_weixin){
        $system =  'weixin';
    }
    // ipad
    $is_ipad    = strripos($useragent,'ipad');
    if($is_ipad){
        $system =  'ipad';
    }
    // ipod
    $is_ipod    = strripos($useragent,'ipod');
    if($is_ipod){
        $system =  'ipod';
    }
    // pc电脑
    $is_pc = strripos($useragent,'windows nt');
    if($is_pc){
        $system =  'pc';
    }*/
    return $system;
}

