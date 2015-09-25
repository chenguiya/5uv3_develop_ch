<?php
/**
 * 给百度站长 链接提交
 * from http://zhanzhang.baidu.com/linksubmit/index?site=http%3A%2F%2F5usport.com%2F
 * 手工执行 http://www.5usport.com/plugin.php?id=fansclub:api&ac=tuisong&site=www.5usport.com&date=2015-07-02&num=10&go=1
赵子发(赵子发) 2015-07-13 10:01:31
* *\/1 * * *  curl -H "HOST:www.5usport.com"  http://192.168.11.4/plugin.php?id=fansclub:api&ac=tuisong&num=2000&go=1 >/dev/null 2>&1
 * m1 2015-07-13 zhangjh 加日志记录 
 */
$log_time = time();
$text = '['.date('Y-m-d H:i:s', $log_time).']'; // 日志记录内容
$urls = array();

// 查询帖子
$num = intval($_GET['num']) > 0 ? intval($_GET['num']) : 1;

$go = intval($_GET['go']);
$site = trim($_GET['site']) == '' ? 'www.5usport.com' : trim($_GET['site']);

if(trim($_GET['date']) == '') // 查1小时
{
	$end_time = time();
	$star_time = $end_time - 60*60;
}
else
{
	$date = trim($_GET['date']); // 查这天
	$star_time = strtotime($date.' 00:00:00');
	$end_time = strtotime($date.' 23:59:59');
}

$text .= $_SERVER['REMOTE_ADDR'].'|http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
$text .= '|star_time='.date('Y-m-d H:i:s', $star_time).'|end_time='.date('Y-m-d H:i:s', $end_time);


$sql = "SELECT tid FROM ".DB::table('forum_thread')." ".
	   "WHERE dateline >= ".$star_time." ".
	   "AND dateline <= ".$end_time." ".
	   "AND displayorder > '-1' ".
	   "ORDER BY ".DB::order('tid', 'DESC').' '.
	   DB::limit(0, $num);
$row = DB::fetch_all($sql);
for($i = 0; $i < count($row); $i++)
{
	$urls[] = 'http://'.$site.'/thread-'.$row[$i]['tid'].'.html';
}
$text .= '|count='.count($row);

if($go == 1)
{
	$api = 'http://data.zz.baidu.com/urls?site='.$site.'&token=3asmjujnmliXcARJ';
	$ch = curl_init();
	$options = array(
		CURLOPT_URL => $api,
		CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POSTFIELDS => implode("\n", $urls),
		CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
	);
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	$text .= '|result='.$result;
	echo $result;
}
else
{
	$text .= '|result=justshow';
	echo "<pre>";
	print_r($urls);
}

// 写日志
/*
-- 球迷会api日志记录表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_api_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_type` char(32) NOT NULL DEFAULT '' COMMENT '类型eg.tuisong',
  `log_time` int(10) unsigned DEFAULT '0' COMMENT '时间',
  `log_text` char(255) NOT NULL DEFAULT '' COMMENT '内容',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球迷会api日志记录表';
*/
$arr_log_data = array(
	'log_type' => 'tuisong',
	'log_time' => $log_time,
	'log_text' => $text,
);
C::t('#fansclub#plugin_fansclub_api_log')->insert($arr_log_data);
