$(function(){
	$("#act_moreid").click(function(){
		$.ajax({
			type:"GET",
			url:"forum.php?mod=group&action=activity&fid=1367&page=2&ajax=1",
			dataType:"json",
			timeout: 1000,  
            cache: false,  
            success: function(data){
            	$("#ch_actbox").html();
            	var json=eval(tt);
            	$.each(json,function(index,item){
            		//循环获取数据
            		var ulHtml+='<li><a href="" class="m_actbox"><span class="act_imgshow"><img src="{$thread[thumb]}"></span><div class="act_info"><h3>{$thread[title]}</h3><p class="act_time">{$thread['starttimefrom']}&nbsp;<!--{if $thread['starttimeto']}-->至&nbsp;{$thread['starttimeto']}<!--{/if}--></p><!--{if $thread[status]}--><p class="act_btn">立即参加</p><!--{else}--><p class="act_btn">已结束</p><!--{/if}--></div></a></li>';
            		console.log(ulHtml);
            		$("#ch_actbox").append("ulHtml");
            	})
            } //成功执行方法      
		});
		return false;
	});
});