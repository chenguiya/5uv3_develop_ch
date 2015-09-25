<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/forum');
require_once libfile('function/forumlist');
require_once libfile('function/credit');
require_once libfile('function/post');
require_once DISCUZ_ROOT.'./source/plugin/autoreply/RTCreative.php';
if (file_exists(DISCUZ_ROOT.'./source/plugin/autoreply/include/functions.inc.php')) {
	require_once DISCUZ_ROOT.'./source/plugin/autoreply/include/functions.inc.php';
} else {
	require_once DISCUZ_ROOT.'./source/plugin/autoreply/include/function.inc.php';
}
if (file_exists(DISCUZ_ROOT.'./source/plugin/autoreply/include/super_var.inc.php')) {
	require_once DISCUZ_ROOT.'./source/plugin/autoreply/include/super_var.inc.php';
}
if (file_exists(DISCUZ_ROOT.'./source/plugin/autoreply/include/multi_reply.inc.php')) {
	require_once DISCUZ_ROOT.'./source/plugin/autoreply/include/multi_reply.inc.php';
}
class plugin_autoreply_forum extends RTCreative
{
	const DEBUG = false;
	public function __construct()
	{
		parent::__construct();
	}
	
	private function autoreply()
	{
		if (!self::$setting['autoreply_open']) {
			return '';
		}
		$forums = array();
		if (self::$setting['forums']) {
			$forums = unserialize(self::$setting['forums']);
			$forums = array_filter($forums);
			$forums = array_values($forums);
		}

		if (self::$setting['working_time']) {
			$arr = explode('-', self::$setting['working_time']);
			if ($arr) {
				$start = intval(trim($arr[0]));
				$end = intval(trim($arr[1]));
				if ($this->is_time($start) && $this->is_time($end)) {
					$cur_hour = date('G');
					if ($cur_hour < $start || $cur_hour > $end) {
						return '';
					}
				}
			}
		}

		$member_tmp = C::t(self::$table['member'])->range(0, 1000);
		if (empty($member_tmp)) {
			return '';
		}

		if (self::$setting['plock']) {
			while(discuz_process::islocked('plugin_autoreply', 3)){
				return '';
			}
		}
		$threads = array();
		$thread_ids = trim(self::$setting['thread_ids']);
		if ($thread_ids && (mt_rand()/mt_getrandmax()) <= 0.5) {
			$tids = explode(',', $thread_ids);
			$tids = array_filter($tids);
			if ($tids) {
				$tid = $tids[mt_rand(0, count($tids)-1)];
				$threads = C::t(self::$table['forum_thread'])->fetch_by_tid($tid);
			}
		}
		if (!$threads && $forums) {
			$dateline = $this->transfor_autoreply_thread_dateline();
			$new = self::$setting['autoreply_priority']==1?true:false;
			if (self::$setting['autoreply_speed'] == 2 && function_exists('_autoreply_multi_reply')) {
				$threads = C::t(self::$table['forum_thread'])->fetch($new, $dateline, $forums, 0, _autoreply_multi_reply());
			} else {
				$threads = C::t(self::$table['forum_thread'])->fetch($new, $dateline, $forums, 0, 1);
			}
		}
		if (empty($threads)) {
			if (self::$setting['plock']) {
				discuz_process::unlock('plugin_autoreply');
			}
			return '';	
		}

		$modnewreplies = 0;
		foreach ($member_tmp as $t) {
			$members[] = array(
				'uid'      => $t['uid'],
				'username' => $t['username'],
			);			
		}
		$members_count = count($members);
		$cur_forum = array();
		$update_login_status = array();
		foreach ($threads as $thread) {
			if (!$this->check_thread($thread)) {
				continue;
			}

			$index  = mt_rand(0, $members_count - 1);
			$member = $members[$index];
			$i = 3;
			$member = array();
			do {
				$index  = mt_rand(0, $members_count - 1);
				$member = $members[$index];
				if (C::t(self::$table['ref'])->fetch_by_tid_uid($thread['tid'], $member['uid'])) {
					$member = null;
					continue;	
				} else {
					break;
				}
			} while (--$i>0);
			
			$message = $this->get_message($thread['fid'], $member['username'], $thread['author'], $thread['subject']);
			if ($message == '') {
				continue;	
			}
			
			if (!$member) {
				if (self::$setting['plock']) {
					discuz_process::unlock('plugin_autoreply');
				}
				return '';	
			}
			$heatthreadset = $this->update_threadpartake($member['uid'], $thread['tid'], true);
			if($thread['displayorder'] == -4) {
				$modnewreplies = 0;
			}
			//$pinvisible = $modnewreplies ? -2 : ($thread['displayorder'] == -4 ? -3 : 0);
			if (!isset($cur_forum[$thread['fid']])) {
				$f = C::t('forum_forum')->fetch_all_by_fid($thread['fid']);
				$f && $cur_forum[$thread['fid']] = $f[$thread['fid']];
			}
			$isanonymous = 0;
			if (self::$setting['autoreply_isanonymous'] && $cur_forum[$thread['fid']]['allowanonymous']) {
				$isanonymous = 1;
			}
			if (!isset($update_login_status[$member['uid']])) {
				$m = C::t('common_member_status')->fetch($member['uid']);
				if ($m && $m['regip'] != 'Manual Acting') {
					$regip = $m['regip'];
				} else {
					$regip = _autoreply_get_random_ip();
					C::t('common_member_status')->update($member['uid'], array(
						'regip'  => $regip,
						'lastip' => $regip,
					));
				}
				$update_login_status[$member['uid']] = $regip;
			}
			$ismobile = false;
			if (self::$setting['autoreply_mobile']) {
				$n = intval(self::$setting['autoreply_mobile']);
				if ($n > 100) {
					$n = 100;
				}
				if (mt_rand(0, 100) <= $n) {
					$ismobile = true;
				}
			}
			$status = $ismobile?8:0;
			$pid = insertpost(array(
				'fid'         => $thread['fid'],
				'tid'         => $thread['tid'],
				'first'       => 0,
				'author'      => $member['username'],
				'authorid'    => $member['uid'],
				'subject'     => '',
				'dateline'    => self::$_G['timestamp'],
				'message'     => $message,
				'useip'       => $update_login_status[$member['uid']],
				'invisible'   => 0,
				'anonymous'   => $isanonymous,
				'usesig'      => 0,
				'htmlon'      => 0,
				'bbcodeoff'   => -1,
				'smileyoff'   => 0,
				'parseurloff' => 0,
				'attachment'  => 0,
				'status'      => $status,
			));
			$updatethreaddata   = $heatthreadset ? $heatthreadset : array();
			$postionid          = C::t('forum_post')->fetch_maxposition_by_tid($thread['posttableid'], $thread['tid']);
			$updatethreaddata[] = DB::field('maxposition', $postionid);
			if(getstatus($thread['status'], 3) && $postionid) {
				$rushinfo      = C::t('forum_threadrush')->fetch($thread['tid']);
				$rushstopfloor = $rushinfo['stopfloor'];
				if($rushstopfloor > 0 && $thread['closed'] == 0 && $postionid >= $rushstopfloor) {
					$updatethreaddata[] = 'closed=1';
				}
			}
			useractionlog($member['uid'], 'pid');
			if($thread['authorid'] != $member['uid'] && getstatus($thread['status'], 6) && !$isanonymous && !$modnewreplies) {
				$thapost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($thread['tid'], 0);
				$this->notification_add($member, $thapost['authorid'], 'post', 'reppost_noticeauthor', array(
					'tid'         => $thread['tid'],
					'subject'     => $thread['subject'],
					'fid'         => $thread['fid'],
					'pid'         => $pid,
					'from_id'     => $thread['tid'],
					'from_idtype' => 'post',
				), 0);
			}

			if($thread['replycredit'] > 0 && !$modnewreplies && $thread['authorid'] != $member['uid'] && $member['uid']) {
				$replycredit_rule = C::t('forum_replycredit')->fetch($thread['tid']);
				if(!empty($replycredit_rule['times'])) {
					$have_replycredit = C::t('common_credit_log')->count_by_uid_operation_relatedid($member['uid'], 'RCA', $thread['tid']);
					if($replycredit_rule['membertimes'] - $have_replycredit > 0 && $thread['replycredit'] - $replycredit_rule['extcredits'] >= 0) {
						$replycredit_rule['extcreditstype'] = $replycredit_rule['extcreditstype'] ? $replycredit_rule['extcreditstype'] : self::$_G['setting']['creditstransextra'][10];
						if($replycredit_rule['random'] > 0) {
							$rand = mt_rand(1, 100);
							$rand_replycredit = $rand <= $replycredit_rule['random'] ? true : false ;
						} else {
							$rand_replycredit = true;
						}
						if($rand_replycredit) {
							$this->updatemembercount($member['uid'], array($replycredit_rule['extcreditstype'] => $replycredit_rule['extcredits']), 1, 'RCA', $thread['tid']);
							C::t('forum_post')->update('tid:'.$thread['tid'], $pid, array('replycredit' => $replycredit_rule['extcredits']));
							$updatethreaddata[] = DB::field('replycredit', $thread['replycredit'] - $replycredit_rule['extcredits']);
						}
					}
				}
			}
			deletethreadcaches($thread['tid']);
			include_once libfile('function/stat');
			$this->updatestat($member['uid'], $thread['isgroup'] ? 'grouppost' : 'post');
			if (!$modnewreplies) {
				$fieldarr = array(
					'lastposter' => array($member['username']),
					'replies'    => 1,
				);
				if($thread['lastpost'] < self::$_G['timestamp']) {
					$fieldarr['lastpost'] = array(self::$_G['timestamp']);
				}
				$views = 1;
				$row = C::t('forum_threadaddviews')->fetch($thread['tid']);
				if(!empty($row)) {
					C::t('forum_threadaddviews')->update($thread['tid'], array('addviews' => 0));
					$views += $row['addviews'];
				}
				if (self::$setting['autoreply_views'] > 0) {
					$views += $thread['replies']?ceil($thread['replies']*self::$setting['autoreply_views']):ceil(self::$setting['autoreply_views']);
				}
				$fieldarr['views'] = $views;
				$updatethreaddata = array_merge($updatethreaddata, C::t('forum_thread')->increase($thread['tid'], $fieldarr, false, 0, true));
				if($thread['displayorder'] != -4) {
					$this->updatepostcredits('+', $member['uid'], 'reply', $thread['fid']);
					$lastpost = "$thread[tid]\t$thread[subject]\t".self::$_G['timestamp']."\t{$member['username']}";
					C::t('forum_forum')->update($thread['fid'], array('lastpost' => $lastpost));
					C::t('forum_forum')->update_forum_counter($thread['fid'], 0, 1, 1);
					if(isset($cur_forum[$thread['fid']]) && $cur_forum[$thread['fid']]['type'] == 'sub') {
						C::t('forum_forum')->update($cur_forum[$thread['fid']]['fup'], array('lastpost' => $lastpost));
					}
				}
				if($updatethreaddata) {
					C::t('forum_thread')->update($thread['tid'], $updatethreaddata, false, false, 0, true);
				}
			}
			if (!C::t(self::$table['ref'])->fetch_by_tid_uid($thread['tid'], $member['uid'])) {
				C::t(self::$table['ref'])->insert(array(
					'tid'         => $thread['tid'],
					'uid'         => $member['uid'],
					'insert_time' => date('Y-m-d H:i:s', self::$_G['timestamp']),
				));
			}
			C::t('common_member_status')->update($member['uid'], array(
				'lastvisit'    => (self::$_G['timestamp']- mt_rand(30, 300)),
				'lastpost'	   => self::$_G['timestamp'],
				'lastactivity' => self::$_G['timestamp'],
				)
			);
		}
		if (self::$setting['plock']) {
			discuz_process::unlock('plugin_autoreply');
		}
		return '';
	}

	private function check_thread($thread)
	{
		if (empty($thread)) {
			return false;
		}
		if ($thread['replies'] == 0 && intval(self::$setting['autoreply_delay']) > 0) {
			if (strstr(self::$setting['autoreply_delay'], '-')) {
				$arr = explode('-', self::$setting['autoreply_delay']);
				$delay = mt_rand(intval($arr[0]), intval($arr[1]));
			} else {
				$delay = intval(self::$setting['autoreply_delay']);
			}
			if (time() - $thread['dateline'] < $delay) {
				return false;
			}
		}
		if(!$thread['isgroup'] && checkautoclose($thread)) {
			return false;
		}
		$member_replies = C::t(self::$table['ref'])->count_by_tid($thread['tid']);
		if ($member_replies >= self::$setting['autoreply_number']) {
			return false;
		}
		if (self::$setting['autoreply_speed'] == 2) {
			if ((self::$_G['timestamp'] - $thread['lastpost']) < $interval) {
				return false;
			}
		} else {
			$interval = $this->transfor_autoreply_interval();
			$reply = C::t(self::$table['ref'])->fetch_newest();
			if ($reply) {
				if (time() -  strtotime($reply['insert_time']) < $interval) {
					return false;
				}
			}
		}
		$noreply_tid = trim(self::$setting['noreply_tid']);
		$noreply_tid = explode(',', $noreply_tid);
		if (in_array($thread['tid'], $noreply_tid)) {
			return false;
		}
		return true;
	}

	private function superman_autoreply()
	{
		try{
			return $this->autoreply();
		} catch (Exception $e) {
			if (self::$setting['debug']) {
				return $e->xdebug_message;
			} else {
				return "<!-- Exception: {$e->xdebug_message} -->";
			}
		} catch (DbException $e) {
			if (self::$setting['debug']) {
				return $e->xdebug_message;
			} else {
				return "<!-- DbException: sql={$e->sql}, {$e->xdebug_message} -->";
			}
		}
		return '';
	}

	private function update_threadpartake($uid, $tid, $getsetarr = false)
	{
		$setarr = array();
		if($uid && $tid) {
			if(self::$_G['setting']['heatthread']['period']) {
				$partaked = C::t('forum_threadpartake')->fetch($tid, $uid);
				$partaked = $partaked['uid'];
				if(!$partaked) {
					C::t('forum_threadpartake')->insert(array('tid' => $tid, 'uid' => $uid, 'dateline' => self::$_G['timestamp']));
					$setarr = C::t('forum_thread')->increase($tid, array('heats' => 1), false, 0, $getsetarr);
				}
			} else {
				$setarr = C::t('forum_thread')->increase($tid, array('heats' => 1), false, 0, $getsetarr);

			}
		}
		if($getsetarr) {
			return $setarr;
		}
	}

	private function updatemembercount($uids, $dataarr = array(), $checkgroup = true, $operation = '', $relatedid = 0, $ruletxt = '')
	{
		if($operation && $relatedid) {
			$writelog = true;
		} else {
			$writelog = false;
		}
		$data = $log = array();
		foreach($dataarr as $key => $val) {
			if(empty($val)) continue;
			$val = intval($val);
			$id = intval($key);
			$id = !$id && substr($key, 0, -1) == 'extcredits' ? intval(substr($key, -1, 1)) : $id;
			if(0 < $id && $id < 9) {
				$data['extcredits'.$id] = $val;
				if($writelog) {
					$log['extcredits'.$id] = $val;
				}
			} else {
				$data[$key] = $val;
			}
		}
		if($writelog) {
			credit_log($uids, $operation, $relatedid, $log);
		}
		if($data) {
			$this->_updatemembercount($data, $uids, $checkgroup, $ruletxt);
		}
	}

	private function _updatemembercount($creditarr, $uids = 0, $checkgroup = true, $ruletxt = '')
	{
		$uids = is_array($uids) ? $uids : array($uids);
		if($uids && ($creditarr || $this->extrasql)) {
			if($this->extrasql) $creditarr = array_merge($creditarr, $this->extrasql);
			$sql = array();
			$allowkey = array('extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6', 'extcredits7', 'extcredits8', 'friends', 'posts', 'threads', 'oltime', 'digestposts', 'doings', 'blogs', 'albums', 'sharings', 'attachsize', 'views', 'todayattachs', 'todayattachsize');
			$creditnotice = self::$_G['setting']['creditnotice'];
			if($creditnotice) {
				if(!isset(self::$_G['cookiecredits'])) {
					self::$_G['cookiecredits'] = getcookie('creditnotice') ? explode('D', getcookie('creditnotice')) : array_fill(0, 9, 0);
					for($i = 1; $i <= 8; $i++) {
						self::$_G['cookiecreditsbase'][$i] = getuserprofile('extcredits'.$i);
					}
				}
				if($ruletxt) {
					self::$_G['cookiecreditsrule'][$ruletxt] = $ruletxt;
				}
			}
			foreach($creditarr as $key => $value) {
				if(!empty($key) && $value && in_array($key, $allowkey)) {
					$sql[$key] = $value;
					if($creditnotice && substr($key, 0, 10) == 'extcredits') {
						$i = substr($key, 10);
						self::$_G['cookiecredits'][$i] += $value;
					}
				}
			}
			if($creditnotice) {
				dsetcookie('creditnotice', implode('D', self::$_G['cookiecredits']).'D'.$uids[0]);
				dsetcookie('creditbase', '0D'.implode('D', self::$_G['cookiecreditsbase']));
				if(!empty(self::$_G['cookiecreditsrule'])) {
					dsetcookie('creditrule', strip_tags(implode("\t", self::$_G['cookiecreditsrule'])));
				}
			}
			if($sql) {
				C::t('common_member_count')->increase($uids, $sql);
			}
			if($checkgroup && count($uids) == 1) $this->checkusergroup($uids[0]);
			$this->extrasql = array();
		}
	}

	private function checkusergroup($uid) 
	{
		$groupid = 0;
		if(!$uid) return $groupid;
		$member = getuserbyuid($uid);
		if(empty($member)) return $groupid;

		$credits = $this->countcredit($uid, false);
		$updatearray = array();
		$groupid = $member['groupid'];
		$group = C::t('common_usergroup')->fetch($member['groupid']);
		if($member['credits'] != $credits) {
			$updatearray['credits'] = $credits;
			$member['credits'] = $credits;
		}
		$member['credits'] = $member['credits'] == '' ? 0 : $member['credits'] ;
		$sendnotify = false;
		if(empty($group) || $group['type'] == 'member' && !($member['credits'] >= $group['creditshigher'] && $member['credits'] < $group['creditslower'])) {
			$newgroup = C::t('common_usergroup')->fetch_by_credits($member['credits']);
			if(!empty($newgroup)) {
				if($member['groupid'] != $newgroup['groupid']) {
					$updatearray['groupid'] = $groupid = $newgroup['groupid'];
					$sendnotify = true;
				}
			}
		}
		if($updatearray) {
			C::t('common_member')->update($uid, $updatearray);
		}
		if($sendnotify) {
			notification_add($uid, 'system', 'user_usergroup', array(
				'usergroup' => '<a href="home.php?mod=spacecp&ac=credit&op=usergroup">'.$newgroup['grouptitle'].'</a>', 
				'from_id' => 0, 
				'from_idtype' => 'changeusergroup'
			), 1);
		}

		return $groupid;
	}

	private function countcredit($uid, $update = true) 
	{
		global $_G;

		$credits = 0;
		if($uid && !empty($_G['setting']['creditsformula'])) {
			$member = C::t('common_member_count')->fetch($uid);
			$credits = round($_G['setting']['creditsformula']);
			if($uid == $_G['uid']) {
				if($update && $_G['member']['credits'] != $credits) {
					C::t('common_member')->update_credits($uid, $credits);
					$_G['member']['credits'] = $credits;
				}
			} elseif($update) {
				C::t('common_member')->update_credits($uid, $credits);
			}
		}
		return $credits;
	}

	private function updatestat($uid, $type, $primary=0, $num=1)
	{
		$updatestat = getglobal('setting/updatestat');
		if(empty($uid) || empty($updatestat)) return false;
		C::t('common_stat')->updatestat($uid, $type, $primary, $num);
	}

	private function updatepostcredits($operator, $uidarray, $action, $fid = 0) {
		$val = $operator == '+' ? 1 : -1;
		$extsql = array();
		if(empty($uidarray)) {
			return false;
		}
		$uidarray = (array)$uidarray;
		$uidarr = array();
		foreach($uidarray as $uid) {
			$uidarr[$uid] = !isset($uidarr[$uid]) ? 1 : $uidarr[$uid]+1;
		}
		foreach($uidarr as $uid => $coef) {
			$opnum = $val*$coef;
			if($action == 'reply') {
				$extsql = array('posts' => $opnum);
			} elseif($action == 'post') {
				$extsql = array('threads' => $opnum, 'posts' => $opnum);
			}
			if(empty($uid)) {
				continue;
			} else {
				batchupdatecredit($action, $uid, $extsql, $opnum, $fid);
			}
		}
		if($operator == '+' && ($action == 'reply' || $action == 'post')) {
			C::t('common_member_status')->update(array_keys($uidarr), array('lastpost' => self::$_G['timestamp']), 'UNBUFFERED');
		}
	}

	public function index_bottom_output()
	{
		return $this->superman_autoreply();
	}

	public function forumdisplay_bottom_output()
	{
		return $this->superman_autoreply();
	}

	public function viewthread_bottom_output()
	{
		return $this->superman_autoreply();
	}

	public function viewthread_postbottom_output()
	{
		$ret = array();
		if (self::$setting['autoreply_ad_open'] && self::$setting['autoreply_ad']) {
			if (C::t(self::$table['ref'])->fetch_by_tid(self::$_G['tid'])) {
				$ret[1] = self::$setting['autoreply_ad'];
			}
		}
		return $ret;
	}

	private function notification_add($member, $touid, $type, $note, $notevars = array(), $system = 0, $category = -1)
	{
		if(!($tospace = getuserbyuid($touid))) {
			return false;
		}
		space_merge($tospace, 'field_home');
		$filter = empty($tospace['privacy']['filter_note'])?array():array_keys($tospace['privacy']['filter_note']);

		if($filter && (in_array($type.'|0', $filter) || in_array($type.'|'.$member['uid'], $filter))) {
			return false;
		}
		if($category == -1) {
			$category = 0;
			$categoryname = '';
			if($type == 'follow' || $type == 'follower') {
				switch ($type) {
							case 'follow' : $category = 5; break;
							case 'follower' : $category = 6; break;
						}
				$categoryname = $type;
			} else {
				foreach(self::$_G['notice_structure'] as $key => $val) {
					if(in_array($type, $val)) {
						switch ($key) {
							case 'mypost' : $category = 1; break;
							case 'interactive' : $category = 2; break;
							case 'system' : $category = 3; break;
							case 'manage' : $category = 4; break;
							default :  $category = 0;
						}
						$categoryname = $key;
						break;
					}
				}
			}
		} else {
			switch ($category) {
				case 1 : $categoryname = 'mypost'; break;
				case 2 : $categoryname = 'interactive'; break;
				case 3 : $categoryname = 'system'; break;
				case 4 : $categoryname = 'manage'; break;
				case 5 : $categoryname = 'follow'; break;
				case 6 : $categoryname = 'follower'; break;
				default :  $categoryname = 'app';
			}
		}
		if($category == 0) {
			$categoryname = 'app';
		} elseif($category == 1 || $category == 2) {
			$categoryname = $type;
		}
		$notevars['actor'] = "<a href=\"home.php?mod=space&uid=$member[uid]\">".$member['username']."</a>";
		if(!is_numeric($type)) {
			$vars = explode(':', $note);
			if(count($vars) == 2) {
				$notestring = lang('plugin/'.$vars[0], $vars[1], $notevars);
			} else {
				$notestring = lang('notification', $note, $notevars);
			}
			$frommyapp = false;
		} else {
			$frommyapp = true;
			$notestring = $note;
		}

		$oldnote = array();
		if($notevars['from_id'] && $notevars['from_idtype']) {
			$oldnote = C::t('home_notification')->fetch_by_fromid_uid($notevars['from_id'], $notevars['from_idtype'], $touid);
		}
		if(empty($oldnote['from_num'])) $oldnote['from_num'] = 0;
		$notevars['from_num'] = $notevars['from_num'] ? $notevars['from_num'] : 1;
		$setarr = array(
			'uid' => $touid,
			'type' => $type,
			'new' => 1,
			'authorid' => $member['uid'],
			'author' => $member['username'],
			'note' => $notestring,
			'dateline' => self::$_G['timestamp'],
			'from_id' => $notevars['from_id'],
			'from_idtype' => $notevars['from_idtype'],
			'from_num' => ($oldnote['from_num']+$notevars['from_num']),
			'category' => $category
		);
		if (!$this->exist_field('home_notification', 'category')) {
			unset($setarr['category']);
		}
		if($system) {
			$setarr['authorid'] = 0;
			$setarr['author'] = '';
		}
		$pkId = 0;
		if($oldnote['id']) {
			C::t('home_notification')->update($oldnote['id'], $setarr);
			$pkId = $oldnote['id'];
		} else {
			$oldnote['new'] = 0;
			$pkId = C::t('home_notification')->insert($setarr, true);
		}
		$banType = array('task');
		if(self::$_G['setting']['cloud_status'] && !in_array($type, $banType)) {
			$noticeService = Cloud::loadClass('Service_Client_Notification');
			if($oldnote['id']) {
				$noticeService->update($touid, $pkId, $setarr['from_num'], $setarr['dateline']);
			} else {
				$extra = $type == 'post' ? array('pId' => $notevars['pid']) : array();
				$noticeService->add($touid, $pkId, $type, $setarr['authorid'], $setarr['author'], $setarr['from_id'], $setarr['from_idtype'], $setarr['note'], $setarr['from_num'], $setarr['dateline'], $extra);
			}
		}

		if(empty($oldnote['new'])) {
			C::t('common_member')->increase($touid, array('newprompt' => 1));
			$newprompt = C::t('common_member_newprompt')->fetch($touid);
			if($newprompt) {
				$newprompt['data'] = unserialize($newprompt['data']);
				if(!empty($newprompt['data'][$categoryname])) {
					$newprompt['data'][$categoryname] = intval($newprompt['data'][$categoryname]) + 1;
				} else {
					$newprompt['data'][$categoryname] = 1;
				}
				C::t('common_member_newprompt')->update($touid, array('data' => serialize($newprompt['data'])));
			} else {
				C::t('common_member_newprompt')->insert($touid, array($categoryname => 1));
			}
			require_once libfile('function/mail');
			$mail_subject = lang('notification', 'mail_to_user');
			sendmail_touser($touid, $mail_subject, $notestring, $frommyapp ? 'myapp' : $type);
		}

		if(!$system && $member['uid'] && $touid != $member['uid']) {
			C::t('home_friend')->update_num_by_uid_fuid(1, $member['uid'], $touid);
		}
	}

	private function transfor_autoreply_interval()
	{
		switch (self::$setting['autoreply_interval']) {
			case 1:
				return mt_rand(10, 30);
			case 2:
				return mt_rand(30, 60);
			case 3:
				return mt_rand(60, 300);
			case 4:
				return mt_rand(300, 600);
			case 5:
				return mt_rand(600, 900);
			case 6:
				return mt_rand(900, 1200);
			case 7:
				return mt_rand(1200, 1500);
			case 8:
				return mt_rand(1500, 1800);
			default:
				return mt_rand(10, 1800);
		}
	}
	
	private function transfor_autoreply_thread_dateline()
	{
		switch (self::$setting['autoreply_thread_dateline']) {
			case 1:
				return 0;
			case 2:
				return self::$_G['timestamp'] - (3 * 24 * 60 * 60);
			case 3:
				return self::$_G['timestamp'] - (7 * 24 * 60 * 60);
			case 4:
				return self::$_G['timestamp'] - (15 * 24 * 60 * 60);
			case 5:
				return self::$_G['timestamp'] - (30 * 24 * 60 * 60);
			case 6:
				return self::$_G['timestamp'] - (90 * 24 * 60 * 60);
			case 7:
				return self::$_G['timestamp'] - (365 * 24 * 60 * 60);
			case 8:
				return strtotime(date('Y-m-d 00:00:00'));
			default:
				return 0;
		}
	}

	private function exist_field($table, $field)
	{
		static $exist = null;
		if (is_null($exist)) {
			$exist = DB::fetch_first('DESC '.DB::table($table)." `$field`");
		}
		return $exist?true:false;
	}

	private function is_time($t)
	{
		return in_array($t, array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23))?true:false;	
	}

	private function get_message( $fid, $username, $author, $subject)
	{
		global $_G;
		static $messages = null;
		if (is_null($messages)) {
			$message_file = $_G['setting']['attachdir'].'plugin_autoreply/autoreply.data';
			if (file_exists($message_file)) {
				$contents = file_get_contents($message_file);
				if ($contents) {
					$messages = unserialize($contents);
				}
			} 

			if ((is_null($messages) || empty($messages[$fid])) && self::$setting['autoreply_say']) {
				$messages[0] = explode("\r\n", self::$setting['autoreply_say']);
			}
		}
		
		if (isset($messages[$fid]) && $messages[$fid]) {
			$rand_index = mt_rand(0, count($messages[$fid]) - 1);
			$message = $messages[$fid][$rand_index];			
		} else {
			$rand_index = mt_rand(0, count($messages[0]) - 1);
			$message = $messages[0][$rand_index];
		}
		if(file_exists(DISCUZ_ROOT.'./source/plugin/autoreply/include/super_var.inc.php')){
			$message = _autoreply_get_super_var($message,$username,$author,$subject);
		}
		return str_replace("<br>", "\n", $message);
	}

	private function debug($var, $exit = false)
	{
		global $_G;
		if (self::DEBUG && $_GET['xx'] == 16888) {
			echo '<pre>';
			print_r($var);
			echo '</pre>';
			if ($exit) exit;
		}	
	}
}
