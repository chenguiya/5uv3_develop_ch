<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('activity');?><?php include template('common/header'); include template('common/topnav'); if($activitylists) { ?>
<section class="ch_activity">
<ul id="ch_actbox"><?php if(is_array($activitylists)) foreach($activitylists as $activity) { ?><li>
    <a href="forum.php?mod=viewthread&amp;tid=<?php echo $activity['tid'];?>" class="m_actbox">
       <span class="act_imgshow"><img src="<?php echo $activity['thumb'];?>"></span>
       <div class="act_info">
            <h3><?php echo $activity['title'];?></h3>
            <p class="act_time"><?php echo $activity['starttimefrom'];?><?php if($activity['starttimeto']) { ?>&nbsp;至&nbsp;<?php echo $activity['starttimeto'];?><?php } ?></p>
            <p class="act_btn"><?php if($activity['status']) { ?>立即参加<?php } else { ?>已结束<?php } ?></p>
       </div>
    </a>
</li>
<?php } ?>		
</ul>
</section>

<?php if($maxpage > 1) { ?>
<div class="act_page"><a href="javascript:void(0);" page="2" totalpage="<?php echo $maxpage;?>" class="act_more" id="actbox_More">加载更多</a></div>
<?php } } ?>
</div><?php include template('common/footer'); ?>