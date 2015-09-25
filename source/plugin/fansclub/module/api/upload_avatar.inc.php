<?php
$arr_result = fansclub_avatar_upload($_GET['uid'], $_POST['image']);
echo $arr_result['message'];