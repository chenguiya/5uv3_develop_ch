<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$action = $_GET['ac'] ? $_GET['ac'] : 'index';
 
$config = include DISCUZ_ROOT.'./source/plugin/fansclub/data/config.php';
include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'; // 公共函数
$arr = array('update_v3_data', 'update_threadtypes', 'update_video_message', 'tuisong', 'upload_avatar', 'get_data', 'mytest', 'alipay_check', 'passport'); // 只允许的action

if(!in_array($action, $arr)) die('不存在ac参数:'.$action);

$file = DISCUZ_ROOT.'./source/plugin/fansclub/module/api/'.$action.'.inc.php'; // 检查模块是否存在

if(!file_exists($file)) die('不存在文件:'.$file);
include $file;

// include template('fansclub:admin/'.$action);
