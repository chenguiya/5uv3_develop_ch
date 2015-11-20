<?php
/**
杨源海(杨源海) 09-15 10:16:34
华仔，昨天的订单号ok后，再帮我做一个页面，独立访问加个密码，列出每日的各个类型订单的成功笔数同收入总数
杨源海(杨源海) 09-15 10:24:07
顺便限下ip把，在公司才可以访问
杨源海(杨源海) 09-15 10:24:25
不要搞复杂，最简单，有分页我就满足了
杨源海(杨源海) 09-15 10:25:44
日期、VIP充值订单数、VIP充值总金额、U币充值订单数、U币充值总金额、线下店订单数、线下店总金额
杨源海(杨源海) 09-15 10:25:55
就甘多数据要求。

http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=alipay_check
http://www.5usport.com/plugin.php?id=fansclub:api&ac=alipay_check
*/

define('ADMIN_USERNAME', 'admin');                  // Username
define('ADMIN_PASSWORD', 'AdminPwdAlipay');         // Password
$arr_ip = array('192.168.2.93', '14.23.102.146');   // Pass IP

if(!in_array($_SERVER['REMOTE_ADDR'], $arr_ip))
{
    show_error('IP Error!');
}

if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)
{
   Header("WWW-Authenticate: Basic realm=\"Check Login\"");
   Header("HTTP/1.0 401 Unauthorized");
   show_error('Wrong Username or Password!');
}

if($_GET['inajax'] == '1')
{
    if($_GET['op'] == 'search')
    {
        $arr_param = array();
        $arr_param['s_time'] = trim($_POST['s_time']);      // 开始时间
        $arr_param['e_time'] = trim($_POST['e_time']);      // 结束时间
        $arr_param['num'] = trim($_POST['num']);            // 每页条数
        $arr_param['stat'] = trim($_POST['stat']);          // 是否统计
        $arr_param['success'] = trim($_POST['success']);    // 是否成功
        $arr_param['page'] = trim($_POST['page']);          // 当前页数
        $arr_param['bill_type'] = trim($_POST['bill_type']);
        
        $arr_return = search($arr_param);
        $str_return = json_encode($arr_return);
        die($str_return);
    }
    else
    {
        die('error');
    }
}

?><!doctype html>
<html>
	<head>
	<meta charset="utf-8">
	<title>充值统计</title>
	<style type="text/css">
	table.gridtable {
		margin-left:auto;
		margin-right:auto;
		/*width:1000px;*/
		margin-top:8px;
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-color: #666666;
		border-collapse: collapse;
	}
	table.gridtable select {
		height:24px;
		line-height:24px;
	}
	
	table.gridtable input {
		height:18px;
		line-height:18px;
		
	}
	
	table.gridtable button {
		height:25px;
		width:100px;
		float:right;
		margin-left:4px;
	}
	
	table.gridtable button.left {
		height:25px;
		width:100px;
		float:left;
		margin-left:0px;
	}
	
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
		background-color: #dedede;
	}
	
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #666666;
	}
	table.gridtable tr.have {
		background-color: #FFF8DC;
	}
	
	table.gridtable td.right {
		text-align:right;
	}
	table.gridtable a {
		color:#07519A;
		text-decoration: none
	}
	table.gridtable a:hover {
		color: #FF6600; 
		text-decoration: underline
	}

	</style>
	<script type="text/javascript" id="seajsnode" src="<?php echo $_G['config']['static']; ?>/jquery.min.js"></script>
</head>
<body>
<form id="form_search" name="form_search" onSubmit="javascript:return false;">
<table  style='width:720px;' class="gridtable">

<tr>
	<th style="width:80px;">注意事项</th>
	<td>
    由于种种原因，从2015-09-22开始的统计才比较准确
	</td>
</tr>

                
<tr>
	<th style="width:80px;">查询时间</th>
	<td>
	<input type="text" id="s_time" name="s_time" value="" style="width:80px;"> 至 <input type="text" id="e_time" name="e_time" value="" style="width:80px;">
	每页 <input type="text" name="num" id="num" value="" style="width:22px;">
    统计 <select name="stat" id="stat"><option value="0">否</option><option value="1" selected>是</option></select>
    成功 <select name="success" id="success"><option value="0">所有</option><option value="1" selected>是</option><option value="2">否</option></select>
    充值类型 <select name="bill_type" id="bill_type"><option value="0">所有</option>
    <option value="1">积分</option>
    <option value="2">VIP</option>
    <option value="3">权益</option>
    <option value="99">实物</option>
    </select>
	<input type="hidden" name="page" id="page" value="">
	<button name="btn_srarch" id="btn_srarch">查询</button>
	</td>
</tr>

</table>

<span id="search_data" name="search_data"></span>

</form>

<script language="javascript">
$(document).ready(function(){
	
	function search()
	{
        if($("#bill_type").val() == 99)
        {
            alert('ecshop支付订单暂时没有查询');
            return false;
        }
        
        $("#btn_srarch").text("查询中...");
        $.ajax({
			url: "plugin.php?id=fansclub:api&ac=alipay_check&op=search&inajax=1",
			type: "post",
			dataType: "json",
			data: $("#form_search").serialize(),
			timeout: 100000,
			cache: false,
			beforeSend: function(XMLHttpRequest){}, 
			success: function(data, textStatus){
				
				if(typeof(data.message) != "undefined")
				{
					if(data.success == true)
					{
                        
                        // 日期、VIP充值订单数、VIP充值总金额、U币充值订单数、U币充值总金额、线下店订单数、线下店总金额
                        if(data.stat == 1)
                        {
                            var str_html = "<table style='width:720px;' class=\"gridtable\"><tr><th>充值类型</th><th>订单时间</th>";
                            str_html += "<th>订单数目</th><th>订单金额</th><th>状态</th></tr>";
                            for(var i = 0; i < data.list.length; i++)
                            {
                                str_html += "<tr ";
                                if(data.list[i].status == '1')
                                {
                                    str_html += "class=\"have\"";
                                    str_html += " onmouseover=\"this.style.backgroundColor='#ffff66';\" onmouseout=\"this.style.backgroundColor='#FFF8DC';\"";
                                }
                                else
                                {
                                    str_html += "onmouseover=\"this.style.backgroundColor='#ffff66';\" onmouseout=\"this.style.backgroundColor='#ffffff';\"";
                                }
                                str_html += ">";
                                str_html += "<td>"+data.list[i].type+"</td>";
                                str_html += "<td>"+data.list[i].time+"</td>";
                                str_html += "<td>"+data.list[i].num+"</td>";
                                str_html += "<td>"+data.list[i].amount+"</td>";
                                str_html += "<td>"+data.list[i].status+"</td>";
                                
                                str_html += "</tr>";
                            }
                            if(data.list.length == 0)
                            {
                                str_html += '<tr><td colspan="5" >没有数据</td></tr></table>';
                            }
                        }
                        else
                        {
                            var str_html = "<table class=\"gridtable\"><tr><th>序号</th><th>充值类型</th>";
                            str_html += "<th>订单ID</th><th>状态</th><th>订单时间</th><th>确认时间</th><th>金额</th><th>购买人</th><th>购买Email</th><th>官方订单号</th></tr>";
                            for(var i = 0; i < data.list.length; i++)
                            {
                                str_html += "<tr ";
                                if(data.list[i].status == '初始')
                                {
                                    str_html += "class=\"have\"";
                                    str_html += " onmouseover=\"this.style.backgroundColor='#ffff66';\" onmouseout=\"this.style.backgroundColor='#FFF8DC';\"";
                                }
                                else
                                {
                                    str_html += "onmouseover=\"this.style.backgroundColor='#ffff66';\" onmouseout=\"this.style.backgroundColor='#ffffff';\"";
                                }
                                str_html += ">";
                                str_html += "<td>"+data.list[i].log_id+"</td>";
                                str_html += "<td>"+data.list[i].type+"</td>";
                                str_html += "<td>"+data.list[i].orderid+"</td>";
                                str_html += "<td>"+data.list[i].status+"</td>";
                                str_html += "<td>"+data.list[i].log_time+"</td>";
                                str_html += "<td>"+data.list[i].confirm_time+"</td>";
                                str_html += "<td>"+data.list[i].amount+"</td>";
                                str_html += "<td>"+data.list[i].username+"</td>";
                                str_html += "<td>"+data.list[i].email+"</td>";
                                str_html += "<td>"+data.list[i].trade_no+"</td>";
                                str_html += "</tr>";
                            }
                            
                            if(data.list.length == 0)
                            {
                                str_html += '<tr><td colspan="10" style="width:710px;">没有数据</td></tr></table>';
                            }
                            else
                            {
                                str_html += '<tr><td colspan="11" class="right"><span id="page_span">'+data.page_html+'</span></td></tr></table>';
                            }
                        }
                        $("#search_data").html(str_html);
					}
					else
					{
						alert(data.message);
					}
					$("#btn_srarch").text("查询");
				}
			},
			complete: function(XMLHttpRequest, textStatus){},
			error: function(){
				alert("返回异常！");
			}
		});
	}
    
		$("body").delegate("#page_span a", "click", function(){
			$("#page").val($(this).text());
			search();
		});
		
		$("#btn_srarch").click(function(){
			$("#page").val(1);
			search();
		});
        
		function set_selected(id, text)
		{
			var count = $("#"+id+" option").length;
			for(var i=0;i<count;i++)  
			 {
				if($("#"+id).get(0).options[i].text == text)
				{
					$("#"+id).get(0).options[i].selected = true;
					$("#"+id).change();
					break;
				}
			}
		}
		
		$('#s_time').val('2015-09-22');
		$('#e_time').val('<?=date('Y-m-d')?>');
		$('#num').val('20');
		$('#page').val('1');
});
	</script>
</body>
</html>
<?php
function show_error($msg = '')
{
    echo "<html><body><h1>Rejected!</h1><big>".$msg."</big></body></html>";
    exit;
}

function search($arr_param)
{
    $arr_return = array('success' => FALSE, 'message' => 'init');
    
    /*
    [s_time] => 2015-09-22
    [e_time] => 2015-09-24
    [num] => 20 // 每页几条
    [stat] => 1 // 是否统计
    [success] => 1 // 是否成功
    [page] => 1 第几页
    */
    
    $s_time = strtotime($arr_param['s_time'].' '.'00:00:00');
    $e_time = strtotime($arr_param['e_time'].' '.'23:59:59');
    $num = intval($arr_param['num']) == 0 ? 20 : intval($arr_param['num']);
    $stat = intval($arr_param['stat']);
    $success = intval($arr_param['success']);
    $page = intval($arr_param['page']) == 0 ? 1 : intval($arr_param['page']);
    $bill_type = intval($arr_param['bill_type']);

    $where = "log_time >= ".$s_time." AND log_time <= ".$e_time;
    
    if($success == 1)
    {
        $where .= " AND status = '2'";
    }
    elseif($success == 2)
    {
        $where .= " AND status != '2'";
    }
    
    if($bill_type != 0)
    {
        $where .= " AND bill_type = '".$bill_type."'";
    }
    
    $pass = ($page-1) * $num;
    
    $order_by = '';
    
    if($stat == 0) // 显示详细
    {
        // $order_by = ' ORDER BY log_id DESC';
        $arr_list = C::t('#ucharge#plugin_ucharge_log')->fetch_by_where($where.$order_by, $pass, $num);
        
        $arr_list_show = array();
        for($i = 0; $i < count($arr_list); $i++)
        {
            $arr_list_show[$i]['log_id'] = $arr_list[$i]['log_id'];
            $arr_list_show[$i]['orderid'] = $arr_list[$i]['orderid'];
            $arr_list_show[$i]['status'] = $arr_list[$i]['status'];
            $arr_list_show[$i]['log_time'] = date('Y-m-d H:i:s', $arr_list[$i]['log_time']);
            $arr_list_show[$i]['confirm_time'] = date('Y-m-d H:i:s', $arr_list[$i]['confirm_time']);
            
            $arr_list_show[$i]['amount'] = $arr_list[$i]['amount']* $arr_list[$i]['price'];
            $arr_list_show[$i]['charge_type'] = $arr_list[$i]['charge_type'];
            $arr_list_show[$i]['username'] = $arr_list[$i]['username'];
            $arr_list_show[$i]['email'] = $arr_list[$i]['email'];
            $arr_list_show[$i]['trade_no'] = $arr_list[$i]['trade_no'];
            $arr_list_show[$i]['subject'] = $arr_list[$i]['subject'];
            $arr_list_show[$i]['body'] = $arr_list[$i]['body'];
            
            switch($arr_list[$i]['status'])
            {
                case 1 : $arr_list_show[$i]['status'] = '初始'; break;
                case 2 : $arr_list_show[$i]['status'] = '成功'; break;
                default : $arr_list_show[$i]['status'] = '其他'; break;
            }
            
            switch($arr_list[$i]['bill_type'])
            {
                case 1 : $arr_list_show[$i]['type'] = '积分'; break;
                case 2 : $arr_list_show[$i]['type'] = 'VIP'; break;
                case 3 : $arr_list_show[$i]['type'] = '权益'; break;
                default : $arr_list_show[$i]['type'] = '其他'; break;
            }
        }
    }
    else
    {
        $arr_list = C::t('#ucharge#plugin_ucharge_log')->stat_by_where($where);
        $arr_list_show = array();
        for($i = 0; $i < count($arr_list); $i++)
        {
            $arr_list_show[$i] = $arr_list[$i];
            switch($arr_list[$i]['bill_type'])
            {
                case 1 : $arr_list_show[$i]['type'] = '积分'; break;
                case 2 : $arr_list_show[$i]['type'] = 'VIP'; break;
                case 3 : $arr_list_show[$i]['type'] = '权益'; break;
                default : $arr_list_show[$i]['type'] = '其他'; break;
            }
            
            switch($arr_list[$i]['status'])
            {
                case 1 : $arr_list_show[$i]['status'] = '初始'; break;
                case 2 : $arr_list_show[$i]['status'] = '成功'; break;
                default : $arr_list_show[$i]['status'] = '其他'; break;
            }
        }
    }
    
    $arr_return['success'] = TRUE;
    $arr_return['message'] = '';
    $arr_return['list'] = $arr_list_show;
    $arr_return['stat'] = $stat;
    
    if($stat == 0) // 如果查详细
    {
        $totle = C::t('#ucharge#plugin_ucharge_log')->count_by_where($where);
        
        $totle_page = ceil($totle/$num);
        $page_html = '';
        $pre_point = '';
        $next_point = '';
        for($i = 1; $i <= $totle_page; $i++)
        {
            if($page == $i)
            {
                $page_html .= '['.$i.'] ';
            }
            else
            {
                if($page - $i > 3 || $i - $page > 3 )
                {
                    if($i == 1)
                    {
                        $page_html .= '[<a href="javascript: void(0);">'.($i).'</a>] ';
                    }
                    if($page - $i > 3 && $pre_point == '')
                    {
                        $pre_point = '... ';
                        $page_html .= $pre_point;
                    }
                    
                    if($i - $page > 3 && $next_point == '')
                    {
                        $next_point = '... ';
                        $page_html .= $next_point;
                    }
                    
                    if($i == $totle_page)
                    {
                        $page_html .= '[<a href="javascript: void(0);">'.($i).'</a>] ';
                    }
                }
                else
                {
                    $page_html .= '[<a href="javascript: void(0);">'.($i).'</a>] ';
                }
            }
        }
        
        $arr_return['page'] = $page;
        $arr_return['num'] = $num;
        $arr_return['page_html'] = $page_html;
    }
    
    return $arr_return;
}
?>