<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) { exit('Access Denied'); }

/*
group_common_onlinetime 在线时间表
字段名	数据类型	默认值	允许非空	自动递增	备注
uid	mediumint(8) unsigned	 0	 NO	 	 
thismonth	smallint(6) unsigned	 0	 NO	 	 本月在线时间
total	mediumint(8) unsigned	 0	 NO	 	 总在线时间
lastupdate	int(10) unsigned	 0	 NO	 	 

select * from group_common_session a order by uid desc 

*/

include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
require_once libfile('function/post');

cpheader();
echo <<<EOT
<script src="static/js/calendar.js"></script>
<script type="text/JavaScript">
	function page(number) {
		$('threadforum').page.value=number;
		$('threadforum').searchsubmit.click();
	}
</script>
EOT;

	// 查询后会消失
	// showtagheader('div', 'threadsearch', !submitcheck('searchsubmit', 1) && empty($newlist));
	// 提交URL
	showformheader('frames=yes&action=plugins&do=34&identifier=my_statistics&pmod=admin&ac=member_online', '', 'threadforum');
	// 表格样式
	showtableheader();
	//showtablerow('', array('class="rowform" colspan="2" style="width:auto;"'), array($forumselect.$typeselect));
	if(!$fromumanage) {
		empty($_GET['starttime']) && $_GET['starttime'] = date('Y-m-d', time() - 86400 * 30);
	}
	echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
	showsetting('用户最后活跃时间', array('starttime', 'endtime'), array($_GET['starttime'], $_GET['endtime']), 'daterange');

	showtagfooter('tbody');
	showsubmit('searchsubmit', 'submit', '', '');
	if(submitcheck('searchsubmit', 1))
	{
		// 点击了提交
		$conditions['starttime'] = $_GET['starttime'] != '' ? $_GET['starttime'] : '';
		$conditions['endtime'] = $_GET['endtime'] != '' ? $_GET['endtime'] : '';
		
		$membercount = 0;
		$members = '';
		
		if($conditions)
		{
			$sql =  "SELECT count(*) as shumu, FROM_UNIXTIME(lastupdate,'%Y-%m-%d') as date ".
					"FROM ".DB::table('common_onlinetime')." ".
					"WHERE lastupdate >= UNIX_TIMESTAMP('".$conditions['starttime']." 00:00:00')";
			if($conditions['endtime'] != '')
				$sql .= " AND lastupdate <= UNIX_TIMESTAMP('".$conditions['endtime']." 23:59:59')";
			$sql .= " GROUP BY date ORDER BY date DESC";
			$arr_row = DB::fetch_all($sql);
			
			$all_liaoqiu = 0;
			for($i = 0; $i < count($arr_row); $i++)
			{
				$members .=  showtablerow('', array('style="width:200px;"', 'style="width:400px;"','','style="width:auto;"'), array(
							$arr_row[$i]['date'],
							$arr_row[$i]['shumu'],
							"",
						), TRUE);
				$membercount += $arr_row[$i]['shumu'];
			}
			
			$members .=  showtablerow('', array('style="width:200px;"', 'style="width:400px;"','','style="width:auto;"'), array(
								'<b>总计</b>',
								'<b>'.$membercount.'</b>'), TRUE);
			
			$multi = '';
		}
		showtableheader('查询结果','notop');
		if(count($arr_row) == 0) {
			showtablerow('', 'colspan="3"', cplang('threads_thread_nonexistence')); // 没有结果
		} else {
				showsubtitle(array('最后活动时间', '活动账号数', ''));
				echo $members;
				showtablefooter();
		}
	}
	
	$online_members = '';
	$sql = "select a.username,FROM_UNIXTIME(a.lastactivity) as lastactivity ".
			"from ".DB::table('common_session')." a ".
			"where a.uid > 0 ".
			"order by lastolupdate desc";
	$arr_row = DB::fetch_all($sql);
	showtableheader('现在在线用户', 'notop');
	for($i = 0; $i < count($arr_row); $i++)
	{
		$online_members .=  showtablerow('', array('style="width:200px;"', 'style="width:400px;"','','style="width:auto;"'), array(
								$arr_row[$i]['username'],
								$arr_row[$i]['lastactivity'],
								"",
							), TRUE);
	}
	$online_members .=  showtablerow('', array('style="width:200px;"', 'style="width:400px;"','','style="width:auto;"'), array(
								'<b>总计</b>',
								'<b>'.count($arr_row).'</b>'), TRUE);
	
	showsubtitle(array('用户名', '最后活动时间', ''));
	echo $online_members;
	showtablefooter();
	showformfooter();
	showtagfooter('div');
?>