<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); if($hot_activity['data']) { ?>
<div class="chComd clearfix">
<div class="chHeader">
<h1 class="fl bt chComd_title">热门活动</h1>
<a href="activity" class="fr chComd_more">更多</a>
</div>
<div class="sidebar chComd"><?php if(is_array($hot_activity['data'])) foreach($hot_activity['data'] as $val) { ?>    <div class="cell">	    
        <a href="<?php echo $val['url'];?>" target="_blank" title="<?php echo $val['title'];?>"><img class="img" <?php if($val['thumbpath']) { ?>src="data/attachment/<?php echo $val['thumbpath'];?>"<?php } else { ?>src="data/attachment/<?php echo $val['pic'];?>"<?php } ?> width="320" height="170" alt="<?php echo $val['title'];?>">	
        <div class="desc">
           <span class="time"><?php echo $val['starttimefrom'];?><?php if($val['starttimeto']) { ?>&nbsp;-&nbsp;<?php echo $val['starttimeto'];?><?php } ?></span>
<?php if($val['status']) { ?>
<a href="<?php echo $val['url'];?>" class="btn-join" target="_blank">立即参加</a>
<?php } else { ?>
<a href="<?php echo $val['url'];?>" class="btn-over" target="_blank">已结束</a>
<?php } ?>
        </div>
    </div>
    <?php } ?>    
</div>
</div>
<?php } ?>