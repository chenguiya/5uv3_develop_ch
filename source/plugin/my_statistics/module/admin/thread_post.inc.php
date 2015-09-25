<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) { exit('Access Denied'); }
require_once libfile('function/post');

cpheader();

$fromumanage = $_GET['fromumanage'] ? 1 : 0;

	$intypes = '';
	if($_GET['inforum'] && $_GET['inforum'] != 'all' && $_GET['intype']) {
		$foruminfo = C::t('forum_forumfield')->fetch($_GET['inforum']);
		$forumthreadtype = $foruminfo['threadtypes'];
		if($forumthreadtype) {
			$forumthreadtype = dunserialize($forumthreadtype);
			foreach($forumthreadtype['types'] as $typeid => $typename) {
				$intypes .= '<option value="'.$typeid.'"'.($typeid == $_GET['intype'] ? ' selected' : '').'>'.$typename.'</option>';
			}
		}
	}
	
	require_once libfile('function/forumlist');
	$forumselect = '<b>'.$lang['threads_search_forum'].':</b><br><br><select name="inforum" onchange="ajaxget(\'forum.php?mod=ajax&action=getthreadtypes&selectname=intype&fid=\' + this.value, \'forumthreadtype\')"><option value="all">&nbsp;&nbsp;> '.$lang['all'].'</option><option value="">&nbsp;</option>'.forumselect(FALSE, 0, 0, TRUE).'</select>';
	$typeselect = $lang['threads_move_type'].' <span id="forumthreadtype"><select name="intype"><option value=""></option>'.$intypes.'</select></span>';
	if(isset($_GET['inforum'])) {
		$forumselect = preg_replace("/(\<option value=\"$_GET[inforum]\")(\>)/", "\\1 selected=\"selected\" \\2", $forumselect);
	}

	echo <<<EOT
<script src="static/js/calendar.js"></script>
<script type="text/JavaScript">
	function page(number) {
		$('threadforum').page.value=number;
		$('threadforum').searchsubmit.click();
	}
</script>
EOT;

	
	// 查询后会消失
	// showtagheader('div', 'threadsearch', !submitcheck('searchsubmit', 1) && empty($newlist));
	// 提交URL
	showformheader('frames=yes&action=plugins&operation=config&do=34&identifier=my_statistics&pmod=admin&ac=thread_post', '', 'threadforum');
	// 表格样式
	showtableheader();
	showtablerow('', array('class="rowform" colspan="2" style="width:auto;"'), array($forumselect.$typeselect));
	if(!$fromumanage) {
		empty($_GET['starttime']) && $_GET['starttime'] = date('Y-m-d', time() - 86400 * 30);
	}
	echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
	showsetting('发表时间范围', array('starttime', 'endtime'), array($_GET['starttime'], $_GET['endtime']), 'daterange');
	
	showtagfooter('tbody');
	showsubmit('searchsubmit', 'submit', '', '');
	
	if(submitcheck('searchsubmit', 1))
	{
		// 点击了提交
		$operation == 'group' && $_GET['inforum'] = 'isgroup';
		$conditions['inforum'] = $_GET['inforum'] != '' && $_GET['inforum'] != 'all' && $_GET['inforum'] != 'isgroup' ? $_GET['inforum'] : '';
		$conditions['isgroup'] = $_GET['inforum'] != '' && $_GET['inforum'] == 'isgroup' ? 1 : 0;
		$conditions['starttime'] = $_GET['starttime'] != '' ? $_GET['starttime'] : '';
		$conditions['endtime'] = $_GET['endtime'] != '' ? $_GET['endtime'] : '';
		
		
		$fids = array();
		$tids = $threadcount = '0';
		$arr_search_result = array();
		
		if($conditions) {
			if(empty($_GET['savethread']) && !isset($conditions['displayorder']) && !isset($conditions['sticky'])) {
				$conditions['sticky'] = 5;
			}
			
			if($_GET['detail'] && FALSE) { // 显示详细的忽略
				$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
				$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
				$start = ($page - 1) * $perpage;
				$threads = '';
				$groupsname = $groupsfid = $threadlist = array();
				$threadcount = C::t('forum_thread')->count_search($conditions);
				
				if($threadcount) {
					foreach(C::t('forum_thread')->fetch_all_search($conditions, 0, $start, $perpage, 'tid', 'DESC', ' FORCE INDEX(PRIMARY) ') as $thread) {
						$fids[] = $thread['fid'];
						if($thread['isgroup']) {
							$groupsfid[$thread[fid]] = $thread['fid'];
						}
						$thread['lastpost'] = dgmdate($thread['lastpost']);
						$threadlist[] = $thread;
					}
					if($groupsfid) {
						$query = C::t('forum_forum')->fetch_all_by_fid($groupsfid);
						foreach($query as $row) {
							$groupsname[$row[fid]] = $row['name'];
						}
					}
					if($threadlist) {
						foreach($threadlist as $thread) {
							$threads .= showtablerow('', array('class="td25"', '', '', '', 'class="td25"', 'class="td25"'), array(
								"<input class=\"checkbox\" type=\"checkbox\" name=\"tidarray[]\" value=\"$thread[tid]\" />",
								"<a href=\"forum.php?mod=viewthread&tid=$thread[tid]".($thread['displayorder'] != -4 ? '' : '&modthreadkey='.modauthkey($thread['tid']))."\" target=\"_blank\">$thread[subject]</a>".($thread['readperm'] ? " - [$lang[threads_readperm] $thread[readperm]]" : '').($thread['price'] ? " - [$lang[threads_price] $thread[price]]" : ''),
								"<a href=\"forum.php?mod=forumdisplay&fid=$thread[fid]\" target=\"_blank\">".(empty($thread['isgroup']) ? $_G['cache']['forums'][$thread[fid]]['name'] : $groupsname[$thread[fid]])."</a>",
								"<a href=\"home.php?mod=space&uid=$thread[authorid]\" target=\"_blank\">$thread[author]</a>",
								$thread['replies'],
								$thread['views'],
								$thread['lastpost']
							), TRUE);
						}
					}

					$multi = multi($threadcount, $perpage, $page, ADMINSCRIPT."?action=threads");
					$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=threads&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
					$multi = str_replace("window.location='".ADMINSCRIPT."?action=threads&amp;page='+this.value", "page(this.value)", $multi);
				}
			} else {
				$threads = '';
				$threadcount = C::t('forum_thread')->count_search($conditions);
				if($threadcount) {
					foreach(C::t('forum_thread')->fetch_all_search($conditions, 0, $start, $perpage, 'tid', 'DESC', ' FORCE INDEX(PRIMARY) ') as $thread) {
						$fids[] = $thread['fid'];
						$tids .= ','.$thread['tid'];
						
						$str_date = date('Y-m-d',$thread['dateline']);
						$arr_search_result[$str_date][$thread['fid']]++;
					}
				}
				
				//echo "<pre>";
				//print_r($arr_search_result);
				$last_date = '';
				$all_post = 0;
				if(count($arr_search_result) > 0)
				{
					foreach($arr_search_result as $key => $value)
					{
						foreach($value as $key2 => $value2)
						{
							$forum_name_info = C::t('forum_forum')->fetch($key2);
							$threads .= showtablerow('', array('style="width:200px;"', 'style="width:400px;"','','style="width:auto;"'), array(
								$key,
								$forum_name_info['name'],
								$value2,
								"",
							), TRUE);
							$all_post += $value2;
						}
					}
					$threads .=  showtablerow('', array('style="width:200px;"', 'style="width:400px;"','','style="width:auto;"'), array(
								'<b>总计</b>',
								'<b></b>',
								'<b>'.$all_post.'</b>',
								"",
							), TRUE);
				}
				$multi = '';
			}
		}
		
		showtableheader('查询结果', 'notop');
		if(!$threadcount) {
			showtablerow('', 'colspan="3"', cplang('threads_thread_nonexistence'));
		} else {
				showsubtitle(array('发表时间', 'forum', '发帖数',''));
				echo $threads;
				
				showtablefooter();
		}

	}
	
	showtablefooter();
	showformfooter();
	showtagfooter('div');

?>