<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE `cdb_plugin_autoreply_kv`;
DROP TABLE `cdb_plugin_autoreply_member`;
DROP TABLE `cdb_plugin_autoreply_ref`;
DROP TABLE `cdb_plugin_autoreply_thread`;
EOF;
runquery($sql);

$finish = FALSE;
