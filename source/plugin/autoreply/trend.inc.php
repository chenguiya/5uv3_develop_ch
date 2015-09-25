<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */
 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once dirname(__FILE__).'/RTCreative.php';

class __plugin_autoreply_trend extends RTCreative
{
	public function __construct()
	{
		loadcache('forums');
		parent::__construct();
	}

	public function show_list()
	{

		showtagheader('div', 'threadlist', TRUE);
		$page = max(1, intval(getgpc('page')));
		$_GET['perpage'] = intval($_GET['perpage']) < 1 ? 20 : intval($_GET['perpage']);
		$perpage = $_GET['pp'] ? $_GET['pp'] : $_GET['perpage'];
		$start = ($page - 1) * $perpage;
		$postcount = C::t(self::$table['ref'])->count();
		$threadcount = C::t(self::$table['ref'])->distinct_count();
		showtableheader(sprintf(self::$plugin_lang['script']['trend_tips'], $postcount, $threadcount), 'nobottom');
		if (!$threadcount) {
			showtablerow('', 'colspan="3"', cplang('threads_thread_nonexistence'));
		} else {
			$sql = "SELECT DISTINCT(t.tid),f.* FROM ".DB::table('forum_thread')." f, ".DB::table('plugin_autoreply_ref')." t WHERE t.tid=f.tid ORDER BY f.lastpost DESC LIMIT $start, $perpage";
			$query = DB::query($sql);
			if($query) {
				while($value = DB::fetch($query)) {
					if($value['isgroup']) {
						$groupsfid[$value['fid']] = $value['fid'];
					}
					$value['lastpost'] = dgmdate($value['lastpost'], 'dt', self::$_G['setting']['timeoffset']);
					$threadlist[] = $value;
				}
			}
			if($groupsfid) {
				$query = C::t('forum_forum')->fetch_all_by_fid($groupsfid);
				foreach($query as $row) {
					$groupsname[$row['fid']] = $row['name'];
				}
			}
			if($threadlist) {
				foreach($threadlist as $thread) {
					$threads .= showtablerow('', array('', '', '', 'class="td25"', 'class="td25"'), array(
						"<a href=\"forum.php?mod=viewthread&tid=$thread[tid]".($thread['displayorder'] != -4 ? '' : '&modthreadkey='.modauthkey($thread['tid']))."\" target=\"_blank\">$thread[subject]</a>".($thread['readperm'] ? " - [".self::$lang['threads_readperm']." $thread[readperm]]" : '').($thread['price'] ? " - [".self::$lang['threads_price']." $thread[price]]" : ''),
						//"<a href=\"forum.php?mod=viewthread&tid=$thread[tid]".($thread['displayorder'] != -4 ? '' : '&modthreadkey='.modauthkey($thread['tid']))."\" target=\"_blank\">$thread[subject]</a>".($thread['readperm'] ? " - [$lang[threads_readperm] $thread[readperm]]" : '').($thread['price'] ? " - [$lang[threads_price] $thread[price]]" : ''),
					"<a href=\"forum.php?mod=forumdisplay&fid=$thread[fid]\" target=\"_blank\">".(empty($thread['isgroup']) ? self::$_G['cache']['forums'][$thread[fid]]['name'] : $groupsname[$thread[fid]])."</a>",
						"<a href=\"home.php?mod=space&uid=$thread[authorid]\" target=\"_blank\">$thread[author]</a>",
						$thread['replies'],
						$thread['views'],
						$thread['lastpost']
					), TRUE);
				}
			}

			$multi = multi($threadcount, $perpage, $page, ADMINSCRIPT."?action=plugins&operation=config&identifier=autoreply&pmod=trend&do=".self::$pluginid);
			$multi = preg_replace("/href=\"".ADMINSCRIPT."\?action=threads&amp;operation=config&amp;identifier=autoreply&amp;pmod=trend&amp;do=".self::$pluginid."&amp;page=(\d+)\"/", "href=\"javascript:page(\\1)\"", $multi);
			$multi = str_replace("window.location='".ADMINSCRIPT."?action=threads&amp;operation=config&amp;identifier=autoreply&amp;pmod=trend&amp;do=".self::$pluginid."&amp;page='+this.value", "page(this.value)", $multi);
			showformheader("plugins&identifier=autoreply&pmod=trend&do=".self::$pluginid."&frame=no".(self::$operation ? '&operation='.self::$operation : ''), 'target="threadframe"');

			showsubtitle(array('subject', 'forum', 'author', 'threads_replies', 'threads_views', 'threads_lastpost'));
			echo $threads;
			showsubmit('', '', '', '', $multi);
			showformfooter();
		}
		showtablefooter();
		echo '<iframe name="threadframe" style="display:none"></iframe>';
		showtagfooter('div');
	}
}

$trend = new __plugin_autoreply_trend;
$trend->show_list();
