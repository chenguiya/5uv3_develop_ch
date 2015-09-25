<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('activity_applylist');?><?php include template('common/header'); ?><header class="chheader">
        <a href="javascript:history.back();" class="head_back">&nbsp;</a>
        <span id="return_<?php echo $_GET['handlekey'];?>"><?php if($isactivitymaster) { ?>活动报名者管理<?php } else { ?>活动报名者<?php } ?></span>
</header>
<section class="ch_mians">
<form id="applylistform" method="post" autocomplete="off" action="forum.php?mod=misc&amp;action=activityapplylist&amp;tid=<?php echo $_G['tid'];?>&amp;applylistsubmit=yes&amp;infloat=yes<?php if(!empty($_GET['from'])) { ?>&amp;from=<?php echo $_GET['from'];?><?php } ?>"<?php if(!empty($_GET['infloat']) && empty($_GET['from'])) { ?> onsubmit="ajaxpost('applylistform', 'return_<?php echo $_GET['handlekey'];?>', 'return_<?php echo $_GET['handlekey'];?>', 'onerror');return false;"<?php } ?>>
<div class="ch_manabox">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<input type="hidden" name="operation" id="operation" value="" />
<?php if(!empty($_GET['infloat'])) { ?><input type="hidden" name="handlekey" value="<?php echo $_GET['handlekey'];?>" /><?php } ?>
<div class="c floatwrap">

<table class="float_table folat_label" cellspacing="0" cellpadding="0" width="100%">
<thead>
<tr>
<?php if($isactivitymaster) { ?><th width="9%">&nbsp;</th><?php } ?>
<th class="labe_one">申请者</th>
<th class="labe_two">真实姓名</th>
<th class="labe_three">手机号码</th>
</tr>
</thead>
</table>
<div class="folat_label" id="folat_labelModel"><?php if(is_array($applylist)) foreach($applylist as $apply) { ?><label>
<?php if($isactivitymaster) { if($apply['uid'] != $_G['uid']) { ?>
<input type="checkbox" name="applyidarray[]" class="pc pcyver" value="<?php echo $apply['applyid'];?>" />
<?php } else { ?>
<input type="checkbox" class="pc" disabled="disabled" />
<?php } } ?>
<span class="labe_one"><?php echo $apply['username'];?></span>
<span class="labe_two">
<?php if($apply['ufielddatanew']['userfield']['realname']) { ?> 
    <?php echo $apply['ufielddatanew']['userfield']['realname'];?> 
    <?php } else { ?>
    --
    <?php } ?>
    </span>
    <span class="labe_three">
    <?php if($apply['ufielddatanew']['userfield']['mobile']) { ?> 
    <?php echo $apply['ufielddatanew']['userfield']['mobile'];?> 
    <?php } else { ?>
    --
    <?php } ?>
</span>
</label>
<?php } ?>
</div>
<div class="folat_more"><a href="javascript:void(0);" class="act_more" id="<?php if($_GET['pid']) { ?>applylist_manage<?php } else { ?>applylist_more<?php } ?>" data-pid="<?php echo $_GET['pid'];?>" data-id="<?php echo $_G['tid'];?>" page="2" totalpage="<?php echo $maxpage;?>">加载更多</a></div>
</div>

</div>

<?php if($isactivitymaster) { ?>
<div class="ch_pnscom">
<label<?php if(!empty($_GET['infloat'])) { ?> class="z"<?php } ?>><input class="pc" type="checkbox" name="chkall" onclick="checkall(this.form, 'applyid')" />全选 </label>
<div class="ch_input">
     <button class="formdialog pnsc pn_btns" type="submit" value="true" id='applylistsubmit' name="applylistsubmit"><span>批准</span></button>
     <button class="formdialog pnsc" type="submit" value="true" id='applylistsubmit_delete' name="applylistsubmit"><span>拒绝</span></button>
</div>
</div>
<?php } ?>
</form>
</section>

<?php if(!empty($_GET['infloat'])) { ?>
<script type="text/javascript" reload="1">
function succeedhandle_<?php echo $_GET['handlekey'];?>(locationhref) {
ajaxget('forum.php?mod=viewthread&tid=<?php echo $_G['tid'];?>&viewpid=<?php echo $_GET['pid'];?>', 'post_<?php echo $_GET['pid'];?>');
hideWindow('<?php echo $_GET['handlekey'];?>');
}
</script>
<?php } ?>
<script>
function checkall(form, prefix, checkall) {
var checkall = checkall ? checkall : 'chkall';
count = 0;
for(var i = 0; i < form.elements.length; i++) {
var e = form.elements[i];
if(e.name && e.name != checkall && !e.disabled && (!prefix || (prefix && e.name.match(prefix)))) {
e.checked = form.elements[checkall].checked;
if(e.checked) {
count++;
}
}
}
return count;
}
</script><?php include template('common/footer'); ?>