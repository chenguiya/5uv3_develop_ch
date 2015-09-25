<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('viewthread');
0
|| checktplrefresh('./template/usportstyle/touch/forum/viewthread.htm', './template/usportstyle/touch/forum/forumdisplay_fastpost.htm', 1443150315, 'diy', './data/template/4_diy_touch_forum_viewthread.tpl.php', './template/usportstyle', 'touch/forum/viewthread')
|| checktplrefresh('./template/usportstyle/touch/forum/viewthread.htm', './template/usportstyle/touch/common/seccheck.htm', 1443150315, 'diy', './data/template/4_diy_touch_forum_viewthread.tpl.php', './template/usportstyle', 'touch/forum/viewthread')
;?><?php include template('common/header'); ?><!-- header start -->
<!-- start of navbar-->
<nav class="navbar"<?php if(!empty($_G['forum']['color'])) { ?> style="background-color:<?php echo $_G['forum']['color'];?>"<?php } ?>>
    <div class="inner">
        <div class="left">
             <a href="javascript:void(0);" onclick="history.go(-1)"><i class="iconfont icon-back"></i></a>
        </div>       
        <div class="center"><?php if(!$_G['forum_thread']['special']) { ?>帖子<?php } elseif($_G['forum_thread']['special'] == 4) { ?>活动 <?php } ?></div>
        <div class="right">
             <a href="#share_<?php echo $_G['tid'];?>" class="display"><i class="fontweb_icon ic_share"></i></a>
        </div>
    </div>
</nav>
<div id="share_<?php echo $_G['tid'];?>" display="true" class="shareMeBox">
     <div class="share_hds">
          <span>分享到：</span>
          <a class="frigh_cuo" href="javascript:void(0)" onclick="display.hide();"><i class="fontweb_icon ic_cuo"></i></a>
     </div>
    <div id="sharebtn_menu" class="share_chbox">
    <div class="bdsharebuttonbox" data-tag="share_thread">
        <a class="bds_tsina fontweb_icon ic_tsina share_thread" data-cmd="tsina"></a>
        <a class="bds_qzone fontweb_icon ic_qzone share_thread" data-cmd="qzone"></a>
        <a class="bds_sqq fontweb_icon ic_sqq share_thread" data-cmd="sqq"></a>
    </div>

</div>
</div>
<!-- end of navbar-->
<div class="ch_mians" style="padding-top:44px;">
     <section class="postlist" id="postBoxd">
          <div id="elecnation_post_title">
        	<?php if($_G['forum_thread']['typeid'] && $_G['forum']['threadtypes']['types'][$_G['forum_thread']['typeid']]) { ?>
[<?php echo $_G['forum']['threadtypes']['types'][$_G['forum_thread']['typeid']];?>]
            <?php } ?>
            <?php if($threadsorts && $_G['forum_thread']['sortid']) { ?>
                [<?php echo $_G['forum']['threadsorts']['types'][$_G['forum_thread']['sortid']];?>]
<?php } ?>
<?php echo $_G['forum_thread']['subject'];?>
            <?php if($_G['forum_thread']['displayorder'] == -2) { ?> <span>(审核中)</span>
            <?php } elseif($_G['forum_thread']['displayorder'] == -3) { ?> <span>(已忽略)</span>
            <?php } elseif($_G['forum_thread']['displayorder'] == -4) { ?> <span>(草稿)</span>
            <?php } ?>
               </div>
           <?php if(is_array($postlist)) foreach($postlist as $post) { ?>           <?php $needhiddenreply = ($hiddenreplies && $_G['uid'] != $post['authorid'] && $_G['uid'] != $_G['forum_thread']['authorid'] && !$post['first'] && !$_G['forum']['ismoderator']);?>               <?php if($post['first']) { ?> 
           <?php if(!empty($_G['setting']['pluginhooks']['viewthread_posttop_mobile'][$postcount])) echo $_G['setting']['pluginhooks']['viewthread_posttop_mobile'][$postcount];?>
           <div id="elecnation_post_message" class="cl">
                <div class="viewhd">
                     <div class="vi_avatar_img">
                               <?php if($post['authorid'] && $post['username'] && !$post['anonymous']) { ?>
                                   <a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=thread&amp;from=thread&amp;mobile=2" style="border:none;"><img src="<?php echo avatar($post[authorid], middle, true);?>" style="border:none;" /></a>
                               <?php } else { ?>
                               <?php if(!$post['authorid']) { ?>
                               <img src="uc_server/images/noavatar_small.gif" />
                               <?php } elseif($post['authorid'] && $post['username'] && $post['anonymous']) { ?>
                               <?php if($_G['forum']['ismoderator']) { ?>
                               <a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=thread&amp;from=thread&amp;mobile=2" style="border:none;" target="_blank"><img src="uc_server/images/noavatar_small.gif" style="border:none;"/></a>
                               <?php } else { ?>
                               <img src="uc_server/images/noavatar_small.gif" />
                               <?php } ?>
                               <?php } else { ?>
                               <img src="<?php echo avatar($post[authorid], middle, true);?>"/>
                               <?php } ?>
                               <?php } ?>
                     </div>
                     <div class="authi_wrap">
                          <div class="authi_top">
                               <a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=thread&amp;from=thread&amp;mobile=2" class="elecnation_dr"><?php echo $post['author'];?></a>
                               <a href="forum.php?mod=viewthread&amp;tid=<?php echo $_G['tid'];?>&amp;page=<?php echo $page;?>&amp;authorid=<?php echo $_G['forum_thread']['authorid'];?>" rel="nofollow" class="look_at">只看楼主</a>
                          </div>
                          <div class="authi_p">
                               <span><?php echo $post['dateline'];?></span>
                                   <em class="em_drs">
                               <?php if($_G['forum']['ismoderator']) { ?>
                               <!-- manage start -->
                               <a class="dialog diabtn" href="forum.php?mod=topicadmin&amp;action=moderate&amp;fid=<?php echo $_G['fid'];?>&amp;moderate[]=<?php echo $_G['tid'];?>&amp;operation=delete&amp;optgroup=3&amp;from=<?php echo $_G['tid'];?>">删除</a> 
                               &nbsp;|&nbsp;
                               <?php if($_G['forum_thread']['displayorder']) { ?>
                               <a class="dialog diabtn" href="forum.php?mod=topicadmin&amp;action=moderate&amp;fid=<?php echo $_G['fid'];?>&amp;moderate[]=<?php echo $_G['tid'];?>&amp;operation=stick&amp;optgroup=3&amp;from=<?php echo $_G['tid'];?>&amp;sticklevel=0">取消置顶</a>
                               <?php } else { ?> 
                               <a class="dialog diabtn" href="forum.php?mod=topicadmin&amp;action=moderate&amp;fid=<?php echo $_G['fid'];?>&amp;moderate[]=<?php echo $_G['tid'];?>&amp;operation=stick&amp;optgroup=3&amp;from=<?php echo $_G['tid'];?>&amp;sticklevel=1">置顶</a>
                               <?php } ?>
                               <!-- manage end -->
                               <?php } ?>
                              <!-- <a href="home.php?mod=spacecp&amp;ac=favorite&amp;type=thread&amp;id=<?php echo $_G['tid'];?>" class="favbtn elecnation_dr">收藏</a> -->
                                  </em>
                          </div>
                     </div>
                </div>
           </div>
           <div class="display_ch pi" href="#replybtn_<?php echo $post['pid'];?>">
                <div class="message">       
                	<?php if($post['warned']) { ?>
                        <span class="grey quote">受到警告</span>
                    <?php } ?>
                    <?php if(!$post['first'] && !empty($post['subject'])) { ?>
                        <h2><strong><?php echo $post['subject'];?></strong></h2>        	
                    <?php } ?>
                    <?php if($_G['adminid'] != 1 && $_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5) || $post['status'] == -1 || $post['memberstatus'])) { ?>
                        <div class="grey quote">提示: <em>作者被禁止或删除 内容自动屏蔽</em></div>
                    <?php } elseif($_G['adminid'] != 1 && $post['status'] & 1) { ?>
                        <div class="grey quote">提示: <em>该帖被管理员或版主屏蔽</em></div>
                    <?php } elseif($needhiddenreply) { ?>
                        <div class="grey quote">此帖仅作者可见</div>
                    <?php } elseif($post['first'] && $_G['forum_threadpay']) { include template('forum/viewthread_pay'); } else { ?>

                    	<?php if($_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5))) { ?>
                            <div class="grey quote">提示: <em>作者被禁止或删除 内容自动屏蔽，只有管理员或有管理权限的成员可见</em></div>
                        <?php } elseif($post['status'] & 1) { ?>
                            <div class="grey quote">提示: <em>该帖被管理员或版主屏蔽，只有管理员或有管理权限的成员可见</em></div>
                        <?php } ?>
                        <?php if($_G['forum_thread']['price'] > 0 && $_G['forum_thread']['special'] == 0) { ?>
                            付费主题, 价格: <strong><?php echo $_G['forum_thread']['price'];?> <?php echo $_G['setting']['extcredits'][$_G['setting']['creditstransextra']['1']]['unit'];?><?php echo $_G['setting']['extcredits'][$_G['setting']['creditstransextra']['1']]['title'];?> </strong> <a href="forum.php?mod=misc&amp;action=viewpayments&amp;tid=<?php echo $_G['tid'];?>" >记录</a>
                        <?php } ?>

                        <?php if($post['first'] && $threadsort && $threadsortshow) { ?>
                        	<?php if($threadsortshow['optionlist'] && !($post['status'] & 1) && !$_G['forum_threadpay']) { ?>
                                <?php if($threadsortshow['optionlist'] == 'expire') { ?>
                                    该信息已经过期
                                <?php } else { ?>
                                    <div class="box_ex2 viewsort">
                                        <h4><?php echo $_G['forum']['threadsorts']['types'][$_G['forum_thread']['sortid']];?></h4>
                                    <?php if(is_array($threadsortshow['optionlist'])) foreach($threadsortshow['optionlist'] as $option) { ?>                                        <?php if($option['type'] != 'info') { ?>
                                            <?php echo $option['title'];?>: <?php if($option['value']) { ?><?php echo $option['value'];?> <?php echo $option['unit'];?><?php } else { ?><span class="elecnation_dy">--</span><?php } ?><br />
                                        <?php } ?>
                                    <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if($post['first']) { ?>
                            <?php if(!$_G['forum_thread']['special']) { ?>
                                <?php echo $post['message'];?>
                            <?php } elseif($_G['forum_thread']['special'] == 1) { ?>
                                <?php include template('forum/viewthread_poll'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 2) { ?>
                                <?php include template('forum/viewthread_trade'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 3) { ?>
                                <?php include template('forum/viewthread_reward'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 4) { ?>
                                <?php include template('forum/viewthread_activity'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 5) { ?>
                                <?php include template('forum/viewthread_debate'); ?>                            <?php } elseif($threadplughtml) { ?>
                                <?php echo $threadplughtml;?>
                                <?php echo $post['message'];?>
                            <?php } else { ?>
                            	<?php echo $post['message'];?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo $post['message'];?>
                        <?php } } ?>
                   </div>
<?php if($_G['setting']['mobile']['mobilesimpletype'] == 0) { if($post['attachment']) { ?>
               <div class="elecnation_dy quote">
               附件: <em><?php if($_G['uid']) { ?>您所在的用户组无法下载或查看附件<?php } else { ?>您需要<a href="member.php?mod=logging&amp;action=login">登录</a>才可以下载或查看附件。没有帐号？<a href="member.php?mod=<?php echo $_G['setting']['regname'];?>" title="注册帐号"><?php echo $_G['setting']['reglinkname'];?></a><?php } ?></em>
               </div>
            <?php } elseif($post['imagelist'] || $post['attachlist']) { ?>
               <?php if($post['imagelist']) { if(count($post['imagelist']) == 1) { ?>
<ul class="img_one"><?php echo showattach($post, 1); ?></ul>
<?php } else { ?>
<ul class="img_list cl vm"><?php echo showattach($post, 1); ?></ul>
<?php } } ?>
                <?php if($post['attachlist']) { ?>
<ul><?php echo showattach($post); ?></ul>
<?php } } } ?>
            </div>
            </section>
            <div class="section_t" id="sectionBoxd">
                 <div class="pic_comment">精彩评论：</div>
                 <?php if(count($postlist) == 1) { ?><p class="no_comtent">暂无评论</p><?php } ?>
            <?php } else { ?>
            
            <div class="chtwo cl">
                 <div class="viewhd">
                      <div class="vi_avatar_img">
        	<?php if($post['authorid'] && $post['username'] && !$post['anonymous']) { ?>
            	<a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=thread&amp;from=thread&amp;mobile=2" style="border:none;"><img src="<?php echo avatar($post[authorid], middle, true);?>" /></a>
            <?php } else { if(!$post['authorid']) { ?>
                <img src="uc_server/images/noavatar_small.gif" />
<?php } elseif($post['authorid'] && $post['username'] && $post['anonymous']) { if($_G['forum']['ismoderator']) { ?>
                	<a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=thread&amp;from=thread&amp;mobile=2" style="border:none;" target="_blank"><img src="uc_server/images/noavatar_small.gif" /></a>
               	 	<?php } else { ?>
                	<img src="uc_server/images/noavatar_small.gif" />
                	<?php } } else { ?>
<img src="<?php echo avatar($post[authorid], middle, true);?>" />
<?php } } ?>
        	      </div>
        	      <div class="two_wrap">
        	      <div class="authi_tw" href="#replybtn_<?php echo $post['pid'];?>">
        	           <strong>
<?php if(isset($post['isstick'])) { ?>
<img src ="<?php echo IMGDIR;?>/settop.png" title="置顶回复" class="vm" /> 来自 <?php echo $post['number'];?><?php echo $postnostick;?>
<?php } elseif($post['number'] == -1) { ?>
推荐
<?php } else { if(!empty($postno[$post['number']]) && $post['number'] == 2) { ?>
<span class="reds_A"><?php echo $postno[$post['number']];?></span>
<?php } elseif(!empty($postno[$post['number']]) && $post['number'] == 3) { ?>
    <span class="reds_B"><?php echo $postno[$post['number']];?></span>
<?php } elseif(!empty($postno[$post['number']]) && $post['number'] == 4) { ?>
    <span class="reds_C"><?php echo $postno[$post['number']];?></span>
<?php } else { ?>
<em><?php echo $post['number'];?><?php echo $postno['0'];?></em>
<?php } } ?>
</strong>
                    <b><?php if($post['authorid'] && $post['username'] && !$post['anonymous']) { ?><a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=thread&amp;from=thread&amp;mobile=2" class="elecnation_dr"><?php echo $post['author'];?></a></b>

<?php } else { if(!$post['authorid']) { ?>
<a href="javascript:;">游客 <em><?php echo $post['useip'];?></em></a>
<?php } elseif($post['authorid'] && $post['username'] && $post['anonymous']) { if($_G['forum']['ismoderator']) { ?><a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=thread&amp;from=thread&amp;mobile=2">匿名</a><?php } else { ?>匿名<?php } } else { ?>
<?php echo $post['author'];?> <em>该用户已被删除</em>
<?php } } ?>
<b class="gresy"><?php echo $post['dateline'];?></b>
        	      </div>
        	      <div class="message">       
                	<?php if($post['warned']) { ?>
                        <span class="grey quote">受到警告</span>
                    <?php } ?>
                    <?php if(!$post['first'] && !empty($post['subject'])) { ?>
                        <h2><strong><?php echo $post['subject'];?></strong></h2>        	
                    <?php } ?>
                    <?php if($_G['adminid'] != 1 && $_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5) || $post['status'] == -1 || $post['memberstatus'])) { ?>
                        <div class="grey quote">提示: <em>作者被禁止或删除 内容自动屏蔽</em></div>
                    <?php } elseif($_G['adminid'] != 1 && $post['status'] & 1) { ?>
                        <div class="grey quote">提示: <em>该帖被管理员或版主屏蔽</em></div>
                    <?php } elseif($needhiddenreply) { ?>
                        <div class="grey quote">此帖仅作者可见</div>
                    <?php } elseif($post['first'] && $_G['forum_threadpay']) { include template('forum/viewthread_pay'); } else { ?>

                    	<?php if($_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5))) { ?>
                            <div class="grey quote">提示: <em>作者被禁止或删除 内容自动屏蔽，只有管理员或有管理权限的成员可见</em></div>
                        <?php } elseif($post['status'] & 1) { ?>
                            <div class="grey quote">提示: <em>该帖被管理员或版主屏蔽，只有管理员或有管理权限的成员可见</em></div>
                        <?php } ?>
                        <?php if($_G['forum_thread']['price'] > 0 && $_G['forum_thread']['special'] == 0) { ?>
                            付费主题, 价格: <strong><?php echo $_G['forum_thread']['price'];?> <?php echo $_G['setting']['extcredits'][$_G['setting']['creditstransextra']['1']]['unit'];?><?php echo $_G['setting']['extcredits'][$_G['setting']['creditstransextra']['1']]['title'];?> </strong> <a href="forum.php?mod=misc&amp;action=viewpayments&amp;tid=<?php echo $_G['tid'];?>" >记录</a>
                        <?php } ?>

                        <?php if($post['first'] && $threadsort && $threadsortshow) { ?>
                        	<?php if($threadsortshow['optionlist'] && !($post['status'] & 1) && !$_G['forum_threadpay']) { ?>
                                <?php if($threadsortshow['optionlist'] == 'expire') { ?>
                                    该信息已经过期
                                <?php } else { ?>
                                    <div class="box_ex2 viewsort">
                                        <h4><?php echo $_G['forum']['threadsorts']['types'][$_G['forum_thread']['sortid']];?></h4>
                                    <?php if(is_array($threadsortshow['optionlist'])) foreach($threadsortshow['optionlist'] as $option) { ?>                                        <?php if($option['type'] != 'info') { ?>
                                            <?php echo $option['title'];?>: <?php if($option['value']) { ?><?php echo $option['value'];?> <?php echo $option['unit'];?><?php } else { ?><span class="elecnation_dy">--</span><?php } ?><br />
                                        <?php } ?>
                                    <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if($post['first']) { ?>
                            <?php if(!$_G['forum_thread']['special']) { ?>
                                <?php echo $post['message'];?>
                            <?php } elseif($_G['forum_thread']['special'] == 1) { ?>
                                <?php include template('forum/viewthread_poll'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 2) { ?>
                                <?php include template('forum/viewthread_trade'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 3) { ?>
                                <?php include template('forum/viewthread_reward'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 4) { ?>
                                <?php include template('forum/viewthread_activity'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 5) { ?>
                                <?php include template('forum/viewthread_debate'); ?>                            <?php } elseif($threadplughtml) { ?>
                                <?php echo $threadplughtml;?>
                                <?php echo $post['message'];?>
                            <?php } else { ?>
                            	<?php echo $post['message'];?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo $post['message'];?>

                        <?php } } ?>

            </div> 
            </div>
        	</div>
<?php if($_G['uid'] && $allowpostreply && !$post['first']) { ?>
<div id="replybtn_<?php echo $post['pid'];?>" display="true" class="elecnation_post_reply">
<a href="forum.php?mod=post&amp;action=reply&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;repquote=<?php echo $post['pid'];?>&amp;extra=<?php echo $_GET['extra'];?>&amp;page=<?php echo $page;?>" style="padding:8px 6px;">回复</a>
</div>
<?php } ?>

            </div>
             <?php if(!empty($_G['setting']['pluginhooks']['viewthread_postbottom_mobile'][$postcount])) echo $_G['setting']['pluginhooks']['viewthread_postbottom_mobile'][$postcount];?>
             <?php $postcount++;?>             <?php } ?>
             <?php } ?>
             <div id="post_new"></div>
            </div>
            <?php if($maxpage) { ?>
            <div class="view_pages"><a href="javascript:void(0);" class="act_more" id="actMores" data-id="<?php echo $_G['tid'];?>" page="2" totalpage="<?php echo $maxpage;?>">加载更多</a></div>
            <?php } ?>
            <div class="view_padding"></div>
            <div class="fast_postwap">
                  

<div class="elecnation_fastpost cl">
<form method="post" autocomplete="off" id="fastpostform" action="forum.php?mod=post&amp;action=reply&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;extra=<?php echo $_GET['extra'];?>&amp;replysubmit=yes&amp;mobile=2">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<div class="fast_chbox">
<div class="avatar" style="display:none;"><img src="<?php echo avatar($_G[uid], middle, true);?>" /></div>  
<div class="pi">
<ul class="fastpost">
<?php if($_G['forum_thread']['special'] == 5 && empty($firststand)) { ?>
<li>
<select id="stand" name="stand" >
<option value="">选择观点</option>
<option value="0">中立</option>
<option value="1">正方</option>
<option value="2">反方</option>
</select>
</li>
<?php } ?>
<li><input type="text" value="我也说一句" class="input grey" color="gray" name="message" id="fastpostmessage"></li>
<li id="fastpostsubmitline" style="display:block;"><?php if(checkperm('seccode') && ($secqaacheck || $seccodecheck)) { $sechash = 'S'.random(4);
$sectpl = !empty($sectpl) ? explode("<sec>", $sectpl) : array('<br />',': ','<br />','');
$secshow = !isset($secshow) ? 1 : $secshow;
$sectabindex = !isset($sectabindex) ? 1 : $sectabindex;
    $ran = random(5, 1);?><?php if($secqaacheck) { include libfile('function/seccode');
    $message = '';
$question = make_secqaa($sechash);
$secqaa = lang('core', 'secqaa_tips').$question;?><?php } ?><?php
$seccheckhtml = <<<EOF

<input name="sechash" type="hidden" value="{$sechash}" class="sechash" />

EOF;
 if($sectpl) { if($secqaacheck) { 
$seccheckhtml .= <<<EOF

<p>
        验证问答: 
        <span class="xg2">{$secqaa}</span>
        <input name="secanswer" id="secqaaverify_{$sechash}" type="text" class="txt" />
        </p>

EOF;
 } if($seccodecheck) { 
$seccheckhtml .= <<<EOF

<div class="sec_code vm">
<input type="text" class="txt px vm" style="ime-mode:disabled;width:60px;background:white;" autocomplete="off" value="" id="seccodeverify_{$sechash}" name="seccodeverify" placeholder="验证码" fwin="seccode">
        <img src="misc.php?mod=seccode&amp;update={$ran}&amp;idhash={$sechash}&amp;mobile=2" class="seccodeimg"/>
</div>

EOF;
 } } 
$seccheckhtml .= <<<EOF


EOF;
?><?php unset($secshow);?><?php if(empty($secreturn)) { ?><?php echo $seccheckhtml;?><?php } ?>

<script type="text/javascript">
(function() {
$('.seccodeimg').on('click', function() {
$('#seccodeverify_<?php echo $sechash;?>').attr('value', '');
var tmprandom = 'S' + Math.floor(Math.random() * 1000);
$('.sechash').attr('value', tmprandom);
$(this).attr('src', 'misc.php?mod=seccode&update=<?php echo $ran;?>&idhash='+ tmprandom +'&mobile=2');
});
})();
</script>
<?php } ?><input type="button" value="回复" class="button_chs" name="replysubmit" id="fastpostsubmit" style="float:left; margin-right:0.8rem;"><a style="float:left;" href="forum.php?mod=post&amp;action=reply&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;reppost=<?php echo $_G['forum_firstpid'];?>&amp;page=<?php echo $page;?>" class=""><i class="fontweb_icon ic_phots"></i></a><?php if(!empty($_G['setting']['pluginhooks']['viewthread_fastpost_button_mobile'])) echo $_G['setting']['pluginhooks']['viewthread_fastpost_button_mobile'];?></li>
</ul>
</div>
</div>
    </form>
</div>
<script type="text/javascript">
(function() {
var form = $('#fastpostform');
<?php if(!$_G['uid'] || $_G['uid'] && !$allowpostreply) { ?>
$('#fastpostmessage').on('focus', function() {
<?php if(!$_G['uid']) { ?>
popup.open('您还未登录，立即登录?', 'confirm', 'member.php?mod=logging&action=login');
<?php } else { ?>
popup.open('您暂时没有权限发表', 'alert');
<?php } ?>
this.blur();
});
<?php } else { ?>
$('#fastpostmessage').on('focus', function() {
var obj = $(this);
if(obj.attr('color') == 'gray') {
obj.attr('value', '');
obj.removeClass('grey');
obj.attr('color', 'black');
$('#fastpostsubmitline').css('display', 'block');
}
})
.on('blur', function() {
var obj = $(this);
if(obj.attr('value') == '') {
obj.addClass('grey');
obj.attr('value', '我也说一句');
obj.attr('color', 'gray');
}
});
<?php } ?>
$('#fastpostsubmit').on('click', function() {
var msgobj = $('#fastpostmessage');
if(msgobj.val() == '我也说一句') {
msgobj.attr('value', '');
}
$.ajax({
type:'POST',
url:form.attr('action') + '&handlekey=fastpost&loc=1&inajax=1',
data:form.serialize(),
dataType:'xml'
})
.success(function(s) {
evalscript(s.lastChild.firstChild.nodeValue);
})
.error(function() {
window.location.href = obj.attr('href');
popup.close();
});
return false;
});

$('#replyid').on('click', function() {
$(document).scrollTop($(document).height());
$('#fastpostmessage')[0].focus();
});

})();

function succeedhandle_fastpost(locationhref, message, param) {
var pid = param['pid'];
var tid = param['tid'];
if(pid) {
$.ajax({
type:'POST',
url:'forum.php?mod=viewthread&tid=' + tid + '&viewpid=' + pid + '&mobile=2',
dataType:'xml'
})
.success(function(s) {
$('#post_new').append(s.lastChild.firstChild.nodeValue);
})
.error(function() {
window.location.href = 'forum.php?mod=viewthread&tid=' + tid;
popup.close();
});
} else {
if(!message) {
message = '本版回帖需要审核，您的帖子将在通过审核后显示';
}
popup.open(message, 'alert');
}
$('#fastpostmessage').attr('value', '');
if(param['sechash']) {
$('.seccodeimg').click();
}
}

function errorhandle_fastpost(message, param) {
popup.open(message, 'alert');
}
</script>
             </div>
</div>
        
</div>

<?php if(!empty($_G['setting']['pluginhooks']['viewthread_bottom_mobile'])) echo $_G['setting']['pluginhooks']['viewthread_bottom_mobile'];?>

<script type="text/javascript">
$('.favbtn').on('click', function() {
var obj = $(this);
$.ajax({
type:'POST',
url:obj.attr('href') + '&handlekey=favbtn&inajax=1',
data:{'favoritesubmit':'true', 'formhash':'<?php echo FORMHASH;?>'},
dataType:'xml',
})
.success(function(s) {
popup.open(s.lastChild.firstChild.nodeValue);
evalscript(s.lastChild.firstChild.nodeValue);
})
.error(function() {
window.location.href = obj.attr('href');
popup.close();
});
return false;
});
</script><?php include template('common/footer'); ?>