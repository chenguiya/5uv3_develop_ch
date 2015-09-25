<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

deldir(DISCUZ_ROOT . "data/attachment/singcere_file/singcere_poll");
$sql = <<<EOF
DROP TABLE IF EXISTS  pre_singcere_poll_attachment;
DROP TABLE IF EXISTS  pre_singcere_poll_theme;
DROP TABLE IF EXISTS  pre_singcere_poll_seltype;
DROP TABLE IF EXISTS  pre_singcere_poll_selitem;
DROP TABLE IF EXISTS  pre_singcere_poll_recorder;
DROP TABLE IF EXISTS  pre_singcere_poll_remark;
DROP TABLE IF EXISTS  pre_singcere_poll_nav;         
EOF;
runquery($sql);
$finish = TRUE;

function deldir($dir) {
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
    closedir($dh);
    if(rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}


?>