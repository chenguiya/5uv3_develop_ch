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

  //share
  $(function($) {
            var bdshare_content = '';
            var bdshare_desc = '';
            var bdshare_pic = '';
            var bdshare_url = '';
            var share_thread = function() {
                $('a.share_thread').off('click.share_thread');
                $('a.share_thread').on('click.share_thread', function() {
                    bdshare_url = location.href;
                    bdshare_pic = $('.message img:first').attr('src');
                    
                    if (typeof bdshare_pic == 'string' && bdshare_pic.search(/http/i) == -1) {
                        bdshare_pic = location.hostname + '/' + bdshare_pic;
                    }
                    bdshare_content = $('#elecnation_post_title').attr('data-title');
                    bdshare_desc = '';
                });
            };
            var baiduShare = function() {
                window._bd_share_config = {
                    common: {
                        bdText: '',
                        bdDesc: '',
                        bdUrl: '',
                        bdPic: '',
                        bdSign: '',
                        bdMini: '',
                        bdMiniList: '',
                        onBeforeClick: function(cmd, config) {
                            config.bdText = bdshare_content;
                            config.bdPic = bdshare_pic;
                            config.bdUrl = bdshare_url || location.href;
                            config.bdDesc = bdshare_desc;
                            return config;
                        },
                        bdPopupOffsetLeft: '',
                        bdPopupOffsetTop: '',
                        bdCustomStyle: ''
                    },
                    share: [
                        {tag: 'share_thread', bdSize:32}
                    ]
                };
                with (document)0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion=' + ~(-new Date() / 36e5)];
            };
            
            share_thread();
            baiduShare();
        }($));

  
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
