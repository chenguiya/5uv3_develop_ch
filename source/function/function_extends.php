<?php
/**
 * 5U体育自定义函数
 */

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function getmessagebytid($tid, $typeid = 0) {
	if (empty($tid)) exit;
	$message = getthreadmessage($tid);
	switch ($typeid) {
		case 2:
			$images = getattachment($tid);
			$newmessage = '<p class="new_title">';
			if (!empty($message)) {
				$newmessage .=  $message . '<br>';
			}
			foreach ($images as $image) {
				$newmessage .=  '<a href="forum.php?mod=viewthread&do=tradeinfo&tid='.$tid.'" target="_blank"><img src="'.$image.'" width="auto" height="140" alt="" class="tn"></a>';;
			}			
			$newmessage .=  '</p>';
			break;
		
		case 3:
			$swf = create_video_html($tid);
			$newmessage = '<p class="new_title">';
			$newmessage .= '<embed src="'.$swf.'" quality="high" width="240" height="200" align="middle" allowScriptAccess="always" allowFullScreen="true" mode="transparent" type="application/x-shockwave-flash"></embed>';
			$newmessage .=  '</p>';
			break;
		
		default:
			$newmessage = '<p class="new_title">' . $message . '</p>';
			break;
	}
	echo $newmessage;
}

/**
 * 通过主题id获取附件列表
 * @param int $tid
 * @return array $images
 */
function getattachment($tid, $num = 5, $bln_get_aid = FALSE) {	
	$attachments = $attachment = $images = array();
	$pid = get_pid_by_tid($tid);
	$attachments = DB::fetch_all("SELECT * FROM ".DB::table('forum_attachment')." WHERE tid=".$tid." AND pid=".$pid);
    if (count($attachments) > $num) {
		for ($i = 0; $i < $num; $i++) {
			if($bln_get_aid) // 是否只是取AID
			{
				$attachment = C::t('forum_attachment_n')->fetch($attachments[$i]['tableid'], $attachments[$i]['aid']);
				$extension = strtolower(fileext($attachment['attachment']));
				if ($extension == 'gif') {
					$images = array();
					$images[] = $attachments[$i]['aid'];
					//return $images;
				} else {
					$images[] = $attachments[$i]['aid'];
				}
			}
			else
			{
				$attachment = C::t('forum_attachment_n')->fetch($attachments[$i]['tableid'], $attachments[$i]['aid']);				
				$images[] = 'data/attachment/forum/' . $attachment['attachment'];
			}
		}
	} else {
		foreach ($attachments as $val) {
			if($bln_get_aid) // 是否只是取AID
			{
                $attachment = C::t('forum_attachment_n')->fetch($val['tableid'], $val['aid']);
				$extension = strtolower(fileext($attachment['attachment']));
				if ($extension == 'gif') {
                    
					$images = array();
					$images[] = $val['aid'];
					//return $images;
				} else {
					$images[] = $val['aid'];
				}				
			}
			else
			{				
				$attachment = C::t('forum_attachment_n')->fetch($val['tableid'], $val['aid']);				
				$images[] = 'data/attachment/forum/' . $attachment['attachment'];
			}
		}
	}
	
	return $images;
}

/**
 * wap通过主题id获取附件列表
 * @param int $tid
 * @return array $images
 */
function wap_getattachment($tid, $num = 3) {
	$attachments = $attachment = $images = array();
	$pid = get_pid_by_tid($tid);
	$attachments = DB::fetch_all("SELECT aid FROM ".DB::table('forum_attachment')." WHERE tid=".$tid.' AND pid='.$pid.' LIMIT '.$num);
	foreach ($attachments as $key => $value) {
		$aids[$key] = $value['aid'];
	}
	return $aids;
}

function get_pid_by_tid($tid) {
	//通过tid获取帖子主题的pid以获取主题的附件
	$thread = DB::fetch_first("SELECT pid FROM ".DB::table('forum_post')." WHERE tid=".$tid." AND first=1");
	return intval($thread['pid']);
}

/**
 * 通过主题id获取附件列表
 * @param int $tid
 * @return array $images
 */
function getallattachment($tid = 0) {
	global $_G;
	$siteurl = $_G['setting']['attachurl'];
	$attachments = $attachment = $images = array();
	$attachments = DB::fetch_all("SELECT * FROM ".DB::table('forum_attachment')." WHERE tid=".$tid);
		
	foreach ($attachments as $val) {
		$attachment = C::t('forum_attachment_n')->fetch($val['tableid'], $val['aid']);
		$images[$val['aid']] = '<div align="center"><img src="' . $siteurl . 'forum/' . $attachment['attachment'] . '" /></div><br />';
	}

	return $images;
}
/**
 * 把帖子内容转成html格式
 * @param string $message
 * @param array $replace
 * @return mixed
 */
function changemessagetohtml($message = '', $replace = array()) {
	preg_match_all('/\[attach\](\d+)\[\/attach\]/i', $message, $matches);
	$search = $matches[0];
	$return = str_replace($search, $replace, $message);
	return $return;
}

/**
 * 通过主题id获取帖子的内容
 * @param int $tid
 */
function getthreadmessage($tid) {
	$result = $message = array();
	$result = DB::fetch_first("SELECT message FROM ".DB::table('forum_post')." WHERE tid=".$tid." AND first=1");
	$message = $result['message'];
	$pattern = array(
		'/\[\w+\]\w+\[\/\w+\]/i',
		'/\[img=(.*?)\](.*?)\[\/\w+\]/i',
		'/\[\/\w+\]/i',
		'/\[(.*?)\]/i',
	);
	$message = preg_replace($pattern, '', $message);
	$message = cutstr($message, 260, '...');
	return trim($message);
}
/**
 * 构造视频播放地址
 * @param int $tid
 * @return string
 */
function create_video($tid) {
	$matches = array();
	$postinfo = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." WHERE tid=".$tid." AND first=1");
	$pattern = '/\[media=swf,(?P<width>\d+),(?P<height>\d*)\](?P<playurl>.*)\[\/media\]/';

	preg_match($pattern, $postinfo['message'], $matches);

	return $matches['playurl'];

}

/**
 * 判断会员/版块/群组(球迷会)是否有对应的权益, uid 会员id、fid 版块id、gid 群组id
 * @param int $rightsid
 * @param int $id
 * @param string $idtype
 * @return boolean
 */
function userrightsperm($rightsid, $id) {
	global $_G;
	$uid = isset($id) ? intval($id) : (isset($_G['uid']) ? intval($_G['uid']) : exit);
	$rights = C::t('#rights#plugin_rights')->fetch_rights_by_id($rightsid);
	if ($rights['regdate'] == 0 && $rights['canceldate'] == 0) {
		if (C::t('#rights#plugin_member_rights')->fetch_rights_buy($uid, $rightsid)) {
			return true;
		} else {
			return false;
		}
	} else {
		if (TIMESTAMP > $rights['canceldate'] || TIMESTAMP < $rights['regdate']) {
			return false;
		} else {
			if (C::t('#rights#plugin_member_rights')->fetch_rights_buy($uid, $rightsid)){
				return true;
			} else {
				return false;
			}
		}
	}
}

//以下为支付宝支付处理流程
/**
 * 生成支付url和订单号
 * @param string $orderid
 * @param array $data
 * @return string
 */
function creatpayurlorderid(&$orderid, $data = array()) {
// 	var_dump($data);die;
	// $orderid = dgmdate(TIMESTAMP, 'YmdHis').random(18);
    $orderid = 'QY'.dgmdate(TIMESTAMP, 'YmdHis').random(16);
	$ags = array(
		'subject'		=> $data['subject'],
		'body'			=> $data['body'],
		'service'		=> 'trade_create_by_buyer',
		'partner'		=> DISCUZ_PARTNER,
		'notify_url'	=> $data['notify_url'],
		'return_url'	=> $data['return_url'],
		'show_url'		=> $data['show_url'],
		'_input_charset'	=> CHARSET,
		'out_trade_no'	=> $orderid,
		'price'			=> $data['price'],
		'quantity'		=> 1,
		'seller_email'	=> $data['seller_email'],
		'extend_param'	=> 'isv^dz11'
	);
	
	if (DISCUZ_DIRECTPAY) {
		$args['service'] = 'create_direct_pay_by_user';
		$args['payment_type'] = '1';
	} else {
		$args['logistics_type'] = 'EXPRESS';
		$args['logistics_fee'] = 0;
		$args['logistics_payment'] = 'SELLER_PAY';
		$args['payment_type'] = 1;
	}
	return trade_returnurl($args);
}
function paydirect($data = array()) {
	global $_G;
// 	var_dump($data);die;
	if ($data['seller_email'] != '') {		//设置了支付宝账号
		require_once libfile('function/trade');
		$orderid = '';
		$requesturl = creatpayurlorderid($orderid, $data);
		if (!empty($orderid)) {
			// 写 forum_order 表，系统自带的
			$arr_data = array();
			$arr_data['orderid'] = $orderid;
			$arr_data['status'] = '1';
			$arr_data['buyer'] = $data['username'];
			$arr_data['uid'] = $data['uid'];
			$arr_data['amount'] = '1';
			$arr_data['price'] = $data['price'];
			$arr_data['submitdate'] = $data['timestamp'];
			$arr_data['email'] = $data['email'];
			$arr_data['ip'] = $data['clientip'];
			C::t('forum_order')->insert($arr_data);
			
			// 写 pre_plugin_ucharge_log 表，自定义的
			$arr_data = array();
			$arr_data['orderid'] = $orderid;
			$arr_data['status'] = '1';
			$arr_data['log_time'] = $data['timestamp'];
			$arr_data['amount'] = '1';
			$arr_data['price'] = $data['price'];
			$arr_data['uid'] = $data['uid'];
			$arr_data['fid'] = $data['fid'];
			$arr_data['charge_type'] = $data['charge_type'];
			$arr_data['username'] = $data['username'];
			$arr_data['email'] = $data['email'];
			$arr_data['ip'] = $data['clientip'];
			$arr_data['subject'] = $data['subject'];
			$arr_data['body'] = $data['body'];
			$arr_data['seller_email'] = $data['seller_email'];;
			$arr_data['notify_url'] = $data['notify_url'];
			$arr_data['return_url'] = $data['return_url'];
			$arr_data['show_url'] = $data['show_url'];
            $arr_data['bill_type'] = '3'; // 2015-09-18 多增加一类型 1:积分充值 2:VIP充值 3:权益购买
			C::t('#ucharge#plugin_ucharge_log')->insert($arr_data);
			
			echo '<input type="hidden" id="my_data" name="my_data" value="'.$orderid.'-'.$arr_data['price'].'-'.$arr_data['uid'].'"">';
			echo '<form id="payform" action="'.$requesturl.'" method="post"></form>';
			echo '<script>setTimeout(function(){document.getElementById("payform").submit();}, 500);</script>';
		}
	}
}
/**
 * 判断群组/会员是否有此权益
 * @param int $rightsid		权益id
 * @param int $uid			用户/群组id
 * @param string $idtype	$uid类型
 * @return boolean
 */
function checkmemberrights($rightsid, $uid, $idtype = 'uid') {
	$ownrights = getallrightsbyuid($uid, $idtype);
	if (in_array($rightsid, $ownrights)) {
		return true;
	} else {
		return false;
	}
}
/**
 * 通过用户id获取用过的所有权益(指的是生效的权益)
 * @param int $uid
 * @param string $idtype
 * @return array $ownrights
 */
function getallrightsbyuid($uid, $idtype = 'uid') {
	$ownrights = $ownrightsids = array();
	foreach ($ownrightsids = DB::fetch_all("SELECT `rightsid`,`dateline` FROM ".DB::table('plugin_member_rights')." WHERE `idtype`='".$idtype."' AND `uid`=".$uid) as $key => $val) {
		if (rightsisenable($val['rightsid'], $val['dateline'])) {
			$ownrights[] = $val['rightsid'];
		}
	}
	
	return $ownrights;
}
/**
 * 判断权益是否有效
 * @param int $rightsid
 * @param int $buytime
 * @return boolean
 */
function rightsisenable($rightsid, $buytime) {
	//获取权益的详细信息
	$rights = C::t('#rights#plugin_rights')->fetch_rights_by_id($rightsid);
//	根据是权益的时效类型做不同处理，此处2期处理
// 	switch ($rights['aging']) {
// 		case value:
// 		;
// 		break;
		
// 		default:
// 			;
// 		break;
// 	}
	//权益是否启用
	if ($rights['available'] == 0) return false;
	//判断权益是否在有效期
	if ($rights['regdate'] > TIMESTAMP || $rights['canceldate'] < TIMESTAMP) return false;
	else return true;
}
/**
 * 获取分区id
 * @param int $fid
 * @return int $groupid
 */
function getgroup($fid) {
	$forum = DB::fetch_first("SELECT `fup`,`type` FROM ".DB::table('forum_forum')." WHERE `fid`=".$fid);
	switch ($forum['type']) {
		case 'forum':
		$groupid = $forum[`fup`];
		break;
		
		case 'sub':
		$group = DB::fetch_first("SELECT `fup`,`type` FROM ".DB::table('forum_forum')." WHERE `fid`=".$forum['fup']);
		$groupid = $group['fup'];
		break;
		
		default:
		$groupid = $fid;
		break;
	}
	return $groupid;
}
/**
 * 加密显示字段
 * @param string $str
 * @param number $start
 * @param number $last
 * @param string $replacechar
 * @return boolean|mixed
 */
function encryptionDisplay($str = '', $start = 3, $last = 4, $replacechar = '*') {
	if (($strlength = strlen($str)) < 1) return false;
	$replacestr = '';
	if (($replacelen = $strlength - $start - $last) < 1) return false;
	for ($i = 0; $i < $replacelen; $i++) {
		$replacestr .= $replacechar;
	}
	$returnstr = substr_replace($str, $replacestr, $start, $replacelen);
	return $returnstr;
}
/**
 * 自定面包屑导航
 * @param array $forum
 * @return string|multitype:string unknown Ambigous <multitype:, unknown>
 */
function get_groupnav_new($forum) {
	global $_G;
	if(empty($forum) || empty($forum['fid']) || empty($forum['name'])) {
		return '';
	}
	loadcache('grouptype');
	$groupnav = '';
	if($forum['type'] == 'sub') {
		$mod_action = $_GET['mod'] == 'forumdisplay' || $_GET['mod'] == 'viewthread' ? 'mod=forumdisplay&action=list' : 'mod=group';
		//$groupnav .= '<em>&rsaquo;</em><a href="forum.php?'.$mod_action.'&fid='.$forum['fid'].'">'.$forum['name'].'球迷会</a>';
		// zhnagjh 2015-06-25 修改
		$groupnav .= '<em>&rsaquo;</em><a href="fans/topic/'.$forum['fid'].'/" target="_blank">'.$forum['name'].'球迷会</a>';
	}
	return array('nav' => $groupnav);
}

/**
 * 帖子详细页显示马甲(只是清理一下缓存而已) by zhangjh 2015-06-26
 * @param int $tid
 * @return void
 */
function set_mask_in_thread($tid)
{
	C::t('forum_thread')->clear_cache($tid);
}

/**
 * 用户发帖后一下帖子列表缓存 by zhangjh 2015-08-26
 * @param int $tid
 * @return void
 */
function clear_threadlist_cache()
{
	$mem_check = memory('check'); // 先检查缓存是否生效
	if($mem_check != '')
	{
		// 足球圈
		memory('rm', 'int_circle_football_threadlist_count');
		$int_maxpage = memory('get', 'int_circle_football_threadlist_maxpage');
		for($i = 1; $i < $int_maxpage; $i++)
		{
			memory('rm', 'arr_circle_football_threadlist_'.$i);
		}
		memory('rm', 'int_circle_football_threadlist_maxpage');
		
		// 篮球圈
		memory('rm', 'int_circle_basket_threadlist_count');
		$int_maxpage = memory('get', 'int_circle_basket_threadlist_maxpage');
		for($i = 1; $i < $int_maxpage; $i++)
		{
			memory('rm', 'arr_circle_basket_threadlist_'.$i);
		}
		memory('rm', 'int_circle_basket_threadlist_maxpage');
	}
}

/**
 * 不显示省市
 * @param string $str
 * @return string
 */
function replace_special_str($str = '') {
	return str_replace(array('市', '省', '自治区', '特别行政区', '回族', '壮族', '维吾尔', '土家族', '苗族', '自治州'), '', $str);
}
/**
 * 清楚所有空格
 * @param string $str
 * @return string
 */
function trimall($str = '') {
	$str = trim($str); //清除字符串两边的空格
	$str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
	$str = preg_replace("/\r\n/","",$str); 
	$str = preg_replace("/\r/","",$str); 
	$str = preg_replace("/\n/","",$str); 
	$str = preg_replace("/ /","",$str);
	$str = preg_replace("/  /","",$str);  //匹配html中的空格
	return trim($str); //返回字符串;
}

function get_fansclub_area($fid) {
	global $_G;
	$arr = array();
	$arr = C::t('#fansclub#plugin_fansclub_info')->fetch($fid);
	$str = trimall($arr['province_city']);
	$str = replace_special_str($str);
	return $str;
}

/**
 * 获取帖子内容
 * @param unknown $tableid
 * @param unknown $tid
 * @return boolean|unknown
 */
function get_message($tableid, $tid) {
	global $_G;
	$tableid = intval($tableid);
	$tid = intval($tid);
	$content = $match = $img = $audio = $media = array();
	$thread = C::t('forum_thread')->fetch($tid);
	$post = C::t('#extends#plugin_forum_post')->fetch_post_by_tid($tableid, $tid);
	$message = $post['message'];
	
	if ($thread['attachment'] == '2') {
		$attach = getattachment($tid, 5, true);
        
		foreach ($attach as $key=>$vo) {
            // $img[$key] = $vo;
            // $img[$key] = $_G['setting']['attachurl'].substr($vo, 16); // zhangjh 2015-10-08 modify
            $img[$key] = getforumimg($vo, 0, 280, 280, 2); // zhangjh 2015-10-08 modify
            
		}
	} else {
        preg_match_all('/\[img\](.*?)\[\/img\]/', $message, $match);
        
        if(count($match[1]) == 0)
        {
            preg_match_all('/\[img(.*)\](.*?)\[\/img\]/', $message, $match);
            $match[1] = $match[2];
            unset($match[2]);
        }

		preg_match_all('/\[attach\]([0-9]*)?\[\/attach\]/', $message, $match2);
		foreach ($match2[1] as $key=>$vo) {
			$aid = intval($vo);
			if (getattachtablebyaid($aid) == 'forum_attachment_unused') {
				return FALSE;
			} else {
				$attach = DB::fetch_first("SELECT tid,filename,attachment FROM ".DB::table(getattachtablebyaid($aid))." WHERE aid='$aid'");
                $img[$key] = $_G['setting']['attachurl'].'forum/'.$attach['attachment'];
			}
		}
	}
    
    preg_match_all('/\[audio\](.*)\[\/audio\]/', $message, $audio);
	preg_match_all('/\[media.*\](.*)\[\/media\]/', $message, $media);
    preg_match_all('/\[flash.*\](.*)\[\/flash\]/', $message, $flash); // add by zhangjh 2015-10-10

    $video_url = '';
	$media_num = count($media[1]);
	if ($media_num > 0) {
		$content['media'] = $media[1][0];
        $video_url = $content['media'];
	} else {
		$audio_num = count($audio[1]);
		if ($audio_num > 0) {
			$content['audio'] = $audio[1][0];
            $video_url = $content['audio'];
		} else {
            
            $flash_num = count($flash[1]);
            if($flash_num > 0)
            {
                $content['flash'] = $flash[1][0];
                $video_url = $content['flash'];
            }
            else
            {
                $img_num = count($match[1]) + count($img);
                
                if ($img_num > 3) {
                    for ($i = 0; $i < 3; $i++) {
                        if ($i < count($match[1])) {
                            $content['img'][$i] = $match[1][$i];
                        } else {
                            $content['img'][$i] = $img[$i-count($match[1])];
                        }
                    }
                } else {
                    for ($i = 0; $i < $img_num; $i++) {
                        if ($i < count($match[1])) {
                            $content['img'][$i] = $match[1][$i];
                        } else {
                            $content['img'][$i] = $img[$i-count($match[1])];
                        }
                    }
                }
            }
		}
	}
	
    // 	echo $message;die;
	
	$message = preg_replace('/\[i.*\].*?\[\/i\]/', '', $message);
	$message = preg_replace('/\[img\].*?\[\/img\]/', '', $message);
    $message = preg_replace('/\[img(.*)\](.*)\[\/img\]/', '', $message);
	$message = preg_replace('/\[attach\].*?\[\/attach\]/', '', $message);
	$message = preg_replace('/\[audio\].*?\[\/audio\]/', '', $message);
	$message = preg_replace('/\[media.*\].*?\[\/media\]/', '', $message);
    $message = preg_replace('/\[flash.*\].*?\[\/flash\]/', '', $message);
	$message = preg_replace('/\[.*?\]/', '', $message);
	$message = preg_replace("/\s+/", "", $message);
	$message = str_cut($message, 230, '...');
	$content['message'] = trimall($message);
    
    // 处理视屏图片
    if($video_url != '')
    {
        $img_num = count($match[1]) + count($img);
        if($img_num > 0) // 有图片取图片，没有的取视频截图
        {
            if ($img_num > 3) {
                for ($i = 0; $i < 3; $i++) {
                    if ($i < count($match[1])) {
                        $content['img'][$i] = $match[1][$i];
                    } else {
                        $content['img'][$i] = $img[$i-count($match[1])];
                    }
                }
            } else {
                for ($i = 0; $i < $img_num; $i++) {
                    if ($i < count($match[1])) {
                        $content['img'][$i] = $match[1][$i];
                    } else {
                        $content['img'][$i] = $img[$i-count($match[1])];
                    }
                }
            }
        }
        else
        {
            include_once DISCUZ_ROOT.'./source/plugin/fansclub/extend/videoapi.php';
            $video = new VideoApi();
            $video_pic = $video->parse($video_url, false, $tid, $_G);
            if(is_array($video_pic))
            {
                $content['img'][0] = $video_pic['img'];
            }
        }
    }
    
	return $content;
}

function str_cut($string, $length, $dot = '...') {
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8') {
		$length = intval($length-strlen($dot)-$length/3);
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut.$dot;
}

/**
 * 根据频道fid获取频道的资料
 * @param number $fid
 * @return multitype:
 */
function get_team_info_by_fid($fid = 0) {
	global $_G;
	include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
	$teaminfo = array();
	$teamid = fansclub_get_team_player_id($fid);
// 	var_dump($teamid);die;
	$url = "http://api.5usport.com/v3/v3_api/get_info/getTeamById/SDsFJO4dS3D4dF64SDF46?id=".$teamid;
	$team = _get_apidata($url);
	$url = "http://api.5usport.com/v3/v3_api/get_info/getScoreboardByTeamidLeagueid/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid."&league_id=".$team->league_id;
	$result = _get_apidata($url);
	$url = "http://api.5usport.com/v3/v3_api/get_info/getSodaCoachByTeamId/SDsFJO4dS3D4dF64SDF46?id=".$teamid."&id_type=tencent_id";
	$coach = _get_apidata($url);
	$result->coach = $coach->name;
	$result = objectToArray($result);
	return $result;
}

function get_match($league_name = '', $team_name = '') {
		//本场比赛
		$url1 = 'http://api.5usport.com/v3/to_v3/phpcms/get_match?league_name='.urlencode($league_name).'&team_name='.urlencode($team_name).'&match_time=&go=&sign=fb574b29f55486b2c8584e7978b0ea88';//本场比赛获取接口
		$data = file_get_contents($url1);
		$data = json_decode($data);
		$current_match = $data->match;
		
		/* zhangjh 2015-08-12 只要一场就可以
		//上一场比赛
		$url2 = 'http://api.5usport.com/v3/to_v3/phpcms/get_match?league_name='.urlencode($league_name).'&team_name='.urlencode($team_name).'&match_time='.$current_match[0]->match_time.'&go=-1&sign=fb574b29f55486b2c8584e7978b0ea88';
		$data2 = file_get_contents($url2);		
		$data2 = json_decode($data2);
		$up_match = $data2->match;
		$data->match[1] = $up_match[0];
		//下一场比赛
		$url3 = 'http://api.5usport.com/v3/to_v3/phpcms/get_match?league_name='.urlencode($league_name).'&team_name='.urlencode($team_name).'&match_time='.$current_match[0]->match_time.'&go=1&sign=fb574b29f55486b2c8584e7978b0ea88';
		$data3 = file_get_contents($url3);
		$data3 = json_decode($data3);
		$down_match = $data3->match[0];
		$data->match[2] = $down_match;
		*/
		$data = objectToArray($data);
// 		$march = $data->match;
// 		var_dump($data);die;
		return $data['match'];
}

function get_logo_by_name($name = '') {
	global $_G;
	$fid = C::t('forum_forum')->fetch_forum_fid_by_name($name);	
	$forumfields = C::t('forum_forum')->fetch_info_by_fid($fid);
	return $forumfields['icon'];	
}

function _get_apidata($url = '', $return='array'){
	$data = file_get_contents($url);
	if($return=='array'){
		$data = json_decode($data);
		$data  =  $data->content;
	}
	return $data;
}

function objectToArray($e){
	$e=(array)$e;
	foreach($e as $k=>$v){
		if( gettype($v)=='resource' ) return;
		if( gettype($v)=='object' || gettype($v)=='array' )
			$e[$k]=(array)objectToArray($v);
	}
	return $e;
}

function follow_check($followuid) {
	global $_G;
	
	if (empty($_G['uid'])) return false;
	$followed = C::t('home_follow')->fetch_by_uid_followuid($_G['uid'], $followuid);
	if ($followed) {
		return true;
	} else {
		return false;
	}
}
/**
 * 是否通过认证
 * @param number $uid
 * @param number $key
 */
function is_verify($uid, $key = 1) {
	if ($result = C::t('common_member_verify')->fetch_verify_item($uid, $key)) {
		return true;
	} else {
		return false;
	}
}

function get_member_info_by_uid($uid, $fields = '') {
	$result = DB::fetch_first("SELECT ".$fields." FROM ".DB::table('common_member_profile')." WHERE uid=".$uid);
	return $result[$fields];
}
