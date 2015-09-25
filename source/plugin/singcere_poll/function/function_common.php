<?php


if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}


function delattachment($aids) {
    $aids = is_array($aids) ? $aids : array($aids);
    $result = C::t('#singcere_poll#singcere_poll_attachment')->fetch_all($aids);
    if (!empty($result)) {
        foreach ($result as $key => $value) {
            @unlink(DISCUZ_ROOT . PRE_PATH . $value['attachment']);
        }
    }

    C::t('#singcere_poll#singcere_poll_attachment')->delete_batch($aids);
}


function chkperm($mark){
 global $_G;
 $editors = explode(",", $_G['cache']['plugin']['singcere_poll']['editors']);
 
 $perm = false;
 if(!empty($_G['uid'])){
 if(in_array($_G['uid'], $editors)||$mark['uid']=$_G['uid']);
   $perm=true;
 }elseif($mark[ip]==$_G['clientip']){
    $perm=true;
 }

return $perm;
}