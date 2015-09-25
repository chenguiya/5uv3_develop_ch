<?php
// 更新主题分类
// http://www.5usport.cn/plugin.php?id=fansclub:api&ac=update_threadtypes&op=update&num=3
/*
SELECT
	a.tid,
	a.fid,
	a.typeid,
	a.attachment,
	a.`subject`,
	FROM_UNIXTIME(a.dateline) AS dateline,
	b.`name`,
	b.`status`,
	d.`name`,
	c.threadtypes
FROM
	pre_forum_thread a,
	pre_forum_forum b,
	pre_forum_forumfield c,
	pre_forum_threadclass d
WHERE
	a.fid = b.fid
AND b.fid = c.fid
AND b.`status` != 3
AND d.fid = a.fid
and a.typeid = d.typeid
ORDER BY
	a.tid DESC
*/
echo "<pre>";

$op = trim($_GET['op']);
$num = intval(trim($_GET['num']));
$num = ($num == 0) ? 1 : $num;

$sql = "select d.new_tid ".
		"from ".DB::table('plugin_fansclub_old_article_log') ." d ".
		"where 1 = 1 ".
		"order by d.new_tid DESC";
echo $sql;
echo "\n";
$j = 0;

$arr = DB::fetch_all($sql);
for($i = 0; $i < count($arr); $i++)
{
	if($j >= $num) break;
	$sql = "select a.tid, a.fid, a.typeid, a.attachment, a.`subject`, FROM_UNIXTIME(a.dateline) AS dateline ".
		"from ".DB::table('forum_thread')." a ".
		"where 1 = 1 ".
		"and a.tid = ".$arr[$i]['new_tid']." ".
		"order by a.tid DESC ";
	//echo $sql;
	//echo "\n";
	$row = DB::fetch_all($sql);
	if(count($row) == 1)
	{
		//print_r($row[0]);
		$typeid = intval($row[0]['typeid']);
		$fid = intval($row[0]['fid']);
		$tid = intval($row[0]['tid']);
		
		if(($typeid == 1 && $fid != 340 && $fid != 339 && $fid != 244) || $typeid == 2 || $typeid == 3)
		{
			if($typeid == 1) // 新闻
			{
				echo "新闻|";
				$sql = "SELECT b.typeid FROM ".DB::table('forum_threadclass')." b WHERE b.fid = ".$fid." AND b.name = '新闻'";
				$row2 = DB::fetch_all($sql);
				echo $sql."|";
				if(count($row2) == 1)
				{
					$new_typeid = intval($row2[0]['typeid']);
					$sql = "UPDATE ".DB::table('forum_thread')." SET typeid = ".$new_typeid." WHERE tid = ".$tid;
					DB::query($sql);
					echo $sql."|"; 
					$j++;
				}
				else
				{
					echo "没有找到分类信息";
				}
				echo "\n";
			}
			elseif($typeid == 2) // 图片
			{
				
				echo "图片|";
				$sql = "SELECT b.typeid FROM ".DB::table('forum_threadclass')." b WHERE b.fid = ".$fid." AND b.name = '图片'";
				$row2 = DB::fetch_all($sql);
				echo $sql."|";
				if(count($row2) == 1)
				{
					$new_typeid = intval($row2[0]['typeid']);
					$sql = "UPDATE ".DB::table('forum_thread')." SET typeid = ".$new_typeid." WHERE tid = ".$tid;
					DB::query($sql);
					echo $sql."|";
					$j++;
				}
				else
				{
					echo "没有找到分类信息";
				}
				echo "\n";
			}
			elseif($typeid == 3) // 视频
			{
				echo "视频|";
				$sql = "SELECT b.typeid FROM ".DB::table('forum_threadclass')." b WHERE b.fid = ".$fid." AND b.name = '视频'";
				$row2 = DB::fetch_all($sql);
				echo $sql."|";
				if(count($row2) == 1)
				{
					$new_typeid = intval($row2[0]['typeid']);
					$sql = "UPDATE ".DB::table('forum_thread')." SET typeid = ".$new_typeid." WHERE tid = ".$tid;
					DB::query($sql);
					echo $sql."|";
					$j++;
				}
				else
				{
					echo "没有找到分类信息";
				}
				echo "\n";
			}
		}
	}
}


/*
// 图片
$sql =	"select a.tid, a.fid, a.typeid, a.attachment, a.`subject`, FROM_UNIXTIME(a.dateline) AS dateline, ".
			"c.`name` as f_name, c.`status`, b.`name` as c_name ".
		"from ".DB::table('plugin_fansclub_old_article_log') ." d, ".DB::table('forum_forum') ." c, ".DB::table('forum_thread')." a ".
		"left join ".DB::table('forum_threadclass')." b on a.typeid = b.typeid ".
		"where 1 = 1 ".
			"and c.fid = a.fid ".
			"and c.status != 3 ".
			//"and a.typeid = 0 ".
			"and d.new_tid = a.tid ".
			//"and a.displayorder >= 0 ".
		"order by a.tid DESC ";
		
if($op == 'update')
{}
else
{
	$sql .= "limit 0, ".$num;
}
$arr = DB::fetch_all($sql);

echo "<table border=1>";
echo "<tr>";
echo "<td>tid</td>";
echo "<td>fid</td>";
echo "<td>typeid</td>";
echo "<td>attachment</td>";
echo "<td>subject</td>";
echo "<td>dateline</td>";
echo "<td>f_name</td>";
echo "<td>status</td>";
echo "<td>主题分类名</td>";
echo "</tr>";

$j = 0;
for($i = 0; $i < count($arr); $i++)
{
	if($j >= $num)
	{
		break;
	}
	else
	{
		if($op == 'showall' || $arr[$i]['typeid'] == 1 || $arr[$i]['typeid'] == 2 || $arr[$i]['typeid'] == 3 )
		{
			echo "<tr>";
			echo "<td>".$arr[$i]['tid']."</td>";
			echo "<td>".$arr[$i]['fid']."</td>";
			echo "<td>".$arr[$i]['typeid']."</td>";
			echo "<td>".$arr[$i]['attachment']."</td>";
			echo "<td>".$arr[$i]['subject']."</td>";
			echo "<td>".$arr[$i]['dateline']."</td>";
			echo "<td>".$arr[$i]['f_name']."</td>";
			echo "<td>".$arr[$i]['status']."</td>";
			echo "<td>".$arr[$i]['c_name']."</td>";
			echo "</tr>";
			$j++;
		}
	}
}
echo "</table>";
*/



