<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$op = $_GET['op'];

$fnav = array("nav1" => lang('plugin/singcere_poll', 'actroduce'), "nav2" =>  lang('plugin/singcere_poll', 'acgift'),
    "nav3" =>lang('plugin/singcere_poll', 'actime'), "nav4" => lang('plugin/singcere_poll', 'acpeople'),
    "p_nav" => lang('plugin/singcere_poll', 'acpoll'), "c_nav" => lang('plugin/singcere_poll', 'accomment'));

$snav = array("nav1" => lang('plugin/singcere_poll', 'actroduce'), "nav2" => lang('plugin/singcere_poll', 'acgift'),
    "nav3" => "", "nav4" => "", "p_nav" => lang('plugin/singcere_poll', 'acpoll'), "c_nav" => lang('plugin/singcere_poll', 'accomment'));

if ($op == "delete") {

    if (is_numeric($_GET['pid'])) {
        deleteTheme($_GET['pid']);
        showmessage(lang('plugin/singcere_poll', 'dsuccess'), dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
    }
}
if ($op == "list") {

    $ordersql = " ORDER BY pid DESC ";

    $curpage = max(1, intval($_GET['page']));
    $perpage = 10;
    $url = "plugin.php?id=singcere_poll:singcere_poll&mod=cp&ac=poll&op=list";
    $themes = C::t('#singcere_poll#singcere_poll_theme')->fetch_by_condition("", $ordersql, ($curpage - 1) * $perpage, $perpage);
    $count = DB::fetch_all("SELECT SUM(pnum) total,pid FROM %t GROUP BY pid ", array("singcere_poll_selitem"), "pid");

    $muti = simplepage(count($themes), $perpage, $curpage, $url);
    include template("singcere_poll:cp/singcere_poll_list");
}
if ($op == "create") {
    if (submitcheck("addpollsubmit")) {

        $pid = C::t('#singcere_poll#singcere_poll_theme')->insert(array(
            troduce => $_GET['troduce'],
            subject => $_GET['subject'],
            nvtype => $_GET['nvtype'],
            uid => $_G['uid'],
            //fid=>$_GET['fid'],
            title => $_GET[title],
            descript => $_GET[descript],
            mark => $_GET['mark'],
            juli => empty($_GET['juli']) ? 400 : $_GET['juli'],
            keywords => $_GET[keywords],
            color => $_GET['color'],
            begin => strtotime($_GET["begin"]),
            period => empty($_GET['period']) ? 0 : $_GET['period'],
            muti => $_GET['muti'],
            blank => $_GET['blank'],
            ip => $_GET['ip'],
            end => strtotime($_GET["end"]),
            nologin => $_GET['nologin'],
            times => $_GET['times'],
            dateline => TIMESTAMP
                ), true);

        if ($pid) {
            $aids = getaids(5, $_GET);
            C::t('#singcere_poll#singcere_poll_attachment')->update_batch($aids, array(field => "pid", value => $pid));
        }
        $j = 1;
        for ($i = 5; $i < 8; $i++) {

            if (!empty($_GET["aid$i"])) {
                ;
                addpictroduc($_GET["aid$i"], $_GET["troduce$j"], $pid);
            }
            $j++;
        }

        if (!empty($_GET[types])) {
            addseltype($pid, $_GET[types]);
        }

        createnav($_GET, $pid, "add");
        showmessage(lang('plugin/singcere_poll', 'adsuccess'), "plugin.php?id=singcere_poll:singcere_poll&mod=index&pid=$pid");
    } else {
        $pid = 0;
        $poll[juli] = 400;
        $poll['blank'] = 2;

        include template("singcere_poll:cp/singcere_poll");
    }
}
if ($op == "edit") {
    if (submitcheck("editpollsubmit")) {
        if (is_numeric($_GET[pid])) {
            C::t('#singcere_poll#singcere_poll_theme')->update($_GET['pid'], array(
                troduce => $_GET['troduce'],
                subject => $_GET['subject'],
                title => $_GET[title],
                descript => $_GET[descript],
                nvtype => $_GET['nvtype'],
                keywords => $_GET[keywords],
                blank => $_GET['blank'],
                color => $_GET['color'],
                mark => $_GET['mark'],
                juli => empty($_GET['juli']) ? 400 : $_GET['juli'],
                //	fid=>$_GET['fid'],
                period => empty($_GET['period']) ? 0 : $_GET['period'],
                muti => $_GET['muti'],
                ip => $_GET['ip'],
                begin => strtotime($_GET["begin"]),
                end => strtotime($_GET["end"]),
                nologin => $_GET['nologin'],
                times => $_GET['times'],
                    ), true);

            for ($i = 0; $i < 5; $i++) {
                updateattach($_GET["aid$i"], $_GET["oaid$i"]);
            }
            $j = 1;
            for ($i = 5; $i < 8; $i++) {

                updateattach($_GET["aid$i"], $_GET["oaid$i"], "pictroduce", $_GET["troduce$j"]);
                $j++;
            }

            if (!empty($_GET[types])) {
                addseltype($_GET[pid], $_GET[types]);
            }

            if (!empty($_GET[otypes]) && !empty($_GET[stid])) {
                updateselTtype($_GET[stid], $_GET[otypes]);
            }

            if (!empty($_GET["ostids"]) && !empty($_GET["otypes"])) {
                updateselTtype($_GET["ostids"], $_GET["otypes"]);
            }
        }
        createnav($_GET, $_GET['pid'], "edit");
        showmessage( lang('plugin/singcere_poll', 'modsuc'), "plugin.php?id=singcere_poll:singcere_poll&mod=index&pid=$_GET[pid]");
    } elseif ($_GET['type'] == "deltype") {
        if (is_numeric($_GET[stid])) {
            $types = C::t('#singcere_poll#singcere_poll_seltype')->delete(array(stid => $_GET[stid]));
        }
    } else {
        if (is_numeric($_GET[pid])) {
            $pid = $_GET['pid'];

            $poll = C::t('#singcere_poll#singcere_poll_theme')->fetch($_GET[pid]);
            if (empty($poll)) {
                showmessage( lang('plugin/singcere_poll', 'noexit'), dreferer());
            }
            $poll[begin] = dgmdate($poll[begin]);
            $poll[end] = dgmdate($poll[end]);

            $attach = C::t('#singcere_poll#singcere_poll_attachment')->fetch_by_condition(array(pid => $poll[pid]));

            if ($poll[nvtype] == 2) {
                $snav = C::t('#singcere_poll#singcere_poll_nav')->fetch($pid);
            } else {
                $fnav = C::t('#singcere_poll#singcere_poll_nav')->fetch($pid);
            }

            //   $attach = current($attach);

            foreach ($attach as $key => $value) {
                $pattach[$value['type']] = $value;
            }

            $types = C::t('#singcere_poll#singcere_poll_seltype')->fetch_by_condition(array(pid => $poll[pid]));

            include template("singcere_poll:cp/singcere_poll");
        }
    }
}

if ($op == "upload") {
    if (!empty($_FILES['Filedata'])) {
        $filepath = getfilepath($_FILES['Filedata']);
        if (!empty($filepath)) {
            $path = substr($filepath, strlen(DISCUZ_ROOT . PRE_PATH));
            $aid = C::t('#singcere_poll#singcere_poll_attachment')->insert(array(
                pid => $_GET[pid],
                sid => 0,
                uid => empty($_G['uid']) ? 0 : $_G['uid'],
                filename => $_FILES['Filedata']['name'],
                filesize => $_FILES['Filedata']['size'],
                attachment => $path,
                type => $_GET['type'],
                dateline => TIMESTAMP
                    ), true);
        }
    }
    echo PRE_PATH . $path . "#" . $aid;
}

function addseltype($pid, $types) {
    foreach ($types as $key => $value) {

        $value = trim($value);
        if (!empty($value)) {
            C::t('#singcere_poll#singcere_poll_seltype')->insert(array(
                pid => $pid,
                name => trim($value),
            ));
        }
    }
}

function updateselTtype($stids, $types) {
    foreach ($stids as $key => $value) {
        if (!empty($types[$key])) {
            C::t('#singcere_poll#singcere_poll_seltype')->update($value, array(name => $types[$key]));
        }
    }
}

function getfilepath($files) {
    $filename = $files['name'];
    if ($filename != "") {
        $path = DISCUZ_ROOT . "data/attachment/singcere_file/singcere_poll" . "/" . "a" . date("Ym");
        if (!file_exists($path)) {
            dmkdir($path);
        }
        $filetype = $files['type'];
        if ($filetype == 'image/jpeg') {
            $type = '.jpg';
        }
        if ($filetype == 'image/jpg') {
            $type = '.jpg';
        }
        if ($filetype == 'image/pjpeg') {
            $type = '.jpg';
        }
        if ($filetype == 'image/gif') {
            $type = '.gif';
        }
        if ($filetype == 'image/png') {
            $type = '.png';
        }
        $picname = md5(rand(0, 10000) . $files['name']) . $type;
        $file = $path . "/" . "a" . $picname;
        move_uploaded_file($files['tmp_name'], $file);
    }
    return $file;
}

function deleteTheme($pid) {
    $attach = C::t('#singcere_poll#singcere_poll_attachment')->fetch_by_condition(array(pid => $pid));
    delattachment(array_keys($attach));
    C::t('#singcere_poll#singcere_poll_seltype')->delete(array(pid => $pid));
    C::t('#singcere_poll#singcere_poll_selitem')->delete(array(pid => $pid));
    C::t('#singcere_poll#singcere_poll_recorder')->delete(array(pid => $pid));

    $poll = C::t('#singcere_poll#singcere_poll_theme')->delete(array(pid => $pid));
}

function updateattach($naid, $oaid, $type, $troduce) {
    if (!isset($type)) {
        if ($naid && $naid != $oaid) {

            delattachment(array($oaid));
        }
    } else {

        if ($naid && $naid != $oaid) {
            C::t('#singcere_poll#singcere_poll_attachment')->update($naid, array(troduce => $troduce));
            delattachment(array($oaid));
        } else {
            C::t('#singcere_poll#singcere_poll_attachment')->update($oaid, array(troduce => $troduce));
        }
    }
}

function createnav($param, $hid, $type) {


    for ($i = 1; $i <= 4; $i++) {
        $values["nav$i"] = $param["nav$i"];
    }
    $values["c_nav"] = $param['c_nav'];
    $values["p_nav"] = $param['p_nav'];
    $values['hid'] = $hid;
    if ($type == "add") {
        C::t('#singcere_poll#singcere_poll_nav')->insert($values);
    } else {

        C::t('#singcere_poll#singcere_poll_nav')->update($hid, $values);
    }
}

function getaids($i, $get) {
    for ($j = 0; $j < $i; $j++) {
        if (!empty($get["aid$j"])) {
            $aids[] = $get["aid$j"];
        }
    }
    return $aids;
}

function addpictroduc($aid, $troduce, $pid) {

    DB::update("singcere_poll_attachment", array(pid => $pid, troduce => $troduce), array(aid => $aid));
}

?>