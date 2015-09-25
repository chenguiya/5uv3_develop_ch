<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$page = dintval($_GET['page']);
$page = $page?$page:1;
$perpage = 12;
$start = ($page-1)*$perpage;
$wherearr = array();
if($sortid){
	$wherearr['sortid'] = $sortid;
}
$orderby = in_array($_GET['orderby'],array('hot','views','credit','sales'))?$_GET['orderby']:'id';
$da = $_GET['da']=='asc'?'asc':'desc';
$wherearr['status'] = 1;
$goodslist = C::t('#dc_mall#dc_mall_goods')->range($start,$perpage,$wherearr,$orderby,$da);
foreach($goodslist as &$g){
	if(!empty($g['pic']))
		$g['thumb'] = $g['pic'].'.thumb.jpg';
}
$count = C::t('#dc_mall#dc_mall_goods')->count($wherearr);
$wherestr='';
foreach($wherearr as $k=>$v){
	if($k=='status')continue;
	$wherestr.='&'.$k.'='.$v;
}
$commonurl = 'plugin.php?id=dc_mall'.$wherestr;
$multiurl = $commonurl;
$multiurl .= in_array($orderby,array('hot','views','credit','sales'))?'&orderby='.$orderby:'';
$multiurl .= $da=='asc'?'&da='.$da:'';
$multi = multi($count, $perpage, $page, $multiurl);

// 2015-07-03 zhangjh 分页替换
include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';
$multi = fansclub_multi($count, $perpage, $page, 'jifen/'.$orderby.'/'.$da.'/'.$sortid.'/');
	
$goodshot = C::t('#dc_mall#dc_mall_goods')->range(0,6,$wherearr,'hot');
foreach($goodshot as &$g){
	if(!empty($g['pic']))
		$g['thumb'] = $g['pic'].'.thumb.jpg';
}

$ordersnew = C::t('#dc_mall#dc_mall_orders')->range(0,20,array(),'id');
loadcache('dcmallinfo');
$navtitle = ($sortid?$mallnav[$sortid]['name'].' - ':'').$cvar['title'];
?>