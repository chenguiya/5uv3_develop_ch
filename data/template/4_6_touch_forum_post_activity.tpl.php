<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<link rel="stylesheet" href="template/usportstyle/touch/common/mobtime.css" type="text/css" media="all">
<li class="exfm">
<div class="sinf sppoll">
<dl>
<dt>活动时间:</dt>
<dd>
    <input type="text" name="starttimefrom[1]" id="starttimefrom_1" class="demo_datetime pxch" onclick="showcalendar(event, this, true)" autocomplete="off" value="<?php echo $activity['starttimefrom'];?>" tabindex="1" /> <span class="rq">*</span>

</dd>
 </dl>
 <dl>
      <dt>活动截止:</dt>
     <dd>
       <input type="text" autocomplete="off" id="starttimeto" name="starttimeto" class="demo_datetime pxch" value="<?php if($activity['starttimeto']) { ?><?php echo $activity['starttimeto'];?><?php } ?>" tabindex="1" /> <span class="rq">*</span>
     </dd>
 </dl>
 <input type="hidden" id="activitytime" name="activitytime" value="1">
 <dl>
<dt><label for="activityplace">活动地点:</label></dt>
<dd>
<input type="text" name="activityplace" id="activityplace" class="pxch" value="<?php echo $activity['place'];?>" tabindex="1" /> <span class="rq">*</span>
</dd>

     </dl>
 <dl>
     <dt><label for="activityexpiration">报名截止:</label></dt>
<dd class="hasd cl">
<span>
<input type="text" class="demo_datetime pxch" name="activityexpiration" id="activityexpiration" onclick="showcalendar(event, this, true)" autocomplete="off" value="<?php echo $activity['expiration'];?>" tabindex="1" />
</span>
<a href="javascript:;" class="dpbtn" onclick="showselect(this, 'activityexpiration')"></a>
</dd>
 </dl>
 <dl>
 <input type="hidden" id="activityclass" name="activityclass" value="默认分类">
<?php if($_G['setting']['activityfield']) { ?>
<dt>必填选项:</dt>
<dd>
<ul class="xl2 cl"><?php if(is_array($_G['setting']['activityfield'])) foreach($_G['setting']['activityfield'] as $key => $val) { ?><li><label for="userfield_<?php echo $key;?>"><input type="checkbox" name="userfield[]" id="userfield_<?php echo $key;?>" class="pc" value="<?php echo $key;?>"<?php if($activity['ufield']['userfield'] && in_array($key, $activity['ufield']['userfield'])) { ?> checked="checked"<?php } ?> /><?php echo $val;?></label></li>
<?php } ?>
</ul>
</dd>
<?php } if($_G['setting']['activityextnum']) { ?>
<dt><label for="extfield">扩展资料项:</label></dt>
<dd>
<textarea name="extfield" id="extfield" class="pt" cols="50" style="width: 270px;"><?php if($activity['ufield']['extfield']) { ?><?php echo $activity['ufield']['extfield'];?><?php } ?></textarea><br />每行代表一个项目，最多 <?php echo $_G['setting']['activityextnum'];?> 项
</dd>
<?php } ?>
</dl>
<?php if($allowpostimg) { ?>
<dl>
<dt>活动图片:</dt>
<dd><a href="javascript:;" class=" acit_photo">
<input type="file" name="Activityimg" id="activityimg" style="opacity:0;"/>
</a>
</dd>
</dl>
<dl>
   <dt></dt>
  <dd><ul id="activityimglist" class="post_imglist cl"></ul></dd>		
</dl>	
<?php } ?>
</div>
</li>

<script type="text/javascript" reload="1">
simulateSelect('gender');
function checkvalue(value, message){
if(!value.search(/^\d+$/)) {
$(message).innerHTML = '';
} else {
$(message).innerHTML = '<b>填写无效</b>';
}
}

EXTRAFUNC['validator']['special'] = 'validateextra';
function validateextra() {
if($('postform').starttimefrom_0.value == '' && $('postform').starttimefrom_1.value == '') {
showDialog('抱歉，请输入活动时间', 'alert', '', function () { if($('activitytime').checked) {$('postform').starttimefrom_1.focus();} else {$('postform').starttimefrom_0.focus();} });
return false;
}
if($('postform').activityplace.value == '') {
showDialog('抱歉，请输入活动地点', 'alert', '', function () { $('postform').activityplace.focus() });
return false;
}
if($('postform').activityclass.value == '') {
showDialog('抱歉，请输入活动类别', 'alert', '', function () { $('postform').activityclass.focus() });
return false;
}
return true;
}
function activityaid_upload(aid, url) {
$('activityaid_url').value = url;
updateactivityattach(aid, url, '<?php echo $_G['setting']['attachurl'];?>forum');
}
</script>
<script src="<?php echo STATICURL;?>js/mobile/mobtime.js?<?php echo VERHASH;?>" type="text/javascript" type="text/javascript"></script>
<script>
        $(function () {
                $('.demo_datetime').mobiscroll().datetime({theme: "mobiscroll", mode: "scroller",display: "modal", lang: "zh",minDate: new Date(2014,3,10,9,22),maxDate: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),stepMinute: 5 });
        })
    </script>