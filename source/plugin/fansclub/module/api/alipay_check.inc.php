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
<table class="gridtable">
<!--
<tr>
	<th style="width:80px;">注意事项</th>
	<td>
    由于种种原因，从2015-09-17开始的统计才比较准确
	</td>
</tr>
-->
<tr>
	<th style="width:80px;">查询时间</th>
	<td>
	<input type="text" id="s_time" name="s_time" value="" style="width:80px;"> 至 <input type="text" id="e_time" name="e_time" value="" style="width:80px;">
	每页 <input type="text" name="num" id="num" value="" style="width:22px;">
    统计 <select name="stat" id="stat"><option value="0">否</option><option value="1">是</option></select>
    成功 <select name="success" id="success"><option value="0">所有</option><option value="1">是</option><option value="2">否</option></select>
	<input type="hidden" name="page" id="page" value="">
	<button name="btn_srarch" id="btn_srarch">查询</button>
    <button name="btn_test" id="btn_test">测试</button>
	</td>
</tr>

</table>

<span id="search_data" name="search_data"></span>

</form>

<script language="javascript">
$(document).ready(function(){
	
	function search()
	{
        alert($("#form_search").serialize());
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
						var str_html = "<table class=\"gridtable\"><tr><th style=\"width:20px;\"><input type='checkbox' id='chkbox_all' /></th><th>类型</th>";
						str_html += "<th>序号</th><th>文章ID</th><th>标题</th><th>关键字</th><th>作者</th><th>添加时间</th><th>已处理</th><th>录入时间</th><th>录入人</th><th>录入版块</th></tr>";
						for(var i = 0; i < data.list.length; i++)
						{
							$("#search_type").val(data.list[i].type);
							if(data.list[i].type == '新闻')
							{
								$("#type").val('1');
							}
							else if(data.list[i].type == '图片')
							{
								$("#type").val('2');
							}
							else if(data.list[i].type == '视频')
							{
								$("#type").val('3');
							}
							
							str_html += "<tr ";
							if(data.list[i].have_log == '是')
							{
								str_html += "class=\"have\"";
							}
							else
							{
								str_html += "onmouseover=\"this.style.backgroundColor='#ffff66';\" onmouseout=\"this.style.backgroundColor='#ffffff';\"";
							}
							str_html += ">";
							str_html += "<td align='center'><input ";
							str_html += (data.list[i].have_log == '是') ? 'disabled ' : '';
							str_html += "type='checkbox' value='"+data.list[i].id+"' /></td>";
							str_html += "<td>"+data.list[i].type+"</td>";
							str_html += "<td>"+data.list[i].lid+"</td>";
							str_html += "<td>"+data.list[i].id+"</td>";
							str_html += "<td><a href='"+data.list[i].url+"' target='_blank'>"+data.list[i].title+"</a></td>";
							str_html += "<td>"+data.list[i].keywords+"</td>";
							str_html += "<td>"+data.list[i].username+"</td>";
							str_html += "<td>"+data.list[i].inputtime+"</td>";
							str_html += "<td>"+data.list[i].have_log+"</td>";
							str_html += "<td>"+data.list[i].log_time+"</td>";
							str_html += "<td>"+data.list[i].log_member+"</td>";
							str_html += "<td>"+data.list[i].forum_info+"</td>";
							str_html += "</tr>";
						}
						str_html += '<tr><th><input type="checkbox" id="chkbox_all2" /></th><td colspan="11" class="right"><button class="left" name="btn_input" id="btn_input">确定录入</button><span id="page_span">'+data.page_html+'</span></td></tr></table>';
						$("#search_data").html(str_html);
					}
					else
					{
						alert(data.message);
					}
					$("#btn_srarch").text("搜索文章");
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
        
        $("#btn_test").click(function(){
			$.ajax({
                url: "http://www.5usport.com/plugin.php?id=fansclub:api&ac=passport&op=register&email=czt@163.com&password=123456aa&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&sign=8ead54dadc9b826e4ca856cd22f1b6a6",
                type: "get",
                dataType: "json",
                data: '',
                timeout: 100000,
                cache: false,
                beforeSend: function(XMLHttpRequest){}, 
                success: function(data, textStatus){
                            alert(data.message);
                },
                complete: function(XMLHttpRequest, textStatus){},
                error: function(){
                    alert("返回异常！");
                }
            });
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
		
		$('#s_time').val('2015-09-17');
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
    echo "<pre>";
    print_r($arr_param);
    exit;
    $cat_id1 = $arr_param['cat_id1'] + 0;
    $cat_id2 = $arr_param['cat_id2'] + 0;
    $s_time = strtotime($arr_param['s_time'].' '.'00:00:00');
    $e_time = strtotime($arr_param['e_time'].' '.'23:59:59');
    $page = $arr_param['page'] + 0 == 0 ? 1 : $arr_param['page'] + 0;
    $num = $arr_param['num'] + 0 == 0 ? 20 : $arr_param['num'] + 0;
    $text_search = trim($arr_param['text_search']);
    $key_search = trim($arr_param['key_search']);
    
    $chk_pwd = $this->_check_password($arr_param);
    
    if(!$chk_pwd['success'])
    {
        $arr_return['message'] = $chk_pwd['message'];
    }
    elseif($cat_id1 == 0)
    {
        $arr_return['message'] = '没有选择一级分类';
    }
    elseif($cat_id2 == 0)
    {
        $arr_return['message'] = '没有选择二级分类';
    }
    else
    {
        $obj_cat = $this->category_model->getCategory($cat_id2);
        if(is_object($obj_cat))
        {
            $modelid = $obj_cat->modelid + 0;
            if($modelid == 1) // 文章类型
            {
                $data = 'id, title, FROM_UNIXTIME(inputtime) as inputtime, username, url, keywords';
                $from = 'news';
                $where = "status = '99' AND catid = '".$cat_id2."' AND inputtime >= ".$s_time." AND inputtime <= ".$e_time;
                if($text_search != '')
                {
                    $where .= " AND title like '%".$text_search."%'";
                }
                if($key_search != '')
                {
                    $where .= " AND keywords like '%".$key_search."%'";
                }
                $pass = ($page-1) * $num;
                $order = 'id DESC';
                $arr_list = $this->news_model->getNewsList($data, $from, $where, $pass, $num, $order);
                for($i = 0; $i < count($arr_list); $i++)
                {
                    $_tmp = (array)$arr_list[$i];
                    $_tmp['type'] = '新闻';
                    $_tmp['lid'] = $i + $pass + 1;
                    
                    $data2 = array();
                    $data2['article_id'] = $_tmp['id'];
                    $data2['data_from'] = 'phpcms';
                    $data2['search_type'] = $_tmp['type'];
                    $have_log = $this->tov3_model->isLogExist($data2);
                    if($have_log)
                    {
                        $arr_log = $this->tov3_model->getLog($data2);
                        $_tmp['have_log'] = '是';
                        $_tmp['log_time'] = date('Y-m-d H:i:s', $arr_log[0]['log_time']);
                        $_tmp['log_member'] = $arr_log[0]['log_member'];
                        $_tmp['forum_info'] = $arr_log[0]['forum_info'];
                    }
                    else
                    {
                        $_tmp['have_log'] = '否';
                        $_tmp['log_time'] = '';
                        $_tmp['log_member'] = '';
                        $_tmp['forum_info'] = '';
                    }
                    
                    $arr_list[$i] = $_tmp;
                }
                $arr_return['success'] = TRUE;
                $arr_return['message'] = '';
                $arr_return['list'] = $arr_list;
                
                $totle = $this->news_model->getWhereNewsTotal($where);
                
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
                                $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                            }
                            if($page - $i > 3 && $pre_point == '')
                            {
                                $pre_point = '...';
                                $page_html .= $pre_point;
                            }
                            
                            if($i - $page > 3 && $next_point == '')
                            {
                                $next_point = '...';
                                $page_html .= $next_point;
                            }
                            
                            if($i == $totle_page)
                            {
                                $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                            }
                        }
                        else
                        {
                            $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                        }
                        
                    }
                }
                $arr_return['page'] = $page;
                $arr_return['num'] = $num;
                $arr_return['page_html'] = $page_html;
                
            }
            elseif($modelid == 3) // 图片类型
            {
                $data = 'id, title, FROM_UNIXTIME(inputtime) as inputtime, username, url, keywords';
                $from = 'picture';
                $where = "status = '99' AND catid = '".$cat_id2."' AND inputtime >= ".$s_time." AND inputtime <= ".$e_time;
                if($text_search != '')
                {
                    $where .= " AND title like '%".$text_search."%'";
                }
                if($key_search != '')
                {
                    $where .= " AND keywords like '%".$key_search."%'";
                }
                $pass = ($page-1) * $num;
                $order = 'id DESC';
                $arr_list = $this->picture_model->getPictureList($data, $from, $where, $pass, $num, $order);
                for($i = 0; $i < count($arr_list); $i++)
                {
                    $_tmp = (array)$arr_list[$i];
                    $_tmp['type'] = '图片';
                    $_tmp['lid'] = $i + $pass + 1;
                    
                    $data2 = array();
                    $data2['article_id'] = $_tmp['id'];
                    $data2['data_from'] = 'phpcms';
                    $data2['search_type'] = $_tmp['type'];
                    $have_log = $this->tov3_model->isLogExist($data2);
                    if($have_log)
                    {
                        $arr_log = $this->tov3_model->getLog($data2);
                        $_tmp['have_log'] = '是';
                        $_tmp['log_time'] = date('Y-m-d H:i:s', $arr_log[0]['log_time']);
                        $_tmp['log_member'] = $arr_log[0]['log_member'];
                        $_tmp['forum_info'] = $arr_log[0]['forum_info'];
                    }
                    else
                    {
                        $_tmp['have_log'] = '否';
                        $_tmp['log_time'] = '';
                        $_tmp['log_member'] = '';
                        $_tmp['forum_info'] = '';
                    }
                    
                    $arr_list[$i] = $_tmp;
                }
                $arr_return['success'] = TRUE;
                $arr_return['message'] = '';
                $arr_return['list'] = $arr_list;
                $totle = $this->picture_model->getWherePictureTotal($where);
                
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
                                $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                            }
                            if($page - $i > 3 && $pre_point == '')
                            {
                                $pre_point = '...';
                                $page_html .= $pre_point;
                            }
                            
                            if($i - $page > 3 && $next_point == '')
                            {
                                $next_point = '...';
                                $page_html .= $next_point;
                            }
                            
                            if($i == $totle_page)
                            {
                                $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                            }
                        }
                        else
                        {
                            $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                        }
                        
                    }
                }
                $arr_return['page'] = $page;
                $arr_return['num'] = $num;
                $arr_return['page_html'] = $page_html;
                
            }
            elseif($modelid == 11) // 视频类型
            {
                $data = 'id, title, FROM_UNIXTIME(inputtime) as inputtime, username, url, keywords';
                $from = 'video';
                $where = "status = '99' AND catid = '".$cat_id2."' AND inputtime >= ".$s_time." AND inputtime <= ".$e_time;
                if($text_search != '')
                {
                    $where .= " AND title like '%".$text_search."%'";
                }
                if($key_search != '')
                {
                    $where .= " AND keywords like '%".$key_search."%'";
                }
                $pass = ($page-1) * $num;
                $order = 'id DESC';
                $arr_list = $this->video_model->getVideoList($data, $from, $where, $pass, $num, $order);
                for($i = 0; $i < count($arr_list); $i++)
                {
                    $_tmp = (array)$arr_list[$i];
                    $_tmp['type'] = '视频';
                    $_tmp['lid'] = $i + $pass + 1;
                    $data2 = array();
                    $data2['article_id'] = $_tmp['id'];
                    $data2['data_from'] = 'phpcms';
                    $data2['search_type'] = $_tmp['type'];
                    $have_log = $this->tov3_model->isLogExist($data2);
                    if($have_log)
                    {
                        $arr_log = $this->tov3_model->getLog($data2);
                        $_tmp['have_log'] = '是';
                        $_tmp['log_time'] = date('Y-m-d H:i:s', $arr_log[0]['log_time']);
                        $_tmp['log_member'] = $arr_log[0]['log_member'];
                        $_tmp['forum_info'] = $arr_log[0]['forum_info'];
                    }
                    else
                    {
                        $_tmp['have_log'] = '否';
                        $_tmp['log_time'] = '';
                        $_tmp['log_member'] = '';
                        $_tmp['forum_info'] = '';
                    }
                    
                    $arr_list[$i] = $_tmp;
                }
                $arr_return['success'] = TRUE;
                $arr_return['message'] = '';
                $arr_return['list'] = $arr_list;
                $totle = $this->video_model->getWhereVideoTotal($where);
                
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
                                $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                            }
                            if($page - $i > 3 && $pre_point == '')
                            {
                                $pre_point = '...';
                                $page_html .= $pre_point;
                            }
                            
                            if($i - $page > 3 && $next_point == '')
                            {
                                $next_point = '...';
                                $page_html .= $next_point;
                            }
                            
                            if($i == $totle_page)
                            {
                                $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                            }
                        }
                        else
                        {
                            $page_html .= '[<a href="javascript:return void(0);">'.($i).'</a>] ';
                        }
                        
                    }
                }
                $arr_return['page'] = $page;
                $arr_return['num'] = $num;
                $arr_return['page_html'] = $page_html;
            }
            else
            {
                $arr_return['message'] = '意外的类型';
            }
        }
        else
        {
            $arr_return['message'] = '没有找到分类信息';
        }
        
    }
    
    return $arr_return;
}
?>