<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: misc_tag.php 32232 2012-12-03 08:57:08Z zhangjie $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$id = intval($_GET['id']);
$type = trim($_GET['type']);
$name = trim($_GET['name']);
$page = intval($_GET['page']);
$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 3;
if($type == 'countitem') {
	$num = 0;
	if($id) {
		$num = C::t('common_tagitem')->count_by_tagid($id);
	}
	include_once template('tag/tag');
	exit();
}
$taglang = lang('tag/template', 'tag');
if($id || $name) {

	$tpp = 20;
	$page = max(1, intval($page));
	$start_limit = ($page - 1) * $tpp;
	if($id) {
		$tag = C::t('common_tag')->fetch_info($id);
	} else {
		if(!preg_match('/^([\x7f-\xff_-]|\w|\s)+$/', $name) || strlen($name) > 20) {
			showmessage('parameters_error');
		}
		$name = addslashes($name);
		$tag = C::t('common_tag')->fetch_info(0, $name);
	}

	if($tag['status'] == 1) {
		showmessage('tag_closed');
	}
	$tagname = $tag['tagname'];
	$id = $tag['tagid'];
	$searchtagname = $name;
	$navtitle = $tagname ? $taglang.' - '.$tagname : $taglang;
	$metakeywords = $tagname ? $taglang.' - '.$tagname : $taglang;
	$metadescription = $tagname ? $taglang.' - '.$tagname : $taglang;


	$showtype = '';
	$count = '';
	$summarylen = 300;

	if($type == 'thread') {
		$showtype = 'thread';
		$tidarray = $threadlist = array();
		$count = C::t('common_tagitem')->select($id, 0, 'tid', '', '', 0, 0, 0, 1);
		if($count) {
			$op = $_GET['op'];
			$shownum = 20;
			$tidarray = $threadlist = array();
			$curpage = isset($_GET['page']) ? intval($_GET['page']) : 1;
			if ($op == 'waterwall') {
				if ($curpage == 1) {
					$shownum = 18;
					$query = C::t('common_tagitem')->select($id, 0, 'tid', 'itemid', 'DESC', 0, $shownum);
					foreach($query as $result) {
						$tidarray[$result['itemid']] = $result['itemid'];
					}
					$threadlist = my_getthreadsbytids($tidarray);
				} else {
					$firstpage = 18;
					$page = max(1, intval($page));
					$start_limit = $firstpage + ($page - 2) * $pagesize;
					$count = C::t('common_tagitem')->select($id, 0, 'tid', '', '', 0, 0, 0, 1);
					if ($count > $firstpage) {
						$maxpage = @ceil(($count-$firstpage)/$pagesize) + 1;
					} else {
						$maxpage = 1;
					}
					$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
					if (($page + 1) > $maxpage) {
						echo json_encode(array('status' => '0'));
						die;
					} else {
						if ($count) {
							$query = C::t('common_tagitem')->select($id, 0, 'tid', 'itemid', 'DESC', $start_limit, $pagesize);
							foreach($query as $result) {
								$tidarray[$result['itemid']] = $result['itemid'];
							}
							$threadlist = my_getthreadsbytids($tidarray);
						}
						include_once template('extend/desktop/column');
						die;
					}
				}
			} else {
				$query = C::t('common_tagitem')->select($id, 0, 'tid', '', '', $start_limit, $tpp);
				foreach($query as $result) {
					$tidarray[$result['itemid']] = $result['itemid'];
				}
				$threadlist = getthreadsbytids($tidarray);
				$multipage = multi($count, $tpp, $page, "misc.php?mod=tag&id=$tag[tagid]&type=thread");
			}
		}
	} elseif($type == 'blog') {
		$showtype = 'blog';
		$blogidarray = $bloglist = array();
		$count = C::t('common_tagitem')->select($id, 0, 'blogid', '', '', 0, 0, 0, 1);
		if($count) {
			$query = C::t('common_tagitem')->select($id, 0, 'blogid', '', '', $start_limit, $tpp);
			foreach($query as $result) {
				$blogidarray[$result['itemid']] = $result['itemid'];
			}
			$bloglist = getblogbyid($blogidarray);

			$multipage = multi($count, $tpp, $page, "misc.php?mod=tag&id=$tag[tagid]&type=blog");
		}
	} else {
		$op = $_GET['op'];
		$shownum = 20;
		$tidarray = $threadlist = array();
		$curpage = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		if ($op == 'waterwall') {
			if ($curpage == 1) {
				$shownum = 18;
				$query = C::t('common_tagitem')->select($id, 0, 'tid', '', '', $shownum);
				foreach($query as $result) {
					$tidarray[$result['itemid']] = $result['itemid'];
				}
				$threadlist = my_getthreadsbytids($tidarray);
			} else {
				$firstpage = 18;
				$page = max(1, intval($page));
				$start_limit = ($page - 1) * $pagesize;				
				$count = C::t('common_tagitem')->select($id, 0, 'tid', '', '', 0, 0, 0, 1);
				if ($count > $firstpage) {
					$maxpage = @ceil(($count-$firstpage)/$pagesize) + 1;
				} else {
					$maxpage = 1;
				}
				$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
				if (($page + 1) > $maxpage) {
					echo json_encode(array('status' => '0'));
					die;
				} else {
					if ($count) {
						$query = C::t('common_tagitem')->select($id, 0, 'tid', '', '', $start_limit, $pagesize);
						foreach($query as $result) {
							$tidarray[$result['itemid']] = $result['itemid'];
						}
						$threadlist = my_getthreadsbytids($tidarray);
					}
					include_once template('extend/desktop/column');
					die;
				}
			}			
		} else {
			$query = C::t('common_tagitem')->select($id, 0, 'tid', '', '', $shownum);
			foreach($query as $result) {
				$tidarray[$result['itemid']] = $result['itemid'];
			}
			$threadlist = getthreadsbytids($tidarray);
		}

		if(helper_access::check_module('blog')) {
			$blogidarray = $bloglist = array();
			$query = C::t('common_tagitem')->select($id, 0, 'blogid', '', '', $shownum);
			foreach($query as $result) {
				$blogidarray[$result['itemid']] = $result['itemid'];
			}
			$bloglist = getblogbyid($blogidarray);
		}

	}	
	
	$nobbname = TRUE;
	$navtitle = $tagname.'_'.$_G['setting']['bbname'];
	$metakeywords = '5U体育'.$tagname;
	$metadescription = $tagname.'最新动态和新闻资讯';	
	
	if ($op == 'waterwall') {
		include_once template('extend/desktop/column');
	} else {
                                           //球员频道换成球队频道
                                            $a = $threadlist;                                         
                                            foreach ($threadlist as $k=>$v){
                                                 foreach(C::t('forum_forum')->my_fetch_all_name_by_fid($v['fid']) as $k1=>$v1){
                                                        if($v1['type'] == 'sub'){
                                                             foreach(C::t('forum_forum')->my_fetch_all_name_by_fid($v1['fup']) as $k2=>$v2){
                                                                 $a[$k]['forumname'] = $v2['name'];
                                                                 $a[$k]['fid'] = $v2['fid'];
                                                             }
                                                        }else{
                                                                $a[$k]['forumname'] = $v['forumname'];
                                                                $a[$k]['fid'] = $v['fid'];
                                                        }
                                               }
                                            }
                                           // echo "<pre>";
                                           // print_r($a);exit;
                                            include_once template('tag/tagitem');
	}
} else {
	$navtitle = $metakeywords = $metadescription = $taglang;
	$viewthreadtags = 100;
	$tagarray = array();
	$query = C::t('common_tag')->fetch_all_by_status(0, '', $viewthreadtags, 0, 0, 'DESC');
	foreach($query as $result) {
		$tagarray[] = $result;
	}
	
	$nobbname = TRUE;
	$navtitle = '5U体育标签_'.$_G['setting']['bbname'];
	$metakeywords = '5U体育,球队,球员';
	$metadescription = '体育相关标签，各球队球员最新帖子';
		
	include_once template('tag/tag');
}

function getthreadsbytids($tidarray) {
	global $_G;
	
	require_once libfile('function/extends');
	$threadlist = array();
	if(!empty($tidarray)) {
		loadcache('forums');
		include_once libfile('function_misc', 'function');
		$fids = array();
		foreach(C::t('forum_thread')->fetch_all_by_tid($tidarray) as $result) {
			if(!isset($_G['cache']['forums'][$result['fid']]['name'])) {
				$fids[$result['fid']] = $result['tid'];
			} else {
				$result['name'] = $_G['cache']['forums'][$result['fid']]['name'];
			}
			if ($result['attachment'] == 2) {
				$attachment = getattachment($result['tid'], 1);
				$result['cover'] = $attachment[0];
			} else {
				$result['cover'] = '';
			}
			if (get_member_info_by_uid($result['authorid'], 'gender') == 0) {
				$result['sex'] = 'secrecy';
			} elseif (get_member_info_by_uid($result['authorid'], 'gender') == 1) {
				$result['sex'] = 'man';
			} elseif (get_member_info_by_uid($result['authorid'], 'gender') == 2) {
				$result['sex'] = 'femen';
			}
			$threadlist[$result['tid']] = procthread($result);
		}
		if(!empty($fids)) {
			foreach(C::t('forum_forum')->fetch_all_by_fid(array_keys($fids)) as $fid => $forum) {
				$_G['cache']['forums'][$fid]['forumname'] = $forum['name'];
				$threadlist[$fids[$fid]]['forumname'] = $forum['name'];
			}
		}
	}
// 	var_dump($threadlist);die;
	return $threadlist;
}

function my_getthreadsbytids($tidarray) {
	global $_G;

	require_once libfile('function/extends');
	$threadlist = array();
	if(!empty($tidarray)) {
// 		loadcache('forums');
		include_once libfile('function_misc', 'function');
		require_once libfile('function/extends');
		$fids = $resultnew = array();
		
// 		$arr = C::t('forum_thread')->my_fetch_all_by_tid($tidarray, 0, 0, 'dateline');
// 		var_dump($arr);die;
		
		foreach(C::t('forum_thread')->my_fetch_all_by_tid($tidarray, 0, 0, 'dateline') as $result) {
			$resultnew['url'] = 'forum.php?mod=viewthread&tid='.$result['tid'];
			if ($result['attachment'] == 2) {
				$attachment = getattachment($result['tid'], 1);
				$resultnew['type'] = 2;
				$resultnew['imgUrl'] = $attachment[0];
			} elseif ($result['attachment'] == 3) {
				$resultnew['type'] = 3;
				$resultnew['videoUrl'] = getplayurlbytid($result['tid']);
			}else {
				$resultnew['imgUrl'] = $_G['style']['tpldir'].'/common/images/lanmu-default.png';
			}
			$resultnew['title'] = $result['subject'];
			$resultnew['authorThumbUrl'] = avatar($result['authorid'], 'small', 1);
			$resultnew['authorName'] = $result['author'];
			$resultnew['authorId'] = $result['authorid'];
// 			if(!isset($_G['cache']['forums'][$result['fid']]['name'])) {
// 				$fids[$result['fid']] = $result['tid'];
// 			} else {
// 				$result['name'] = $_G['cache']['forums'][$result['fid']]['name'];
// 			}
			
// 			$gender = get_member_info_by_uid($result['authorid'], 'gender');
// 			var_dump($gender);die;
			
			if (get_member_info_by_uid($result['authorid'], 'gender') == 0) {
				$resultnew['sex'] = 'secrecy';
			} elseif (get_member_info_by_uid($result['authorid'], 'gender') == 1) {
				$resultnew['sex'] = 'man';
			} elseif (get_member_info_by_uid($result['authorid'], 'gender') == 2) {
				$resultnew['sex'] = 'femen';
			}
			$resultnew['time'] = date('Y-m-d', $result['dateline']);
			$resultnew['replay'] = $result['replies'];
			
// 			var_dump($resultnew);die;
			$threadlist[$result['tid']] = $resultnew;
		}
// 		if(!empty($fids)) {
// 			foreach(C::t('forum_forum')->fetch_all_by_fid(array_keys($fids)) as $fid => $forum) {
// 				$_G['cache']['forums'][$fid]['forumname'] = $forum['name'];
// 				$threadlist[$fids[$fid]]['forumname'] = $forum['name'];
// 			}
// 		}
	}
// 	var_dump($threadlist);die;
	return $threadlist;
}

function getblogbyid($blogidarray) {
	global $_G;

	$bloglist = array();
	if(!empty($blogidarray)) {
		$data_blog = C::t('home_blog')->fetch_all($blogidarray, 'dateline', 'DESC');
		$data_blogfield = C::t('home_blogfield')->fetch_all($blogidarray);

		require_once libfile('function/spacecp');
		require_once libfile('function/home');
		$classarr = array();
		foreach($data_blog as $curblogid => $result) {
			$result = array_merge($result, (array)$data_blogfield[$curblogid]);
			$result['dateline'] = dgmdate($result['dateline']);
			$classarr = getclassarr($result['uid']);
			$result['classname'] = $classarr[$result[classid]]['classname'];
			if($result['friend'] == 4) {
				$result['message'] = $result['pic'] = '';
			} else {
				$result['message'] = getstr($result['message'], $summarylen, 0, 0, 0, -1);
			}
			$result['message'] = preg_replace("/&[a-z]+\;/i", '', $result['message']);
			if($result['pic']) {
				$result['pic'] = pic_cover_get($result['pic'], $result['picflag']);
			}
			$bloglist[] = $result;
		}
	}
	return $bloglist;
}

/**
 * 通过tid获取视频播放连接
 * @param number $tid
 * @return string
 */
function getplayurlbytid($tid = '') {
	$tableid = 0;
	$post = C::t('#extends#plugin_forum_post')->fetch_post_by_tid($tableid, $tid);
	$message = $post['message'];
	preg_match_all('/\[audio\](.*)\[\/audio\]/', $message, $audio);
	preg_match_all('/\[media.*\](.*)\[\/media\]/', $message, $media);
	preg_match_all('/\[flash.*\](.*)\[\/flash\]/', $message, $flash);
	
// 	var_dump($media);die;
	
	$audio_num = count($audio[1]);
	$media_num = count($media[1]);
	$flash_num = count($flash[1]);
	if ($audio_num > 0) {
		$url = $audio[1][0];
	} elseif ($media_num > 0) {
		$url = $media[1][0];
	} elseif ($flash_num > 0) {
		$url = $flash[1][0];
	}
	$url = str_replace(array('autoPlay=1', 'auto=1'), array('autoPlay=0', 'auto=0'), $url);
	return $url;
}
?>