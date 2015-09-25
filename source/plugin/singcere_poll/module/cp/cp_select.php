<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$op = $_GET['op'];
if ($op == "delete") {
    if (is_numeric($_GET['sid'])) {
        deleteselitem($_GET['sid']);
        showmessage(lang('plugin/singcere_poll', 'dsuccess'), dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 1, 'showmsg' => true));
    }
}
if ($op == "list") {
    $pid = $_GET[pid];
    $ordersql = " ORDER BY sid DESC ";
    $curpage = max(1, intval($_GET['page']));
    $perpage = 10;
    $url = "plugin.php?id=singcere_poll:singcere_poll&mod=cp&ac=select&op=list&pid=$pid";
    $selitem = C::t('#singcere_poll#singcere_poll_selitem')->fetch_by_condition(array("pid"=>$pid), $ordersql, ($curpage - 1) * $perpage, $perpage);
    $muti = simplepage(count($selitem), $perpage, $curpage, $url);
    include template("singcere_poll:cp/singcere_poll_list");
}
if ($op == "create") {

    if (submitcheck("addselsubmit")) {

        $sid = C::t('#singcere_poll#singcere_poll_selitem')->insert(array(
            troduce => $_GET['troduce'],
            name => $_GET['name'],
            tid => $_GET['tid'],
            pid => $_GET['pid'],
            stid => $_GET["stid"],
            pnum => 0,
            dateline => TIMESTAMP
                ), true);
        if ($_GET['aids'] && $sid) {
            C::t('#singcere_poll#singcere_poll_attachment')->update_batch($_GET['aids'], array(field => "sid", value => $sid));
        }
        showmessage(lang('plugin/singcere_poll', 'aditemsuc'), dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 3, 'showmsg' => true));
    } else {
        $pid = $_GET['pid'];
        $sid = 0;
        $types = C::t('#singcere_poll#singcere_poll_seltype')->fetch_by_condition(array(pid => $pid));
        include template("singcere_poll:cp/singcere_poll_select");
    }
}
if ($op == "edit") {
    if (submitcheck("editselsubmit")) {
        if (is_numeric($_GET[sid])) {
            C::t('#singcere_poll#singcere_poll_selitem')->update($_GET['sid'], array(
               troduce => $_GET['troduce'],
                name => $_GET['name'],
                tid => $_GET['tid'],
                stid => $_GET["stid"],
            ));
            showmessage(lang('plugin/singcere_poll', 'upitemsuc'), dreferer(), array(), array('alert' => 'right', 'showdialog' => true, 'locationtime' => 1, 'msgtype' => 3, 'showmsg' => true));
        } 

    }else {
            if (intval($_GET["sid"])) {
                $sid = $_GET['sid'];
                $pid=$_GET["pid"];
                $selitem = C::t('#singcere_poll#singcere_poll_selitem')->fetch($sid);
                if (!empty($selitem)) {
                    $types = C::t('#singcere_poll#singcere_poll_seltype')->fetch_by_condition(array(pid => $pid));
                    $attach = C::t('#singcere_poll#singcere_poll_attachment')->fetch_by_condition(array(sid => $sid));
                }

               include template("singcere_poll:cp/singcere_poll_select");
            }
               include template("singcere_poll:cp/singcere_poll_select");
        }
    
}
if ($op == "upload") {
    if ($_GET['type']== "delete") {
        if (intval($_GET[aid])) {
            delattachment(array($_GET[aid]));
        }
    } else {
        if (!empty($_FILES['Filedata'])) {
            $filepath = getfilepath($_FILES['Filedata']);
            $sid = empty($_GET['sid']) ? 0 : $_GET['sid'];
            if (!empty($filepath)) {
                $path = substr($filepath, strlen(DISCUZ_ROOT . PRE_PATH));
                $aid = C::t('#singcere_poll#singcere_poll_attachment')->insert(array(
                    pid => 0,
                    sid => $sid,
                    uid => empty($_G['uid']) ? 0 : $_G['uid'],
                    filename => $_FILES['Filedata']['name'],
                    filesize => $_FILES['Filedata']['size'],
                    attachment => $path,
                    dateline => TIMESTAMP
                        ), true);
            } echo $aid . "#" . PRE_PATH . $path;
        }
    }
}

function getfilepath($files) {
 include libfile("class/image");
    $filename = $files['name'];
    if ($filename != "") {
        $path = DISCUZ_ROOT . "data/attachment/singcere_file/singcere_poll" . "/" . "a" . date("Ym");
        if (!file_exists($path)) {
            dmkdir($path);
        }
      

       $picname = md5(rand(0, 10000) . $files['name']) . strrchr($files['name'], ".");
       
        $file = $path . "/" . "a" . $picname;
		$image = new image;
        $image->Thumb($file, "", 250,null, 2,1);
        move_uploaded_file($files['tmp_name'], $file);
		
    }
    return $file;
}


function deleteselitem($sid) {
    $attach = C::t('#singcere_poll#singcere_poll_attachment')->fetch_by_condition(array(sid => $sid));
    delattachment(array_keys($attach));
    C::t('#singcere_poll#singcere_poll_selitem')->delete(array(sid => $sid));
}

?>