<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); if($_G['uid']) { ?>
    	<div id="set_menu" class="p_pop" style="display: none;">
<a href="home.php?mod=spacecp">修改信息</a>
<a href="home.php?mod=spacecp&amp;ac=credit&amp;op=buy">积分充值</a>
<a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo $_G['formhash'];?>">退出</a>
<i></i>
       </div>
   
   <div id="profile_menu" class="p_pop" style="display: none;">
<a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=thread&amp;from=space"><?php echo $_G['username'];?></a>
<a href="home.php?mod=spacecp&amp;ac=credit&amp;op=buy">积分充值</a>
<a href="home.php?mod=spacecp">设置</a>
<a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo $_G['formhash'];?>">退出</a>
<i></i>
       </div>
   
<div id="fansclub_menu" class="p_pop" style="display: none;">
<a href="plugin.php?id=userspace:mygroup&amp;uid=<?php echo $_G['uid'];?>&amp;do=club&amp;from=space">球迷会</a>
<i></i>
       </div>
<div id="um" class="chum_on">
<!--
<div class="avt_item y">
<a href="home.php?mod=space&amp;do=pm" id="myprompt" <?php if(!empty($_G['member']['newpm'])) { ?>class="showmenus"<?php } else { ?>class="showmenus"<?php } ?>  onmouseover="showMenu({'ctrlid':'myprompt'});"></a>
<a href="home.php?mod=spacecp" class="set" id="fansclub" onmouseover="showMenu({'ctrlid':'fansclub'});"></a>
</div>
-->

<!--<div class="avt y"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>" id="profile" onmouseover="showMenu({'ctrlid':'profile'});"><?php echo avatar($_G[uid],small);?></a></div>-->

     <div class="avt_item y">
       <?php if(!empty($_G['setting']['pluginhooks']['global_usernav_extra4'])) echo $_G['setting']['pluginhooks']['global_usernav_extra4'];?>
   <a href="home.php?mod=space&amp;do=pm" id="myprompt" class="showmenus<?php if(!empty($_G['member']['newpm'])) { ?> newtip<?php } ?>"></a>
   <!-- <a href="home.php?mod=space&amp;do=pm" id="myitem" class="showmenus" onmouseover="showMenu({'ctrlid':'myitem'});"></a> -->
   <a href="home.php?mod=spacecp" class="set" id="set"></a>
 </div>
<div class="avt y">
<a id="mypersonal" class="" href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>" style="display: block;"><?php echo avatar($_G[uid],small);?></a>
</div>
<div class="header-prompt header-personal" style="display: none;">
<div class="hover"></div>
<div class="triangle" style="top: -5px;right: auto;left: 10px;"></div>
<ul class="list">
<li class="name"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=thread&amp;from=space"><?php echo $_G['username'];?></a></li>
<li class="jifen"><a href="home.php?mod=spacecp&amp;ac=credit&amp;op=buy">积分充值</a></li>
<li class="setting"><a href="home.php?mod=spacecp">设置</a></li>
<li class="logout"><a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo $_G['formhash'];?>">退出</a></li>
</ul>
</div>

<div class="header-prompt header-message">
<div class="hover"></div>
<div class="triangle" style="top: -5px;right: 29px;"></div>
<ul class="list">
<li class="msg"><a href="home.php?mod=space&amp;do=pm">消息</a></li>
<li class="fensi"><a href="home.php?mod=follow&amp;do=follower&amp;uid=<?php echo $_G['uid'];?>">粉丝</a></li>
<li class="post"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=thread&amp;view=me&amp;from=space">帖子</a></li>
<li class="manage"><a href="home.php?mod=spacecp">管理</a></li>
</ul>
</div><?php include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');?><?php $arr_fansclub_get_havejoin = fansclub_get_havejoin($_G[uid], 9);?><?php $arr_fansclub_get_haveview = fansclub_get_haveview($_G[uid], 3);?><div class="header-prompt header-setting" style="display: none;">
<div class="hover"></div>
<div class="triangle" style="top: -5px;right: 20px;"></div>
<ul class="list">
<li>
<a href="plugin.php?id=userspace:mygroup&amp;uid=<?php echo $_G['uid'];?>&amp;do=club&amp;from=space">加入的球迷会</a>
<div class="show"><?php if(is_array($arr_fansclub_get_havejoin)) foreach($arr_fansclub_get_havejoin as $val) { ?><img src="<?php echo $val['icon'];?>" style="cursor:pointer" onClick="javascript:window.location.href='fans/topic/<?php echo $val['fid'];?>';" title="<?php echo $val['name'];?>" alt="<?php echo $val['name'];?>"/>
<?php } ?>
</div>
</li>
<li>
<a href="football/allcategory">浏览过的频道</a>
<div class="show"><?php if(is_array($arr_fansclub_get_haveview)) foreach($arr_fansclub_get_haveview as $val) { ?><img src="<?php echo $val['icon'];?>" style="cursor:pointer" onClick="javascript:window.location.href='group/<?php echo $val['fid'];?>';" title="<?php echo $val['name'];?>" alt="<?php echo $val['name'];?>"/>
<?php } ?>
</div>
</li>
</ul>
</div>
<script>
jQuery("#mypersonal").hover(function () {
jQuery(".header-prompt.header-personal").show();
}, function () {
jQuery(".header-prompt.header-personal").hide();
});
jQuery(".header-personal").hover(function () {
jQuery(this).show();
}, function () {
jQuery(this).hide();
});
jQuery("#myprompt").hover(function () {
jQuery(".header-prompt.header-message").show();
}, function () {
jQuery(".header-prompt.header-message").hide();
});
jQuery(".header-message").hover(function () {
jQuery(this).show();
}, function () {
jQuery(this).hide();
});
jQuery("#set").hover(function () {
jQuery(".header-prompt.header-setting").show();
}, function () {
jQuery(".header-prompt.header-setting").hide();
});
jQuery(".header-setting").hover(function () {
jQuery(this).show();
}, function () {
jQuery(this).hide();
});
</script>
<p style="display:none;">
<?php if($_G['group']['allowinvisible']) { ?>
<span id="loginstatus">
<a id="loginstatusid" href="member.php?mod=switchstatus" title="切换在线状态" onclick="ajaxget(this.href, 'loginstatus');return false;" class="xi2"></a>
</span>
<?php } ?>
<?php if(!empty($_G['setting']['pluginhooks']['global_usernav_extra1'])) echo $_G['setting']['pluginhooks']['global_usernav_extra1'];?>
<span class="pipe">|</span><a href="home.php?mod=spacecp">设置</a>
<span class="pipe">|</span><a href="home.php?mod=space&amp;do=notice" id="myprompt" class="a showmenu<?php if($_G['member']['newprompt']) { ?> new<?php } ?>" onmouseover="showMenu({'ctrlid':'myprompt'});">提醒<?php if($_G['member']['newprompt']) { ?>(<?php echo $_G['member']['newprompt'];?>)<?php } ?></a><span id="myprompt_check"></span>
<?php if(empty($_G['cookie']['ignore_notice']) && ($_G['member']['newpm'] || $_G['member']['newprompt_num']['follower'] || $_G['member']['newprompt_num']['follow'] || $_G['member']['newprompt'])) { ?><script language="javascript">delayShow($('myprompt'), function() {showMenu({'ctrlid':'myprompt','duration':3})});</script><?php } if($_G['setting']['taskon'] && !empty($_G['cookie']['taskdoing_'.$_G['uid']])) { ?><span class="pipe">|</span><a href="home.php?mod=task&amp;item=doing" id="task_ntc" class="new">进行中的任务</a><?php } if(($_G['group']['allowmanagearticle'] || $_G['group']['allowpostarticle'] || $_G['group']['allowdiy'] || getstatus($_G['member']['allowadmincp'], 4) || getstatus($_G['member']['allowadmincp'], 6) || getstatus($_G['member']['allowadmincp'], 2) || getstatus($_G['member']['allowadmincp'], 3))) { ?>
<span class="pipe">|</span><a href="portal.php?mod=portalcp"><?php if($_G['setting']['portalstatus'] ) { ?>门户管理<?php } else { ?>模块管理<?php } ?></a>
<?php } if($_G['uid'] && $_G['group']['radminid'] > 1) { ?>
<span class="pipe">|</span><a href="forum.php?mod=modcp&amp;fid=<?php echo $_G['fid'];?>" target="_blank"><?php echo $_G['setting']['navs']['2']['navname'];?>管理</a>
<?php } if($_G['uid'] && getstatus($_G['member']['allowadmincp'], 1)) { ?>
<span class="pipe">|</span><a href="admin.php" target="_blank">管理中心</a>
<?php } ?>
<?php if(!empty($_G['setting']['pluginhooks']['global_usernav_extra2'])) echo $_G['setting']['pluginhooks']['global_usernav_extra2'];?>
<span class="pipe">|</span><a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>">退出</a>
</p>
<p style="display:none;">
<?php if(!empty($_G['setting']['pluginhooks']['global_usernav_extra3'])) echo $_G['setting']['pluginhooks']['global_usernav_extra3'];?>
<a href="home.php?mod=spacecp&amp;ac=credit&amp;showcredit=1" id="extcreditmenu"<?php if(!$_G['setting']['bbclosed']) { ?> onmouseover="delayShow(this, showCreditmenu);" class="showmenu"<?php } ?>>积分: <?php echo $_G['member']['credits'];?></a>
<span class="pipe">|</span><a href="home.php?mod=spacecp&amp;ac=usergroup" id="g_upmine" class="showmenu" onmouseover="delayShow(this, showUpgradeinfo)">用户组: <?php echo $_G['group']['grouptitle'];?><?php if($_G['member']['freeze']) { ?><span class="xi1">(已冻结)</span><?php } ?></a>
</p>
</div>
<?php } elseif(!empty($_G['cookie']['loginuser'])) { ?>
<p>
<strong><a id="loginuser" class="noborder"><?php echo dhtmlspecialchars($_G['cookie']['loginuser']); ?></a></strong>
<span class="pipe">|</span><a href="member.php?mod=logging&amp;action=login" onclick="showWindow('login', this.href)">激活</a>
<span class="pipe">|</span><a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>">退出</a>
</p>
<?php } elseif(!$_G['connectguest']) { include template('member/login_simple'); } else { ?>

<!--修改QQ登录而没有完善的样式  by zhangjh-->
<div id="um" class="chum_on">
<div id="set_menu" class="p_pop" style="display: none;">
<a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo $_G['formhash'];?>">退出</a>
<i></i>
</div>
<div class="avt_item y">
<a href="home.php?mod=space&amp;do=pm" id="myprompt" class="showmenus" onmouseover="showMenu({'ctrlid':'myprompt'});"></a>
<a href="home.php?mod=spacecp" class="set" id="set" onmouseover="showMenu({'ctrlid':'set'});"></a>
</div>
<div class="avt y"><a href="member.php?mod=connect" title="完善帐号信息"><?php echo avatar(0,small);?></a> 
<a href="member.php?mod=connect" class="name_ch" title="完善帐号信息"><?php echo $_G['member']['username'];?></a></div>
</div>

<?php } ?>