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
$langvar=lang('plugin/nimba_regs');
$name='userlist';
define('FOOTERDISABLED', false);
ob_end_clean();
dheader('Cache-control: max-age=0');
dheader('Expires: '.gmdate('D, d M Y H:i:s', TIMESTAMP - 31536000).' GMT');
dheader('Content-Encoding: none');
dheader('Content-Disposition: attachment; filename='.$name.'.csv');
dheader('Content-Type: text/plain');	
$output='UID,'.$langvar['username'].','.$langvar['pw1'].','.$langvar['email'].','.$langvar['time']."\r\n";
$data=C::t('#nimba_regs#nimba_member')->fetch_all();
foreach($data as $k=>$v){
	$v['dateline']=date('Y-m-d H:i:s',$v['dateline']);
	$row='';
	foreach($v as $id=>$con){
		$row.=empty($row)? $con:','.$con;
	}
	$output.=$row."\r\n";
}
if(strtolower(CHARSET)=='utf-8') $output=iconv('UTF-8','GBK',$output);
echo $output;
exit();
?>