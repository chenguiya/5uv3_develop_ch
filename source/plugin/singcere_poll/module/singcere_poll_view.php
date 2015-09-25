<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$editors = explode(",", $_G['cache']['plugin']['singcere_poll']['editors']);

$perm = true;
if ($_G['uid'] == 0 || !in_array($_G['uid'], $editors)) {
    $perm = false;
}
$now = strtotime(date("Y-m-d", TIMESTAMP));

$pid = $_GET[pid];
//$pid = 1;

$_G['style']['tplsavemod'] = 0;

$uid = empty($_G['uid']) ? 0 : $_G['uid'];
$theme = C::t('#singcere_poll#singcere_poll_theme')->fetch($pid);
$url = "plugin.php?id=singcere_poll:singcere_poll&mod=index&pid=$theme[pid]";
if (!empty($theme)) {
    $types = C::t('#singcere_poll#singcere_poll_seltype')->fetch_by_condition(array(pid => $pid));

    $temp = C::t('#singcere_poll#singcere_poll_selitem')->fetch_by_condition(array("pid" => $pid));


    foreach ($types as $key1 => $value1) {
        foreach ($temp as $key => $value) {
            if ($value1[stid] == $value['stid']) {
                $value["cname"] = $types[$value["stid"]][name];
                $selitem[$value["stid"]][] = $value;
            }
        }
    }

    $attach = C::t('#singcere_poll#singcere_poll_attachment')->fetch_all_by_sids(array_keys($temp));



    $marks = C::t('#singcere_poll#singcere_poll_remark')->fetch_by_condition(array("pid" => $pid), "ORDER BY rid desc");

    $hot = C::t('#singcere_poll#singcere_poll_remark')->fetch_by_condition(array("pid" => $pid, "hot" => 1), "ORDER BY hdateline desc", 0, 3);

    list($navtitle, $metadescription, $metakeywords) = array($theme[title], $theme[keywords], $theme[descript]);
    $mnav = C::t('#singcere_poll#singcere_poll_nav')->fetch($_GET['pid']);

    if (defined('IN_MOBILE')) {
        $temp = C::t('#singcere_poll#singcere_poll_attachment')->fetch_by_condition(array(pid => $pid));
       $bdate = explode("-",dgmdate($theme[begin],"Y-m-d"));
       $endate= explode("-",dgmdate($theme[end],"Y-m-d"));
        foreach ($temp as $key => $value) {
            if (in_array($value['type'], array("gift1", "gift2", "gift3")))
               {
                $pattach["gift"][] = $value;
            }else{
            $pattach[$value['type']][attachment] = $value[attachment];
            }
        }
      
        include template('singcere_poll:singcere_poll_view');
    } else {
        $primaltplname = "singcere_poll_view";
        include template('diy:singcere_poll_view:' . intval($theme['pid']), NULL, 'source/plugin/singcere_poll/template', NULL, $primaltplname);
    }
} else {

    showmessage(lang('plugin/singcere_poll', 'noexit'), dreferer());
}
