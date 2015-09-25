<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

// include_once DISCUZ_ROOT.'./source/plugin/my_statistics/function/function.inc.php'; // 公共函数
$action = $_GET['ac'] ? $_GET['ac'] : 'member_register';
$arr = array('member_register', 'member_online', 'thread_post'); // 只允许的action
if(!in_array($action, $arr)) die('不存在ac参数:'.$action);

$file = DISCUZ_ROOT.'./source/plugin/my_statistics/module/admin/'.$action.'.inc.php'; // 检查模块是否存在
if(!file_exists($file)) die('不存在文件:'.$file);

include $file;
// include template('my_statistics:admin/'.$action); // 可以不用
