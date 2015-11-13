<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
list($navtitle, $metadescription, $metakeywords) = get_seosetting('portal');

$channel_title 		= "5U体育_承载自由体育文化，传递不一样的体育声音";
$channel_description = "5U体育是以体育新闻、NBA、CBA、英超、西甲、中超、中国足球等的垂直体育社群门户，体育明星在线互动，独特的体育观点与草根体育专栏，尽在你我的体育社区。";
$channel_url = trim($_G['siteurl']);

//数据获取,默认100条
$num = isset($_GET['num']) ? intval($_GET['num']) : '100';
$sql = "SELECT a.subject, a.tid, a.fid, c.pid, c.message, a.author, a.dateline FROM ".DB::table('forum_thread')." a, ".DB::table('forum_threadclass')." b, ".DB::table('forum_post')." c WHERE a.typeid=b.typeid AND a.tid=c.tid AND c.first=1 AND b.name='新闻' AND a.displayorder>=0 ORDER BY a.tid DESC LIMIT $num";
$result = DB::fetch_all($sql);


//获取栏目缓存
loadcache('forums');
$siteurl = 'http://www.5usport.com/';
require_once libfile('function/extends');
require_once libfile('function/discuzcode');
include DISCUZ_ROOT.'./source/plugin/extends/classes/class_rssbuilder.php';
$title = $link = $description = '';
$rss = new rssbuilder($title, $link, $description);

foreach ($result as $key => $value) {
	$title = $value['subject'];
	$link = $siteurl.'thread-'.$value['tid'].'.html';
	
	$attachments = getallattachment($value['tid']);
		
	$message = discuzcode($value['message'], 1, 0);
	$description = changemessagetohtml($message, $attachments);
	$author = trim($value['author']);
	$fup = $_G['cache']['forums'][$value['fid']]['fup'];
	$category = $_G['cache']['forums'][$fup]['name'];
	$pubDate = date(DATE_RFC2822, $value['dateline']);
	$rss->AddItem($title, $link, $description, $author, $category, $pubDate);
}
$rss->BuildRSSForZark($channel_title, $channel_url, $channel_description);
$rss->Show();
?>
