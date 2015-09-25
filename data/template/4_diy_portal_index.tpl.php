<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('index');
block_get('147,126,130,129');?><?php include template('common/header'); ?><style id="diy_style" type="text/css"></style>
<div class="wp">
<!--[diy=diy1]--><div id="diy1" class="area"></div><!--[/diy]-->
</div>

<div class="blank10"></div>
<div class="ad_banner" id="inxch1">
     <!-- 108821：首页-顶部通栏 类型：固定广告位 尺寸：1000x0-->
</div>
<div class="site_mian">
     <div class="slide_wrap cl">
          <!--大轮播 start-->
          <?php if(!empty($_G['setting']['pluginhooks']['index_focus'])) echo $_G['setting']['pluginhooks']['index_focus'];?>
          <!--大轮播 end-->
          <!--官方发布 start-->
          <?php if(!empty($_G['setting']['pluginhooks']['index_official'])) echo $_G['setting']['pluginhooks']['index_official'];?>
          <!--官方发布 end-->
     </div>
     <!--推荐球迷会 start-->
     <?php if(!empty($_G['setting']['pluginhooks']['index_fansclub_recommend'])) echo $_G['setting']['pluginhooks']['index_fansclub_recommend'];?>
     <!--推荐球迷会 end-->
     <!--共部分 start-->
     <div class="indexCon cl" style="margin-top: 10px;">
          <!--热门话题start-->
          <?php if(!empty($_G['setting']['pluginhooks']['index_hot_thread'])) echo $_G['setting']['pluginhooks']['index_hot_thread'];?>
          <!--热门话题end-->
          <div class="modech_right">
               <!--热门活动 start-->
               <?php if(!empty($_G['setting']['pluginhooks']['index_hot_activity'])) echo $_G['setting']['pluginhooks']['index_hot_activity'];?>             
               <!--热门活动 end-->
               <div class="ad_boxs" id="inxch3">
                    <!-- 108823：首页-右侧button1 类型：固定广告位 尺寸：300x0-->
               </div>
               <div class="blank15"></div>
               <!--神回复 start-->
               <?php if(!empty($_G['setting']['pluginhooks']['index_godreply'])) echo $_G['setting']['pluginhooks']['index_godreply'];?>
               <!--神回复 end-->
               <!--认证用户 start-->
               <?php if(!empty($_G['setting']['pluginhooks']['index_recommend_user'])) echo $_G['setting']['pluginhooks']['index_recommend_user'];?>
               <!--认证用户 end-->
               <div class="ad_boxs" id="inxch4">
                    <!-- 108825：首页-右侧button2 类型：固定广告位 尺寸：300x0-->
               </div>
               <div class="blank15"></div>
               <!--活跃粉丝 start-->
               <?php if(!empty($_G['setting']['pluginhooks']['index_active_fans'])) echo $_G['setting']['pluginhooks']['index_active_fans'];?>               
               <!--活跃粉丝 end-->
               <!--专题 start-->
               <!--[diy=special]--><div id="special" class="area"><div id="frameM8EYT4" class="frame move-span cl frame-1"><div id="frameM8EYT4_left" class="column frame-1-c"><div id="frameM8EYT4_left_temp" class="move-span temp"></div><?php block_display('147');?></div></div></div><!--[/diy]-->               
               <!--专题 end-->
          </div>
     </div>
     <!--共部分 end-->
</div>
<div class="blank15"></div>
<div class="ad_banner" id="inxch2">
     <!-- 108827：首页-底部通栏 类型：固定广告位 尺寸：1000x0-->

</div>
<div class="parter_bg">
     <!--合作伙伴 start-->
      <div class="parter_nav">
           <div class="parter_Tag">
                 <span class="active">合作伙伴</span>
                  <span class="">友情链接</span>
            </div>
            <div class="parter_link clearfix">
            <ul style="display: block;">
            <!--[diy=diylink1]--><div id="diylink1" class="area"><div id="frame5osP94" class="frame move-span cl frame-1"><div id="frame5osP94_left" class="column frame-1-c"><div id="frame5osP94_left_temp" class="move-span temp"></div><?php block_display('126');?></div></div></div><!--[/diy]-->
            </ul>
            <ul style="display: none;">
            <!--[diy=diylink3]--><div id="diylink3" class="area"><div id="frame18es6o" class="frame move-span cl frame-1"><div id="frame18es6o_left" class="column frame-1-c"><div id="frame18es6o_left_temp" class="move-span temp"></div><?php block_display('130');?></div></div></div><!--[/diy]-->
            </ul>          
            </div>
      </div>
      <!--合作伙伴 end-->
      <!--媒体分站 start-->
      <!--[diy=diylink2]--><div id="diylink2" class="area"><div id="framecJ78vd" class="frame move-span cl frame-1"><div id="framecJ78vd_left" class="column frame-1-c"><div id="framecJ78vd_left_temp" class="move-span temp"></div><?php block_display('129');?></div></div></div><!--[/diy]-->
      <!--媒体分站     end-->
</div>
<script type="text/javascript">
_acK({aid:108821,format:0,mode:1,gid:1,destid:"inxch1",serverbaseurl:"afp.csbew.com/"});
</script>
<script type="text/javascript">
_acK({aid:108827,format:0,mode:1,gid:1,destid:"inxch2",serverbaseurl:"afp.csbew.com/"});
</script>
<script type="text/javascript">
_acK({aid:108823,format:0,mode:1,gid:1,destid:"inxch3",serverbaseurl:"afp.csbew.com/"});
</script>
<script type="text/javascript">
_acK({aid:108825,format:0,mode:1,gid:1,destid:"inxch4",serverbaseurl:"afp.csbew.com/"});
</script><?php include template('common/footer'); ?>