<?php
echo "1"."<br>";
$mem_check = memory('check'); // 先检查缓存是否生效
echo $mem_check;
exit;
// echo "<pre>这个是测试\n";

// fansclub_videoscreenshot2(); // 这个只测试到youku

// fansclub_videoscreenshot3();

// 首页帖子

$orderid = $prefix.dgmdate(TIMESTAMP, 'YmdHis').random(18 - strlen($prefix));

echo $orderid;

exit;

include_once libfile('function/extends');
$data['data'] = C::t('#extends#plugin_common_block_item')->fetch_all_by_bid(135, 1, 50, true);

$item = C::t('common_block_item')->fetch_all_by_bid(135, true);
$count = count($item);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 50;

$maxpage = @ceil($count/$pagesize);
$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
$multipage_more = "plugin.php?id=extends&action=hotthread&pagesize=".$pagesize;

foreach ($data['data'] as $key => $value) {
    $data['data'][$key]['fields'] = dunserialize($value['fields']);
    $data['data'][$key]['summary'] = get_message(0, $value['id']);
    $thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$value['id']);
    $data['data'][$key]['replies'] = $thread['replies'];
    $data['data'][$key]['support_num'] = $thread['recommend_add'];
    $data['data'][$key]['dateline'] = date('m月d日 H:s', $data['data'][$key]['fields']['dateline']);
}

$return = array();
$i = 0;

// block/6e/6eb593705ae14a95266520441ef8b80d.jpg
// forum/201508/17/170747qgbobvsvpc4azqzs.jpg

foreach($data['data'] as $key => $value)
{
    $return[$i]['id'] = $value['id'];
    $return[$i]['title'] = $value['title'];
    $return[$i]['author'] = $value['fields']['author'];
    $return[$i]['authorid'] = trim($value['fields']['authorid']);
    $return[$i]['avatar_middle'] = $value['fields']['avatar_middle'];
    $return[$i]['message'] = $value['summary']['message'];
    foreach($value['summary']['img'] as $key1 => $value2)
    {
        $return[$i]['img'][] = 'http://www.5usport.com/'.$value2;
    }
    $i++;
}

echo json_encode($return);