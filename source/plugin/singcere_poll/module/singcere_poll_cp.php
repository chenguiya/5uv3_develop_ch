<?php


if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$editors = explode(",", $_G['cache']['plugin']['singcere_poll']['editors']);

$uid=  empty($_GET["uid"])?$_G['uid']:$_GET["uid"];
if ($uid == 0 || !in_array($uid, $editors)) {
    showmessage("没有权限", "plugin.php?id=singcere_fact:singcere_fact");
}
$ac=$_GET['ac'];
if(in_array($_GET['ac'], array('poll',"select"))) {
    require_once libfile('cp/'.$_GET['ac'], 'plugin/singcere_poll/module');
} else {
    showmessage('undefined_action');
}
