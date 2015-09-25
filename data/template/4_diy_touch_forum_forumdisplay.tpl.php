<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('forumdisplay');?><?php include template('common/header'); ?><!-- header start -->
<header class="header">
    <div class="nav">
    	<div class="category" style="width:100%; text-align:center; ">
        <?php if($subexists && $_G['page'] == 1) { ?>
<div class="display name vm" href="#subname_list">
<h2 class="tit"><?php echo strip_tags($_G['forum']['name']) ? strip_tags($_G['forum']['name']) : $_G['forum']['name'];?></h2>
<img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_arrow_down.png">
</div>
<div id="subname_list" class="subname_list" display="true" style="display:none;">
<ul><?php if(is_array($sublist)) foreach($sublist as $sub) { ?><li>
<a href="forum.php?mod=forumdisplay&amp;fid=<?php echo $sub['fid'];?>"><?php echo $sub['name'];?></a>
</li>
<?php } ?>
</ul>
</div>
<?php } else { ?>
<div class="name"><?php echo strip_tags($_G['forum']['name']) ? strip_tags($_G['forum']['name']) : $_G['forum']['name'];?></div>
<?php } ?>
            <div style="width:36px; position:absolute; left:0; top:0;">
                <a href="forum.php?forumlist=1" class="z"><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_back.png" /></a>
            </div>
            <div class="icon_edit y" style="width:36px; position:absolute; right:0; top:0;">
                <a href="forum.php?mod=post&amp;action=newthread&amp;fid=<?php echo $_G['fid'];?>" title="发帖"><span class="none">发帖</span></a>
            </div>
        </div>    
    </div>
</header>
<!-- header end -->

<?php if(!empty($_G['setting']['pluginhooks']['forumdisplay_top_mobile'])) echo $_G['setting']['pluginhooks']['forumdisplay_top_mobile'];?>
<!-- main threadlist start -->
<?php if(!$subforumonly) { ?>
<div class="threadlist">
<div>
<?php if($_G['forum_threadcount']) { if(is_array($_G['forum_threadlist'])) foreach($_G['forum_threadlist'] as $key => $thread) { if(!$_G['setting']['mobile']['mobiledisplayorder3'] && $thread['displayorder'] > 0) { continue;?><?php } ?>
                	<?php if($thread['displayorder'] > 0 && !$displayorder_thread) { ?>
                <?php $displayorder_thread = 1;?>                    <?php } if($thread['moved']) { $thread[tid]=$thread[closed];?><?php } ?>

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
                        	<?php if($thread['isgroup'] != 1) { ?><?php echo $thread['replies'];?><?php } else { ?><?php echo $groupnames[$thread['tid']]['replies'];?><?php } ?>
                            回复<br />
<?php if($thread['isgroup'] != 1) { ?><?php echo $thread['views'];?><?php } else { ?><?php echo $groupnames[$thread['tid']]['views'];?><?php } ?>
                            查看
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
                        	<?php if($thread['isgroup'] != 1) { ?><?php echo $thread['replies'];?><?php } else { ?><?php echo $groupnames[$thread['tid']]['replies'];?><?php } ?>
                            回复<br />
<?php if($thread['isgroup'] != 1) { ?><?php echo $thread['views'];?><?php } else { ?><?php echo $groupnames[$thread['tid']]['views'];?><?php } ?>
                            查看
                        </td>
                      </tr>
                        <?php } ?>
                    </table>
                    </div>
                    </div>
</a>

                <?php } ?>
            <?php } else { ?>
<div id="elecnation_noinfo">本版块或指定的范围内尚无主题</div>
<?php } ?>
</div>

<?php echo $multipage;?>
<div id="elecnation_multi_footer"></div>

</div>

<?php } ?>
<!-- main threadlist end -->
<?php if(!empty($_G['setting']['pluginhooks']['forumdisplay_bottom_mobile'])) echo $_G['setting']['pluginhooks']['forumdisplay_bottom_mobile'];?>
<div class="pullrefresh" style="display:none;"></div><?php include template('common/footer'); ?>