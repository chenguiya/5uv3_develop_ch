<?php
	if (!defined('IN_DISCUZ')) {
		exit('Access Denied');
	}
	
	// zhangjh 2015-06-29 加 navtitle metakeywords 和 metadescription
	$nobbname = TRUE;
	$navtitle = $_G['setting']['bbname'].'_承载自由体育文化，传递不一样的体育声音';
	$metakeywords = '足球明星,篮球明星,体育明星,球队';
	$metadescription = $_G['setting']['bbname'].'是以体育新闻、NBA、CBA、英超、西甲、中超、中国足球等的垂直体育社群门户，体育明星在线互动，独特的体育观点与草根体育专栏，尽在你我的体育社区。';
	
	$ac = $_GET['ac'];
	include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
	
	$arr_forum_list = fansclub_get_forum_list();
	$page = $_GET['page'] ? $_GET['page']:1;
	$num = 20;
	$star = ($page-1)*$num;
	//debug($arr_forum_list);
	$sort = trim($_GET['sort']);
	if($sort=='new' || empty($sort)){
		$orderby='dateline desc';
	}elseif($sort=='hot'){
		$orderby='views desc';
	}elseif($sort=='stick'){
		$orderby='digest desc';
	}else
	{
		$orderby='dateline desc';
	}
	
	if($ac=='news'){
		
		if(strpos($_SERVER['HTTP_HOST'], '5usport.com') !== FALSE) // 正式服跳转足球圈 by zhangjh 2015-08-19
		{
			dheader("Location:http://".$_SERVER['HTTP_HOST']."/football/");
		}

		$navtitle = '最新足球新闻篮球资讯_'.$_G['setting']['bbname'];
		$metakeywords = '足球,篮球,新闻';
		$metadescription = '最新足球，篮球新闻资讯图片尽在5u体育新闻中心。';
		
		$sql = "select a.* from ".DB::table('forum_thread')." a, ".DB::table('forum_threadclass')." b where a.typeid = b.typeid and b.name = '新闻' and a.displayorder >= 0 order by {$orderby} limit {$star},{$num}";
		$arr = DB::fetch_all($sql);
		if($_GET['return']=='ajax'){
			include template('fansclub:index/index_news_ajax');
		}else{
			include template('fansclub:index/index_news');
		}
	}elseif($ac=='pic'){
		$navtitle = '最精彩的体育图片_性感体育宝贝花边照片_'.$_G['setting']['bbname'];
		$metakeywords = '体育图片,足球图片,篮球图片,球星图片';
		$metadescription = '最全，最新足球，篮球图片尽在5u体育图片中心。';
		
		$fid = intval($_GET['fid']);
		$is_group = intval($_GET['is_group']);
		
		if($is_group == 1) // 如果是球迷会分会的话
		{
			$arr = C::t('#fansclub#plugin_forum_thread')->fetch_list_attachment($fid, $num, 2, $star);
		}
		else
		{
			if($fid > 0) // 如果是版块的话
			{
				$sql = "select a.* from ".DB::table('forum_thread')." a, ".DB::table('forum_threadclass')." b where a.fid = ".$fid." AND a.typeid = b.typeid and b.name = '图片' and a.displayorder >= 0 order by {$orderby} limit {$star},{$num}";
			}
			else
			{
				$sql = "select a.* from ".DB::table('forum_thread')." a, ".DB::table('forum_threadclass')." b where a.typeid = b.typeid and b.name = '图片' and a.displayorder >= 0 order by {$orderby} limit {$star},{$num}";
			}
			$arr = DB::fetch_all($sql);
		}
		
		$arr = fansclub_get_video_list($arr);
		if($_GET['return']=='ajax')
		{
			echo json_encode($arr);
			exit();
		}else{
			include template('fansclub:index/index_picture');
		}
	}
	elseif($ac == 'video')
	{
		$navtitle = '最新足球视频大全_篮球TOP10视频_'.$_G['setting']['bbname'];
		$metakeywords = '足球视频,NBA视频,篮球视频';
		$metadescription = '最全，最新足球，篮球视频尽在5u体育视频中心。';
		
		$sql = "select a.* from ".DB::table('forum_thread')." a, ".DB::table('forum_threadclass')." b where a.typeid = b.typeid and b.name = '视频' and a.displayorder >= 0 order by {$orderby} limit {$star},{$num}";
		$arr = DB::fetch_all($sql);
		$arr = fansclub_get_video_list($arr);
		if($_GET['return'] == 'ajax')
		{
			echo json_encode($arr);
			exit();
		}
		else
		{
			include template('fansclub:index/index_video');
		}
	}
	elseif ($ac == 'feed') {
		global $_G;		
		$fid = isset($_GET['fid']) ? intval($_GET['fid']) : showmessage('球迷会不存在');
		
		require_once libfile('function/feed');
		require_once libfile('function/home', 'plugin/fansclub');
		require_once libfile('function/extends');
		
		if(empty($_G['setting']['feedhotday'])) {
			$_G['setting']['feedhotday'] = 2;
		}
		
		$minhot = $_G['setting']['feedhotmin']<1?3:$_G['setting']['feedhotmin'];
		$clubinfo = C::t('forum_forum')->fetch_info_by_fid($fid);
		
		//获取球迷会球迷成员
		$allfanslist = C::t('forum_groupuser')->groupuserlist($fid);
		
		if(empty($_GET['order'])) {
			$_GET['order'] = 'dateline';
		}
		
		if (empty($_GET['do'])) {
			$_GET['do'] = 'all';
		}
		
		$uids = array();
		foreach ($allfanslist as $fans) {
			$uids[] = $fans['uid'];
		}
		$struids = implode(',', $uids);
		$whereall = "`uid` IN (" . $struids . ") AND `icon` IN ('thread','album','blog','friend')";
		$count = C::t('#fansclub#plugin_home_feed')->count_by_where($whereall);
		
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
		
		$maxpage = @ceil($count/$pagesize);
		$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
		$multipage = "plugin.php?id=fansclub:index&ac=feed&fid=".$fid."&page=" . $nextpage . "&pagesize=" . $pagesize;
		
		$where = '';
		if ($_GET['do'] == 'all') {
			$where = "uid IN (" . $struids . ") AND icon IN ('thread','album','blog','friend')";
		} elseif ($_GET['do'] == 'hot') {
			$where = "uid IN (" . $struids . ") AND hot!=0";
		}
		
		$feedlist = C::t('#fansclub#plugin_home_feed')->fetch_feedlist($where, $page, $pagesize, $_GET['order']);
		foreach ($feedlist as $key => $feed) {
			$feedlist[$key]['avatar'] = avatar($feed['uid'], 'middle', 1);
			$feedlist[$key]['dateline'] = date('Y年m月d日 H:i', $feed['dateline']);
			switch ($feed['icon']) {
				case 'thread':
					if (!empty($feed['image_1'])) {
						$feedlist[$key]['attachment'] = get_attachment($feed['id']);
					} elseif ($swf = create_video_html($feed['id'])) {
						$feedlist[$key]['video'] = '<embed src="'.$swf.'" quality="high" width="240" height="200" align="middle" allowScriptAccess="always" allowFullScreen="true" mode="transparent" type="application/x-shockwave-flash"></embed>';
					} 
					
					if ($credits = get_add_credits($feed['id'], $fid)) $feedlist[$key]['credits'] = $credits;
					$body_data = dunserialize($feed['body_data']);
					$feedlist[$key]['title_template'] = $body_data['subject'];
					$feedlist[$key]['message'] = $body_data['message'];
					$feedlist[$key]['data'] = get_thread_data($feed['id']);
					break;
				case 'album':
					$body_data = dunserialize($feed['body_data']);
					$feedlist[$key]['title_template'] = $body_data['album'];
					break;
				case 'blog':
					$body_data = dunserialize($feed['body_data']);
					$feedlist[$key]['title_template'] = $body_data['subject'];
					break;
						
				case 'friend':
					$body_data = dunserialize($feed['body_data']);
					$touser = dunserialize($feed['title_data']);
					$feedlist[$key]['title_template'] = $feed[username].'和'.$touser['touser'].'成为了好友';
					break;
						
				default:
					$feedlist[$key] = $feed;
					break;
			}
		}

		$multipage = multi($count, $pagesize, $page);
		include template('fansclub:index/feed_list');
		echo $return;
		exit;
	}



















?>