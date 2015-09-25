<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class plugin_singcere_poll {
      function global_header() {
         global $_G;
         if($_GET['id'] == 'singcere_poll:singcere_poll') {
            $_G['style']['tplsavemod']=1;
        }
    }
}

?>

