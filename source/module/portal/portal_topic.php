<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: portal_topic.php 33660 2013-07-29 07:51:05Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['diy']=='yes' && !$_G['group']['allowaddtopic'] && !$_G['group']['allowmanagetopic']) {
	$_GET['diy'] = '';
	showmessage('topic_edit_nopermission');
}

$topicid = $_GET['topicid'] ? intval($_GET['topicid']) : 0;

if($topicid) {
	$topic = C::t('portal_topic')->fetch($topicid);
} elseif($_GET['topic']) {
	$topic = C::t('portal_topic')->fetch_by_name($_GET['topic']);
}

if(empty($topic)) {
	showmessage('topic_not_exist');
}

if($topic['closed'] && !$_G['group']['allowmanagetopic'] && !($topic['uid'] == $_G['uid'] && $_G['group']['allowaddtopic'])) {
	showmessage('topic_is_closed');
}

if($_GET['diy'] == 'yes' && $topic['uid'] != $_G['uid'] && !$_G['group']['allowmanagetopic']) {
	$_GET['diy'] = '';
	showmessage('topic_edit_nopermission');
}

if(!empty($_G['setting']['makehtml']['flag']) && $topic['htmlmade'] && !isset($_G['makehtml']) && empty($_GET['diy'])) {
	dheader('location:'.fetch_topic_url($topic));
}

$topicid = intval($topic['topicid']);

C::t('portal_topic')->increase($topicid, array('viewnum' => 1));
if($topicid == 5){
    $navtitle = "2015曼联vs曼城同城德比-曼彻斯特德比的恩怨历史-5U体育";
    $metadescription = empty($topic['summary']) ? "2015赛季曼彻斯特德比专题，英超双雄曼联与曼城德比的恩怨历史，回顾曼市德比中的经典战役，全方位分析两队球员、战术、教练等，让你全新了解曼切斯特德比": $topic['summary'];
    $metakeywords =  empty($topic['keyword']) ? "曼彻斯特,曼市德比,曼联,曼城,同城德比" : $topic['keyword'];
}else{
    $navtitle = empty($topic['title']) ? $topic['title'] : $topic['title'] ;
    $metadescription = empty($topic['summary']) ? $topic['summary'] : $topic['summary'];
    $metakeywords = empty($topic['keyword']) ? $topic['keyword'] : $topic['keyword'];
}


$attachtags = $aimgs = array();

list($seccodecheck, $secqaacheck) = seccheck('publish');

if(isset($_G['makehtml'])) {
	helper_makehtml::portal_topic($topic);
}

$file = 'portal/portal_topic_content:'.$topicid;
$tpldirectory = '';
$primaltplname = $topic['primaltplname'];
if(strpos($primaltplname, ':') !== false) {
	list($tpldirectory, $primaltplname) = explode(':', $primaltplname);
}
$topicurl = fetch_topic_url($topic);

$data_a = get_pic_by_tid(57600,1);
$data_b = get_pic_by_tid(57374,1);
$data_c = get_pic_by_tid(57348,1);
$data_d = get_pic_by_tid(57357,1);
$data_e = get_pic_by_tid(57619,1);
//曼联帖子
$data_f = get_pic_by_tid(58230);
$data_g = get_pic_by_tid(58144);
$data_h = get_pic_by_tid(58085);
$data_i = get_pic_by_tid(58018);
//曼城帖子
$data_j = get_pic_by_tid(58319);
$data_k = get_pic_by_tid(58094);
$data_l = get_pic_by_tid(57986);
$data_m = get_pic_by_tid(57963);
//曼联
$data_n = get_pic_by_tid(58079);
$data_o = get_pic_by_tid(58099);
//曼城
$data_p = get_pic_by_tid(57988);
$data_q = get_pic_by_tid(58141);

//var_dump($data_a);exit;
if($_GET['action']){
        if($_G['uid'] != 0){ 
            if($_GET['action'] == 'like'){
                like_ding_cai($_G['uid'],1);
            }elseif($_GET['action'] == 'unlike'){
                like_ding_cai($_G['uid'],0);
            }        
            $like_num = user_count('like')+404;
            $unlike_num = user_count('unlike')+358;

            $arr['success']=1;
            $arr['like'] = $like_num;
            $arr['unlike'] = $unlike_num;
            $like_percent = round($like_num/($like_num+$unlike_num),2);
            $arr['like_percent'] = $like_percent;
            $arr['unlike_percent'] = (1-$like_percent);
           // echo "<pre>";
            //print_r($arr);exit;
            //var_dump(json_encode($arr));exit;
            echo json_encode($arr);exit;
    }else{
              $arr['success'] = 0;
              $arr['message'] = "你还没有登录";
              echo json_encode($arr);exit;
    }
}

            $like_num = user_count('like')+404;
            $unlike_num = user_count('unlike')+358;

            $percent = round($like_num/($like_num+$unlike_num),2);
            $upercent = round($unlike_num/($like_num+$unlike_num),2);
            $like_percent= 100*$percent.'%';
            $unlike_percent =100* ($upercent).'%';
            $player['like'] = get_player_by_tid(58347);
            foreach ($player['like'] as $v){
                if($v['votes'] != 0){
                    $like_player += $v['votes'];
                }
            }
            foreach ($player['like'] as $key=>$val){
                $player['like'][$key]['percent'] = round($val['votes']/$like_player,2);
            }
            
            $player['unlike'] = get_player_by_tid(58349);
             foreach ($player['unlike'] as $v){
                if($v['votes'] != 0){
                    $unlike_player += $v['votes'];
                }
            }
            foreach ($player['unlike'] as $key=>$val){
                $player['unlike'][$key]['percent'] = round($val['votes']/$unlike_player,2);
            }
           // echo "<pre>";
            //print_r($player);exit;
if(check_wap()) // 2015-10-20 zhangjh wap设置也可以访问
{
    
    $touch_file = DISCUZ_ROOT.$tpldirectory.'/touch/'.$primaltplname.'.htm';
    if(file_exists($touch_file))
    {
        //echo template('touch/'.$primaltplname);exit;
        include template('touch/'.$primaltplname);
    }
    else
    {
        die('wap file:'.$tpldirectory.'/touch/'.$primaltplname.'.htm 不存在');
    }
}
else
{            
   include template('diy:'.$file, NULL, $tpldirectory, NULL, $primaltplname);
}

function portaltopicgetcomment($topcid, $limit = 20, $start = 0) {
	global $_G;
	$topcid = intval($topcid);
	$limit = intval($limit);
	$start = intval($start);
	$data = array();
	if($topcid) {
		$query = C::t('portal_comment')->fetch_all_by_id_idtype($topcid, 'topicid', 'dateline', 'DESC', $start, $limit);
		foreach($query as $value) {
			if($value['status'] == 0 || $value['uid'] == $_G['uid'] || $_G['adminid'] == 1) {
				$data[$value['cid']] = $value;
			}
		}
	}
	return $data;
}

//专题顶或踩
function like_ding_cai($uid,$type){
    if(!user_played($uid)){
                $query = C::t('portal_topic_five')->insert_type_by_uid($uid,$type);
                return $query ? true : false;
    }
}

//判断用户是否已经顶或踩过
function user_played($uid){
    global $_G;
    $uid = intval($uid);
    if($uid){
        $query = C::t('portal_topic_five')->fetch_all_by_uid($uid);
        return !empty($query) ?  true : false ;
    }
    return false;
}

function user_count($action){
    $action = trim($action);
    if($action){
         $query = C::t('portal_topic_five')->count_ding_cai($action);
          return $query[0]['count(*)'];
    }
}

//取球员数据
function get_player_by_tid($tid){
    $tid = intval($tid);
    if($tid){
        $query = C::t('forum_polloption')->fetch_all_by_tid($tid);
        return $query;
    }
}

//取帖子图片
function get_pic_by_tid($tid, $is_org = 0){
    global $_G;
        $tid = intval($tid);
    if($tid){        
        $title = C::t('forum_thread')->fetch($tid);        

        $query = C::t('forum_attachment')->fetch_all_by_tid($tid);        

        $tableid = $query[0]['tableid'];
        $res = C::t('forum_attachment_n')->fetch_all_by_tid($tableid,$tid);

       //  foreach ($title as $key=>$val){
       //             $data['subject'] = $val['subject'];  
       //     }    
            //$res[0]['aid'];
            $data['tid'] = $res[0]['tid'];
           // $data['attachment'] = $res[0]['attachment'];
           if($is_org == 1)
           {
               $data['attachment'] = $_G['setting']['attachurl'].'forum/'.$res[0]['attachment'];
           }
           else
           {
            $data['attachment'] = getforumimg($res[0]['aid'], 0, 420, 280, 1);
           }
//print_r($data['attachment']);exit;
            $data['subject'] = $title['subject'];

           return $data;
    }
}
?>