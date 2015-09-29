<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: portal.php 33234 2013-05-08 04:13:19Z andyzheng $
 */

define('APPTYPEID', 4);
define('CURSCRIPT', 'portal');

require './source/class/class_core.php';
$discuz = C::app();

if (check_wap()) {
	header("HTTP/1.1 301 Moved Permanently");
	dheader("Location: forum.php?mobile=yes");
}

$cachelist = array('userapp', 'portalcategory', 'diytemplatenameportal');
$discuz->cachelist = $cachelist;
$discuz->init();

require DISCUZ_ROOT.'./source/function/function_home.php';
require DISCUZ_ROOT.'./source/function/function_portal.php';

if(empty($_GET['mod']) || !in_array($_GET['mod'], array('list', 'view', 'comment', 'portalcp', 'topic', 'attachment', 'rss', 'block'))) $_GET['mod'] = 'index';


define('CURMODULE', $_GET['mod']);
runhooks();

$navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['portal']);
$_G['disabledwidthauto'] = 1;

require_once libfile('portal/'.$_GET['mod'], 'module');

function check_wap(){
	// 先检查是否为wap代理，准确度高
	if(stristr($_SERVER['HTTP_VIA'],"wap")){
		return true;
	}
	// 检查浏览器是否接受 WML.
	elseif(strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0){
		return true;
	}
	//检查USER_AGENT
	elseif(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])){
		return true;
	}
	else{
		return false;
	}
}
?>