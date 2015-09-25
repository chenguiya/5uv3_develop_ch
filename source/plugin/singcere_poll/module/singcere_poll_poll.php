<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if (formhash() == $_GET['formhash']) {
    if (is_numeric($_GET['pid']) && $_GET['pid'] != 0) {
        $theme = C::t('#singcere_poll#singcere_poll_theme')->fetch($_GET[pid]);
        if (!empty($theme)) {
            if (is_numeric($_GET['sid']) && $_GET['sid'] != 0) {
               $selitem = C::t('#singcere_poll#singcere_poll_selitem')->fetch($_GET['sid']);
                if (!empty($selitem)) {
                    $cktime = checktime($theme[begin], $theme[end]);
                    if ($cktime != 1) {
                        echo $cktime;
						return ;
                    }
                    $ckrecord = checkcanpoll($theme, $_GET['sid']);
                    if ($ckrecord == 1) {
                        $rid = C::t('#singcere_poll#singcere_poll_recorder')->insert(array(
                            uid => $_G['uid'],
                            pid => $theme[pid],
                            sid => $_GET['sid'],
                            ip => $_G['clientip'],
                            dateline => TIMESTAMP
                                ), true);
                        if($rid){
                           C::t('#singcere_poll#singcere_poll_selitem')->update($_GET[sid],array(pnum=>$selitem[pnum]+1));
                            echo "1"."#".($selitem[pnum]+1);
                        }
                    } else {
                        echo $ckrecord;
                    }
                } else {
                    echo -3;
                }
            } else {

                echo -2;
            }
        } else {
            echo -2;
        }
    } else {

        echo -2;
    }
} else {

    echo -1;
}

function checktime($start, $end) {

    if (TIMESTAMP < $start) {
        return -4;
    } elseif (TIMESTAMP > $end) {
        return -5;
    } else {
        return 1;
    }
}

function checkcanpoll($theme, $sid) {
    global $_G;
    if (!$_G['uid']) {
        if ($theme['nologin']) {
            $recent = C::t('#singcere_poll#singcere_poll_recorder')->fetch_by_condition(array("pid" => $theme['pid'], "ip" => "'$_G[clientip]'"), "ORDER BY rid DESC");
        } else {
            return -10;
        }
    } else {
        $recent = C::t('#singcere_poll#singcere_poll_recorder')->fetch_by_condition(array("pid" => $theme['pid'], "uid" => $_G['uid']), "ORDER BY rid DESC");
    }
    if ($theme['nologin'] && !$_G['uid']) {
        
    } elseif (!$theme['nologin']) {
        
    }
    $recent = C::t('#singcere_poll#singcere_poll_recorder')->fetch_by_condition(array("pid" => $theme['pid'], "uid" => $_G['uid']), "ORDER BY rid DESC");

    if (empty($recent) || count($recent) == 0) {
        return 1;
    } else {
        if ($theme[period] == 0) {
            $same = FALSE;
            foreach ($recent as $key => $value) {
                if ($value[sid] == $sid) {
                    $same = TRUE;
                }
            }

            if (count($recent) < $theme[times]) {
                if (!$theme[muti] && $same) {
                    return -6; 
                }
                return 1;
            } else {
                return -7;
            }
        } else { 
            $last = strtotime(date("Y-m-d", $recent[0][dateline]));
            $now = strtotime(date("Y-m-d", TIMESTAMP));
            $con = $last + $theme[period] * 86400;
            if ($now == $last) {
                $same = FALSE;
                $t_count = 0;
                foreach ($recent as $key => $value) {
                    $t = strtotime(date("Y-m-d", $value[dateline]));
                    if ($now == $t) {
                        $t_count++;
                        if ($sid == $value[sid]) {
                            $same = TRUE;
                        }
                    }
                }
            } else {
                if ($now >= $con) {
                    $t_count = 0;
                } else {
                    return -8; 
                }
            }
            if ($t_count >= $theme["times"]) {
                return -9; 
            } else {
                if (!$theme[muti] && $same) {
                    return -6; 
                }
                return 1;
            }
        }
    }
}
