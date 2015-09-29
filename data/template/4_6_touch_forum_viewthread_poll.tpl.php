<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<div id="postmessage_<?php echo $post['pid'];?>" class="postmessage"><?php echo $post['message'];?></div>

<div class="poll_chbd">
<form id="poll" name="poll" method="post" autocomplete="off" action="forum.php?mod=misc&amp;action=votepoll&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;pollsubmit=yes<?php if($_GET['from']) { ?>&amp;from=<?php echo $_GET['from'];?><?php } ?>&amp;quickforward=yes&amp;mobile=2" >
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<?php if($allowvotepolled) { ?>
<div>
<?php if($multiple) { ?><strong>多选投票</strong><?php if($maxchoices) { ?>：( 最多可选 <?php echo $maxchoices;?> 项 )<?php } } else { ?><strong>单选投票</strong>：<?php } if($visiblepoll && $_G['group']['allowvote']) { ?> , 投票后结果可见<?php } ?>
</div>

<?php if($_G['forum_thread']['remaintime']) { ?>
<p>
距结束还有:
<span class="xg1">
<?php if($_G['forum_thread']['remaintime']['0']) { ?><?php echo $_G['forum_thread']['remaintime']['0'];?> 天<?php } if($_G['forum_thread']['remaintime']['1']) { ?><?php echo $_G['forum_thread']['remaintime']['1'];?> 小时<?php } ?>
<?php echo $_G['forum_thread']['remaintime']['2'];?> 分钟
</span>
</p>
<?php } elseif($expiration && $expirations < TIMESTAMP) { ?>
<p><strong>投票已经结束</strong></p>
<?php } ?>
<div class="poll_option">
        <?php if(is_array($polloptions)) foreach($polloptions as $key => $option) { ?>            <p>            
                <input type="<?php echo $optiontype;?>" id="option_<?php echo $key;?>" name="pollanswers[]" class="selet" value="<?php echo $option['polloptionid'];?>" <?php if($_G['forum_thread']['is_archived']) { ?>disabled="disabled"<?php } ?>  />
            <label for="option_<?php echo $key;?>"><?php echo $key;?>.<?php echo $option['polloption'];?></label>
           
            </p>
        <?php } ?>
        	<input type="submit" name="pollsubmit" id="pollsubmit" class="pollsubmits" value="提交" />       
<?php } else { ?>
        <div class="poll_conman">
             <p><strong>投票结果如下：</strong></p>
        <?php if(is_array($polloptions)) foreach($polloptions as $key => $option) { ?>            <div class="poll_result">
                 <label for="option_<?php echo $key;?>"><?php echo $key;?>.<?php echo $option['polloption'];?></label>
                 <?php if(!$visiblepoll) { ?>
                 <div class="option_lr">
                 <span><i style="width:<?php echo $option['percent'];?>%; background:#eb6100;"></i></span>
                 <em><font><?php echo $option['votes'];?></font>票 (<?php echo $option['percent'];?>% )</em>
                 </div>
                 <?php } ?>
            </div>
        <?php } ?>
        </div>
<?php } ?>
</div>
</form>
</div>
