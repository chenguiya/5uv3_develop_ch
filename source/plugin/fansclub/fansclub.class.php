<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');

class plugin_fansclub {
	function __construct() {
		global $_G;
		loadcache('plugin');
	}
	
// 	function global_header() {
// 		global $_G;
// 		var_dump($_G['clientip']);
// 	}
	/**
	 * 判断会员是否已有球迷会贡献
	 * @param int $gid
	 * @return boolean
	 */
	function checkfansclubcredits($gid) {
		if (!($result = C::t('#fansclub#plugin_fansclub_balance')->fetch_first($gid))) {
			return false;
		} else {
			return $result['balance_id'];
		}
	}
	
	function getjoinclub($uid, $fid, $type) {
		$fansclub_id = array();
		switch ($type) {			
			case 'forum'://当前版块为球队板块
			$subforum = C::t('forum_forum')->fetch_all_subforum_by_fup($fid);
			$forum = C::t('forum_forum')->fetch_info_by_fid($fid);
			$id[] = explode(',', $forum['relatedgroup']);
			foreach ($subforum as $sub) {
				$subinfo = C::t('forum_forum')->fetch_info_by_fid($sub['fid']);
				$id[] = explode(',', $subinfo['relatedgroup']);
			}
			for ($i = 0; $i < count($id); $i++) {
				$fansclub_id = array_values_merge($fansclub_id, $id[$i]);
			}
			break;
			
			default://当前板块为球星板块
			$fansclub = C::t('forum_forum')->fetch_info_by_fid($fid);
			$fansclub_id = explode(',', $fansclub['relatedgroup']);
			break;
		}
		
		foreach ($fansclub_id as $clubid) {
			$data = C::t(forum_groupuser)->fetch_userinfo($uid, $clubid);
			$clubinfo = C::t('forum_forum')->fetch_info_by_fid($clubid);		
			if(!empty($data) && $clubinfo['level'] > 0) {
				$joinclub[$data['fid']]['clubid'] = $data['fid'];
				$joinclub[$data['fid']]['clubname'] = $clubinfo['name'];
				$joinclub[$data['fid']]['dateline'] = $data['joindateline'];
			}
		}
		//按加入时间安排序
		$datelines = array();
		foreach ($joinclub as $club) {
			$datelines[] = $club['dateline'];
		}
		array_multisort($datelines, SORT_ASC, $joinclub);
		return $joinclub;
	}
	
	function open($op) {
		loadcache('plugin');
		global $_G;
// 		$_settings = $_G['cache']['plugin']['']
	}	
	//首页球迷会分类列表	
	function global_fansclub_lists () {
		return ''; // 这个首页已不存在，可以不处理
		global $_G;
		$icon = array(
			'英超'=>'nava',
			'西甲'=>'navb',
			'德甲'=>'navf',
			'意甲'=>'nave',
			'法甲'=>'navg',
			'亚洲'=>'navi',
			'中超'=>'navh',
			'其他'=>'navj',
			'NBA'=>'navc',
			'CBA'=>'navd',
			'综合'=>'navk',
		);
		$forumlists = array();		
		if(!isset($_G['cache']['forums'])) loadcache('forums');	

		include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function/function_core.php');
				
		foreach ($_G['cache']['forums'] as $key => $forum) {
			if ($forum['status'] == '1' && (!$forum['viewperm'] && $_G['group']['readaccess']) || ($forum['viewperm'] && forumperm($forum['viewperm']))) {
				if ($forum['type'] == 'group') {
					$forumlists[$key] = $forum;
				}
				if ($forum['type'] == 'forum') {
					$forumlists[$forum['fup']]['branch'][$key] = $forum;
					$forumlists[$forum['fup']]['branch'][$key]['credits'] = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_credits($key);
					$clubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_relation_fid($key);
					if (!empty($clubinfo)) {
						$forumlists[$forum['fup']]['branch'][$key]['clubid'] = $clubinfo['fup'];
					}					
				}
			}			
		}
		
		foreach ($forumlists as $_key => $_forum) {
			$new_branch = multi_array_sort($_forum['branch'], 'credits', SORT_DESC);
			$i = 0;
			foreach ($new_branch as $vo) {
				if ($i < 3 && $vo['credits'] > 0) {
					$forumlists[$_key]['branch'][$vo['fid']]['hot'] = 1;
				} else {
					$forumlists[$_key]['branch'][$vo['fid']]['hot'] = 0;
				}
				$i++;
			}
		}
// 		var_dump($forumlists);die;	
		include template('fansclub:portal/clublist');
		return $clublists;
	}
	//首页优秀会长列表
	function global_fansclub_chairman() {
		include_once libfile('function/extends');
		$chairmanlists = array();
		foreach ($chairmanlists = C::t('#fansclub#plugin_fansclub_balance')->fetch_club_ban('extendcredits3', 5) as $key => $value) {
			//获取球迷会信息
			$clubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_fid($value['relation_fid']);
			//获取球迷会会长信息
			$userinfo = DB::fetch_first('SELECT * FROM '.DB::table('forum_groupuser').' WHERE fid='.$value['relation_fid']." AND level=1");
			$chairmanlists[$key]['checkrights'] = userrightsperm(32, $value['relation_fid']);
			$chairmanlists[$key]['name'] = $clubinfo['name'];			
			$chairmanlists[$key]['uid'] = $userinfo['uid'];			//会长用户id
			$chairmanlists[$key]['avatar'] = avatar($userinfo['uid'], 'middle', true);
			$chairmanlists[$key]['username'] = $userinfo['username'];		//会长用户名
// 			var_dump($userinfo['uid']);die;
			$userfield = DB::fetch_first("SELECT * FROM ".DB::table('common_member_field_forum')." WHERE uid=".intval($userinfo['uid']));
			$chairmanlists[$key]['sign'] = $userfield['sightml'];
		}
		
		include template('fansclub:portal/chairman');
		return $return;
	}
	//首页贡献达人
	function global_fansclub_fans($param) {
		global $_G;
		$fanslists = array();
		$fanslists = C::t('#fansclub#plugin_fans_credit')->fetch_sum_credits_ban();
		if ($fanslists) {
			foreach ($fanslists as $key => $value) {
				$mainclub = C::t('#fansclub#plugin_fans_credit')->fetch_max_credits_fansclub($value['uid']);
				$clubinfo = C::t('forum_forum')->fetch_info_by_fid($mainclub[0]['gid']);
				$fanslists[$key]['mainclub_name'] = $clubinfo['name'];
				$fanslists[$key]['mainclub_fid'] = $clubinfo['fid'];
				$fanslists[$key]['avatar'] = avatar($value['uid'], 'middle', true);
			}
			include template('fansclub:portal/fans');
			return $return;
		} else {
			return false;
		}		
	}
	//首页球迷会排行
	function global_fansclub_ban() { 
		$fansclublists = array();
		foreach ($fansclublists = C::t('#fansclub#plugin_fansclub_balance')->fetch_club_ban('extendcredits3', 6) as $key => $value) {
			$fansclubinfo = C::t('forum_forum')->fetch_info_by_fid($value['relation_fid']);
			$fansclubinfo_ext = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_fid($value['relation_fid']);
			
			$fansclublists[$key]['name'] = $fansclubinfo['name'];
			$fansclublists[$key]['icon'] = 'data/attachment/group/'.$fansclubinfo['icon'];
			$fansclublists[$key]['area'] = $fansclubinfo_ext['province_city'];
			$fansclublists[$key]['membernum'] = $fansclubinfo['membernum'];
			$fansclublists[$key]['posts'] = $fansclubinfo['posts'];
			$fansclublists[$key]['desc'] = $fansclubinfo['description'];			
		}
// 		var_dump($fansclublists);die;
		include template('fansclub:portal/clubban');
		return $return;		
	}
	
	//首页球迷分会推荐
	function global_portal_branch() {
		include_once DISCUZ_ROOT.'source/plugin/fansclub/function.inc.php';
		foreach ($result = DB::fetch_all("SELECT * FROM ".DB::table('common_block_item')." WHERE bid=72") as $key => $value) {
			// 			$result[$key]['city'] = fansclub_get_province_city($value['id']);
			$arr = C::t('#fansclub#plugin_fansclub_info')->fetch($value['id']);
			$result[$key]['info'] = unserialize($value['fields']);
			$result[$key]['province'] = fansclub_get_district_name($arr['province_id']);
			$result[$key]['city'] = fansclub_get_district_name($arr['city_id']);
		}
// 		var_dump($result);die;
		include template('fansclub:portal/branch');
		return $return;
	}
	
	//首页最新活跃会员
	function global_protal_activemember() {
		$activemembers = array();
		$activemembers = C::t('#fansclub#plugin_fans_credit')->fetch_active_member();
		$num1 = count($activemembers);
		$where = '0';
		
		foreach ($activemembers as $key => $val) {
			$activemembers[$key]['avatar'] = avatar($val['uid'], 'middle', true);
			$where .= ',' . $val['uid'];
		}
		if ($num1 < 11) {
			$limit = 11 - $num1;
			$newmember = C::t('#fansclub#plugin_forum_groupuser')->fetch_new_member($where, $limit);
			foreach ($newmember as $_key => $_val) {
				$activemembers[$num1+$_key+1]['uid'] = $_val['uid'];
				$activemembers[$num1+$_key+1]['avatar'] = avatar($_val['uid'], 'middle', true);
			}
		}		
		include template('fansclub:portal/activemember');
		return $return;
	}
}

class plugin_fansclub_forum extends plugin_fansclub {
	/**
	 * 用户在板块成功发帖后球迷会获取球迷贡献，球迷获取球迷对球迷会的贡献
	 */
	function post_feedlog_message($var) {
		global $_G, $tid, $pid;
				
		$_GET['majiauid'] = intval($_GET['majiauid']);
		$uid = $_GET['majiauid'];
		if($uid == $_G['uid']) $username = $_G['username'];
		else $username = DB::result_first("SELECT username FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]' and useruid='$uid'");
		$username = daddslashes($username);
		
		$tid = $var['param'][2]['tid'];
		$pid = $var['param'][2]['pid'];
		$mygid = isset($var['param'][2]['mygid']) ? $var['param'][2]['mygid'] : 0;
		$action = $var['param'][0];
		
		$message = $var['param'][2]['message'];
		
		if (strstr($message, '[media' ) || strstr($message, '[flash' )) {
			C::t('forum_thread')->update(array('tid'=>$tid), array('attachment'=>3));
		}
				
		$oprationtype = array(
			'NTR' => '发布新话题',
			'RPL' => '回复主题',
			'BUY' => '购买产品',
			'VIP' => 'VIP充值',	
		);
		if ($action == 'post_newthread_succeed') {
			if ($mygid > 0) {
				$group = C::t('forum_forum')->fetch_info_by_fid($mygid);			
				$level = DB::fetch_first('SELECT * FROM '.DB::table('forum_grouplevel').' WHERE levelid='.$group['level']);
				$creditspolicy = dunserialize($level['creditspolicy']);
				if ($creditspolicy['post'] == 1) {
					//球迷会积分业务处理
					if ($balanceid = $this->checkfansclubcredits($mygid)) {
						C::t('#fansclub#plugin_fansclub_balance')->update_fansclub_balance($mygid, (empty($creditspolicy['post_credits']) ? 0 : intval($creditspolicy['post_credits'])));
					} else {
						$balanceid = C::t('#fansclub#plugin_fansclub_balance')->insert(array('relation_fid'=>$mygid, 'extendcredits3'=>$creditspolicy['post_credits']));
					}
					//球迷会积分写日志
					$fansclubcredits_log_data = array(
						'balance_id' => $balanceid,
						'type' => 'NTR',
						'amount' => $creditspolicy['post_credits'],
						'log_time' => TIMESTAMP,
						
					);
					C::t('#fansclub#plugin_fansclub_balance_log')->insert($fansclubcredits_log_data);
					
					//球迷个人积分处理
					if ($item = C::t('#fansclub#plugin_fans_credit')->fetch_credit_by_fid_uid($uid, $mygid)) {
						C::t('#fansclub#plugin_fans_credit')->update_credit($uid, $mygid, $creditspolicy['post_credits'], $item['lastdate']);
					} else {
						C::t('#fansclub#plugin_fans_credit')->insert(array(
							'uid' => $uid,
							'username' => $username,
							'gid' => $mygid,
							'weekcredits' => $creditspolicy['post_credits'],
							'credits' => $creditspolicy['post_credits'],
							'lastdate' => TIMESTAMP,
						));
					}
					
					//球迷个人积分日志				
					C::t('#fansclub#plugin_fans_credit_log')->insert(array(
						'uid' => $uid,
						'username' => $username,
						'tid' => $tid,
						'fid' => $_G['forum']['fid'],
						'gid' => $mygid,
						'operation' => 'NTR',
						'credits' => $creditspolicy['post_credits'],
						'dateline' => TIMESTAMP,
						'ip' => $_G['clientip'],
					));
				}
			}
		} elseif ($action == 'post_reply_succeed') {
			if ($mygid > 0) {
				$group = C::t('forum_forum')->fetch_info_by_fid($mygid);			
				$level = DB::fetch_first('SELECT * FROM '.DB::table('forum_grouplevel').' WHERE levelid='.$group['level']);
				$creditspolicy = dunserialize($level['creditspolicy']);
	// 			var_dump($creditspolicy);die;
				if ($creditspolicy['reply'] == 1) {
					//球迷会积分业务处理
					if ($balanceid = $this->checkfansclubcredits($mygid)) {
						C::t('#fansclub#plugin_fansclub_balance')->update_fansclub_balance($mygid, (empty($creditspolicy['post_credits']) ? 0 : intval($creditspolicy['post_credits'])));
					} else {
						$balanceid = C::t('#fansclub#plugin_fansclub_balance')->insert(array('relation_fid'=>$mygid, 'extendcredits3'=>$creditspolicy['reply_credits']));
					}
					//球迷会积分写日志
					$fansclubcredits_log_data = array(
						'balance_id' => $balanceid,
						'type' => 'RPL',
						'amount' => $creditspolicy['reply_credits'],
						'log_time' => TIMESTAMP,
						
					);
					C::t('#fansclub#plugin_fansclub_balance_log')->insert($fansclubcredits_log_data);
					
					//球迷个人积分处理
					if ($item = C::t('#fansclub#plugin_fans_credit')->fetch_credit_by_fid_uid($uid, $mygid)) {
						C::t('#fansclub#plugin_fans_credit')->update_credit($uid, $mygid, $creditspolicy['reply_credits'], $item['lastdate']);
					} else {
						C::t('#fansclub#plugin_fans_credit')->insert(array(
							'uid' => $uid,
							'username' => $username,
							'gid' => $mygid,
							'weekcredits' => $creditspolicy['reply_credits'],
							'credits' => $creditspolicy['reply_credits'],
							'lastdate' => TIMESTAMP,
						));
					}
					
					//球迷个人积分日志				
					C::t('#fansclub#plugin_fans_credit_log')->insert(array(
						'uid' => $uid,
						'username' => $username,
						'fid' => $_G['forum']['fid'],
						'gid' => $mygid,
						'operation' => 'RPL',
						'credits' => $creditspolicy['reply_credits'],
						'dateline' => TIMESTAMP,
						'ip' => $_G['clientip'],
					));
				}
			}
		}		
	}	
	
	/*
	 *   板块右边的发帖数
	 *	  by:mai
	 */
	function forumdisplay_right_info1(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$foruminfo = $_G['forum'];
		
		$branch = explode(',', $_G['forum']['relatedgroup']);
		
		// zhangjh 2015-06-13 只显示plugin_fansclub_info有的球迷会数目
		$branch = fansclub_forum_recount($_G['forum']['fid']);
		
		$data['branchnum'] = count($branch);
		//会员数
		foreach ($branch as $vo) {
			$forum = C::t('forum_forum')->fetch_info_by_fid($vo);
			$data['membernum'] += $forum['membernum'];
		}
		//帖子数
		$data['threadnum'] = $_G['forum']['threads'];
		
		//debug($foruminfo);
		include template("fansclub:right/info1");
		return $html;
	}
	
	/*
	 *   球迷会的管理小组
	 *	  by:mai
	 */
	function forumdisplay_info_manage(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		foreach(explode("\t", $_G['forum']['moderators']) as $k=>$moderator) {
			$arr[$k]= $moderator;
			$sql = "select uid,username,groupid from ".DB::table('common_member')." where username='{$moderator}'";
			$userinfo = DB::fetch_first($sql);
			$userinfoarr[$userinfo['groupid']][$k] = $userinfo;
			$userinfoarr[$userinfo['groupid']][$k]['avatar'] = avatar($userinfo['uid'], 'small', 1);
		}
		include template("fansclub:right/info_manage");
		return $html;
	}
	/*
	 *   频道的管理小组
	 *	  by:mai
	 */
	function forumdisplay_forum_manage(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		foreach(explode("\t", $_G['forum']['moderators']) as $k=>$moderator) {
			$arr[$k]= $moderator;
			$sql = "select uid,username,groupid from ".DB::table('common_member')." where username='{$moderator}'";
			$userinfo = DB::fetch_first($sql);
			$userinfoarr[$userinfo['groupid']][$k] = $userinfo;
			$userinfoarr[$userinfo['groupid']][$k]['avatar']=avatar($userinfo['uid'], 'small');
		}
		include template("fansclub:right/forum_manage");
		return $html;
	}
	/*
	 *   VIP服务
	 *	  by:mai
	 */
	function forumdisplay_server_vip(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		include template("fansclub:right/server_vip");
		return $html;
	
	}
	/*
	 *   球迷会排行榜（按人气排）
	 *	  by:mai
	 */
	function forumdisplay_sort_fansclub(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$sql = "select ff.fid,ff.name,ff.posts,ffd.membernum,ffd.icon from ".DB::table('forum_forumfield')." as ffd left join ".DB::table('forum_forum')." as ff on ff.fid=ffd.fid where ff.type ='sub' order by ffd.membernum desc limit 0,6";
		$arr = DB::fetch_all($sql);
		$info = get_fansclub_info(2);
		// zhangjh 2015-06-14 加等级图标
		for($i = 0; $i < count($arr); $i++)
		{
			$arr[$i]['level_img'] = fansclub_get_level_img($arr[$i]['fid']);
		}
		include template("fansclub:right/sort_fansclub");
		return $html;
	}
	/*
	 *   视频/图片排行榜（按人气排）
	 *	  by:mai
	 */
	function forumdisplay_sort_media(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$time = time()-(3600*24*30);
		$sql="select ft.subject,ft.tid,fp.message from ".DB::table('forum_thread')." as ft left join ".DB::table('forum_post')." as fp on ft.tid=fp.tid  where ft.dateline>{$time} and ft.attachment=2 group by tid  order by ft.dateline desc  limit 0,5";
		$arr = DB::fetch_all($sql);
		foreach($arr as $k=>$v){
			preg_match_all("/\[flash\](.*)\[\/flash\]/i", $v['message'], $flag);
			if(!empty($flag[1])){
				$arr[$k]['vflag']=1;
			}else{
				$arr[$k]['vflag']=0;
			}
		}
		include template("fansclub:right/sidbar_media");
		return $html;
	}
	
	/*
	 *   球队频道下的球员列表
	 *	  by:mai
	 */
	function forumdisplay_player_lists(){	
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		if($_G['forum']['type']!='forum'){
			return '';
		}else{
			$query=DB::fetch_all("select fid from ".DB::table('forum_forum')." where fup={$_G['forum']['fid']} order by status desc limit 0,10" );
			foreach($query as $k=>$v){
				$arr[$k]=get_fansclub_info($v['fid']);
			} 
			include template('fansclub:right/player_lists');
			return $html;
		}
	}
	
	/*
	 *  频道首页下的VIP服务
	 *	  by:mai
	 */
	function forumdisplay_index_server_vip(){	
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		include template("fansclub:right/index_server_vip");
		return $html;
	}

	function forumdisplay_hotclub_output() {
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$fid = $_G['forum']['fid'];
		$data = array();
		$where = ' f.relation_fid='.$fid;
		$data['data'] = C::t('#fansclub#plugin_fansclub_info')->fetch_all_for_search($where, 0, 4, 'members');
		
// 		var_dump($data['data']);die;
		include template('forum/forumdisplay/hotclub');
		return $return;
	}	
	
	/*
	 *  频道首页下的球迷会列表
	 *	  by:mai
	 */
	function forumdisplay_fansclub_lists(){	
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$fid=$_G['forum']['fid'];
		$data = array();
		$data['city_id'] = 0; // 城市ID
		$data['fid'] = $fid; // 版块ID
		$data['sort'] = 'contribution'; // contribution 按贡献值(默认), fansnum 粉丝数, level 认证等级
		$data['limit'] = 7; // 显示多少个
		$data['start'] = 0; // 从哪个开始显示
		$lists = fansclub_list($data);
		if($_G['uid']){
			foreach($lists as $k=>$v){
				$arr[$k]=$v; 
			}
		}else{
			$arr=$lists;
		}
		$first_arr=array_shift($arr);
		include template("fansclub:right/fansclub_lists");
		return $html;
	}
	/*
	 *  优秀会长
	 *	  by:mai
	 */
	function forumdisplay_good_chairman(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$_G['forum']['fid'] = intval($_G['forum']['fid']);
		$fids = DB::fetch_first('select relatedgroup from '.DB::table('forum_forumfield').' where fid='.$_G['forum']['fid']);
		$strfids=$fids['relatedgroup'];
		if($strfids){
			$strfids='('.trim($strfids,',').')';
			$sql="select fg.uid,fg.fid from ".DB::table('forum_groupuser')."  as fg left join ".DB::table('plugin_fans_credit')." as pfc on fg.uid=pfc.uid where fg.fid in ".$strfids." and fg.level=1 group by fg.uid order by pfc.credits desc limit 0,7";
			$info = DB::fetch_all($sql);
			//debug($sql);
			foreach($info as $k=>$v){
				$member = getuserbyuid($v['uid']);
				$groupinfo = get_fansclub_info($v['fid']);
				$arr[$k]=array(
								'username'=>$member['username'],
								'name'=>$groupinfo['name'],
								'uid'=>$v['uid'],
								'fid'=>$v['fid'],
								'description'=>$groupinfo['description']
								);
			}
		}
		include template("fansclub:right/good_chairman");
		return $html;
	}
	/*
	 *  贡献达人
	 *	  by:mai
	 */
	function forumdisplay_much_contribution_man(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$fids = DB::fetch_first('select relatedgroup from '.DB::table('forum_forumfield').' where fid='.$_G['forum']['fid']);
		$strfids=$fids['relatedgroup'];
		if($strfids){
			$strfids='('.trim($strfids,',').')';
			$sql="select fg.uid,fg.fid,pfc.credits from ".DB::table('forum_groupuser')."  as fg left join ".DB::table('plugin_fans_credit')." as pfc on fg.uid=pfc.uid where fg.fid in ".$strfids." group by fg.uid order by pfc.credits desc  limit 0,7";
			$info = DB::fetch_all($sql);
			foreach($info as $k=>$v){
				$member = getuserbyuid($v['uid']);
				$credits=$v['credits']?$v['credits']:0;
				$arr[$k]=array(
								'username'=>$member['username'],
								'uid'=>$v['uid'],
								'credits'=>$credits
								);
			}
		}
		include template("fansclub:right/much_contribution_man");
		return $html;
	}
	/*
	 *  频道新闻
	 *	  by:mai
	 */
	function forumdisplay_clubnews(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		//$time=strtotime(date('Y-m-d',time()));
		$time=0;
		$sql="select * from ".DB::table('forum_thread')." where  attachment=0 and  fid={$_G['forum']['fid']} order by dateline desc limit 0,7";
		
		// zhangjh 2015-07-14 不显示已删除的新闻
		$sql="select * from ".DB::table('forum_thread')." where  attachment=0 and displayorder >= 0 and  fid={$_G['forum']['fid']} order by dateline desc limit 0,7";
		
		$arr = DB::fetch_all($sql);
	 	include DISCUZ_ROOT.'./source/function/function.core.php';
		 foreach($arr as $k=>$v){
			$post = DB::fetch_first('select * from '.DB::table('forum_post').' where tid='.$v['tid'].' and first=1');
			$message =$post['message'];
			$message=strip_tags($message);
			$message =  preg_replace("/\[attach\](.*)\[\/attach\]/i",'',$message);
			$message =  preg_replace("/\[flash\](.*)\[\/flash\]/i",'',$message);
			$message =  preg_replace("/\[img(.*)\](.*)\[\/img\]/i",'',$message);
			$message =  preg_replace("/\[(.*?)\]/i",'',$message);
			$intro = mb_substr($message,0,80,'utf-8');
		    preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $v['message'], $matchaids);
			$arr[$k]['dateline']=date('Y年m月d日 H:i:s',$v['dateline']);
			$arr[$k]['intro']=$intro ;
			$arr[$k]['attachnum']=count($matchaids[1]);
			 if(count($matchaids[1])>=1 && count($matchaids[1])<3){
				$arr[$k]['icon']=getforumimg($matchaids[1][0],'',138,83);
			}
			if(count($matchaids[1])>=3){
				foreach($matchaids[1] as $k1=>$v1){
					$arr[$k]['img'][$k1]=getforumimg($v1,'',138,83);
				}
			}   
		} 
		include template("fansclub:right/clubnews");
		return $html;
	}
	
	/*
	 *  频道新闻
	 *	  by:mai
	 */
	function forumdisplay_recommend_news(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$block = DB::fetch_all("select * from ".DB::table('common_block_item')." where bid=74 order by itemid desc");
		$i=0;
		foreach($block as $k=>$v){
			$fields = unserialize($v['fields']);
			$forumurl = $fields['forumurl'];
			$turefid='&fid='.$_G['forum']['fid'].'"';
			if($i<3){
				if(stristr($v['fields'],$turefid)){
					$i++;
					if(empty($v['summary'])){
						$v['summary']=$v['title'];
					}
					$tinfo=array(
								'url'=>$v['url'],
								'title'=>$v['title'],
								'intro'=>$v['summary']
							);
					$arr[$k]=$tinfo;		
				}}
		}
		include template("fansclub:right/recommend_news");
		return $html;
	}
	/*
	 *  频道首页推荐图片
	 *	  by:mai
	 */
	function forumdisplay_recommend_img(){
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;

		$block = DB::fetch_all("select * from ".DB::table('common_block_item')." where bid=73 and pic!='static/image/common/nophoto.gif' order by itemid desc");
		foreach($block as $k=>$v){
			$fields = unserialize($v['fields']);
			$forumurl = $fields['forumurl'];
			$turefid='&fid='.$_G['forum']['fid'].'"';
				if(stristr($v['fields'],$turefid)){
					$i++;
					$tinfo=array(
								'url'=>$v['url'],
								'title'=>$v['title'],
								'pic'=>'data/attachment/'.$v['pic']
							);		
					break;
				}
		}
		include template("fansclub:right/recommend_img");
		return $html;
	}
	/**
	 * 积分排行
	 */
// 	function viewthread_credits_ban() {
// 		global $_G;
// 		$data = array();
// // 		var_dump($_G['forum']);die;
// 		$fid = $_G['forum']['fid'];
// 		$credits = C::t('#fansclub#plugin_fans_credit')->fetch_credits_ban_by_fid($fid);
// 		$starttime = TIMESTAMP - 604800;
// 		$weekcredits = C::t('#fansclub#plugin_fans_credit_log')->fetch_weekcredits_ban($fid, $starttime);
// // 		var_dump($credits);die;
// 		include template("extend/desktop/sidebar_contribute");
// 		return $return;
// 	}

	function post_middle_output() {
		loadcache('plugin');
		global $_G;
		$uid = intval($_G['uid']);
		$fid = $_G['forum']['fid'];
		$type = $_G['forum']['type'];
		
		$joinclub = $this->getjoinclub($uid, $fid, $type);
		include template('fansclub:forum/selectgroup');
		return $return;
	}
	
	function viewthread_fastpost_content() {
		loadcache('plugin');
		global $_G;
		$uid = intval($_G['uid']);
		$fid = $_G['forum']['fid'];
		$type = $_G['forum']['type'];
		
		$joinclub = $this->getjoinclub($uid, $fid, $type);
		include template('fansclub:forum/selectgroup');
		return $return;
	}
}

class plugin_fansclub_group extends plugin_fansclub 
{
	//公用头部
	function group_hd_top() {
		global $_G;
		$gid = isset($_GET['fid']) ? intval($_GET['fid']) : exit;		
		$fansclubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_fid($gid);
		$clubinfo = C::t('forum_forum')->fetch_info_by_fid($fansclubinfo['relation_fid']);
		$branchids = explode(',', $clubinfo['relatedgroup']);
		
		// zhangjh 2015-06-13 只显示plugin_fansclub_info有的球迷会数目
		$branchids = fansclub_forum_recount($fansclubinfo['relation_fid']);
		
		$fansnum = $postsnum = 0;
		$clubinfo['branchnum'] = count($branchids);
		foreach ($branchids as $id) {
			$branchinfo = C::t('forum_forum')->fetch_info_by_fid($id);
			$clubinfo['fansnum'] += $branchinfo['membernum'];
			$clubinfo['postsnum'] += $branchinfo['posts'];
		}
		
		// 2015-05-15 zhangjh 增加默认icon
		if($clubinfo['icon'] == '')
			$clubinfo['icon'] = $_G['siteurl'].'template/usportstyle/common/images/default_icon.jpg';
		else
			$clubinfo['icon'] = $_G['siteurl'].'data/attachment/common/'.$clubinfo['icon'];
					
		include template('fansclub:index/head_top');		
		return $return;
	}
	//球迷会头部
	function forumdisplay_hd_top() {
		global $_G;
		$gid = isset($_GET['fid']) ? intval($_GET['fid']) : exit;
		$fansclubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_fid($gid);
		$clubinfo = C::t('forum_forum')->fetch_info_by_fid($fansclubinfo['relation_fid']);
		$branchids = explode(',', $clubinfo['relatedgroup']);
		
		// zhangjh 2015-06-13 只显示plugin_fansclub_info有的球迷会数目
		$branchids = fansclub_forum_recount($fansclubinfo['relation_fid']);
		
		$fansnum = $postsnum = 0;
		$clubinfo['branchnum'] = count($branchids);
		foreach ($branchids as $id) {
			$branchinfo = C::t('forum_forum')->fetch_info_by_fid($id);
			$clubinfo['fansnum'] += $branchinfo['membernum'];
			$clubinfo['postsnum'] += $branchinfo['posts'];
		}
	
		// 2015-05-15 zhangjh 增加默认icon
		if($clubinfo['icon'] == '')
			$clubinfo['icon'] = $_G['siteurl'].'template/usportstyle/common/images/default_icon.jpg';
		else
			$clubinfo['icon'] = $_G['siteurl'].'data/attachment/common/'.$clubinfo['icon'];
			
		include template('fansclub:index/head_top');
		return $return;
	}
	//球迷会首页加载内容
	function group_feed_list() {
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
		$multipage_more = "plugin.php?id=fansclub:index&ac=feed&fid=".$fid."&page=2&pagesize=20";
		
		$where = '';
		if ($_GET['do'] == 'all') {
			$where = "uid IN (" . $struids . ") AND icon IN ('thread','album','blog','friend')";
		} elseif ($_GET['do'] == 'hot') {
			$where = "uid IN (" . $struids . ") AND hot!=0";
		}
		
		$group = C::t('forum_forum')->fetch_info_by_fid($fid);
		$level = DB::fetch_first('SELECT * FROM '.DB::table('forum_grouplevel').' WHERE levelid='.$group['level']);
		$creditspolicy = dunserialize($level['creditspolicy']);
		
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
// 		var_dump($feedlist);die;

		include template('fansclub:index/feed_list');
		return $return;
	}
	
	//自定义二级导航
	function group_nav_extra_output() {
		return '<li><a href="plugin.php?id=fansclub:fansclub&ac=lists&fid='.$_GET['fid'].'&type=thread">话题</a></li><li><a href="plugin.php?id=fansclub:fansclub&ac=lists&fid='.$_GET['fid'].'&type=pic">图片</a></li><li><a href="plugin.php?id=fansclub:fansclub&ac=lists&fid='.$_GET['fid'].'&type=video">视频</a></li>';
	}	

	//球迷会发帖球迷会和球迷个人贡献处理
	function post_feedlog_message($var) {
		global $_G, $tid, $pid;
				
// 	$_GET['majiauid'] = intval($_GET['majiauid']);
		$uid = intval($_GET['majiauid']);
		if($uid == $_G['uid']) $username = $_G['username'];
		else $username = DB::result_first("SELECT username FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]' and useruid='$uid'");
		$username = daddslashes($username);
		
		$tid = $var['param'][2]['tid'];
		$pid = $var['param'][2]['pid'];
		$mygid = isset($var['param'][2]['fid']) ? $var['param'][2]['fid'] : 0;
		$action = $var['param'][0];
		
		$message = $var['param'][2]['message'];
		
		if (strstr($message, '[media' ) || strstr($message, '[flash' )) {
			C::t('forum_thread')->update(array('tid'=>$tid), array('attachment'=>3));
		}
				
		$oprationtype = array(
			'NTR' => '发布新话题',
			'RPL' => '回复主题',
			'BUY' => '购买产品',
			'VIP' => 'VIP充值',	
		);
		if ($action == 'post_newthread_succeed') {
			if ($mygid > 0) {
				$group = C::t('forum_forum')->fetch_info_by_fid($mygid);			
				$level = DB::fetch_first('SELECT * FROM '.DB::table('forum_grouplevel').' WHERE levelid='.$group['level']);
				$creditspolicy = dunserialize($level['creditspolicy']);
				if ($creditspolicy['post'] == 1) {
					//球迷会积分业务处理
					if ($balanceid = $this->checkfansclubcredits($mygid)) {
						C::t('#fansclub#plugin_fansclub_balance')->update_fansclub_balance($mygid, (empty($creditspolicy['post_credits']) ? 0 : intval($creditspolicy['post_credits'])));
					} else {
						$balanceid = C::t('#fansclub#plugin_fansclub_balance')->insert(array('relation_fid'=>$mygid, 'extendcredits3'=>$creditspolicy['post_credits']));
					}
					//球迷会积分写日志
					$fansclubcredits_log_data = array(
						'balance_id' => $balanceid,
						'type' => 'NTR',
						'amount' => $creditspolicy['post_credits'],
						'log_time' => TIMESTAMP,
						
					);
					C::t('#fansclub#plugin_fansclub_balance_log')->insert($fansclubcredits_log_data);
					
					//球迷个人积分处理
					if ($item = C::t('#fansclub#plugin_fans_credit')->fetch_credit_by_fid_uid($uid, $mygid)) {
						C::t('#fansclub#plugin_fans_credit')->update_credit($uid, $mygid, $creditspolicy['post_credits'], $item['lastdate']);
					} else {
						C::t('#fansclub#plugin_fans_credit')->insert(array(
							'uid' => $uid,
							'username' => $username,
							'gid' => $mygid,
							'weekcredits' => $creditspolicy['post_credits'],
							'credits' => $creditspolicy['post_credits'],
							'lastdate' => TIMESTAMP,
						));
					}
					
					//球迷个人积分日志				
					C::t('#fansclub#plugin_fans_credit_log')->insert(array(
						'uid' => $uid,
						'username' => $username,
						'tid' => $tid,
						'fid' => $_G['forum']['fid'],
						'gid' => $mygid,
						'operation' => 'NTR',
						'credits' => $creditspolicy['post_credits'],
						'dateline' => TIMESTAMP,
						'ip' => $_G['clientip'],
					));
				}
			}
		} elseif ($action == 'post_reply_succeed') {
			if ($mygid > 0) {
				$group = C::t('forum_forum')->fetch_info_by_fid($mygid);			
				$level = DB::fetch_first('SELECT * FROM '.DB::table('forum_grouplevel').' WHERE levelid='.$group['level']);
				$creditspolicy = dunserialize($level['creditspolicy']);
	// 			var_dump($creditspolicy);die;
				if ($creditspolicy['reply'] == 1) {
					//球迷会积分业务处理
					if ($balanceid = $this->checkfansclubcredits($mygid)) {
						C::t('#fansclub#plugin_fansclub_balance')->update_fansclub_balance($mygid, (empty($creditspolicy['post_credits']) ? 0 : intval($creditspolicy['post_credits'])));
					} else {
						$balanceid = C::t('#fansclub#plugin_fansclub_balance')->insert(array('relation_fid'=>$mygid, 'extendcredits3'=>$creditspolicy['reply_credits']));
					}
					//球迷会积分写日志
					$fansclubcredits_log_data = array(
						'balance_id' => $balanceid,
						'type' => 'RPL',
						'amount' => $creditspolicy['reply_credits'],
						'log_time' => TIMESTAMP,
						
					);
					C::t('#fansclub#plugin_fansclub_balance_log')->insert($fansclubcredits_log_data);
					
					//球迷个人积分处理
					if ($item = C::t('#fansclub#plugin_fans_credit')->fetch_credit_by_fid_uid($uid, $mygid)) {
						C::t('#fansclub#plugin_fans_credit')->update_credit($uid, $mygid, $creditspolicy['reply_credits'], $item['lastdate']);
					} else {
						C::t('#fansclub#plugin_fans_credit')->insert(array(
							'uid' => $uid,
							'username' => $username,
							'gid' => $mygid,
							'weekcredits' => $creditspolicy['reply_credits'],
							'credits' => $creditspolicy['reply_credits'],
							'lastdate' => TIMESTAMP,
						));
					}
					
					//球迷个人积分日志				
					C::t('#fansclub#plugin_fans_credit_log')->insert(array(
						'uid' => $uid,
						'username' => $username,
						'fid' => $_G['forum']['fid'],
						'gid' => $mygid,
						'operation' => 'RPL',
						'credits' => $creditspolicy['reply_credits'],
						'dateline' => TIMESTAMP,
						'ip' => $_G['clientip'],
					));
				}
			}
		}	
	}
	
	// 省市下拉框 名称一定是 group_ 开头 by zhangjh
	function group_province_city_select()
	{
		global $_G;
		include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
		$fansclubcityhtml = fansclub_showdistrict(array(0, 0, 0, 0), array('fansclubprovince', 'fansclubcity', 'fansclubdistrict', 'fansclubcommunity'), 'fansclubcitybox', null, 'fansclub');
		$fansclub_info = get_fansclub_info($_G['gp_fid'] + 0);
		include template("fansclub:ajax/province_city_select");
		return $html; //  html 是模板里的 block 的名字
	}
	
	// 版块下拉框 by zhangjh
	function group_league_club_star_select()
	{
		global $_G;
		include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
		$fansclub_info = get_fansclub_info($_G['gp_fid'] + 0);
		$arr_forum_list = fansclub_get_forum_list();
		include template("fansclub:ajax/province_city_select");
		return $league_club_star; //  html 是模板里的 block 的名字
	}
	
	function group_member_list()
	{
		global $_G;
		if($_G['gp_mod'] == 'group' || $_G['gp_action'] == 'memberlist') // 群组的成功管理列表
		{
			$arr_group_user_level = fansclub_get_member_level();
			
			$arr_moderator = array();
			//print_r($arr_group_user_level);
			for($i = 0; $i < count($arr_group_user_level); $i++)
			{
				$id = $arr_group_user_level[$i]['id'];
				$title = $arr_group_user_level[$i]['title'];
				$moderator = $arr_group_user_level[$i]['moderator'];
				// 其他的权限以后再加
				if($moderator == '1') // 是管理组的
				{
					$arr_moderator[$id]['title'] = $title;
				}
			}
			$_G['cache']['fansclub_group_user_moderator'] = $arr_moderator;
		}
		return null;
	}
	/*
	 *   管理小组
	 *	  by:mai
	 */
	function group_info_manage(){
		global $_G;
		foreach($_G['forum']['moderators'] as $key=>$val){
			$userinfoarr[$val['level']][$key]=$val;
			$userinfoarr[$val['level']][$key]['avatar'] = avatar($val['uid'], 'small', 1);
		}
		
		include template("fansclub:right/info_manage");
		return $html;
	}
	
	function group_viewthread_credits_ban()
	{
		//return plugin_fansclub_forum::viewthread_credits_ban();
		// zhangjh 2015-06-18 不显示积分榜
		return '';
	}
	
	/*
	 *   管理小组
	 *	  by:mai
	 */
	function forumdisplay_info_manage(){
		global $_G;
		require_once libfile('function/extends');
		foreach($_G['forum']['moderators'] as $key=>$val){
			$userinfoarr[$val['level']][$key]=$val;
			$userinfoarr[$val['level']][$key]['avatar']=avatar($val['uid'], 'small', 1);
			$userinfoarr[$val['level']][$key]['bio'] = get_member_info_by_uid($val['uid'], 'bio');
		}
// 		var_dump($userinfoarr);die;
		include template("fansclub:right/info_manage");
		return $html;
	}
	
	//球迷会首页动态数处理
	function group_feed_num_output() {
		global $_G;		
		$fid = isset($_GET['fid']) ? intval($_GET['fid']) : showmessage('球迷会不存在');
		
		require_once libfile('function/feed');
		require_once libfile('function/home', 'plugin/fansclub');
		
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
		
		// ckstart($start, $perpage);
		$uids = array();
		foreach ($allfanslist as $fans) {
			$uids[] = $fans['uid'];
		}
		$struids = implode(',', $uids);
		$whereall = "`uid` IN (" . $struids . ") AND `icon` IN ('thread','album','blog','friend')";
		$today = strtotime(date('Y-m-d', TIMESTAMP));
		$wheretoday = "`uid` IN (" . $struids . ") AND `icon` IN ('thread','album','blog','friend') AND `dateline`>".$today;
		$feedallnum = C::t('#fansclub#plugin_home_feed')->count_by_where($whereall);
		$feedtodaynum = C::t('#fansclub#plugin_home_feed')->count_by_where($wheretoday);
		return '<span>今天有<font>'.$feedtodaynum.'</font>条动态</span><span>总共有<font>'.$feedallnum.'</font>条动态</span>';
	}
}
?>