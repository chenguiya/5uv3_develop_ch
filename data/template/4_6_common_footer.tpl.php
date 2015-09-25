<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
	</div><?php $focusid = getfocus_rand($_G[basescript]);?><?php if($focusid !== null) { $focus = $_G['cache']['focus']['data'][$focusid];?><?php $focusnum = count($_G['setting']['focus'][$_G[basescript]]);?><div class="focus" id="sitefocus">
<div class="bm">
<div class="bm_h cl">
<a href="javascript:;" onclick="setcookie('nofocus_<?php echo $_G['basescript'];?>', 1, <?php echo $_G['cache']['focus']['cookie'];?>*3600);$('sitefocus').style.display='none'" class="y" title="关闭">关闭</a>
<h2>
<?php if($_G['cache']['focus']['title']) { ?><?php echo $_G['cache']['focus']['title'];?><?php } else { ?>站长推荐<?php } ?>
<span id="focus_ctrl" class="fctrl"><img src="<?php echo IMGDIR;?>/pic_nv_prev.gif" alt="上一条" title="上一条" id="focusprev" class="cur1" onclick="showfocus('prev');" /> <em><span id="focuscur"></span>/<?php echo $focusnum;?></em> <img src="<?php echo IMGDIR;?>/pic_nv_next.gif" alt="下一条" title="下一条" id="focusnext" class="cur1" onclick="showfocus('next')" /></span>
</h2>
</div>
<div class="bm_c" id="focus_con">
</div>
</div>
</div><?php $focusi = 0;?><?php if(is_array($_G['setting']['focus'][$_G['basescript']])) foreach($_G['setting']['focus'][$_G['basescript']] as $id) { ?><div class="bm_c" style="display: none" id="focus_<?php echo $focusi;?>">
<dl class="xld cl bbda">
<dt><a href="<?php echo $_G['cache']['focus']['data'][$id]['url'];?>" class="xi2" target="_blank"><?php echo $_G['cache']['focus']['data'][$id]['subject'];?></a></dt>
<?php if($_G['cache']['focus']['data'][$id]['image']) { ?>
<dd class="m"><a href="<?php echo $_G['cache']['focus']['data'][$id]['url'];?>" target="_blank"><img src="<?php echo $_G['cache']['focus']['data'][$id]['image'];?>" alt="<?php echo $_G['cache']['focus']['data'][$id]['subject'];?>" /></a></dd>
<?php } ?>
<dd><?php echo $_G['cache']['focus']['data'][$id]['summary'];?></dd>
</dl>
<p class="ptn cl"><a href="<?php echo $_G['cache']['focus']['data'][$id]['url'];?>" class="xi2 y" target="_blank">查看 &raquo;</a></p>
</div><?php $focusi ++;?><?php } ?>
<script type="text/javascript">
var focusnum = <?php echo $focusnum;?>;
if(focusnum < 2) {
$('focus_ctrl').style.display = 'none';
}
if(!$('focuscur').innerHTML) {
var randomnum = parseInt(Math.round(Math.random() * focusnum));
$('focuscur').innerHTML = Math.max(1, randomnum);
}
showfocus();
var focusautoshow = window.setInterval('showfocus(\'next\', 1);', 5000);
</script>
<?php } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && $_G['setting']['showpatchnotice'] == 1) { ?>
<div class="focus patch" id="patch_notice"></div>
<?php } ?><?php echo adshow("footerbanner/wp a_f/1");?><?php echo adshow("footerbanner/wp a_f/2");?><?php echo adshow("footerbanner/wp a_f/3");?><?php echo adshow("float/a_fl/1");?><?php echo adshow("float/a_fr/2");?><?php echo adshow("couplebanner/a_fl a_cb/1");?><?php echo adshow("couplebanner/a_fr a_cb/2");?><?php echo adshow("cornerbanner/a_cn");?><?php if(!empty($_G['setting']['pluginhooks']['global_footer'])) echo $_G['setting']['pluginhooks']['global_footer'];?>
<div class="chfooter">
<div id="ft" class="wp cl">
<div class="footer_beta">
 <a href="javascript:void(0);" class="ch_weibo"></a>
 <a href="javascript:void(0);" class="ch_weixin"></a>
 <a href="javascript:void(0);" class="ch_ipad"></a>
 <div class="openBox sinaBox ch_weibo-box">
            	<!-- <a href="http://weibo.com/usportnews" target="_blank" title="新浪微博" rel="nofollow"><span style=" background:#FFF"><iframe width="220" height="120" class="share_self"  frameborder="0" scrolling="no" src="http://widget.weibo.com/weiboshow/index.php?language=&amp;width=220&amp;height=150&amp;fansRow=2&amp;ptype=1&amp;speed=0&amp;skin=1&amp;isTitle=1&amp;noborder=1&amp;isWeibo=0&amp;isFans=0&amp;uid=1735856841&amp;verifier=a36ceb4f&amp;dpc=1"></iframe></span></a> -->
            	<div class="openBg code_xl"><img src="<?php echo $_G['config']['static'];?>/images/code_wb.jpg" width="100" height="100"/><p>关注5U新浪微博</p><div><a href="http://www.weibo.com/usportnews" target="_blank"><strong>+</strong>关注</a></div></div>
            </div>
        	<div class="openBox code_wx ch_weixin-box">
            	<div class="openBg"><img src="<?php echo $_G['config']['static'];?>/images/code_wx.jpg" width="100" height="100"/><p>关注5U微信</p></div>
            </div>
            <div class="openBox code_sj ch_ipad-box">
            	<div class="openBg"><img src="<?php echo $_G['config']['static'];?>/images/code_sj.jpg" width="100" height="100"/><p>下载聊球APP</p></div>
            </div>
 <p class="foot_logo"><a href="<?php echo $_G['setting']['siteurl'];?>" target="_blank"><img src="<?php echo $_G['config']['static'];?>/images/logo_white.png" width="175" height="45" border="0"></a></p>
</div>

<div id="frt">
     <p class="foot_nav">
<a href="about/aboutus/" target="_blank" title="关于我们">关于我们</a>|
<a href="about/advert/" target="_blank" title="广告服务">广告服务</a>|
<a href="about/hr/" target="_blank" title="招聘信息">招聘信息</a>|
<a href="about/copyright/" target="_blank" title="法律声明">法律声明</a>|
<a href="about/contactus/" target="_blank" title="联系方式">联系方式</a>|
<a href="about/sitemap/" target="_blank" title="网站地图">网站地图</a><!--|
<a href="http://about.5usport.com/about/link/" target="_blank" title="友情链接">友情链接</a>-->
</p>
<p>Copyright © 2009-2014 5usport.com, All rights reserved. 广州市晌网文化传播有限公司 <a href="http://gdcainfo.miitbeian.gov.cn/publish/query/indexFirst.action" target="_blank" class="fo_color" rel="nofollow">粤ICP备11095914号</a></p>
<p>免责声明：本站部分文章作品转载自合作网站，如有侵权问题，请及时联系，我们将尽快处理。</p>
</div><?php updatesession();?><?php if($_G['uid'] && $_G['group']['allowinvisible']) { ?>
<script type="text/javascript">
var invisiblestatus = '<?php if($_G['session']['invisible']) { ?>隐身<?php } else { ?>在线<?php } ?>';
var loginstatusobj = $('loginstatusid');
if(loginstatusobj != undefined && loginstatusobj != null) loginstatusobj.innerHTML = invisiblestatus;
</script>
<?php } ?>
</div>
</div>


<?php if(!$_G['setting']['bbclosed'] && !$_G['member']['freeze'] && !$_G['member']['groupexpiry']) { if($_G['uid'] && !isset($_G['cookie']['checkpm'])) { ?>
<script src="home.php?mod=spacecp&ac=pm&op=checknewpm&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } if($_G['uid'] && helper_access::check_module('follow') && !isset($_G['cookie']['checkfollow'])) { ?>
<script src="home.php?mod=spacecp&ac=follow&op=checkfeed&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } if(!isset($_G['cookie']['sendmail'])) { ?>
<script src="home.php?mod=misc&ac=sendmail&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && !isset($_G['cookie']['checkpatch'])) { ?>
<script src="misc.php?mod=patch&action=checkpatch&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } } if($_GET['diy'] == 'yes') { if(check_diy_perm($topic) && (empty($do) || $do != 'index')) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>common_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script src="<?php echo $_G['setting']['jspath'];?>portal_diy<?php if(!check_diy_perm($topic, 'layout')) { ?>_data<?php } ?>.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php } if($space['self'] && CURMODULE == 'space' && $do == 'index') { ?>
<script src="<?php echo $_G['setting']['jspath'];?>common_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script src="<?php echo $_G['setting']['jspath'];?>space_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php } } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && $_G['setting']['showpatchnotice'] == 1) { ?>
<script type="text/javascript">patchNotice();</script>
<?php } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && empty($_G['cookie']['pluginnotice'])) { ?>
<div class="focus plugin" id="plugin_notice"></div>
<script type="text/javascript">pluginNotice();</script>
<?php } if(!$_G['setting']['bbclosed'] && !$_G['member']['freeze'] && !$_G['member']['groupexpiry'] && $_G['setting']['disableipnotice'] != 1 && $_G['uid'] && !empty($_G['cookie']['lip'])) { ?>
<div class="focus plugin" id="ip_notice"></div>
<script type="text/javascript">ipNotice();</script>
<?php } if($_G['member']['newprompt'] && (empty($_G['cookie']['promptstate_'.$_G['uid']]) || $_G['cookie']['promptstate_'.$_G['uid']] != $_G['member']['newprompt']) && $_GET['do'] != 'notice') { ?>
<script type="text/javascript">noticeTitle();</script>
<?php } if(($_G['member']['newpm'] || $_G['member']['newprompt']) && empty($_G['cookie']['ignore_notice'])) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>html5notification.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script type="text/javascript">
var h5n = new Html5notification();
if(h5n.issupport()) {
<?php if($_G['member']['newpm'] && $_GET['do'] != 'pm') { ?>
h5n.shownotification('pm', '<?php echo $_G['siteurl'];?>home.php?mod=space&do=pm', '<?php echo avatar($_G[uid],small,true);?>', '新的短消息', '有新的短消息，快去看看吧');
<?php } if($_G['member']['newprompt'] && $_GET['do'] != 'notice') { if(is_array($_G['member']['category_num'])) foreach($_G['member']['category_num'] as $key => $val) { $noticetitle = lang('template', 'notice_'.$key);?>h5n.shownotification('notice_<?php echo $key;?>', '<?php echo $_G['siteurl'];?>home.php?mod=space&do=notice&view=<?php echo $key;?>', '<?php echo avatar($_G[uid],small,true);?>', '<?php echo $noticetitle;?> (<?php echo $val;?>)', '有新的提醒，快去看看吧');
<?php } } ?>
}
</script>
<?php } userappprompt();?><?php if($_G['basescript'] != 'userapp') { ?>
<div class="ch_scrollbtn" id="rightSideScrollTopBtn">
 <?php if($_G['fid']) { ?><a id="newspecialtmp" <?php if(!$_G['forum']['allowspecialonly'] && empty($_G['forum']['picstyle']) && !$_G['forum']['threadsorts']['required']) { ?> onclick="showWindow('newthread', '<?php echo $_G['siteurl'];?>forum.php?mod=post&action=newthread&fid=<?php echo $_G['fid'];?>')"<?php } else { ?> onclick="location.href='<?php echo $_G['siteurl'];?>forum.php?mod=post&action=newthread&fid=<?php echo $_G['fid'];?>';return false;"<?php } ?> href="javascript:;" title="发帖" class="thread_ch">发帖</a><?php } ?>
 <a href="download/app/liaoqiu/" class="app_load" target="_blank">APP下载</a>
 <a href="thread-247.html" class="feedback" target="_blank">问题反馈</a>
 <a title="返回顶部" onclick="window.scrollTo('0','0')" class="scrolltops">top</a>
</div>

<!--热门频道-->
<?php if(!empty($_G['setting']['pluginhooks']['global_hot_forum'])) echo $_G['setting']['pluginhooks']['global_hot_forum'];?>
<!--热门频道end-->
<script type="text/javascript">_attachEvent(window, 'scroll', function () { showTopLink(); });checkBlind();</script>
<?php } if(isset($_G['makehtml'])) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>html2dynamic.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script type="text/javascript">
var html_lostmodify = <?php echo TIMESTAMP;?>;
htmlGetUserStatus();
<?php if(isset($_G['htmlcheckupdate'])) { ?>
htmlCheckUpdate();
<?php } ?>
</script>
<?php } output();?><div style="display:none;"><?php if($_G['setting']['statcode']) { ?><?php echo $_G['setting']['statcode'];?><?php } ?></div>

</body>
</html>
