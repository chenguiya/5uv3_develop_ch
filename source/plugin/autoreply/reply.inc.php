<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if (file_exists(DISCUZ_ROOT.'./source/plugin/autoreply/include/functions.inc.php')) {
require_once DISCUZ_ROOT.'./source/plugin/autoreply/include/functions.inc.php';
} else {
require_once DISCUZ_ROOT.'./source/plugin/autoreply/include/function.inc.php';
}

$url_params = "plugins&operation=config&identifier=autoreply&pmod=reply&do=$pluginid";
$message_path = $_G['setting']['attachdir'].'plugin_autoreply';
$message_filename = 'autoreply.data';
if (!submitcheck('replysubmit')) {
	manage_autoreply_message($message_path, $message_filename, $url_params);
} else {
	update_autoreply_message($message_path, $message_filename, $url_params);
}

///////////////////////////////////////////////////////////////
function manage_autoreply_message($message_path, $message_filename, $url_params)
{
	global $_G, $scriptlang, $pluginid;
	$messages = array();
	if (file_exists("$message_path/$message_filename")) {
		$contents = file_get_contents("$message_path/$message_filename");
		if ($contents) {
			$messages = unserialize($contents);
			if ($messages) {
				foreach ($messages as $fid=>$msg) {
					$messages[$fid] = implode("\r\n", $msg);
				}	
			}
		}
	}
	$forums = DB::fetch_all("SELECT `fid`,`name`,`threads`,`posts`,`todayposts` FROM ".DB::table('forum_forum')." WHERE `status`='1' AND (`type`='forum' OR `type`='sub') ORDER BY displayorder");
	if ($forums) {
		if (!isset($_G['cache']['plugin']['autoreply'])) {
			loadcache('plugin');
		}
		$seleted_forums = $_G['cache']['plugin']['autoreply']['forums'];
		if ($seleted_forums) {
			$seleted_forums = unserialize($seleted_forums);	
			$seleted_forums = array_filter($seleted_forums);
			$seleted_forums = array_values($seleted_forums);
			if (!$seleted_forums) {
				cpmsg($scriptlang['autoreply']['reply_manage_forum_select'], "action=plugins&operation=config&do=$pluginid", 'error'); 
			}
		} else {
			cpmsg($scriptlang['autoreply']['reply_manage_forum_select'], "action=plugins&operation=config&do=$pluginid", 'error'); 
		}
		showformheader($url_params);
		showtableheader($scriptlang['autoreply']['reply_manage_tips']);
		$ret = '';
		foreach ($forums as $forum) {
			if (!in_array($forum['fid'], $seleted_forums)) {
				continue;
			}
			$message = isset($messages[$forum['fid']])?$messages[$forum['fid']]:'';
			$ret .= showtablerow('', 'class="td27" s="1"', $forum['name']."<span style='font-weight:normal'>({$scriptlang['autoreply']['threads']}={$forum['threads']}&nbsp;&nbsp;{$scriptlang['autoreply']['posts']}={$forum['posts']})</span>");
			$ret .= showtablerow(
				'class="noborder"', 
				array('class="vtop rowform"', 'class="vtop tips2" s="1"'),
				array('<textarea rows="6" ondblclick="textareasize(this, 1)" onkeyup="textareasize(this, 0)" name="message['.$forum['fid'].']" cols="50" class="tarea">'.$message.'</textarea>', $scriptlang['autoreply']['reply_manage_input_tips'].cplang('tips_textarea'))
			);
		}
		echo $ret;
		showsubmit('replysubmit');
		showtablefooter();
		showformfooter();
	} else {
		cpmsg($scriptlang['autoreply']['reply_manage_forum_null'], "action=forums", 'error'); 
	}
}

function update_autoreply_message($message_path, $message_filename, $url_params)
{
	global $_G, $scriptlang;
	$url_params = "action=$url_params";

	if (!_autoreply_writeable($message_path)) {
		cpmsg(sprintf($scriptlang['autoreply']['reply_manage_check_path'], $message_path), $url_params, 'error'); 
	}

	$messages = $_GET['message'];
	if ($messages) {
		$contents = array();
		foreach ($messages as $fid=>$msg) {
			if ($msg != '') {
				$contents[$fid] = explode("\r\n", $msg);
			}
		}
		file_put_contents("$message_path/$message_filename", serialize($contents));
	}
	cpmsg($scriptlang['autoreply']['succeed'], $url_params, 'succeed');
}

function _autoreply_writeable($dir)
{
	if (!is_dir($dir)) {
		@mkdir($dir, 0777);	
		if (is_dir($dir)) {
			$filename = "$dir/test.test";
			if ($fp = @fopen($filename, 'w')) {
				@fclose($fp);
				@unlink($filename);
				return true;
			}
		}
	} else {
		return true;
	}
	return false;
}
