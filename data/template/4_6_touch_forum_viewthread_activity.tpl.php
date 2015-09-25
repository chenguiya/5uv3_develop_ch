<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<div class="act_vbox">
 <?php if($activity['thumb']) { ?><img src="<?php echo $activity['thumb'];?>"/><?php } else { ?><img src="<?php echo IMGDIR;?>/nophoto.gif" /><?php } ?>
<dl>
<!-- <dt><em>活动类型:</em> <span><?php echo $activity['class'];?></span></dt> -->
<dt><em>开始时间:</em>
<?php if($activity['starttimeto']) { ?>
<span><?php echo $activity['starttimefrom'];?> 至 <?php echo $activity['starttimeto'];?> 商定</span>
<?php } else { ?>
<span><?php echo $activity['starttimefrom'];?></span>
<?php } ?>
</dt>
<dt><em>活动地点:</em> <span><?php echo $activity['place'];?></span></dt>
<dt><em>性&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;别:</em>
<?php if($activity['gender'] == 1) { ?>
<span>男</span>
<?php } elseif($activity['gender'] == 2) { ?>
<span>女</span>
<?php } else { ?>
 <span>不限</span>
<?php } ?>
</dt>
<?php if($activity['cost']) { ?>
<dt><em>每人花销:</em> <span><?php echo $activity['cost'];?> 元</span></dt>
<?php } ?>
    <?php if(!$_G['forum_thread']['is_archived']) { ?>
<dt><em>已报人数:</em>
<span class="xi2"><?php echo $allapplynum;?> 人				
</dt>
<?php if($activity['number']) { ?>
<dt><em>剩余名额:</em>
<span><?php echo $aboutmembers;?> 人</span>
</dt>
<?php } if($activity['expiration']) { ?>
<dt><em>报名截止:</em> <span><?php echo $activity['expiration'];?></span></dt>
<?php } ?>
<dt>
<?php if($post['invisible'] == 0) { if($applied && $isverified < 2) { } elseif(!$activityclose) { ?>
                        <?php if($isverified != 2) { ?>
                        <?php } else { ?>
                        <p class="pns mtn">
                            <input value="完善资料" name="ijoin" id="ijoin" />
                        </p>
                        <?php } } } ?>
</dt>
<?php } ?>
</dl>

</div>
<div id="postmessage_<?php echo $post['pid'];?>" class="postmessage"><?php echo $post['message'];?></div>
<?php if($_G['uid'] && !$activityclose && (!$applied || $isverified == 2)) { ?>
<div class="acty_joins"><a href="#activityjoin_<?php echo $post['pid'];?>" class="popup elecnation_dr">我要参加</a></div>
<div id="activityjoin_<?php echo $post['pid'];?>" popup="true" class="bm mtn">
    	<div class="pd5 bm_chact">
        <div class="xw1">我要参加</div>
<?php if($_G['forum']['status'] == 3 && helper_access::check_module('group') && $isgroupuser != 'isgroupuser') { ?>
        <p>您还不是本 <?php echo $_G['setting']['navs']['3']['navname'];?> 的成员不能参与此活动</p>
        <p><a href="forum.php?mod=group&amp;action=join&amp;fid=<?php echo $_G['fid'];?>&amp;mobile=2" class="xi2">点此处马上加入 <?php echo $_G['setting']['navs']['3']['navname'];?></a></p>
<?php } else { ?>
<form name="activity" id="activity" method="post" autocomplete="off" action="forum.php?mod=misc&amp;action=activityapplies&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;pid=<?php echo $post['pid'];?><?php if($_GET['from']) { ?>&amp;from=<?php echo $_GET['from'];?><?php } ?>&amp;mobile=2" >
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />

<?php if($_G['setting']['activitycredit'] && $activity['credit'] && !$applied) { ?><p class="xi1">注意：参加此活动将扣除您 <?php echo $activity['credit'];?> <?php echo $_G['setting']['extcredits'][$_G['setting']['activitycredit']]['title'];?></p><?php } ?>
                <?php if($activity['cost']) { ?>
                   <p>支付方式 <label><input class="pr" type="radio" value="0" name="payment" id="payment_0" checked="checked" />承担自己应付的花销</label> <label><input class="pr" type="radio" value="1" name="payment" id="payment_1" />支付 </label> <input name="payvalue" size="3" class="txt_s" /> 元</p>
                <?php } ?>
                <?php if(!empty($activity['ufield']['userfield'])) { ?>
                    <?php if(is_array($activity['ufield']['userfield'])) foreach($activity['ufield']['userfield'] as $fieldid) { ?>                    <?php if($settings[$fieldid]['available']) { ?>
                        <strong><?php echo $settings[$fieldid]['title'];?><span class="xi1">*</span></strong>
                        <?php echo $htmls[$fieldid];?>
                    <?php } ?>
                    <?php } ?>
                <?php } ?>
                <?php if(!empty($activity['ufield']['extfield'])) { ?>
                    <?php if(is_array($activity['ufield']['extfield'])) foreach($activity['ufield']['extfield'] as $extname) { ?>                        <?php echo $extname;?><input type="text" name="<?php echo $extname;?>" maxlength="200" class="txt" value="<?php if(!empty($ufielddata)) { ?><?php echo $ufielddata['extfield'][$extname];?><?php } ?>" />
                    <?php } ?>
                <?php } ?>
<div class="pns_ch">
<?php if($_G['setting']['activitycredit'] && $activity['credit'] && checklowerlimit(array('extcredits'.$_G['setting']['activitycredit'] => '-'.$activity['credit']), $_G['uid'], 1, 0, 1) !== true) { ?>
<p class="xi1"><?php echo $_G['setting']['extcredits'][$_G['setting']['activitycredit']]['title'];?> 不足<?php echo $activity['credit'];?></p>
<?php } else { ?>
<input type="hidden" name="activitysubmit" value="true">
<em class="xi1" id="return_activityapplies"></em>
<button type="submit" class="sub_btns" ><span>提交</span></button>
<a href="javascript:void(0);" onclick="popup.close();" style="margin-left:0.5rem;">取消</a>
<?php } ?>
</div>
</form>

<script type="text/javascript">
function succeedhandle_activityapplies(locationhref, message) {
showDialog(message, 'notice', '', 'location.href="' + locationhref + '"');
}
</script>
<?php } ?>
    	</div>
   </div>
<?php } elseif($_G['uid'] && !$activityclose && $applied) { ?>
<div id="activityjoincancel" class="bm mtn">
<div class="bm_c pd5">
        <form name="activity" method="post" autocomplete="off" action="forum.php?mod=misc&amp;action=activityapplies&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;pid=<?php echo $post['pid'];?><?php if($_GET['from']) { ?>&amp;from=<?php echo $_GET['from'];?><?php } ?>">
        <input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
        <p style="display:none">
            留言<input type="text" name="message" maxlength="200" class="px" value="" />
        </p>
        <p class="mtn">
        <button type="submit" name="activitycancel" value="true" class="cacel_btns"><span>取消报名</span></button>
        </p>
        </form>
    </div>
</div>
<?php } if($applylist) { ?>
<div class="act_vtable">
     <p class="pd5">已通过 (<?php echo $applynumbers;?> 人)</p>
     <ul class="act_vul">
     	 <li class="fir">用户</li>
         <?php if($activity['cost']) { ?>
         <li class="fir">每人花销</li>
         <?php } ?>
         <li class="fir">申请时间</li>
     </ul>
    <table class="act_vtd" cellpadding="0" cellspacing="0" width="100%">
        <?php if(is_array($applylist)) foreach($applylist as $apply) { ?>            <tr>
                <td>
                    <a target="_blank" href="home.php?mod=space&amp;uid=<?php echo $apply['uid'];?>&amp;do=profile"><?php echo $apply['username'];?></a>
                </td>
                <?php if($activity['cost']) { ?>
                <td><?php if($apply['payment'] >= 0) { ?><?php echo $apply['payment'];?> 元<?php } else { ?>自付<?php } ?></td>
                <?php } ?>
                <td><?php echo $apply['dateline'];?></td>
            </tr>
        <?php } ?>
    </table>
    <?php if($applynumbers > $_G['setting']['activitypp']) { ?>
<div class="actMors cl">		
<a href="forum.php?mod=misc&amp;action=getactivityapplylist&amp;tid=<?php echo $_G['tid'];?>">查看更多>></a>
</div>
<?php } ?>
</div>
<?php } if($applylistverified) { ?>
<div class="act_vtable">
<p class="pd5">
暂未通过 (<?php echo $noverifiednum;?> 人)
<?php if($post['invisible'] == 0 && ($_G['forum_thread']['authorid'] == $_G['uid'] || (in_array($_G['group']['radminid'], array(1, 2)) && $_G['group']['alloweditactivity']) || ( $_G['group']['radminid'] == 3 && $_G['forum']['ismoderator'] && $_G['group']['alloweditactivity']))) { ?>					
<a href="forum.php?mod=misc&amp;action=activityapplylist&amp;tid=<?php echo $_G['tid'];?>&amp;pid=<?php echo $post['pid'];?><?php if($_GET['from']) { ?>&amp;from=<?php echo $_GET['from'];?><?php } ?>" title="管理">管理</a>
<?php } ?>
</p>
<ul class="act_vul">
     	 <li class="fir">用户</li>
         <?php if($activity['cost']) { ?>
         <li class="fir">每人花销</li>
         <?php } ?>
         <li class="fir">申请时间</li>
     	</ul>
<table class="act_vtd" cellpadding="0" cellspacing="0"><?php if(is_array($applylistverified)) foreach($applylistverified as $apply) { ?><tr>
<td>
<!-- <a target="_blank" href="home.php?mod=space&amp;uid=<?php echo $apply['uid'];?>" class="ratl vm"><?php echo avatar($apply['uid'], 'small'); ?></a> -->
<a target="_blank" href="home.php?mod=space&amp;uid=<?php echo $apply['uid'];?>&amp;do=profile"><?php echo $apply['username'];?></a>
</td>					
<?php if($activity['cost']) { ?>
<td><?php if($apply['payment'] >= 0) { ?><?php echo $apply['payment'];?> 元<?php } else { ?>自付<?php } ?></td>
<?php } ?>
<td><?php echo $apply['dateline'];?></td>
</tr>
<?php } ?>
</table>
</div>
<?php } ?>


