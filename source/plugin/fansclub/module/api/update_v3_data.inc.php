<?php
// 更新论坛数据 by zhangjh 2015-05-22
// 访问URL
// http://www.5usport.cn/plugin.php?id=fansclub:api&ac=update_v3_data&num=2

// 取旧数据
// $url = 'http://zhangjh.dev.usport.cc';
$url = 'http://www.5usport.com';
$num = $_GET['num'] + 0;
if($num + 0 < 1) $num = 1; // 取1条记录

$api_url = $url.'/v3/to_v3/phpcms/get_article?num='.$num.'&sign=fb574b29f55486b2c8584e7978b0ea88';
echo $api_url."\n";
$result = curl_access($api_url);
$arr_result = json_decode($result, TRUE);

if($arr_result['success'] === TRUE)
{
	for($i = 0; $i < count($arr_result['list']); $i++)
	{
		$info = $arr_result['list'][$i];
		$log_id = $info['log_id'];
		$api_url_detail = $url.'/v3/to_v3/phpcms/get_article_detail?log_id='.$log_id.'&sign=fb574b29f55486b2c8584e7978b0ea88';
		echo $api_url_detail."\n";
		$result_detail = curl_access($api_url_detail);
		$arr_result_detail = json_decode($result_detail, TRUE);
		
		if($arr_result_detail['success'] == TRUE)
		{
			$detail = $arr_result_detail['list'];
			
			//print_r($detail);
			
			// 1、先查一下是否有这个板块
			$arr_forum = explode(' ', trim($detail['forum_info']));
			// print_r($arr_forum);
			$arr_forum_list = fansclub_get_forum_list();
			// print_r($arr_forum_list);
			// $star = trim()
			
			$league = trim(@$arr_forum[1]);
			$club = trim(@$arr_forum[2]);
			$star = trim(@$arr_forum[3]);
			echo $league."|".$club."|".$star."|";
			echo "[".$detail['title']."]|";
			
			if($league == '' || $club == '')
			{
				die('league或club为空，程序中止，log_id='.$log_id);
			}
			// 
			$fid = fansclub_insert_forum($league, $club, $star);
			
			//echo 'fid='.$fid."\n";
			
			// 查询作者是否在在UC中，没有的话要加
			$arr_author = fansclub_add_ucmember_for_author($detail['article_author']);

			$author = $arr_author['author'];
			$authorid = $arr_author['authorid'];
			
			echo $author."|";
			$search_type = $detail['search_type'];
			echo $search_type.'|';
			if($search_type == '新闻')
			{
				$typeid = 1;
			}elseif($search_type == '图片')
			{
				$typeid = 2;
			}
			elseif($search_type == '视频')
			{
				$typeid = 3;
			}
			else
			{
				$typeid = 4;
			}
			
			$newthread = array(
				'fid' => $fid,
				'posttableid' => 0,
				'readperm' => 0,
				'price' => 0,
				'typeid' => $typeid, // 主题分类id 1新闻，2图片，3视频
				'sortid' => 0,
				'author' => $author,
				'authorid' => $authorid,
				'subject' => $detail['title'],
				'dateline' => $detail['article_time'],
				'lastpost' => $detail['article_time'],
				'lastposter' => $author,
				'displayorder' => 0,
				'digest' => 0,
				'special' => 0,
				'attachment' => 0,
				'moderated' => 0,
				'status' => 0,
				'isgroup' => 0,
				'replycredit' => 0,
				'closed' => 0
			);
			
			$message = $detail['detail'];
			if($typeid == 1) // 处理新闻内容
			{
				$patterns = array ("/<strong>(.*?)<\/strong>/",
								   "/<span style=\"color\:(.*?)\;\">(.*?)<\/span>/",
								   "/<img(.*?)src=\"(.*?)\" style=\"width: (.*?)px; height: (.*?)px;\" \/>/",
								   "/<img(.*?)src=\"(.*?)\"(.*?)\/>/",
								   "/<p(.*)>(.*?)<\/p>/");
				$replace = array ("[b]\${1}[/b]", 
								  "[color=\${1}]\${2}[/color]",
								  "[img=\${3},\${4}]\${2}[/img]",
								  "[img]\${2}[/img]",
								  "\${2}\n");
				$message = preg_replace($patterns, $replace, $message);
			}
			elseif($typeid == 2)
			{
				$regex = "|'url' \=\> '(.*)'|U";
				preg_match_all($regex, $message, $_tmp_arr, PREG_PATTERN_ORDER);
				
				$message = '';
				if(count($_tmp_arr[1]) > 0)
				{
					for($j = 0; $j < count($_tmp_arr[1]); $j++)
					{
						$message .= '[img]'.$_tmp_arr[1][$j].'[/img]';
					}
				}
			}
			elseif($typeid == 3)
			{
				$regex = "|src=\"(.*)\"|U";
				preg_match_all($regex, $message, $_tmp_arr, PREG_PATTERN_ORDER);
				$message = '';
				if(count($_tmp_arr[1]) > 0)
				{
					for($j = 0; $j < count($_tmp_arr[1]); $j++)
					{
						$message .= ''.$_tmp_arr[1][$j].'';
					}
				}
			}
			

			$tid = C::t('forum_thread')->insert($newthread, true);
			C::t('forum_newthread')->insert(array(
				'tid' => $tid,
				'fid' => $fid,
				'dateline' => $detail['article_time'],
			));
			
			echo 'tid='.$tid."|";
			
			
			
			$tagstr = '';
			$keywords = implode(' ', preg_split("/\s+/", $detail['keywords']));
			if(trim($keywords) != '')
			{
				$class_tag = new tag();
				$tagstr = $class_tag->add_tag($keywords, $tid, 'tid');
			}
			
			include_once libfile('function/forum');
			require_once libfile('function/post');

			$pid = insertpost(array(
				'fid' => $fid,
				'tid' => $tid,
				'first' => '1',
				'author' => $author,
				'authorid' => $authorid,
				'subject' => $detail['title'],
				'dateline' => $detail['article_time'],
				'message' => $message,
				'useip' => '127.0.0.1',
				'port' => '',
				'invisible' => 0,
				'anonymous' => 0,
				'usesig' => 0,
				'htmlon' => 0,
				'bbcodeoff' => 0,
				'smileyoff' => 0,
				'parseurloff' => 0,
				'attachment' => '0',
				'tags' => $tagstr,
				'replycredit' => 0,
				'status' => 0
			));
			echo 'pid='.$pid."\n";
			
			// 写记录
			$detail['title'] = str_replace("'", '\\\'', $detail['title']); //
			$detail['detail'] = str_replace("'", '\\\'', $detail['detail']); //
			$detail['keywords'] = str_replace("'", '\\\'', $detail['keywords']); // 
			
			$result = DB::query("INSERT INTO ".DB::table('plugin_fansclub_old_article_log')."(`old_id`, `old_catid`,`old_url`,`old_detail`,".
			"`title`,`thumb`,`keywords`,`type`,`new_tid`,`new_fid`,`new_detail`) VALUES ('".$detail['id']."','".$detail['catid']."','".$detail['url']."','".$detail['detail']."',".
			"'".$detail['title']."','".$detail['thumb']."','".$detail['keywords']."','".$typeid."','".$tid."','".$fid."','".$message."') ");
			echo 'log_id='.$result.'|';
			
			
			// 更新 pre_forum_forum 表的 threads 和 posts
			updateforumcount($fid);
			
		}
	}
}
else
{
	echo "ERROR:".$arr_result['message'];
}




