<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if (submitcheck("remarksubmit")) {
  $mark=$_GET['mark'];
  if($mark==1){
    if (intval($_GET['pid'])) {
        if (!empty($_GET['message'])) {
            $rid = C::t('#singcere_poll#singcere_poll_remark')->insert(array(
                uid => $_G['uid'],
                message => $_GET['message'],
                pid => $_GET['pid'],
                username=>$_G['username'],
                dateline => TIMESTAMP
                    ), true);

            if ($rid) {
                showmessage('do_success',$_GET['reffer']."#comment");
            }
        } else {
            showmessage(lang('plugin/singcere_poll', 'inlege'), dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 3, 'showmsg' => true));
        }
    }
	}else{
	
	showmessage(lang('plugin/singcere_poll', 'nopercom'), dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 3, 'showmsg' => true));
	
	}
}

if ($_GET[op] == "delete") {
    if ($_GET["formhash"] == formhash()) {
        $pid=$_GET['pid'];

        if (intval($_GET['rid'])) {
            $reslut = C::t('#singcere_poll#singcere_poll_remark')->fetch($_GET[rid]);
            if (empty($reslut)) {
                showmessage(lang('plugin/singcere_poll', 'noexit'),dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
            } else {
                if (!chkperm($reslut[uid])) {
                    showmessage(lang('plugin/singcere_poll', 'noperm'),dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
                } else {

                    $reslut = C::t('#singcere_poll#singcere_poll_remark')->delete(array(rid => $_GET[rid]));
                    showmessage(lang('plugin/singcere_poll', 'dsuccess'), dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
                }
            }
        }
    } else {
        showmessage("submit_invalid", dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
    }
}if($_GET[op]=="sethot"||$_GET[op]=="unsethot"){
if ($_GET["formhash"] == formhash()) {
 if (!chkperm($reslut[uid])) {
                    showmessage(lang('plugin/singcere_poll', 'noperm'),dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
                } else {
				 if($_GET['op']=="sethot"){
				 C::t('#singcere_poll#singcere_poll_remark')->update($_GET[rid],array(hot=>1,hdateline=>TIMSTAMP));
				 }else{
				 C::t('#singcere_poll#singcere_poll_remark')->update($_GET[rid],array(hot=>0,hdateline=>TIMSTAMP));
				 }
				  showmessage(lang('plugin/singcere_poll', 'success'),dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
				}

}else{
 showmessage("submit_invalid", dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
}

}

