<?php
//http://www.5usport.com/plugin.php?id=fansclub:api&ac=update_video_message&num=1
/*
$test = '<p><strong>U体育7月20日报道</strong> 中超联赛第15轮的一场比赛在广州打响，广州恒大主场迎战山东鲁能。下半场比赛迪亚曼蒂在错失一次机会时，愤怒地将球衣撕成了深V款。<span style="color:#ff0000;"><strong><u>相关链接&mdash;&mdash;<a href="http://www.usportnews.com/goal/csl/74035.html" target="_blank">全场战报</a>&mdash;&mdash;<a href="http://www.usportnews.com/goal/csl/74039.html" target="_blank">全场统计</a>&mdash;&mdash;<a href="http://www.usportnews.com/goal/csl/74037.html" target="_blank">U评分</a></u></strong></span></p>
<p style="text-align: center;"><img alt="中超恒大VS鲁能直播GIF 迪亚曼蒂怒撕球衣变深V款" src="http://img.usportnews.com/uploadfile/2014/0720/20140720094741370.gif" style="width: 363px; height: 222px;" /></p>
<p style="text-align: center;">迪亚曼蒂怒撕球衣</p>
';
$patterns = array ("/<span style=\"color\:(.*?)\;\">(.*?)<\/span>/",
					"/<strong>(.*?)<\/strong>/",
					 "/<p(.*?)>/",
					 "/<\/p>/",
					  "/<img(.*?)src=\"(.*?)\"(.*?)\/>/",
					  "/<a(.*?)href=\"(.*?)\"(.*?)>(.*?)<\/a>/",
					 "/&mdash;/",
					 "/<u>(.*?)<\/u>/");
$replace = array (
					  "[color=\${1}]\${2}[/color]",
					  "[b]\${1}[/b]",
						"",					  
					  "\n",
					  "[img]\${2}[/img]",
					  "[url=\${2}]\${4}[/url]",
					  "—",
					  "[u]\${1}[/u]");
$new_message = preg_replace($patterns, $replace, $test);
echo $new_message;
exit;

*/



echo "<pre>";
$op = trim($_GET['op']);
$num = intval(trim($_GET['num']));
$num = ($num == 0) ? 1 : $num;

$sql = "select d.new_tid, d.old_detail, d.new_detail  ".
		"from ".DB::table('plugin_fansclub_old_article_log') ." d ".
		"where 1 = 1 AND d.type = 1 AND d.new_detail = '' AND d.old_detail <> '' ".
		"order by d.new_tid DESC";
echo $sql;
echo "\n";
$j = 0;
$arr = DB::fetch_all($sql);
// print_r($arr);
for($i = 0; $i < count($arr); $i++)
{
	if($j >= $num) break;
	$sql = "select a.* ".
		"from ".DB::table('forum_post')." a ".
		"where 1 = 1 ".
		"and a.tid = ".$arr[$i]['new_tid']." ".
		"and a.first = 1 and a.message = '' ".
		"order by a.tid DESC";
	$row = DB::fetch_all($sql);
	if(count($row) == 1)
	{
		$pid = $row[0]['pid'];
		
		$patterns = array ("/<span style=\"color\:(.*?)\;\">(.*?)<\/span>/",
					"/<strong>(.*?)<\/strong>/",
					 "/<p(.*?)>/",
					 "/<\/p>/",
					  "/<img(.*?)src=\"(.*?)\"(.*?)\/>/",
					  "/<a(.*?)href=\"(.*?)\"(.*?)>(.*?)<\/a>/",
					 "/&mdash;/",
					 "/<u>(.*?)<\/u>/");
$replace = array (
					  "[color=\${1}]\${2}[/color]",
					  "[b]\${1}[/b]",
						"",					  
					  "\n",
					  "[img]\${2}[/img]",
					  "[url=\${2}]\${4}[/url]",
					  "—",
					  "[u]\${1}[/u]");
		$new_message = preg_replace($patterns, $replace, $arr[$i]['old_detail']);
		
		$sql = "UPDATE ".DB::table('forum_post')." SET message = '".$new_message."' WHERE pid = ".$pid;
		DB::query($sql);
		echo $sql."|\n"; 
		$j++;
	}
}


/*
// 处理视频
$sql = "select d.new_tid ".
		"from ".DB::table('plugin_fansclub_old_article_log') ." d ".
		"where 1 = 1 AND d.type = 3 ".
		"order by d.new_tid DESC";
echo $sql;
echo "\n";
$j = 0;
$arr = DB::fetch_all($sql);
for($i = 0; $i < count($arr); $i++)
{
	if($j >= $num) break;
	$sql = "select a.* ".
		"from ".DB::table('forum_post')." a ".
		"where 1 = 1 ".
		"and a.tid = ".$arr[$i]['new_tid']." ".
		"and a.first = 1 ".
		"and a.message not like '%media%'".
		"order by a.tid DESC ";
	$row = DB::fetch_all($sql);
	// print_r($row);
	$pid = $row[0]['pid'];
	$message = $row[0]['message'];
	if($message != '')
	{
		$new_message = '[media=swf,500,375]'.$message.'[/media]';
		$sql = "UPDATE ".DB::table('forum_post')." SET message = '".$new_message."' WHERE pid = ".$pid;
		DB::query($sql);
		echo $sql."|\n"; 
		$j++;
	}
}
*/
exit;
