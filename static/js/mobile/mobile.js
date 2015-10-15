$(function(){
   //@ch 
  $("#actMores").on("click",function(){
  	 getpageShow('forum.php?mod=viewthread', '#actMores','#sectionBoxd', '#postBoxd .chtwo')
  	});
  $('#applylist_more').on("click",function(){
    getpageShow('forum.php?mod=misc&action=getactivityapplylist','#applylist_more','#folat_labelModel','#folat_labelModel label')
  });

  $('#actbox_More').on("click",function(){
    var url,num;
    var page=parseInt($(this).attr('page'));
    var totalpage=parseInt($(this).attr('totalpage'));
    url='forum.php?mod=activity'+'&page='+page+'&mobile=2';
    if(page <= totalpage){
    $.get(url,function(data){   
          $("#ch_actbox").append($(data).find("#ch_actbox li"));
          num=page+1;
          $("#actbox_More").attr("page",num);
          if(num==totalpage+1)
          {
            $("#actbox_More").html('没有更多了');
          }
      });
    }else{
       $("#actbox_More").html('没有更多了');
    }
  });

  $('#notice_More').on("click",function(){
     var num;
     var url='home.php'+window.location.search;
     var page=parseInt($(this).attr('page'));
     var totalpage=parseInt($(this).attr('totalpage'));
     if(page <= totalpage){
        $.get(url,{page:page},function(data){
            $("#notice_box").append($(data).find("#notice_box li"));
            num=page+1;
            $("#notice_More").attr("page",num);
            if(num==totalpage+1){
              $("#notice_More").html("没有更多了");
            }
        });
      }else{
        $("#notice_More").html("没有更多了");
      }
  });

  //@lazyload
  $(".lazyload").lazyload({
    placeholder : "static/image/grey.gif",
    threshold : 200 ,
    effect : "fadeIn",
    skip_invisible:false
  });
  
});

//@ch more
function getpageShow(url,id,sourceSelector,targetSelector){
   var url,id,sourceSelector,targetSelector,num;
   var page=parseInt($(id).attr('page'));
   var totalpage=parseInt($(id).attr('totalpage'));
   var tid=parseInt($(id).attr('data-id'));
   url=url+'&tid='+tid+'&page='+page+'&mobile=2';
   $.get(url,function(html){
      if(page <= totalpage){
      $(sourceSelector).append($(html).find(targetSelector));
      num=page+1;
      $(id).attr('page',num);

      }else{
       $(id).html('没有更多了');
      }
   });
  
}
