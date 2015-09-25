<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

sc_superman_autoreply::show();	
exit;

////////////////////////////////////////////////////////////////////////////
class sc_superman_autoreply
{
	public static function show()
	{
		global $_G, $lang, $arm_action, $scriptlang,$pluginid;
		$mylang = $scriptlang['autoreply'];
		if (file_exists(DISCUZ_ROOT.'./source/plugin/autoreply/include/functions.inc.php')) {
			$functions = '<p style="color: #009900;">'.$mylang['extend_y'].'</p>';
		}else {
			$functions = '<p style="color: #FF0000;">'.$mylang['extend_w'].'<a href="http://addon.discuz.com/?@autoreply.plugin.23401">'.$mylang['extend_d'].'</a></p>';
		}		
		if (file_exists(DISCUZ_ROOT.'./source/plugin/autoreply/include/super_var.inc.php')) {
			$super_var = '<p style="color: #009900;">'.$mylang['extend_y'].'</p>';
		}else {
			$super_var = '<p style="color: #FF0000;">'.$mylang['extend_w'].'<a href="http://addon.discuz.com/?@autoreply.plugin.31631">'.$mylang['extend_d'].'</a></p>';
		}
		if (file_exists(DISCUZ_ROOT.'./source/plugin/autoreply/include/multi_reply.inc.php')) {
			$multi_reply = '<p style="color: #009900;">'.$mylang['extend_y'].'</p>';
		}else {
			$multi_reply = '<p style="color: #FF0000;">'.$mylang['extend_w'].'<a href="http://addon.discuz.com/?@autoreply.plugin.35769">'.$mylang['extend_d'].'</a></p>';
		}
		
		//showtips('');
		showformheader("plugins&operation=manage&do=$pluginid&identifier=superman_autoreply&pmod=manage");
		showtableheader($mylang['extend']);
		showsubtitle(array(
			$mylang['extend_mc'], 
			$mylang['extend_ms'], 
			$mylang['extend_zt'], 
		));
		showtablerow(
			'',		//trstryle
			array('class="td31"', '', 'class="td31"'),	//tdstyle
			array(
				$mylang['extend_zs'],
				$mylang['extend_zc'],
				$functions,
			)	//tdtext
		);
		showtablerow(
			'',		//trstryle
			array('class="td31"', '', 'class="td31"'),	//tdstyle
			array(
				$mylang['extend_cj'],
				$mylang['extend_cj1'],
				$super_var,
			)	//tdtext
		);
		showtablerow(
			'',		//trstryle
			array('class="td31"', '', 'class="td31"'),	//tdstyle
			array(
				$mylang['extend_multi_reply'],
				$mylang['extend_multi_reply_desc'],
				$multi_reply,
			)	//tdtext
		);
		showtablefooter();
		showformfooter();
	}
}
