<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

// 大事记
// eg http://192.168.2.169/discuz/plugin.php?id=fansclub&action=event&fid=80
require_once libfile('function/extends');
include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';
$fid = $_GET['fid'] ? $_GET['fid'] + 0 : 0;
foreach ($_G['forum']['moderators'] as $key =>$value){
       $uid = $key;
}
if(defined('IN_MOBILE')){
        $wap_bigevent = array();  
        $wap_bigevent = wap_get_big_event($fid);
}  elseif($_GET['type'] == 'read') {
    
        $title = trim($_POST['title']) ? trim($_POST['title']) : '' ;
        //时间
        $event = array();
        $event = !empty($_POST['eventtimefrom']) ? $_POST['eventtimefrom'] : '' ;
        $log_time = strtotime($event[0]);
       
        $path = trim($_POST['path']) ? trim($_POST['path']) : '' ;
        
        $arr = add_big_event($fid,$uid,$title,$log_time,$path) ;
       
}else{
        $fid_info = C::t('forum_forum')->fetch_info_by_fid($fid);
        if(count($fid_info) == 0)
        {
                showmessage('球迷会不存在', 'plugin.php?id=fansclub:fansclub');
        }

        $fansclub_name = $fid_info['name'];

        $arr_year_split = array(); // 年分割点
        $arr_big_event = get_big_event($fid, $arr_year_split);
        //活动
        $arr_event_activity = wap_get_big_event($fid);
        //$a[] = $arr_big_event[count($arr_big_event)- 1];
        
        $arr_ = get_add_big_event($fid);
        if(!empty($arr_)){
                $arr_add = array();             
                foreach ($arr_ as $k=>$v){
                    if($v['title'] != ''){
                        $arr_add[$k]['time'] = date("Y.m.d",$v['log_time']);
                        $arr_add[$k]['title'] = $v['title'];
                        $arr_add[$k]['url'] = $v['url'];
                        $arr_add[$k]['log_time'] = $v['log_time'];
                     }else{
                         continue;
                    }
                }

                //$a_add = array();
               //foreach ($a as $key=>$val){
                   
                 //  $val['time'] = strtr($val['time'], ".", "-");
                //   $a_add[$key]['log_time'] = strtotime($val['time']);
                //   $a_add[$key]['title'] = $val['title'];
                //   $a_add[$key]['time'] = $val['time'];
            //   },$a_add
                    $new_arr = array_merge ($arr_add,$arr_event_activity);        
                    $log_time  = array();
                    foreach ($new_arr as $v) {
                       $log_time[] = $v['log_time'];
                    }
                   array_multisort($log_time, SORT_DESC, $new_arr);
            }else{
                    //$a_add = array();
                   // foreach ($a as $key=>$val){
                   //     $a_add[$key]['log_time'] = strtotime($val['time']);
                  //      $a_add[$key]['title'] = $val['title'];
                  //      $a_add[$key]['time'] = $val['time'];
                //    }                array_merge (    ,$a_add);
                    $new_arr = $arr_event_activity;        
                    //$log_time  = array();
                   // foreach ($new_arr as $v) {
                   //    $log_time[] = $v['log_time'];
                  //  }
                  // array_multisort($log_time, SORT_DESC, $new_arr);
            }

}
//添加大事记
function add_big_event($fid,$uid,$title,$log_time,$path)
{
     if(intval($fid) <= 0) return array();
     $title = trim($title);
     $log_time = intval($log_time);
     if($title == '' || $log_time ==''){
         return array();
     }
     $path = trim($path);
     $result = C::t('#fansclub#plugin_fansclub_event_log')->add_big_event_by_fid($fid,$uid,$title,$log_time,$path); // 添加事件
     return $result;
}
//取添加大事记
function get_add_big_event($fid){
    $arr_event = C::t('#fansclub#plugin_fansclub_event_log')->fetch_all_by_fid($fid, 0, 9, 'DESC'); // 取所有事      
    return $arr_event;
}
// 取大事
function get_big_event($fid, &$arr_year_split)
{
	global $config;          
                      $arr_event = C::t('#fansclub#plugin_fansclub_event_log')->fetch_all_by_fid($fid, 0, 0, 'ASC'); // 取所有事             
                       if($_GET['type'] == 'read'){
                           $arr_event = C::t('#fansclub#plugin_fansclub_event_log')->fetch_all_by_fid($fid, 0, 9, 'DESC'); // 取所有事        
                           return $arr_event;
                       }
                       //echo "<pre>";
	//print_r($arr_event);
	//exit;

	$arr_return = array();
	$arr_first = array();
	$init_remark = $init_remark2 = '';
	for($i = 0; $i < count($arr_event); $i++)
	{
		if($i > 6)
		{
			// $arr_event[$i]['log_time'] = 1469854801; // 测试效果 
		}
		
		$year = date('Y', $arr_event[$i]['log_time']);
		$type = $arr_event[$i]['type'];
		$_arr = explode('|', $arr_event[$i]['remark']);
		
		$_tmp = array();
		$_tmp['time'] = date('Y.m.d', $arr_event[$i]['log_time']);
		$_tmp['title'] = $config['event_type'][$type];
		$_tmp['content'] = $_tmp['title'];
		
		$arr_event[$i]['pictures'] = 0; // 暂定为0
		$arr_event[$i]['vedios'] = 0;
		$_tmp['year'] = $year;
		
		$_tmp['add_info'] = '总会员：'.$arr_event[$i]['members'].' 图片数：'.$arr_event[$i]['pictures'].' '.
							'总话题：'.$arr_event[$i]['posts'].' 视频数：'.$arr_event[$i]['vedios'];
		$_tmp['add_info'] = array('members' => $arr_event[$i]['members'],
								  'pictures' => $arr_event[$i]['pictures'],
								  'posts' => $arr_event[$i]['posts'],
								  'vedios' => $arr_event[$i]['vedios']);
		
		if($type == '1') // zhangjh 2015-06-08 这个不显示
		{
			// $_tmp['title'] = $config['event_type'][$type];
			// $_tmp['content'] = '由['.$_arr[0].']发起申请';
			$init_remark = '<'.$_arr[2].'>正式成立，创始人<'.$_arr[0].'>';
			$init_remark2 = '<'.$_arr[2].'>';
			continue;
		}
		elseif($type == '4' || $type == '98')
		{
			//$_tmp['add_info'] = '';
			if($type == '4') // 审核通过
			{
				$_tmp['content'] = $init_remark;
			}
		}
		elseif($type == '6' || $type == '7')
		{
			if(in_array($type, $arr_first))
			{
				$arr_show_times = array(500, 1000, 2000, 3000);
				
				//if($type == '6' && $arr_event[$i]['members']%10 == 0 && in_array($arr_event[$i]['members'], $arr_show_times)) // 10的倍数 500 、 1000 、 2000 、 3000
				if($type == '6' && in_array($arr_event[$i]['members'], $arr_show_times))
				{
					$_tmp['title'] = '第'.$arr_event[$i]['members'].'名'.$_tmp['title'];
					// $_tmp['content'] = '账号是['.$_arr[0].']';
					$_tmp['content'] = $init_remark2.'人数已经突破<'.$arr_event[$i]['members'].'>人，贡献值达到<'.($arr_event[$i]['contribution']+0).'>';
					
				}
				else
				{
					continue;
				}
			}
			else
			{
				$arr_first[] = $type;
				continue; // 不显示第一次
				
				// $_tmp['title'] = '第一次'.$_tmp['title'];
				// $_tmp['content'] = '账号是['.$_arr[0].']';
			}
		}
		elseif($type == '8') // 成员变换
		{
			if($_arr[1] == '1' || $_arr[2] == '1') // 只涉及群主
			{
				$_tmp['content'] = '['.$_arr[0].']从“'.$config['group_user_level'][$_arr[1]].'”变更到“'.$config['group_user_level'][$_arr[2]].'”';
			}
			else
			{
				continue;
			}
		}
		elseif($type == '11')
		{
			if(in_array($type, $arr_first))
			{
				$arr_show_times = array(500, 1000, 2000, 3000);
				if($type == '11' && in_array($arr_event[$i]['posts'], $arr_show_times))
				{
					$_tmp['title'] = '第'.$arr_event[$i]['posts'].'张主题';
					// $_tmp['content'] = '由账号['.$_arr[0].']发表题为“'.$_arr[1].'”';
					$_tmp['content'] = $init_remark2.'主题数已突破'.'<'.$arr_event[$i]['posts'].'>，其中精华帖子数<'.($arr_event[$i]['digests']+0).'>';
				}
				else
				{
					continue;
				}
			}
			else
			{
				$arr_first[] = $type;
				continue; // 不显示第一次
				
				// $_tmp['title'] = '第一次'.$_tmp['title'];
				// $_tmp['content'] = '账号是['.$_arr[0].']';
			}
		}
		elseif($type == '13')
		{
			if(in_array($type, $arr_first))
			{
				continue;
			}
			else
			{
				$arr_first[] = $type;
				$_tmp['title'] = '第一次'.$_tmp['title'];
				$_tmp['content'] = '账号是['.$_arr[0].']';
			}
		}
		elseif($type == '9') //  || $type == '10') 9升级 10降级
		{
			//$_tmp['content'] = '从“'.$config['group_level'][$_arr[1]].'”变更到“'.$config['group_level'][$_arr[2]].'”';
			$_tmp['content'] = '恭喜！'.$init_remark2.'顺利通过了<'.$config['group_level'][$_arr[2]].'认证>，并相应获得了相对应的权益';
		}
		elseif($type == '14') // '机构认证'
		{
			// 6|3|4|0|0 => fid|版块id|status|active_month|expired_time
			if($_arr[2] == '3')
			{
				$_tmp['content'] = '恭喜！'.$init_remark2.'顺利通过了<'.$config['group_level_special'][0].'>，并相应获得了相对应的权益';
			}
			else
			{
				continue;
			}
		}
		elseif($type == '15') // 5U认证
		{
			// 6|3|3|3|1441764915 => fid|版块id|status|active_month|expired_time
			if($_arr[2] == '3')
			{
				$_tmp['content'] = '恭喜！'.$init_remark2.'顺利通过了<'.$config['group_level_special'][1].'>，并相应获得了相对应的权益。';
				$_tmp['content'] .= '此认证为期<'.$_arr[3].'个月>，将在<'.date('Y.m.d', $_arr[4]).'>过期';
			}
			else
			{
				continue;
			}
		}
		elseif($type == '16') // 友情球迷会
		{
			$linkgid = intval($_arr[1]); // 对方球迷会
			$arr_link = get_group_info($linkgid);
			// new.gid,'|',new.linkgid,'|',new.status,'|',new.createtime
			$_tmp['content'] = $init_remark2.'和<'.$arr_link['name'].'>成为友情球迷会，友谊万岁！';
		}
		elseif($type == '17') //
		{
			$_tmp['content'] = $init_remark2.'原会长<'.$_arr[1].'>任，<'.$arr[3].'>成为现会长，恭喜！';
		}
		else
		{
			continue;
		}
		$arr_return[] = $_tmp;
	}
	
	// 倒序输出
	if(count($arr_return) > 0)
	{
		$_arr_tmp = array();
		$j = 0;
		for($i = count($arr_return) - 1; $i >= 0; $i--)
		{
			if($i == (count($arr_return) - 1))
			{
				$arr_year_split[$j] = $arr_return[$i]['year'];
			}
			elseif($arr_return[$i]['year'] != $arr_return[$i+1]['year'])
			{
				$arr_year_split[$j] = $arr_return[$i]['year'];
			}
			$arr_return[$i]['content'] = htmlentities($arr_return[$i]['content']);
			$_arr_tmp[$j] = $arr_return[$i];
			$j++;
		}
		$arr_return = $_arr_tmp;
	}
	return $arr_return;
}
//wap 大事记
function wap_get_big_event($fid)
{
    //echo $fid;exit;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 9;
    
        $start = ($page - 1) * $pagesize;
        $limit = " LIMIT ".$start.','.$pagesize;
                            $activitylists = DB::fetch_all("SELECT a.*,b.subject FROM ".DB::table('forum_activity')." as  a , ".DB::table('forum_thread')." as b  WHERE   a.tid = b.tid and b.fid = $fid and b.special = 4 ORDER BY a.starttimefrom DESC ".$limit);
                            foreach ($activitylists as $key => $activity) {
      //                              取开始月 - 日
                                        $activitylists[$key]['starttimefrom'] = date('m-d', $activity['starttimefrom']);
                                        $activitylists[$key]['log_time'] = $activity['starttimefrom'];
                                        $activitylists[$key]['starttimefrom_a'] = date('Y.m.d', $activity['starttimefrom']);
      //                                  if ($activity['starttimeto'] && $activity['starttimeto'] >= TIMESTAMP) {
      //                                         $activitylists[$key]['status'] = true;
     //                                   } else {
     //                                          $activitylists[$key]['status'] = false;
     //                                   }
     //                              取开始年                                  
                                        $activitylists[$key]['starttimeyears'] = date('Y', $activity['starttimefrom']);
    //                                    $thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$activity['tid']);

   //                                     $activitylists[$key]['title'] = str_cut($thread['subject'], 57, '...');
                                        $attach = C::t('forum_attachment_n')->fetch('tid:'.$activity['tid'], $activity['aid']);
   //                                     if($attach['isimage']) {
   //                                             $activitylists[$key]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
  //                                              $activitylists[$key]['thumb'] = $attach['thumb'] ? getimgthumbname($activitylists[$key]['attachurl']) : $activitylists[$key]['attachurl'];
  //                                              $activitylists[$key]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
  //                                      }
                            }
                            return $activitylists;                    
}
