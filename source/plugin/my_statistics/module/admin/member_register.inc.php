<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) { exit('Access Denied'); }
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
	showformheader('frames=yes&action=plugins&do=34&identifier=my_statistics&pmod=admin&ac=member_register', '', 'threadforum');
	// 表格样式
	showtableheader();
	//showtablerow('', array('class="rowform" colspan="2" style="width:auto;"'), array($forumselect.$typeselect));
	if(!$fromumanage) {
		empty($_GET['starttime']) && $_GET['starttime'] = date('Y-m-d', time() - 86400 * 30);
	}
	echo '<input type="hidden" name="fromumanage" value="'.$fromumanage.'">';
	showsetting('用户注册时间', array('starttime', 'endtime'), array($_GET['starttime'], $_GET['endtime']), 'daterange');

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
			$arr_row1 = $arr_row2 = array();
			
			// 1、取论坛的注册数据
			$sql =  "SELECT count(*) as shumu, FROM_UNIXTIME(regdate,'%Y-%m-%d') as date ".
					"FROM ".DB::table('ucenter_members')." ".
					"WHERE regdate >= UNIX_TIMESTAMP('".$conditions['starttime']." 00:00:00')";
			if($conditions['endtime'] != '')
				$sql .= " AND regdate <= UNIX_TIMESTAMP('".$conditions['endtime']." 23:59:59')";
			$sql .= " GROUP BY date ORDER BY date DESC";
			$arr_row1 = DB::fetch_all($sql);
			
			// 2、CRUL取聊球(2015-06-15改版后，大用户中心启用前可使用)
			// $url = 'http://www.5usport.com';
			if(strpos($_G['siteurl'], 'www.5usport.com') > 0)
			{
				$url = 'http://www.5usport.com';
			}
			elseif(strpos($_G['siteurl'], 'zhangjh.usport.com.cn') > 0)
			{
				$url = 'http://zhangjh.dev.usport.cc';
			}
			else
			{
				$url = 'http://www.usport.cc';
			}
			
			$api_url = $url.'/v3/to_v3/phpcms/get_register_count?starttime='.$conditions['starttime'].'&endtime='.$conditions['endtime'].'&sign=fb574b29f55486b2c8584e7978b0ea88';
			// echo $api_url."\n";
			$result = curl_access($api_url);
			$arr_result = json_decode($result, TRUE);
			if($arr_result['success'] === TRUE)
			{
				$arr_row2 = $arr_result['result'];
			}
			
			//print_r($arr_row2);
			
			$arr_row_all = array();
			if(count($arr_row1) > 0)
			{
				foreach($arr_row1 as $key => $value)
				{
					$arr_row_all[$value['date']]['luntan'] += $value['shumu'];
					$arr_row_all[$value['date']]['liaoqiu'] += 0;
				}
			}
			
			if(count($arr_row2) > 0)
			{
				foreach($arr_row2 as $key => $value)
				{
					$arr_row_all[$value['date']]['liaoqiu'] += $value['shumu'];
					$arr_row_all[$value['date']]['luntan'] += 0;
				}
			}
			
			if(count($arr_row_all) > 0)
			{
				$all_luntan = $all_liaoqiu = 0;
				foreach($arr_row_all as $key => $value)
				{
					$members .=  showtablerow('', array('style="width:200px;"', 'style="width:400px;"','','style="width:auto;"'), array(
								$key,
								$value['luntan'],
								$value['liaoqiu'],
								"",
							), TRUE);
					$all_luntan += $value['luntan'];
					$all_liaoqiu += $value['liaoqiu'];
				}
				
				$members .=  showtablerow('', array('style="width:200px;"', 'style="width:400px;"','','style="width:auto;"'), array(
								'<b>总计</b>',
								'<b>'.$all_luntan.'</b>',
								'<b>'.$all_liaoqiu.'</b>',
								"",
							), TRUE);
			}
			$multi = '';
		}
		
		showtableheader('查询结果', 'notop');
		if(count($arr_row_all) == 0) {
			showtablerow('', 'colspan="3"', cplang('threads_thread_nonexistence')); // 没有结果
		} else {
				showsubtitle(array('注册时间', '论坛注册数', '聊球注册数',''));
				echo $members;
				showtablefooter();
		}
	}
	
	showtablefooter();
	showformfooter();
	showtagfooter('div');

?>