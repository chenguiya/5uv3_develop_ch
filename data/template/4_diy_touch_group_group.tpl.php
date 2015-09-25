<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('group');
0
|| checktplrefresh('./template/usportstyle/touch/group/group.htm', './template/usportstyle/touch/group/group_index.htm', 1443168714, 'diy', './data/template/4_diy_touch_group_group.tpl.php', './template/usportstyle', 'touch/group/group')
|| checktplrefresh('./template/usportstyle/touch/group/group.htm', './template/usportstyle/touch/group/group_list.htm', 1443168714, 'diy', './data/template/4_diy_touch_group_group.tpl.php', './template/usportstyle', 'touch/group/group')
|| checktplrefresh('./template/usportstyle/touch/group/group.htm', './template/usportstyle/touch/group/group_activity.htm', 1443168714, 'diy', './data/template/4_diy_touch_group_group.tpl.php', './template/usportstyle', 'touch/group/group')
|| checktplrefresh('./template/usportstyle/touch/group/group.htm', './template/usportstyle/touch/group/group_memberlist.htm', 1443168714, 'diy', './data/template/4_diy_touch_group_group.tpl.php', './template/usportstyle', 'touch/group/group')
|| checktplrefresh('./template/usportstyle/touch/group/group.htm', './template/usportstyle/touch/group/group_create.htm', 1443168714, 'diy', './data/template/4_diy_touch_group_group.tpl.php', './template/usportstyle', 'touch/group/group')
|| checktplrefresh('./template/usportstyle/touch/group/group.htm', './template/usportstyle/touch/group/group_manage.htm', 1443168714, 'diy', './data/template/4_diy_touch_group_group.tpl.php', './template/usportstyle', 'touch/group/group')
|| checktplrefresh('./template/usportstyle/touch/group/group.htm', './template/usportstyle/touch/group/group_introduce.htm', 1443168714, 'diy', './data/template/4_diy_touch_group_group.tpl.php', './template/usportstyle', 'touch/group/group')
|| checktplrefresh('./template/usportstyle/touch/group/group.htm', './template/usportstyle/touch/group/group_activity.htm', 1443168714, 'diy', './data/template/4_diy_touch_group_group.tpl.php', './template/usportstyle', 'touch/group/group')
;?><?php include template('common/header'); ?><!-- start of navbar-->
<nav class="navbar"<?php if(!empty($_G['forum']['color'])) { ?> style="background-color:<?php echo $_G['forum']['color'];?>"<?php } ?>>
    <div class="inner">
        <div class="left">
            <?php if($_GET['op'] == 'checkuser') { ?>
            <a href="javascript: history.go(-1);"><i class="iconfont icon-back"></i></a>
            <?php } else { ?>
            <a class="logo" href="<?php echo $_G['siteurl'];?>"><img src="template/usportstyle/touch/common/images/logo-03.png" alt="5u体育"></a>
            <?php } ?>
        </div>
        <?php if($_GET['op'] == 'checkuser') { ?><div class="center">会员审核 </div><?php } ?>
        <div class="right">
            <?php if($action == 'index' && $status != 2 && $status != 3) { ?><a href="forum.php?mod=post&amp;action=newthread&amp;fid=<?php echo $_G['fid'];?>"><i class="iconfont icon-edit"></i></a><?php } ?>
            <?php if($action == 'activity') { ?><a href="forum.php?mod=post&amp;action=newthread&amp;special=4&amp;fid=<?php echo $_G['fid'];?>&amp;cedit=yes"><i class="iconfont icon-edit huodong"></i></a><?php } ?>
            <?php if($action == 'memberlist' && $groupuser['level'] == 1) { ?>
            <a href="forum.php?mod=group&amp;action=manage&amp;op=checkuser&amp;fid=<?php echo $_G['fid'];?>"><i class="iconfont icon-shen"></i></a>
            <a href="javascript:;"><i class="iconfont icon-garbage"></i></a>
            <?php } ?>            
            <a href="javascript:;" class="ybtn ybtn-small cancel" style="display:none">取消</a>
            <!--<?php if($action == 'introduce') { ?><a href="javascript:;"><i class="iconfont icon-hamburger"></i></a><?php } ?>-->
            
            <?php if($_GET['op'] == 'checkuser') { ?>
            <a href="#" class="ybtn ybtn-small checkuser">全部通过</a>
            <?php } else { ?>
            <a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile&amp;mycenter=1">
                <i class="iconfont icon-me" style="position: relative;">
                    <?php if($_G['member']['newprompt']) { ?>
                    <span class="newtips"></span>
                    <?php } ?>
                </i>

            </a>
            <?php } ?>
            
        </div>
    </div>
</nav>
<!-- end of navbar-->
<!-- start of ymain -->
<div class="ymain">
    <header class="yheader">
        <img class="team-banner" src="<?php echo $_G['forum']['banner'];?>" alt=""/>
        <div class="head clearfix">
            <div class="logo"><img src="<?php echo $_G['forum']['icon'];?>" alt=""/></div>
            <div class="head-center">
                <div class="team-name"><?php echo $_G['forum']['name'];?><!-- <span class="ybadge ybadge-2">L2</span> --></div>
                <div class="team-info">
                    <span class="info">人数：<span><?php echo $_G['forum']['membernum'];?></span></span><span style="color: #b5b5b6;margin: 0 8px;font-size: 12px;">
                    |</span><span class="info">地区：<span><?php echo $_G['forum']['province_name'];?>&nbsp;<?php echo $_G['forum']['city_name'];?></span></span>
                </div>
            </div>
            <div class="head-right">
                <?php if($status == 'isgroupuser') { ?>
                <a class="ybtn ybtn-primary ybtn-small dialog" href="forum.php?mod=group&amp;action=out&amp;fid=<?php echo $_G['fid'];?>">退出</a>
                <?php } elseif($status == 5) { ?>
                <a class="ybtn ybtn-primary ybtn-small" href="javascript：void(0);">审核</a>
                <?php } else { ?>
                <a class="ybtn ybtn-primary ybtn-small dialog" href="forum.php?mod=group&amp;action=join&amp;fid=<?php echo $_G['fid'];?>">加入</a>
                <?php } ?>
            </div>
            </div>
    </header>
    <div class="tabs">
        <ul class="tab-title">
            <li class="<?php if($action == 'index' && $status != 2 && $status != 3) { ?>active<?php } ?>"><a href="forum.php?mod=group&amp;fid=<?php echo $_G['fid'];?>">首页</a></li>
            <li class="<?php if($action == 'activity') { ?>active<?php } ?>"><a href="forum.php?mod=group&amp;action=activity&amp;fid=<?php echo $_G['fid'];?>">活动</a></li>
            <li class="<?php if($action == 'memberlist' || $_GET['op'] == 'checkuser') { ?>active<?php } ?>"><a href="forum.php?mod=group&amp;action=memberlist&amp;fid=<?php echo $_G['fid'];?>">会员</a></li>
            <li class="<?php if($action == 'introduce') { ?>active<?php } ?>"><a href="forum.php?mod=group&amp;action=introduce&amp;fid=<?php echo $_G['fid'];?>">介绍</a></li>
            <!-- <li class="<?php if($action == 'introduce') { ?>active<?php } ?>"><a href="forum.php?mod=group&amp;action=introduce&amp;fid=<?php echo $_G['fid'];?>">大记事</a></li> -->
        </ul>
        <!-- tabs-body -->
        <?php if($action == 'index' && $status != 2 && $status != 3) { ?>
        <?php if($status != 2) { ?>
<div class="tab-body">
    <ul class="ylist">
<?php if($threadlist) { if(is_array($threadlist)) foreach($threadlist as $thread) { if($thread['displayorder'] == 5) { ?>
<li class="row" href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;extra=<?php echo $extra;?>">
<div class="row-item">
<span class="title" style="font-size: 16px;line-height: 20px;"><i class="iconfont icon-contacts" style="color: #5694e7;font-size: 20px;line-height: 20px;margin-right: 8px;"></i><?php echo $thread['subject'];?></span>
</div>
</li>
<?php } else { ?>
<li class="row" href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;extra=<?php echo $extra;?>">
    <div class="author-thumb">
        <img src="<?php echo avatar($thread[authorid], middle, true);?>" alt="<?php echo $thread['subject'];?>"/>
    </div>
    <div class="row-item">
        <div class="m"><?php echo $thread['author'];?></div>
        <div class="s"><?php echo $thread['dateline'];?></div>
    </div>
    <div class="row-badge-wrap">
    	<?php if($thread['displayorder']) { ?><div class="ybadge ybadge-4">置顶</div><?php } ?>
        <span class="social"><i class="iconfont icon-replay"></i><?php echo $thread['replies'];?></span>
    </div>
</li>
<div class="title"><?php echo $thread['subject'];?><?php if($thread['special'] == 1) { ?>[投票]<?php } ?></div>
<?php if($thread['img'] && $thread['special'] == 1) { ?>
<div class="images" href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;extra=<?php echo $extra;?>">
         <?php if(is_array($thread['img'])) foreach($thread['img'] as $img) { ?>           <img src="<?php echo $img;?>" alt="<?php echo $thread['subject'];?>_1"/>
           <?php } ?>
        </div>
    <?php } ?>

    <?php if($thread['video']) { ?>
<!-- <div class="images">        
        <embed src="<?php echo $thread['video'];?>" class="row_embed" width="100" height="42" allownetworking="internal" allowscriptaccess="never" quality="high" bgcolor="#ffffff" wmode="transparent" allowfullscreen="true" type="application/x-shockwave-flash"></embed>
    </div> -->
    <?php } ?>
    <?php } } } ?>
</ul>
</div>
<?php if($nextpage > 1) { ?>
<div style="padding: 0 8px">
<a id="load-more-group-index" class="ybtn loadmore" href="javascript:;">加载更多</a>
</div>
<?php } } ?>
<script id="load-more-group-index-templ" type="text/x-dot-template">
{{ for (var key in it) { }}
<li class="row" href="forum.php?mod=viewthread&amp;tid={{=it[key].tid}}&amp;extra=<?php echo $extra;?>">
<div class="author-thumb">
<img src="{{=it[key].author_avatar}}" alt="{{=it[key].subject}}"/>
</div>
<div class="row-item">
<div class="m">{{=it[key].author}}</div>
<div class="s">
<?php echo $thread['dateline'];?>
{{? it[key].special == 1}}[投票]{{?}}
</div>
</div>
<div class="row-badge-wrap">
<span class="social"><i class="iconfont icon-replay"></i>{{=it[key].recommend_add}}</span>
</div>
</li>
<div class="title">{{=it[key].subject}}</div>
{{? it[key].img && it[key].special == 1}}
<div class="images" href="forum.php?mod=viewthread&amp;tid={{=it[key].tid}}&amp;extra=<?php echo $extra;?>">
<div class="author-thumb" style="margin-right:1.1rem;"></div>
<div class="img_right">
{{~it[key].img :value:index}}
<img src="{{=value}}" alt="{{=it[key].subject}}_1"/>
{{~}}
</div>
</div>
{{?}}
{{ } }}
</script>
<script>
$(".tab-body").delegate(".row", "click", function () {
window.location.href = $(this).attr("href");
});
$(".tab-body").delegate(".imagesh", "click", function () {
window.location.href = $(this).attr("href");
});
$(".ylist .row .s").each(function () {
var me = $(this);
if (me.text().indexOf('\u524D') > 0) {
me.css("color", "#eb6100");
}
});
</script>        <?php } elseif($action == 'list') { ?>
        <div id="elecnation_group_title_line" style="margin-top:18px;"></div>
<!--<div id="elecnation_group_title" style="width:60px;"><?php if($_GET['specialtype'] == 'activity') { ?>活动区<?php } else { ?>讨论区<?php } ?></div>-->

<?php if(helper_access::check_module('group')) { if($_GET['specialtype'] == 'activity') { if($threadlist) { ?>
<section class="ch_activity">
<ul id="ch_actbox"><?php if(is_array($threadlist)) foreach($threadlist as $thread) { ?><li>
        <a href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;extra=<?php echo $extra;?>" class="m_actbox">
           <span class="act_imgshow"><img src="<?php echo $thread['thumb'];?>"></span>
           <div class="act_info">
                <h3><?php echo $thread['title'];?></h3>
                <p class="act_time"><?php echo $thread['starttimefrom'];?>&nbsp;<?php if($thread['starttimeto']) { ?>至<br>&nbsp;<?php echo $thread['starttimeto'];?><?php } ?></p>
                <?php if($thread['status']) { ?><p class="act_btn">立即参加</p><?php } else { ?><p class="act_over">已经结束</p><?php } ?>
            </div>
        </a>
    </li>		
<?php } ?>
</ul>

<?php if($nextpage > 1) { ?>
<div style="padding: 0 8px;">
<a id="load-more-group-activity"  href="javascript:;" class="ybtn loadmore">加载更多</a>
</div>
<?php } ?>
</section>
<?php } ?>
<script id="load-more-group-activity-templ" type="text/x-dot-template">
{{ for (var key in it) { }}
<li>
<a href="forum.php?mod=viewthread&amp;tid={{=it[key].tid}}&amp;extra=<?php echo $extra;?>" class="m_actbox">
<span class="act_imgshow"><img src="{{=it[key].thumb}}"></span>
<div class="act_info">
<h3>{{=it[key].title}}</h3>
<p class="act_time">{{=it[key].starttimefrom}}&nbsp;{{? it[key].starttimeto }}至&nbsp;{{=it[key].starttimeto}}{{?}}</p>
{{? it[key].status === 1 }}
<p class="act_btn">立即参加</p>
{{??}}
<p class="act_over">已结束</p>
{{?}}
</div>
</a>
</li>
{{ } }}
</script><?php } else { ?>
<div class="threadlist">
<div>
<?php if($_G['forum_threadcount']) { if(is_array($_G['forum_threadlist'])) foreach($_G['forum_threadlist'] as $key => $thread) { if(!$_G['setting']['mobile']['mobiledisplayorder3'] && $thread['displayorder'] > 0) { continue;?><?php } ?>
              	<?php if($thread['displayorder'] > 0 && !$displayorder_thread) { ?>
              <?php $displayorder_thread = 1;?>                  <?php } if($thread['moved']) { $thread[tid]=$thread[closed];?><?php } ?>

<?php if(!empty($_G['setting']['pluginhooks']['forumdisplay_thread_mobile'][$key])) echo $_G['setting']['pluginhooks']['forumdisplay_thread_mobile'][$key];?>
                  <a href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;extra=<?php echo $extra;?>" <?php echo $thread['highlight'];?> >
<div id="threadlist_li">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="18" valign="top">
                      <?php if(in_array($thread['displayorder'], array(1, 2, 3, 4))) { ?>
                      	<span><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_top.gif" width="13" height="12"></span>
                      <?php } elseif($thread['attachment'] == 2 && $_G['setting']['mobile']['mobilesimpletype'] == 0) { ?>
                          <span><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_img.gif" width="13" height="12"></span>
                      <?php } elseif($thread['digest'] > 0) { ?>
                      	<span><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_dig.gif" width="13" height="12"></span>
                      <?php } else { ?>
<span><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_tid.gif" width="13" height="12"></span>
                      <?php } ?>
                    </td>
                      <td valign="top"><?php echo $thread['subject'];?></td>
                    </tr>
                  </table>
                  
                  <div style="margin-left:18px; font-size:11px; color:#AAAAAA; line-height:16px;">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <?php if($thread['replies'] > 0) { ?>
                    <tr>
                      <td width="80%">
                      	作者 : 
<?php if($thread['author'] && !$thread['anonymous']) { ?>
                          <?php echo $thread['author'];?>
                          <?php } else { ?>
                          匿名
                          <?php } ?>
                      	 @ <?php echo $thread['dateline'];?>
                          <br />
                          回复 : 
                          <?php if($thread['lastposter'] && !$thread['anonymous']) { ?>
                          <?php echo $thread['lastposter'];?> @ <?php echo $thread['lastpost'];?>
                          <?php } else { ?>
                          	匿名 @ <?php echo $thread['lastpost'];?>
                        	<?php } ?>
                          
                      </td>
                      <td align="right">
                      	<?php echo $thread['allreplies'];?> 回复<br />
<?php echo $thread['views'];?> 查看
                      </td>
                    </tr>
  <?php } else { ?>
                    <tr>
                      <td width="80%">
                      	作者 :
                          <?php if($thread['author'] && !$thread['anonymous']) { ?>
                          <?php echo $thread['author'];?>
                          <?php } else { ?>
                          匿名
                          <?php } ?>
                      	 @ <?php echo $thread['dateline'];?>
                      </td>
                      <td align="right">
                      	<?php echo $thread['allreplies'];?> 回复<br />
<?php echo $thread['views'];?> 查看
                      </td>
                    </tr>
                      <?php } ?>
                  </table>
                  </div>
                  </div>
</a>

              <?php } ?>
          <?php } else { ?>
<div id="elecnation_noinfo">本版块或指定的范围内尚无主题。</div>
<?php } ?>
</div>
<?php echo $multipage;?>
<div id="elecnation_multi_footer"></div>

</div>
<?php } } ?>


<?php if(!empty($_G['setting']['pluginhooks']['forumdisplay_bottom_mobile'])) echo $_G['setting']['pluginhooks']['forumdisplay_bottom_mobile'];?>
<div class="pullrefresh" style="display:none;"></div>
        <?php } elseif($action == 'activity') { ?>
        <?php if($threadlist) { ?>
<section class="ch_activity">
<ul id="ch_actbox"><?php if(is_array($threadlist)) foreach($threadlist as $thread) { ?><li>
        <a href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;extra=<?php echo $extra;?>" class="m_actbox">
           <span class="act_imgshow"><img src="<?php echo $thread['thumb'];?>"></span>
           <div class="act_info">
                <h3><?php echo $thread['title'];?></h3>
                <p class="act_time"><?php echo $thread['starttimefrom'];?>&nbsp;<?php if($thread['starttimeto']) { ?>至<br>&nbsp;<?php echo $thread['starttimeto'];?><?php } ?></p>
                <?php if($thread['status']) { ?><p class="act_btn">立即参加</p><?php } else { ?><p class="act_over">已经结束</p><?php } ?>
            </div>
        </a>
    </li>		
<?php } ?>
</ul>

<?php if($nextpage > 1) { ?>
<div style="padding: 0 8px;">
<a id="load-more-group-activity"  href="javascript:;" class="ybtn loadmore">加载更多</a>
</div>
<?php } ?>
</section>
<?php } ?>
<script id="load-more-group-activity-templ" type="text/x-dot-template">
{{ for (var key in it) { }}
<li>
<a href="forum.php?mod=viewthread&amp;tid={{=it[key].tid}}&amp;extra=<?php echo $extra;?>" class="m_actbox">
<span class="act_imgshow"><img src="{{=it[key].thumb}}"></span>
<div class="act_info">
<h3>{{=it[key].title}}</h3>
<p class="act_time">{{=it[key].starttimefrom}}&nbsp;{{? it[key].starttimeto }}至&nbsp;{{=it[key].starttimeto}}{{?}}</p>
{{? it[key].status === 1 }}
<p class="act_btn">立即参加</p>
{{??}}
<p class="act_over">已结束</p>
{{?}}
</div>
</a>
</li>
{{ } }}
</script>        <?php } elseif($action == 'memberlist') { ?>
        <div class="tab-body">
<?php if($alluserlist) { ?>
<ul class="author-thumbs clearfix"><?php if(is_array($alluserlist)) foreach($alluserlist as $user) { ?><li>
<div class="author-thumb">
<a class="author-thumb-img" href="home.php?mod=space&amp;uid=<?php echo $user['uid'];?>&amp;do=thread&amp;from=space"
   data-url="home.php?mod=space&amp;uid=<?php echo $user['uid'];?>&amp;do=thread&amp;from=space">
<img src="<?php echo $user['avatar'];?>" alt="<?php echo $user['username'];?>"/>
</a>
<a href="forum.php?mod=group&amp;action=manage&amp;op=manageuser&amp;fid=<?php echo $_G['fid'];?>&amp;muid=<?php echo $user['uid'];?>&amp;mobile=2" class="iconfont icon-del dialog"></a>
</div>
<div class="name"><a href="home.php?mod=space&amp;uid=<?php echo $user['uid'];?>&amp;do=thread&amp;from=space"><?php echo $user['username'];?></a></div>
</li>
<?php } ?>
</ul>
<?php } ?>	
</div>
<?php if($nextpage > 1) { ?>
<div style="padding: 0 8px">
<a id="load-more-group-memberlist" href="<?php echo $multipage_more;?>" class="ybtn loadmore">加载更多</a>
</div>
<?php } ?>
<script id="load-more-group-memberlist-templ" type="text/x-dot-template">
{{ for (var key in it) { }}
<li>
<div class="author-thumb">
<a class="author-thumb-img" href="home.php?mod=space&amp;uid=<?php echo $user['uid'];?>&amp;do=thread&amp;from=space"
   data-url="home.php?mod=space&amp;uid=<?php echo $user['uid'];?>&amp;do=thread&amp;from=space">
<img src="{{=it[key].avatar}}" alt="{{=it[key].username}}"/>
</a>
<a href="forum.php?mod=group&amp;action=manage&amp;op=manageuser&amp;fid=<?php echo $_G['fid'];?>&amp;muid=<?php echo $user['uid'];?>&amp;mobile=2" class="iconfont icon-del dialog"></a>
</div>
<div class="name">{{=it[key].username}}</div>
</li>
{{ } }}
</script>
        <?php } elseif($action == 'create') { ?>
        <div style="padding:18px; line-height:22px;" id="main_messaqge">
<form method="post" autocomplete="off" name="groupform" id="groupform" onsubmit="checkCategory();ajaxpost('groupform', 'returnmessage4', 'returnmessage4', 'onerror');return false;" action="forum.php?mod=group&amp;action=create">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<input type="hidden" name="referer" value="<?php echo dreferer(); ?>" />
<input type="hidden" name="handlekey" value="creategroup" />
<table cellspacing="0" cellpadding="0" summary="创建<?php echo $_G['setting']['navs']['3']['navname'];?>">
<tbody>
<tr>
<th colspan="3">
  <style type="text/css">
#returnmessage4 { display: none; color: <?php echo NOTICETEXT;?>; font-weight: bold; }
#returnmessage4.onerror { display: block; }
</style>
  <p id="returnmessage4"></p>					    </th>
</tr>
<tr>
  <th colspan="3"><?php echo $_G['setting']['navs']['3']['navname'];?>名称:</th>
  </tr>
<tr>
  <th colspan="3">
                      <input type="text" name="name" id="name" size="36" tabindex="1" value="" autocomplete="off" onBlur="checkgroupname()" tabindex="1" />
                      </th>
  </tr>
<tr>
  <th colspan="3">所属分类:</th>
  </tr>
<tr>
  <th colspan="3">
                      <select name="parentid" tabindex="2" onchange="ajaxget('forum.php?mod=ajax&action=secondgroup&fupid='+ this.value, 'secondgroup');">
                      <option value="0">请选择</option>
                      <?php echo $groupselect['first'];?>
                      </select>
                      <em id="secondgroup"></em>
                      <span id="groupcategorycheck"></span>
                      </th>
  </tr>
<tr>
  <th colspan="3"><?php echo $_G['setting']['navs']['3']['navname'];?>简介:</th>
  </tr>
<tr>
  <th colspan="3">
                      <script type="text/javascript">
var allowbbcode = allowimgcode = parsetype = 1;
var allowhtml = forumallowhtml = allowsmilies = 0;
</script>
<script src="<?php echo $_G['setting']['jspath'];?>bbcode.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<div>

<div>
<textarea id="descriptionmessage" name="descriptionnew" tabindex="3" rows="8"></textarea>
</div>
</div>
                      </th>
  </tr>
<tr>
  <th colspan="3">浏览权限:</th>
  </tr>
<tr>
  <th><label><input type="radio" name="gviewperm" tabindex="4" value="1" checked="checked" />所有人</label></th>
  <th><label style="width:33%"><input type="radio" name="gviewperm" value="0" />仅成员</label></th>
  <th>&nbsp;</th>
      </tr>
<tr>
  <th colspan="3">加入方式:</th>
  </tr>
<tr>
  <th><label><input type="radio" name="jointype" tabindex="5" value="0" checked="checked" />自由加入</label></th>
  <th><label><input type="radio" name="jointype" value="2" />审核加入</label></th>
  <th><label><input type="radio" name="jointype" value="1" />邀请加入</label></th>
      </tr>
<tr>
  <th colspan="3">
                      <div>
                      <input type="hidden" name="createsubmit" value="true"><button type="submit" tabindex="6"><strong>创建</strong></button>
<?php if($_G['group']['buildgroupcredits']) { ?>&nbsp;&nbsp;&nbsp;(<strong>创建<?php echo $_G['setting']['navs']['3']['navname'];?>需要消耗 <?php echo $_G['group']['buildgroupcredits'];?> <?php echo $_G['setting']['extcredits'][$creditstransextra]['unit'];?><?php echo $_G['setting']['extcredits'][$creditstransextra]['title'];?></strong>)<?php } ?>
                      </div>
                      </th>
  </tr>
</tbody>
</table>
</form>
</div>
<script src="<?php echo $_G['setting']['jspath'];?>common.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script type="text/javascript">
function checkgroupname() {
var groupname = trim($('name').value);
ajaxget('forum.php?mod=ajax&forumcheck=1&infloat=creategroup&handlekey=creategroup&action=checkgroupname&groupname=' + (BROWSER.ie && document.charset == 'utf-8' ? encodeURIComponent(groupname) : groupname), 'groupnamecheck');
}
function checkCategory(){
var groupcategory = trim($('fup').value);
if(groupcategory == ''){
$('groupcategorycheck').innerHTML = '请选择<?php echo $_G['setting']['navs']['3']['navname'];?>分类';
return false;
} else {
$('groupcategorycheck').innerHTML = '';
}
}
<?php if($_GET['fupid']) { ?>
ajaxget('forum.php?mod=ajax&action=secondgroup&fupid=<?php echo $_GET['fupid'];?><?php if($_GET['groupid']) { ?>&groupid=<?php echo $_GET['groupid'];?><?php } ?>', 'secondgroup');
<?php } ?>
if($('name')) {
$('name').focus();
}
</script>        <?php } elseif($action == 'manage') { ?>
        <?php if($_GET['op'] == 'manageuser') { ?>
<div class="tab-body">
<?php if($userlist) { ?>
<ul class="author-thumbs clearfix"><?php if(is_array($userlist)) foreach($userlist as $user) { ?><li>
<a href="forum.php?mod=group&amp;action=manage&amp;op=manageuser&amp;fid=<?php echo $_G['fid'];?>&amp;muid=<?php echo $user['uid'];?>">
<div class="author-thumb">
<img src="<?php echo avatar($user[uid], middle, true);?>" />
<?php if($user['level'] != 1) { ?>
<a href="forum.php?mod=group&amp;action=manage&amp;op=manageuser&amp;fid=<?php echo $_G['fid'];?>&amp;muid=<?php echo $user['uid'];?>&amp;mobile=2" class="iconfont icon-del active dialog"></a>
<?php } ?>
</div>
<div class="name"><?php echo $user['username'];?></div>
</a>
</li>
<?php } ?>
</ul>
<?php } ?>	
</div>
<?php if($nextpage > 1) { ?>
<div style="padding: 0 8px;">
<a id="load-more-group-manage" href="<?php echo $multipage_more;?>" class="ybtn loadmore">加载更多</a>
</div>
<?php } } elseif($_GET['op'] == 'checkuser') { ?>
<div class="user_check">
<ul><?php if(is_array($checkusers)) foreach($checkusers as $user) { ?><li class="ucheck_list">
<div class="ucl_img"><a href=""><img src="<?php echo avatar($user[uid], middle, true);?>" alt=""></a></div>
<div class="ucl_user"><a href=""><?php echo $user['username'];?></a></div>
<div class="ucl_apply"><span><?php echo $user['joindateline'];?></span></div>
<a href="forum.php?mod=group&amp;action=manage&amp;op=checkuser&amp;fid=<?php echo $_G['fid'];?>&amp;uid=<?php echo $user['uid'];?>&amp;checktype=1" class="ucl_btn uclb_y dialog">通过</a>
<a href="forum.php?mod=group&amp;action=manage&amp;op=checkuser&amp;fid=<?php echo $_G['fid'];?>&amp;uid=<?php echo $user['uid'];?>&amp;checktype=2" class="ucl_btn uclb_n dialog">忽略</a>
</li>
<?php } ?>		
</ul>
</div>
<?php } ?>
<script id="load-more-group-manage-templ" type="text/x-dot-template">
{{ for (var key in it) { }}
<li>
<a href="forum.php?mod=group&amp;action=manage&amp;op=manageuser&amp;fid={{=it[key].fid}}&amp;muid={{=it[key].uid}}">
<div class="author-thumb">
<img src="{{=it[key].avatar}}" />
<a href="forum.php?mod=group&amp;action=manage&amp;op=manageuser&amp;fid={{=it[key].fid}}&amp;muid={{=it[key].uid}}&amp;mobile=2" class="iconfont icon-del active dialog"></a>
</div>
<div class="name">{{=it[key].username}}</div>
</a>
</li>
{{ } }}
</script>        <?php } elseif($action == 'introduce') { ?>
        <div>
<div class="gp_intro">
<p class="p1">球迷会地址：</p>
<p class="p2"><?php echo $_G['forum']['province_name'];?>&nbsp;<?php echo $_G['forum']['city_name'];?></p>
</div>
<div class="gp_hr"></div>
<div class="gp_intro">
<p class="p1">球迷会简介：</p>
<p class="p2"><?php if($_G['forum']['description']) { ?><?php echo $_G['forum']['description'];?><?php } else { ?>暂无简介!<?php } ?></p>
</div>
<div class="gp_intro">
<p class="p1">球迷会二维码：</p>
<p class="p2"><img src="data/attachment/temp/qrcode_<?php echo $_G['uid'];?>.png" width="170" height="170" /></p>
</div>

</div>        <?php } ?>
    </div>
</div>
<!-- end of ymain --><?php include template('common/footer'); ?>