<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('space_thread');?><?php include template('common/header'); ?><!--别人 start-->
<?php if($member['self'] == 0) { ?>
<header class="cenhead">
        <a href="javascript:;" onclick="history.go(-1)" class="head_back"></a>
        <div class="cen_img">
             <p><img src="<?php echo avatar($space[uid], big, true);?>" alt="<?php echo $discuz_userss;?>" /><?php if($space['gender']==1) { ?><i class="fontweb_icon ic_nan"></i><?php } elseif($space['gender']==2) { ?><i class="fontweb_icon ic_nv"></i><?php } elseif($space['gender']==0) { ?><i class="fontweb_icon ic_secret"></i><?php } ?></p>   
        </div>
        <div class="cen_names"><?php echo $space['username'];?></div>
        <div class="cen_introd"><?php if($space['bio']) { ?>简介：<?php echo $space['bio'];?><?php } else { ?>简介：这家伙很懒，什么都没有留下。<?php } ?></div>
</header> 
<?php } ?>
<!--别人 end-->

<?php if($member['self'] == 1) { ?>
<nav class="navbar">
<div class="inner">
<div class="left"><a href="javascript:;" onclick="history.go(-1)"><i class="iconfont icon-angle-left"></i></a></div>
<div class="center">我的帖子</div>
</div>
</nav>
<?php } ?>

<div class="ymain ymain-padding" <?php if($member['self'] == 0) { ?>style="padding-top:0.5rem;" <?php } ?>>
<ul id="mypost" class="ylist">

<?php if($subjectarrlen == 1) { if(is_array($subjectarr)) foreach($subjectarr as $key=>$value) { ?><li class="row" href="forum.php?mod=viewthread&amp;tid=<?php echo $key;?>">
<div class="author-thumb">
<a href="home.php?mod=space&amp;uid=<?php echo $space['uid'];?>&amp;do=thread&amp;from=thread&amp;mobile=2"><img src="<?php echo avatar($space[uid], big, true);?>" alt=""/></a>
</div>

<div class="row-item">
<div class="title"><a href="forum.php?mod=viewthread&amp;tid=<?php echo $key;?>" target="_self" ><?php echo $value['subject'];?></a></div>
<span class="<?php if($value['dateline'] < 60) { ?> s accent <?php } else { ?> s <?php } ?>"><?php echo $value['dateline'];?></span>
</div>
<div class="row-badge-wrap">
<a class="social" href="forum.php?mod=viewthread&amp;tid=<?php echo $key;?>"><i class="fa fa-commenting-o"></i><?php echo $value['replies'];?></a>
</div>
</li>
<?php } } else { ?>
还没有帖子，快去<a href="forum.php?mod=group&amp;fid=1367&amp;mobile=2"><b>发帖吧...</b></a>
<?php } ?>
</ul>
</div>
<script>
$("#mypost li").click(function () {
window.location.href = $(this).attr("href");
});
</script><?php include template('common/footer'); ?>