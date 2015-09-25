<?php
/**
 *	RTCreative.php (C)2012-2099 Powered by 时创科技.
 *	Version: 6.1
 *	Date: 2014-08-29 12:15:34
 */
class RTCreative
{
	const PLUGIN_NAME = 'autoreply';

	public static $_G;
	public static $setting;
	public static $pluginid;
	public static $operation;
	public static $plugin_lang = array(
		'script'   => '',
		'template' => '',
	);
	public static $lang;
	public static $table = array(
		'kv'           => 'plugin_autoreply_kv',
		'ref'          => 'plugin_autoreply_ref',
		'thread'       => 'plugin_autoreply_thread',
		'member'       => 'plugin_autoreply_member',
		'forum_thread' => 'forum_thread_plugin',
	);
	private static $instance;

	public static function instance()
	{
		if (self::$instance === NULL) {
			return new RTCreative;
		}
		return self::$instance;
	}

	public function __construct()
	{
		if (self::$instance === NULL) {
			self::$instance = $this;
		}
		global $_G, $scriptlang, $templatelang, $pluginid, $operation, $lang;
		self::$_G               = $_G;
		self::$setting          = self::$_G['cache']['plugin'][self::PLUGIN_NAME];
		self::$setting['debug'] = false;
		if (self::$setting['debug']) {
			error_reporting(E_ALL & ~E_NOTICE | E_STRICT);
			ini_set('display_errors', 1);
		} else {
			error_reporting(0);
			ini_set('display_errors', 0);
		}
		self::$pluginid                = $pluginid;
		self::$operation               = $operation;
		self::$plugin_lang['script']   = $scriptlang[self::PLUGIN_NAME];
		self::$plugin_lang['template'] = $templatelang[self::PLUGIN_NAME];
		self::$lang                    = $lang;
		if (count(self::$table)) {
			foreach (self::$table as $k=>$v) {
				self::$table[$k] = '#'.self::PLUGIN_NAME.'#'.$v;
			}
		}
	}
}
?>
