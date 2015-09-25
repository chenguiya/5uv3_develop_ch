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
global $arm_action;
$arm_action = "action=plugins&operation=config&identifier=autoreply&pmod=member&do=$pluginid";

sync_member_data();

export_member_data();

if(!submitcheck('importsubmit') && !submitcheck('deletesubmit') && !submitcheck('avatarsubmit') && !submitcheck('removesubmit') && !submitcheck('avatarzipsubmit')) {
	manage_autoreply_member();
} else {
	$pact = $_GET['pact'];
	if ($pact == 'import') {
		$flag = intval($_GET['import_member']);
		if ($flag == 1) {
			import_autoreply_member_by_uid();	
		} else {
			import_autoreply_member_by_username();	
		}
	} else if ($pact == 'delete') {
		$btnsubmit = $_GET['btnsubmit'];
		if ($btnsubmit == 'delete') {
			delete_autoreply_member();	
		} else if ($btnsubmit == 'remove') {
			remove_autoreply_member();	
		}
	} else if ($pact == 'upload') {
		upload_avatar();
	} else if ($pact == 'uploadzip') {
		upload_avatar_zip();
	}
}

function sync_member_data()
{
	global $_G, $lang, $arm_action, $scriptlang;
	$mylang = $scriptlang['autoreply'];

	if (submitcheck('syncsubmit')) {
		$halt = true;
		cpmsg($mylang['sync_member_loading'], "$arm_action&_step=2",'loading', '', '', $halt);
	}
	if ($_GET['_step'] == 2) {
		$continue = false;
		$pagesize = 100;
		$start = 0;
		$total = 0;
		do {
			$res = DB::fetch_all('SELECT uid FROM '.DB::table('plugin_autoreply_member')." LIMIT $start, $pagesize");	
			if ($res) {
				foreach ($res as $v) {
					$uid = $v['uid'];	
					if (!DB::fetch_first('SELECT uid FROM '.DB::table('common_member')." WHERE uid=$uid")) {
						DB::delete('plugin_autoreply_member', array('uid' => $uid));
						$total += 1;
					}
				}
				if (count($res) == $pagesize) {
					$start += $pagesize;
					$continue = true;	
				} else {
					$continue = false;
				}
			}
		} while($continue);
		$msg = sprintf($mylang['sync_member_result'], $total);
		cpmsg($msg, $arm_action, 'succeed');
	}
}

function export_member_data()
{
	global $arm_action;
	define('FOOTERDISABLED',1);
	if ($_GET['pact']=='export') {
		$all_uid = C::t('#autoreply#plugin_autoreply_member')->fetch_all_uid();
		$detail = '';
		foreach ($all_uid as $v) {
			$detail .= $v."\r\n";
		}
		ob_end_clean();
		header('Content-Encoding: none');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=autoreply-'.date('YmdHis').'.txt');
		header('Pragma: no-cache');
		header('Expires: 0');
		echo $detail;
		exit();
	}
}

function manage_autoreply_member()
{
	global $_G, $lang, $arm_action, $scriptlang;
	$mylang = $scriptlang['autoreply'];

	$groupselect = array();
	$query = C::t('common_usergroup')->fetch_all_by_not_groupid(array(5, 6, 7));
	foreach($query as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		if($group['type'] == 'member' && $group['creditshigher'] == 0) {
			$groupselect[$group['type']] .= "<option value=\"$group[groupid]\" selected>$group[grouptitle]</option>\n";
		} else {
			$groupselect[$group['type']] .= "<option value=\"$group[groupid]\">$group[grouptitle]</option>\n";
		}
	}
	$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup>';

	showformheader("plugins&operation=member&do=$pluginid&identifier=autoreply&pmod=member&pact=import", 'enctype');
	showtableheader($mylang['upload_avatar_table_header']);
	showtablerow('class="noborder"', array('class="vtop rowform"', 'class="vtop tips2" s="1"'), 
		array('<label><input type="radio" name="import_member" value="0" onclick="check_import_member(this.value)"/>'.$mylang['upload_avatar_create'].'</label>&nbsp;&nbsp;<label><input type="radio" name="import_member" value="1" onclick="check_import_member(this.value)"/>'.$mylang['upload_avatar_import'].'</label>'));
		//TODO
		//加入导出数据包
	showtablefooter();

	//create
	showtableheader('', '', 'id="member_manage_username_table" style="display:none; margin-top:0"');
	showsetting('usergroup', '', '', '<select name="newgroupid">'.$groupselect.'</select>');
	showtablerow('', 'class="td27" s="1"', $mylang['import_member_upload']);
	showtablerow('class="noborder"', array('class="vtop rowform"', 'class="vtop tips2" s="1"'),
		array('<textarea rows="6" ondblclick="textareasize(this, 1)" onkeyup="textareasize(this, 0)" name="usernames" cols="50" class="tarea"></textarea>', $mylang['import_member_username_comment']));
	showtablerow('', 'class="td27" s="1"', $mylang['import_member_password']);
	$newpassword = substr(md5(time()), 0, mt_rand(6, 10));
	showtablerow('class="noborder"', array('class="vtop rowform"', 'class="vtop tips2" s="1"'),
		array('<input name="newpassword" value="'.$newpassword.'" type="text" class="txt" />',
			$mylang['import_member_password_comment']));
	showtablefooter();

	//import
	showtableheader('', '', 'id="member_manage_uid_table" style="display:none; margin-top:0"');
	showtablerow('', 'class="td27" s="1"', "UID:");
	showtablerow('class="noborder"', array('class="vtop rowform"', 'class="vtop tips2" s="1"'),
		array('<textarea rows="6" ondblclick="textareasize(this, 1)" onkeyup="textareasize(this, 0)" id="uids" name="uids" cols="50" class="tarea"></textarea>', $mylang['import_member_uid_comment']));
	showtablefooter();

	showsubmit('importsubmit');
	showformfooter();
	echo <<<EOF
<script>
function check_import_member(value)
{
	var t1 = document.getElementById('member_manage_username_table');
	var t2 = document.getElementById('member_manage_uid_table');
	if (value == '1') {
		t1.style.display = 'none';
		t2.style.display = '';
	} else {
		t1.style.display = '';
		t2.style.display = 'none';
	}
}
</script>
EOF;

	$page = max(1, $_GET['page']);
	$start = ($page - 1) * $_G['setting']['memberperpage'];
	$membernum = C::t('#autoreply#plugin_autoreply_member')->count();
	$members = '';
	if($membernum > 0) {
		$usergroups = array();
		foreach(C::t('common_usergroup')->range() as $group) {
			switch($group['type']) {
				case 'system': $group['grouptitle'] = '<b>'.$group['grouptitle'].'</b>'; break;
				case 'special': $group['grouptitle'] = '<i>'.$group['grouptitle'].'</i>'; break;
			}
			$usergroups[$group['groupid']] = $group;
		}

		$tmp = C::t('#autoreply#plugin_autoreply_member')->range($start, $_G['setting']['memberperpage'], 'DESC');
		$uids = array();
		foreach ($tmp as $k=>$v) {
			array_push($uids, $k);	
		}
		$multipage = multi($membernum, $_G['setting']['memberperpage'], $page, ADMINSCRIPT."?$arm_action");
		$member_fields = C::t('common_member')->fetch_all($uids);
		$othermember_fields = C::t('common_member_count')->fetch_all($uids);
		$member_status = C::t('common_member_status')->fetch_all($uids);
		krsort($member_fields);

		foreach($member_fields as $uid=>$member) {
			$member = array_merge($member, $othermember_fields[$uid]);
			$member = array_merge($member, $member_status[$uid]);
			$ip = convertip($member['regip']);
			$ip = trim(str_replace('-', '', $ip));
			$ip == '' && $ip = 'Manual Acting';
			$memberextcredits = array();
			if($_G['setting']['extcredits']) {
				foreach($_G['setting']['extcredits'] as $id => $credit) {
					$memberextcredits[] = $_G['setting']['extcredits'][$id]['title'].': '.$member['extcredits'.$id].' ';
				}
			}
			//$member['username'] = DB::result_first('SELECT `username` FROM '.DB::table('common_member').' WHERE `uid`='.$member[uid]);
			$lockshow = $member['status'] == '-1' ? '<em class="lightnum">['.cplang('lock').']</em>' : '';
			$username = $member['username'];
			$members .= showtablerow(
			'', 
			array('class="td25"', '', '', 'title="'.implode("\n", $memberextcredits).'"'), 
			array(
				//"<input type=\"checkbox\" name=\"uidarray[]\" value=\"$member[uid]\"".($member['adminid'] == 1 ? 'disabled' : '')." class=\"checkbox\">",
				"<input type=\"checkbox\" name=\"uidarray[]\" value=\"$member[uid]\" class=\"checkbox\">",
				"<a target='_blank' href='home.php?mod=space&uid=$member[uid]'>"._avatar($member['uid'], 'small')."</a><br>".
				($_G['setting']['connect']['allow'] && $member['conisbind'] ? '<img class="vmiddle" src="static/image/common/connect_qq.gif" /> ' : '')."<a href=\"home.php?mod=space&uid=$member[uid]\" target=\"_blank\">$username</a>",
				"<a href='###' style='color:#f60' class='act' onclick='return show_upload_avatar(\"{$member['uid']}\", \"{$member['username']}\")'>{$mylang['upload_avatar']}</a>",
				$member['credits'],
				$member['posts'],
				$usergroups[$member['adminid']]['grouptitle'],
				$usergroups[$member['groupid']]['grouptitle'].$lockshow,
				$ip,
				"<a href=\"".ADMINSCRIPT."?action=members&operation=group&uid=$member[uid]\" class='act'>$lang[usergroup]</a><a href=\"".ADMINSCRIPT."?action=members&operation=access&uid=$member[uid]\" class='act'>$lang[members_access]</a>".
				($_G['setting']['extcredits'] ? "<a href=\"".ADMINSCRIPT."?action=members&operation=credit&uid=$member[uid]\" class='act'>$lang[credits]</a>" : "<span disabled>$lang[edit]</span>").
				"<a href=\"".ADMINSCRIPT."?action=members&operation=medal&uid=$member[uid]\" class='act'>$lang[medals]</a>".
				"<a href=\"".ADMINSCRIPT."?action=members&operation=repeat&uid=$member[uid]\" class='act'>$lang[members_repeat]</a>".
				"<a href=\"".ADMINSCRIPT."?action=members&operation=edit&uid=$member[uid]\" class='act'>$lang[detail]</a>".
				"<a href=\"".ADMINSCRIPT."?action=members&operation=ban&uid=$member[uid]\" class='act'>$lang[members_ban]</a>",
			), TRUE);
		}
		showformheader("plugins&operation=member&do=$pluginid&identifier=autoreply&pmod=member&pact=delete");
		$no_avatar = scan_no_avatar();
		$tips = sprintf($mylang['upload_avatar_no_avatar_tips'], $membernum, $no_avatar);
		showtableheader($tips.'&nbsp;<input type="button" class="btn" value="'.$mylang['upload_avatar_all'].'" onclick="show_batch_upload_avatar()"/>&nbsp;<!--span style="color:#f00;font-weight:normal">'.$mylang['upload_avatar_all_tips'].'</span-->&nbsp;&nbsp;<input type="button" class="btn" value="'.$mylang['export_uid'].'" onclick="window.location.href=\''.ADMINSCRIPT.'?'.$arm_action.'&pact=export\'"/>&nbsp;&nbsp;<input type="button" class="btn" value="'.$mylang['upload_avatar_zip'].'" onclick="show_upload_avatar_dialog(\'upload_avatar_zip\')"/>&nbsp;&nbsp;<a href="http://discuz.aixiyou.com/thread-29-1-1.html" target="_blank" style="font-weight:normal;text-decoration:underline;color:#000;">'.$mylang['upload_avatar_file_limit'].'</a>');
		//showtableheader($title, );
		showsubtitle(array('', 'username', '', 'credits', 'posts', 'admingroup', 'usergroup', 'ip', ''));
		echo $members;
		$condition_str = str_replace('&tablename=master', '', $condition_str);
		echo '<tr><td class="td25" colspan="9">';
		echo '<input type="hidden" id="_btnsubmit" name="btnsubmit" value="delete"/>';
		echo '<input type="checkbox" name="chkall" onclick="checkAll(\'prefix\', this.form, \'uidarray\');if(this.checked){$(\'deleteallinput\').style.display=\'\';}else{$(\'deleteall\').checked = false;$(\'deleteallinput\').style.display=\'none\';}" class="checkbox">'.cplang('select_all');
		echo '&nbsp;&nbsp;<span id="deleteallinput" style="display:none"><input id="deleteall" type="checkbox" name="deleteall" class="checkbox">'.$mylang['delete_all_member'].'</span>';
		echo '&nbsp;&nbsp;<input type="submit" onclick="if(confirm(\''.$mylang['delete_all_member_confirm'].'\')){document.getElementById(\'_btnsubmit\').value=\'delete\';return true;}else{return false;}" class="btn" name="deletesubmit" id="submit_deletesubmit" value="'.cplang('delete').'"/>';
		echo '&nbsp;&nbsp;<input type="submit" onclick="if(confirm(\''.$mylang['remove_member_confirm'].'\')){document.getElementById(\'_btnsubmit\').value=\'remove\';return true;}else{return false;}" class="btn" name="removesubmit" id="submit_removesubmit" value="'.$mylang['remove_member'].'"/>';
		if ($multipage) {
			echo '<div class="cuspages right">'.$multipage.'</div>';
		}
		echo '</td></tr>';
		showtablefooter();
		showformfooter();
		$form_action = ADMINSCRIPT."?$arm_action&pact=upload";
		$formhash = FORMHASH;
		$upload_avatar = $mylang['upload_avatar'];
		$upload_avatar_size_tips = $mylang['upload_avatar_size_tips'];
		$upload_avatar_size_tips2 = $mylang['upload_avatar_size_tips2'];
		$submit = $mylang['submit'];
		$download_tips = $mylang['upload_avatar_data_download'];
		$addon_discuz = $mylang['addon_discuz'];
		$shichuangkeji = $mylang['shichuangkeji'];
		$browser_tips = $mylang['browser_tips'];
		echo <<<EOF
<div id="upload_avatar_menu" class="custom" style="position:fixed;z-index:301;left:400px;top:230px; display:none">
	<div class="cmain" id="upload_avatar_main" style="width:400px">
		<div style="width:400px" class="cnote">
			<span class="right">
				<a onclick="close_upload_avatar('upload_avatar_menu');return false;" class="flbc" href="###"></a>
			</span>
			<h3><span id="upload_avatar_username"></span> - $upload_avatar<span style="margin-left:20px;color:#f00;font-weight:normal;font-size:12px;">$upload_avatar_size_tips</span></h3>
			
		</div>
		<div id="upload_avatar_form" class="cmlist" style="width:400px;height:50px"></div>
	</div>
	<div class="cfixbd"></div>
</div>
<div id="batch_upload_avatar_menu" class="custom" style="position:fixed;z-index:301;left:400px;top:230px; display:none">
	<div class="cmain" id="upload_avatar_main" style="width:400px">
		<div style="width:400px" class="cnote">
			<span class="right">
				<a onclick="close_upload_avatar('batch_upload_avatar_menu');return false;" class="flbc" href="###"></a>
			</span>
			<h3>{$mylang['upload_all']}&nbsp;<span style="color:#f00;font-weight:normal;font-size:12px;">$upload_avatar_size_tips2</span></h3>
		</div>
		<div id="batch_upload_avatar_form" class="cmlist" style="width:400px;height:50px"></div>
		<p id="batch_upload_avatar_info"></p>
		<p>
			<span style="font-weight:bold;">$download_tips</span>
			<a style="color:#f00;text-decoration:underline;" href="http://addon.discuz.com/?@autoreply.plugin.24239" target="_blank">$addon_discuz</a>&nbsp;&nbsp;
			
		</p>
	</div>
	<div class="cfixbd"></div>
</div>
<script>
function show_upload_avatar(uid, username)
{
	var menu = document.getElementById('upload_avatar_menu');
	menu.style.display = '';

	document.getElementById('upload_avatar_username').innerHTML = username;

	var id = 'upload_avatar_form_'+uid;
	var objForm = document.getElementById(id);
	if (!objForm) {
		var p = document.getElementById('upload_avatar_form');			
		var s ='<form action="$form_action" method="post" enctype="multipart/form-data">';
		s += '<input type="hidden" name="formhash" value="$formhash" />';
		s += '<input type="hidden" id="formscrolltop" name="scrolltop" value="" />';
		s += '<input type="hidden" name="anchor" value="" />';
		s += '<input type="hidden" name="uid" value="'+uid+'">';
		s += '<input type="file" name="avatar" class="txt uploadbtn">';
		s += '&nbsp;<input type="submit" name="avatarsubmit" value="$upload_avatar" class="btn">';
		s += '</form>';
		p.innerHTML = s;
	}
	return false;
}

function show_batch_upload_avatar()
{
	var menu = document.getElementById('batch_upload_avatar_menu');
	menu.style.display = '';

	var id = 'upload_avatar_form_0';
	var objForm = document.getElementById(id);
	if (!objForm) {
		var p = document.getElementById('batch_upload_avatar_form');			
		var s ='<form action="$form_action" method="post" enctype="multipart/form-data">';
		s += '<input type="hidden" name="batch_upload" value="1" />';
		s += '<input type="hidden" name="formhash" value="$formhash" />';
		s += '<input type="hidden" id="formscrolltop" name="scrolltop" value="" />';
		s += '<input type="hidden" name="anchor" value="" />';
		s += '<input type="file" name="avatar[]" class="txt uploadbtn" onchange="show_batch_upload_info(this.form)" multiple="true">';
		s += '&nbsp;<input type="submit" name="avatarsubmit" value="$upload_avatar" class="btn">';
		s += '</form>';
		p.innerHTML = s;
	}
	return false;
}

function show_batch_upload_info(obj)
{
	if (window.File && window.FileList) {
		/*
		var info = document.getElementById('batch_upload_avatar_info');	
		var count = obj["avatar[]"].files.length;
		info.innerHTML = "已选择 "+count+" 个文件";
		*/
		//do nothing
	} else {
		alert('$browser_tips');
	}
}

function close_upload_avatar(id)
{
	var menu = document.getElementById(id);
	menu.style.display = 'none';
}
function show_upload_avatar_dialog(id)
{
	var menu = document.getElementById(id);
	menu.style.display = '';
}
</script>
EOF;
		$zip_exists = false;
		if (class_exists('ZipArchive', false)) {
			$zip_exists = true;
		}
		$zip_tips = '<span style="color:#aaa">&nbsp;'.$mylang['upload_avatar_zip_tip1'].'</span><br/><br/>';
		$zip_disable = '';
		if (!$zip_exists) {
			$zip_tips = '<span style="color:#f00">&nbsp;'.$mylang['upload_avatar_zip_tip2'].'</span><br/><br/>';
			$zip_disable = 'disabled';
		}
		$form_action = ADMINSCRIPT."?$arm_action&pact=uploadzip";
		echo <<<EOF
<div id="upload_avatar_zip" class="custom" style="position:fixed;z-index:301;left:400px;top:230px; display:none;">
	<div class="cmain" style="width:360px">
		<div style="width:360px" class="cnote">
			<span class="right">
				<a onclick="close_upload_avatar('upload_avatar_zip');return false;" class="flbc" href="javascript:void(0)"></a>
			</span>
			<h3>{$mylang['upload_avatar_zip_tip3']}&nbsp;<span style="color:#f00;font-weight:normal;font-size:12px;">{$mylang['upload_avatar_zip_tip4']}</span></h3>
		</div>
		<div class="cmlist" style="width:360px;height:230px">
			<form action="$form_action" method="post" enctype="multipart/form-data" class="rowform" style="width:360px;">
				<input type="hidden" name="formhash" value="$formhash" />
				<strong>{$mylang['upload_avatar_zip_tip5']}</strong><br/>
				<label><input type="radio" name="avatar_cover" value="1">{$mylang['yes']}</lable>
				<label><input type="radio" name="avatar_cover" value="0" checked>{$mylang['no']}</lable>
				<br/><br/>
				<strong>{$mylang['upload_avatar_zip_tip6']}</strong><span style="color:#f00">{$mylang['upload_avatar_zip_tip7']}</span><br/>
				<input type="file" name="avatar_zip" $zip_disable><br/>
				$zip_tips
				<strong>{$mylang['upload_avatar_zip_tip8']}<span style="color:#f00;">{$mylang['upload_avatar_zip_tip9']}</span></strong><br/>
				<input type="text" name="avatar_dir" class="txt" style="width:320px;" placeholder="{$mylang['upload_avatar_zip_tip10']}"><br/>
				<span style="color:#aaa">&nbsp;{$mylang['upload_avatar_zip_tip11']} source/plugin/autoreply/</span><br/><br/>
				<input type="submit" name="avatarzipsubmit" value="$submit" class="btn">
			</form>
		</div>
		<p>
			<span style="font-weight:bold;">$download_tips</span>
			<a style="color:#f00;text-decoration:underline;" href="http://addon.discuz.com/?@autoreply.plugin.24239" target="_blank">$addon_discuz</a>&nbsp;&nbsp;
		</p>
	</div>
	<div class="cfixbd"></div>
</div>
EOF;
	}
	showformheader("plugins&operation=member&do=$pluginid&identifier=autoreply&pmod=member");
	showsubmit('syncsubmit', $mylang['sync_member'], '', '', '', false);
	showformfooter();
}

function import_autoreply_member_by_username()
{
	global $_G, $lang, $arm_action, $scriptlang;
	$mylang = $scriptlang['autoreply'];

	$membernum = C::t('#autoreply#plugin_autoreply_member')->count();
	if ($membernum >= 5*5*6*20) {
		cpmsg(lang('plugin/autoreply', 'import_member_over_limit'), $arm_action, 'error'); 
	}

	$group = C::t('common_usergroup')->fetch($_GET['newgroupid']);
	$newadminid = in_array($group['radminid'], array(1, 2, 3)) ? $group['radminid'] : ($group['type'] == 'special' ? -1 : 0);
	if($group['radminid'] == 1) {
		cpmsg('members_add_admin_none', '', 'error');
	}
	if(in_array($group['groupid'], array(5, 6, 7))) {
		cpmsg('members_add_ban_all_none', '', 'error');
	}
	if ($_GET['usernames'] != '') {
		$usernames = explode("\r\n", $_GET['usernames']);
		$usernames = array_unique($usernames);
		$usernames = array_slice($usernames, 0, 5*5*6*20);
	} else {
		cpmsg(lang('plugin/autoreply', 'import_member_tips'), '', 'error');
	}
	$newpassword = 'sc-autoreply';
	if (isset($_GET['newpassword']) && $_GET['newpassword'] != '') {
		$newpassword = $_GET['newpassword'];
	}
	loaducenter();
	$profile = $verifyarr = array();
	loadcache('fields_register');
	$init_arr = explode(',', $_G['setting']['initcredits']);

	$count = count($usernames);
	$username_duplicate = array();
	$other_err = array();
	if ($count) {
		$total = 0;
		foreach ($usernames as $newusername) {
			$newusername = trim($newusername);
			if ($newusername == '') {
				continue;
			}
			if(C::t('common_member')->fetch_uid_by_username($newusername) || C::t('common_member_archive')->fetch_uid_by_username($newusername)) {
				$username_duplicate[] = $newusername;
				continue;
				//cpmsg('members_add_username_duplicate', '', 'error');
			}
			
			$newemail = mt_rand(100000, 99999999999).'@qq.com';
			$uid = uc_user_register(addslashes($newusername), $newpassword, $newemail);
			if($uid <= 0) {
				if ($uid == -6) {
					$username_duplicate[] = $newusername;
				} else {
					$other_err[] = "$newusername($uid)";	
				}
				continue;
				/*
				if($uid == -1) {
					cpmsg('members_add_illegal', '', 'error');
				} elseif($uid == -2) {
					cpmsg('members_username_protect', '', 'error');
				} elseif($uid == -3) {
					if(empty($_GET['confirmed'])) {
						cpmsg('members_add_username_activation', $arm_action.'&pact=import&importsubmit=yes&newgroupid='.$_GET['newgroupid'].'&newusername='.rawurlencode($newusername), 'form');
					} else {
						list($uid,, $newemail) = uc_get_user(addslashes($newusername));
					}
				} elseif($uid == -4) {
					cpmsg('members_email_illegal', '', 'error');
				} elseif($uid == -5) {
					cpmsg('members_email_domain_illegal', '', 'error');
				} elseif($uid == -6) {
					cpmsg('members_email_duplicate', '', 'error');
				}
				 */
			}
			$rip = _autoreply_get_random_ip();
			C::t('common_member')->insert($uid, $newusername, md5($newpassword), $newemail, $rip, $_GET['newgroupid'], $init_arr, $newadminid);

			DB::insert('plugin_autoreply_member', array(
				'uid' => $uid,
				'username' => $newusername,
			));
			$total += 1;
			$membernum += 1;
			if ($membernum >= 5*5*6*20) {
				//cpmsg(lang('plugin/autoreply', 'import_member_over_limit'), $arm_action, 'error'); 
				break;
			}
		}
		updatecache('setting');
		$msg = sprintf(lang('plugin/autoreply', 'import_member_successed'), $total);
		if (count($username_duplicate)) {
			$username_duplicate = implode(', ', $username_duplicate);	
			$username_duplicate = sprintf($mylang['import_member_username_tip'], $username_duplicate);
		} else {
			$username_duplicate = '';
		}
		if (!empty($other_err)) {
			$username_duplicate .= '<br/>'.implode(', ', $other_err);	
		}
		cpmsg($msg, $arm_action, 'succeed', array(), $username_duplicate);
	} else {
		cpmsg(lang('plugin/autoreply', 'import_member_failed'), $arm_action, 'error'); 
	}
}

function import_autoreply_member_by_uid()
{
	global $_G, $lang, $arm_action, $scriptlang;
	$mylang = $scriptlang['autoreply'];

	$membernum = C::t('#autoreply#plugin_autoreply_member')->count();
	if ($membernum >= 5*5*6*20) {
		cpmsg(lang('plugin/autoreply', 'import_member_over_limit'), $arm_action, 'error'); 
	}

	if ($_GET['uids'] != '') {
		$uids = explode("\r\n", $_GET['uids']);
		$uids = array_unique($uids);
		$uids = array_slice($uids, 0, 5*5*6*20);
	} else {
		cpmsg(lang('plugin/autoreply', 'import_member_tips'), '', 'error');
	}
	$count = count($uids);
	if ($count) {
		$total = 0;
		$member_not_exist = array();
		foreach ($uids as $uid) {
			$uid = trim($uid);
			if ($uid == '') {
				continue;
			}
			$uid = intval($uid);
			if ($uid <= 0) {
				continue;
			}
			$member = DB::fetch_first('SELECT username FROM '.DB::table('common_member').' WHERE uid='.$uid);
			if (!$member) {
				$member_not_exist[] = $uid;	
				continue;
			}
			$username = $member['username'];
			try {
				DB::insert('plugin_autoreply_member', array(
					'uid' => $uid,
					'username' => $username,
				));
			} catch (Exception $e) {
				$member_not_exist[] = $uid;	
			}
			$total += 1;
			$membernum += 1;
			if ($membernum >= 5*5*6*20) {
				break;
			}
		}
		updatecache('setting');
		$msg = sprintf(lang('plugin/autoreply', 'import_member_successed'), $total);
		if (count($member_not_exist)) {
			$member_not_exist = implode(', ', $member_not_exist);	
			$member_not_exist = sprintf($mylang['import_member_uid_tip'], $member_not_exist);
		} else {
			$member_not_exist = '';
		}
		cpmsg($msg, $arm_action, 'succeed', array(), $member_not_exist);
	} else {
		cpmsg(lang('plugin/autoreply', 'import_member_failed'), $arm_action, 'error'); 
	}
}

function delete_autoreply_member()
{
	global $_G, $lang, $arm_action;

	$isfounder = true;
	$uids = array();
	$extra = '';
	$delmemberlimit = 5*5*6*20;
	$deletestart = intval($_GET['deletestart']);
	if(!empty($_GET['deleteall'])) {
		$uids = C::t('#autoreply#plugin_autoreply_member')->fetch_all_uid();
		$membernum = count($uids);
	} else if(!empty($_GET['uidarray'])) {
		$allmember = C::t('common_member')->fetch_all($_GET['uidarray']);
		$count = count($allmember);
		$membernum = 0;
		foreach($allmember as $uid => $member) {
			if($member['adminid'] != 1 && $member['groupid'] != 1) {
				if($count <= $delmemberlimit) {
					$extra .= '<input type="hidden" name="uidarray[]" value="'.$member['uid'].'" />';
					$uids[] = $member['uid'];
					$membernum ++;
				} else {
					break;
				}
			}
		}
	} else {
		cpmsg('members_no_find_deluser', $arm_action, 'error');
	}
	$allnum = intval($_GET['allnum']);

	if((empty($membernum) || empty($uids))) {
		if($deletestart) {
			cpmsg('members_delete_succeed', $arm_action, 'succeed', array('numdeleted' => $allnum));
		}
		cpmsg('members_no_find_deluser', $arm_action, 'error');
	}
	if(!$_GET['confirmed']) {
		cpmsg('members_delete_confirm', $arm_action."&btnsubmit=delete&pact=delete&deletesubmit=yes&confirmed=yes&deleteall=".($_GET['deleteall']?'1':'0'), 'form', array('membernum' => $membernum), $extra.'<br /><input type="hidden" name="includeuc" value="1"/><label><input type="checkbox" name="includepost" value="1" class="checkbox" />'.$mylang['members_delete_all'].'</label>', '');
	} else {
		if(empty($_GET['includepost'])) {

			C::t('#autoreply#plugin_autoreply_member')->delete_by_uid($uids);

			require_once libfile('function/delete');
			$numdeleted = deletemember($uids, 0);

			if($isfounder && !empty($_GET['includeuc'])) {
				loaducenter();
				uc_user_delete($uids);
				$_GET['includeuc'] = 1;
			} else {
				$_GET['includeuc'] = 0;
			}
			if($uids) {
				cpmsg('members_delete_succeed', $arm_action, 'succeed', array('numdeleted' => $numdeleted));
			} else {
				$allnum += $membernum < $delmemberlimit ? $membernum : $delmemberlimit;
				$nextlink = $arm_action."&btnsubmit=delete&pact=delete&deletesubmit=yes&confirmed=yes&submit=yes".(!empty($_GET['includeuc']) ? '&includeuc=yes' : '')."&allnum=$allnum&deletestart=".($deletestart+$delmemberlimit);
				cpmsg(cplang('members_delete_user_processing_next', array('deletestart' => $deletestart, 'nextdeletestart' => $deletestart+$delmemberlimit)), $nextlink, 'loadingform', array());
			}

		} else {
			if(empty($uids)) {
				cpmsg('members_no_find_deluser', $arm_action, 'error');
			}
			$numdeleted = $numdeleted ? $numdeleted : count($uids);
			$pertask = 1000;
			$current = $_GET['current'] ? intval($_GET['current']) : 0;
			$deleteitem = $_GET['deleteitem'] ? trim($_GET['deleteitem']) : 'post';
			$nextdeleteitem = $deleteitem;

			$next = $current + $pertask;

			if($deleteitem == 'post') {
				$threads = $fids = $threadsarray = array();
				foreach(C::t('forum_thread')->fetch_all_by_authorid($uids, $pertask) as $thread) {
					$threads[$thread['fid']][] = $thread['tid'];
				}

				if($threads) {
					require_once libfile('function/post');
					foreach($threads as $fid => $tids) {
						deletethread($tids);
					}
					if($_G['setting']['globalstick']) {
						require_once libfile('function/cache');
						updatecache('globalstick');
					}
				} else {
					$next = 0;
					$nextdeleteitem = 'blog';
				}
			}

			if($deleteitem == 'blog') {
				$blogs = array();
				$query = C::t('home_blog')->fetch_blogid_by_uid($uids, 0, $pertask);
				foreach($query as $blog) {
					$blogs[] = $blog['blogid'];
				}

				if($blogs) {
					deleteblogs($blogs);
				} else {
					$next = 0;
					$nextdeleteitem = 'pic';
				}
			}

			if($deleteitem == 'pic') {
				$pics = array();
				$query = C::t('home_pic')->fetch_all_by_uid($uids, 0, $pertask);
				foreach($query as $pic) {
					$pics[] = $pic['picid'];
				}

				if($pics) {
					deletepics($pics);
				} else {
					$next = 0;
					$nextdeleteitem = 'doing';
				}
			}

			if($deleteitem == 'doing') {
				$doings = array();
				$query = C::t('home_doing')->fetch_all_by_uid_doid($uids, '', '', 0, $pertask);
				foreach ($query as $doings) {
					$doings[] = $doing['doid'];
				}

				if($doings) {
					deletedoings($doings);
				} else {
					$next = 0;
					$nextdeleteitem = 'share';
				}
			}

			if($deleteitem == 'share') {
				$shares = array();
				foreach(C::t('home_share')->fetch_all_by_uid($uids, $pertask) as $share) {
					$shares[] = $share['sid'];
				}

				if($shares) {
					deleteshares($shares);
				} else {
					$next = 0;
					$nextdeleteitem = 'comment';
				}
			}

			if($deleteitem == 'comment') {
				$comments = array();
				$query = C::t('home_comment')->fetch_all_by_uid($uids, 0, $pertask);
				foreach($query as $comment) {
					$comments[] = $comment['cid'];
				}

				if($comments) {
					deletecomments($comments);
				} else {
					$next = 0;
					$nextdeleteitem = 'allitem';
				}
			}

			if($deleteitem == 'allitem') {
				require_once libfile('function/delete');
				$numdeleted = deletemember($uids);

				if($isfounder && !empty($_GET['includeuc'])) {
					loaducenter();
					uc_user_delete($uids);
				}
				if(!empty($_GET['uidarray'])) {
					cpmsg('members_delete_succeed', $arm_action, 'succeed', array('numdeleted' => $numdeleted));
				} else {
					$allnum += $membernum < $delmemberlimit ? $membernum : $delmemberlimit;
					$nextlink = $arm_action."&btnsubmit=delete&pact=delete&deletesubmit=yes&confirmed=yes&submit=yes&includepost=yes".(!empty($_GET['includeuc']) ? '&includeuc=yes' : '')."&allnum=$allnum&deletestart=".($deletestart+$delmemberlimit);
					cpmsg(cplang('members_delete_user_processing_next', array('deletestart' => $deletestart, 'nextdeletestart' => $deletestart+$delmemberlimit)), $nextlink, 'loadingform', array());
				}
			}
			$nextlink = $arm_action."&btnsubmit=delete&pact=delete&deletesubmit=yes&confirmed=yes&submit=yes&includepost=yes".(!empty($_GET['includeuc']) ? '&includeuc=yes' : '')."&current=$next&pertask=$pertask&lastprocess=$processed&allnum=$allnum&deletestart=$deletestart";
			if(empty($_GET['uidarray'])) {
				$deladdmsg = cplang('members_delete_user_processing', array('deletestart' => $deletestart, 'nextdeletestart' => $deletestart+$delmemberlimit)).'<br>';
			} else {
				$deladdmsg = '';
			}
			if($nextdeleteitem != $deleteitem) {
				$nextlink .= "&deleteitem=$nextdeleteitem";
				cpmsg(cplang('members_delete_processing_next', array('deladdmsg' => $deladdmsg, 'item' => cplang('members_delete_'.$deleteitem), 'nextitem' => cplang('members_delete_'.$nextdeleteitem))), $nextlink, 'loadingform', array(), $extra);
			} else {
				$nextlink .= "&deleteitem=$deleteitem";
				cpmsg(cplang('members_delete_processing', array('deladdmsg' => $deladdmsg, 'item' => cplang('members_delete_'.$deleteitem), 'current' => $current, 'next' => $next)), $nextlink, 'loadingform', array(), $extra);
			}
		}
	}
}

function remove_autoreply_member()
{
	global $_G, $arm_action;

	if(empty($_GET['uidarray'])) {
		cpmsg('members_no_find_deluser', $arm_action, 'error');
	}
	if(!empty($_GET['deleteall'])) {
		$_GET['uidarray'] = '';
	}
	if(!empty($_GET['uidarray'])) {
		$uids = $_GET['uidarray'];
	} else {
		$uids = C::t('#autoreply#plugin_autoreply_member')->fetch_all_uid();
	}
	C::t('#autoreply#plugin_autoreply_member')->delete_by_uid($uids);
	cpmsg('members_delete_succeed', $arm_action, 'succeed', array('numdeleted' => count($uids)));
}

function upload_avatar()
{
	global $_G, $lang, $arm_action, $scriptlang;
	$mylang = $scriptlang['autoreply'];

	if (!isset($_G['cache']['plugin']['autoreply'])) {
		loadcache('plugin');
	}
	$uc_data_dir = $_G['cache']['plugin']['autoreply']['uc_data_dir'];
	if ($uc_data_dir == '') {
		$uc_data_dir = DISCUZ_ROOT.'/uc_server/data/';
	} else {
		$uc_data_dir = DISCUZ_ROOT."./$uc_data_dir";
	}
	$batch_upload = $_GET['batch_upload'];
	if ($batch_upload == 1) {
		if ($_FILES['avatar']['name'][0] == '') {
			cpmsg($mylang['upload_avatar_no_picture'], $arm_action, 'error');
		}
		//get uid
		$uids = C::t('#autoreply#plugin_autoreply_member')->fetch_all_uid();
		if (!$uids) {
			cpmsg($mylang['upload_avatar_no_member'], $arm_action, 'error');
		}
		sort($uids);

		$failed = $succeed = array();
		foreach ($_FILES['avatar']['error'] as $key=>$error) {
			if ($uids) {
				$uid = array_pop($uids);
			} else {
				break;				
			}
			if ($error != 0) {
				$failed['count']++;
				$failed[$key] = $error;
			}
			$filepath = $_FILES['avatar']['tmp_name'][$key];
			$filename = $_FILES['avatar']['name'][$key];
			list($width, $height, $type, $attr) = getimagesize($filepath);
			$suffix = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			if (!in_array($suffix, array('jpg', 'gif', 'png', 'jpeg'))) {
				$failed['count']++;
				$failed[$key] = "'$suffix' no allow";
				continue;
			}
			if ($width > 120 || $height > 120) {
				$failed['count']++;
				$failed[$key] = "'width=$width,height=$height' overflow";
				continue;
			}
			//echo $uid.','.$succeed['count'].'<br>';
			$filesize = $_FILES['avatar']['size'][$key];
			$home = _get_home($uid);
			if(!is_dir($uc_data_dir.'./avatar/'.$home)) {
				_set_home($uid, $uc_data_dir.'./avatar/');
			}
			$bigavatarfile    = $uc_data_dir.'./avatar/'._get_avatar($uid, 'big');
			$middleavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'middle');
			$smallavatarfile  = $uc_data_dir.'./avatar/'._get_avatar($uid, 'small');
			$avatar           = @implode('', file($filepath));
			$bigavatar        = $avatar;
			$middleavatar     = $avatar;
			$smallavatar      = $avatar;
			@unlink($_FILES['avatar']['tmp_name'][$key]);
			
			$fp = @fopen($bigavatarfile, 'wb');
			$bmark = @fwrite($fp, $bigavatar);
			@fclose($fp);

			$fp = @fopen($middleavatarfile, 'wb');
			$mmark = @fwrite($fp, $middleavatar);
			@fclose($fp);
			
			$fp = @fopen($smallavatarfile, 'wb');
			$smark = @fwrite($fp, $smallavatar);
			@fclose($fp);

			$biginfo    = @getimagesize($bigavatarfile);
			$middleinfo = @getimagesize($middleavatarfile);
			$smallinfo  = @getimagesize($smallavatarfile);
			if (!$biginfo || !$middleinfo || !$smallinfo || $biginfo[2] == 4 || $middleinfo[2] == 4 || $smallinfo[2] == 4) {
				file_exists($bigavatarfile) && unlink($bigavatarfile);
				file_exists($middleavatarfile) && unlink($middleavatarfile);
				file_exists($smallavatarfile) && unlink($smallavatarfile);
				die();
			}
			$succeed['count']++;
		}
		$upload_avatar_tips = sprintf($mylang['upload_avatar_succeed2'], $succeed['count']);
		if ($failed) {
			$upload_avatar_tips .= '<br><span style="color:#ccc">'.json_encode($failed).'</span>';
		}
		cpmsg($upload_avatar_tips, $arm_action, 'succeed');	
	} else {
		if (!is_uploaded_file($_FILES['avatar']['tmp_name'])) {
			cpmsg($mylang['upload_avatar_error']."(error={$_FILES['avatar']['error']})", $arm_action, 'error');
		}

		$uid = $_GET['uid'];
		$home = _get_home($uid);
		if(!is_dir($uc_data_dir.'./avatar/'.$home)) {
			_set_home($uid, $uc_data_dir.'./avatar/');
		}
		$bigavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'big');
		$middleavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'middle');
		$smallavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'small');

		$avatar       = @implode('', file($_FILES['avatar']['tmp_name']));
		$bigavatar    = $avatar;
		$middleavatar = $avatar;
		$smallavatar  = $avatar;
		@unlink($_FILES['avatar']['tmp_name']);

		$fp = @fopen($bigavatarfile, 'wb');
		@fwrite($fp, $bigavatar);
		@fclose($fp);

		$fp = @fopen($middleavatarfile, 'wb');
		@fwrite($fp, $middleavatar);
		@fclose($fp);

		$fp = @fopen($smallavatarfile, 'wb');
		@fwrite($fp, $smallavatar);
		@fclose($fp);

		$biginfo    = @getimagesize($bigavatarfile);
		$middleinfo = @getimagesize($middleavatarfile);
		$smallinfo  = @getimagesize($smallavatarfile);
		if(!$biginfo || !$middleinfo || !$smallinfo || $biginfo[2] == 4 || $middleinfo[2] == 4 || $smallinfo[2] == 4) {
			file_exists($bigavatarfile) && unlink($bigavatarfile);
			file_exists($middleavatarfile) && unlink($middleavatarfile);
			file_exists($smallavatarfile) && unlink($smallavatarfile);
		}
		cpmsg($mylang['upload_avatar_succeed'], $arm_action, 'succeed');	
	}
}

function upload_avatar_zip()
{
	global $_G, $lang, $arm_action, $scriptlang;
	$mylang = $scriptlang['autoreply'];

	if (!isset($_G['cache']['plugin']['autoreply'])) {
		loadcache('plugin');
	}
	$uc_data_dir = $_G['cache']['plugin']['autoreply']['uc_data_dir'];
	if ($uc_data_dir == '') {
		$uc_data_dir = DISCUZ_ROOT.'/uc_server/data/';
	} else {
		$uc_data_dir = DISCUZ_ROOT."./$uc_data_dir";
	}

	$avatar_cover = $_GET['avatar_cover']?true:false;
	if (is_uploaded_file($_FILES['avatar_zip']['tmp_name'])) {
		$upload = new discuz_upload;
		if ($upload->init($_FILES['avatar_zip']) && $upload->save(1)) {
			$zip_file = DISCUZ_ROOT.'/data/attachment/temp/'.$upload->attach['attachment'];
		} else {
			cpmsg($mylang['upload_avatar_error']."(error={$_FILES['avatar_zip']['error']})", $arm_action, 'error');
		}
		if (!class_exists('ZipArchive')) {
			cpmsg($mylang['upload_avatar_zip_tip2'], $arm_action, 'error');	
		}
		$zip = new ZipArchive();
		$errno = $zip->open($zip_file);
		$avatar_dir = DISCUZ_ROOT.'/source/plugin/autoreply/avatar_'.date('YmdHis').'/';
		if ($errno === TRUE) {
			$zip->extractTo($avatar_dir);
			$zip->close();
		} else {
			cpmsg("Zip open '$zip_file' failed, error=".$zip->getStatusString(), $arm_action, 'error');
		}
		$img_path = _get_avatar_from_unzip($avatar_dir);
		if (!$img_path) {
			cpmsg("Get avatar failed from '$avatar_dir'", $arm_action, 'error');	
		}
		$uids = C::t('#autoreply#plugin_autoreply_member')->fetch_all_uid();
		if (!$uids) {
			cpmsg($mylang['upload_avatar_no_member'], $arm_action, 'error');
		}
		foreach ($uids as $uid) {
			$home = _get_home($uid);
			if(!is_dir($uc_data_dir.'./avatar/'.$home)) {
				_set_home($uid, $uc_data_dir.'./avatar/');
			}
			$bigavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'big');
			$middleavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'middle');
			$smallavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'small');
			if (!$avatar_cover && file_exists($smallavatarfile)) {
				continue;	//exist avatar
			}
			$img_path = _get_avatar_from_unzip($avatar_dir);
			$avatar       = @implode('', file($img_path));
			$fp = @fopen($bigavatarfile, 'wb');
			@fwrite($fp, $avatar);
			@fclose($fp);
			$fp = @fopen($middleavatarfile, 'wb');
			@fwrite($fp, $avatar);
			@fclose($fp);
			$fp = @fopen($smallavatarfile, 'wb');
			@fwrite($fp, $avatar);
			@fclose($fp);
			$biginfo    = @getimagesize($bigavatarfile);
			$middleinfo = @getimagesize($middleavatarfile);
			$smallinfo  = @getimagesize($smallavatarfile);
			if(!$biginfo || !$middleinfo || !$smallinfo || $biginfo[2] == 4 || $middleinfo[2] == 4 || $smallinfo[2] == 4) {
				file_exists($bigavatarfile) && unlink($bigavatarfile);
				file_exists($middleavatarfile) && unlink($middleavatarfile);
				file_exists($smallavatarfile) && unlink($smallavatarfile);
			}
		}
		cpmsg($mylang['upload_avatar_succeed'], $arm_action, 'succeed');	
	} else if ($_GET['avatar_dir'] != '') {
		$avatar_dir = DISCUZ_ROOT.'source/plugin/autoreply/'.$_GET['avatar_dir'].'/';
		if (!is_dir($avatar_dir)) {
			cpmsg("{$mylang['upload_avatar_zip_tip12']} '$avatar_dir'", $arm_action, 'error');	
		}
		$img_path = _get_avatar_from_unzip($avatar_dir);
		if (!$img_path) {
			cpmsg("Get avatar failed from '$avatar_dir'", $arm_action, 'error');	
		}
		$uids = C::t('#autoreply#plugin_autoreply_member')->fetch_all_uid();
		if (!$uids) {
			cpmsg($mylang['upload_avatar_no_member'], $arm_action, 'error');
		}
		foreach ($uids as $uid) {
			$home = _get_home($uid);
			if(!is_dir($uc_data_dir.'./avatar/'.$home)) {
				_set_home($uid, $uc_data_dir.'./avatar/');
			}
			$bigavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'big');
			$middleavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'middle');
			$smallavatarfile = $uc_data_dir.'./avatar/'._get_avatar($uid, 'small');
			if (!$avatar_cover && file_exists($smallavatarfile)) {
				continue;	//exist avatar
			}

			$img_path = _get_avatar_from_unzip($avatar_dir);
			if (!$img_path) {
				continue;	
			}
			$avatar       = @implode('', file($img_path));
			$bigavatar    = $avatar;
			$middleavatar = $avatar;
			$smallavatar  = $avatar;

			$fp = @fopen($bigavatarfile, 'wb');
			@fwrite($fp, $bigavatar);
			@fclose($fp);

			$fp = @fopen($middleavatarfile, 'wb');
			@fwrite($fp, $middleavatar);
			@fclose($fp);

			$fp = @fopen($smallavatarfile, 'wb');
			@fwrite($fp, $smallavatar);
			@fclose($fp);

			$biginfo    = @getimagesize($bigavatarfile);
			$middleinfo = @getimagesize($middleavatarfile);
			$smallinfo  = @getimagesize($smallavatarfile);
			if(!$biginfo || !$middleinfo || !$smallinfo || $biginfo[2] == 4 || $middleinfo[2] == 4 || $smallinfo[2] == 4) {
				file_exists($bigavatarfile) && unlink($bigavatarfile);
				file_exists($middleavatarfile) && unlink($middleavatarfile);
				file_exists($smallavatarfile) && unlink($smallavatarfile);
			}
		}
		cpmsg($mylang['upload_avatar_succeed'], $arm_action, 'succeed');	
	} else {
		cpmsg($mylang['param_error'], $arm_action, 'error');
	}
}

function _avatar($uid, $size)
{
	global $_G;
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$ucenterurl = $_G['setting']['ucenterurl'];
	$file = $ucenterurl.'/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'_avatar_'.$size.'.jpg';
	return '<img width="48" height="48" src="'.$file.'?t='.time().'" onerror="this.onerror=null;this.src=\''.$ucenterurl.'/images/noavatar_'.$size.'.gif\'" />';
}

function _get_home($uid) {
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	return $dir1.'/'.$dir2.'/'.$dir3;
}

function _set_home($uid, $dir = '.') {
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	!is_dir($dir.'/'.$dir1) && mkdir($dir.'/'.$dir1, 0777);
	!is_dir($dir.'/'.$dir1.'/'.$dir2) && mkdir($dir.'/'.$dir1.'/'.$dir2, 0777);
	!is_dir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3) && mkdir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3, 0777);
}

function _get_avatar($uid, $size = 'big', $type = 'virtual') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	return  $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}

function scan_no_avatar()
{
	global $_G;
	if (!isset($_G['cache']['plugin']['autoreply'])) {
		loadcache('plugin');
	}
	$uc_data_dir = $_G['cache']['plugin']['autoreply']['uc_data_dir'];
	if ($uc_data_dir == '') {
		$uc_data_dir = DISCUZ_ROOT.'/uc_server/data/';
	} else {
		$uc_data_dir = DISCUZ_ROOT."./$uc_data_dir";
	}
	$count = 0;
	$uids = C::t('#autoreply#plugin_autoreply_member')->fetch_all_uid();
	if (!$uids) {
		return $count;	
	}
	foreach ($uids as $uid) {
		$avatar = $uc_data_dir.'./avatar/'._get_avatar($uid);
		if (!file_exists($avatar)) {
			$count++;
		}
	}
	return $count;
}

function _get_avatar_from_unzip($dir) 
{
	static $index = 0;

	if (!is_dir($dir)) {
		return '';	
	}
	if ($d = opendir($dir)) {
		$i = 0;
		while (($file = readdir($d)) !== false) {
			if (is_dir("$dir/$file") || $file == '.' || $file == '..') {
				continue;
			}
			$suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			if (!in_array($suffix, array('jpg', 'gif', 'png', 'jpeg'))) {
				continue;
			}
			if ($i == $index) {
				$index += 1;	
				return $dir.$file;
			}
			$i += 1;
		}
	}
	$index = 0;
	return _get_avatar_from_unzip($dir);
}
