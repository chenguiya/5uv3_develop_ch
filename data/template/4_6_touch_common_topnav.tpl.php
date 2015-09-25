<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<nav class="navbar">
    <div class="inner">
        <div class="left">
            <?php if($_GET['op'] == 'checkuser') { ?>
            <a href="javascript: history.go(-1);"><i class="iconfont icon-angle-left"></i></a>
            <?php } else { ?>
            <a class="logo" href="<?php echo $_G['siteurl'];?>"><img
                    src="template/usportstyle/touch/common/images/logo-03.png" alt="5u体育"></a>
            <?php } ?>
        </div>
        
        <div class="right">
        	<a href="home.php?mod=space&amp;do=group&amp;mycenter=1&amp;mobile=2"><i class="iconfont icon-qmh"></i></a>            
            <a href="home.php?mod=space&amp;do=profile&amp;mycenter=1&amp;mobile=2"><i class="iconfont icon-me"></i></a>    
        </div>
    </div>
</nav>

<div class="ymain">
<ul class="ysub-navbar">
    <li<?php if(APPTYPEID == 2 && $mod == 'index') { ?> class="active"<?php } ?>><a href="<?php if(APPTYPEID == 2 && $mod == 'index') { ?>javascript:;<?php } else { ?>forum.php?mobile=yes<?php } ?>">广场</a></li>
    <li<?php if(APPTYPEID == 2 && $mod == 'activity') { ?> class="active"<?php } ?>><a href="<?php if(APPTYPEID == 2 && $mod == 'activity') { ?>javascript:;<?php } else { ?>forum.php?mod=activity<?php } ?>">活动</a></li>
    <li<?php if(APPTYPEID == 3) { ?> class="active"<?php } ?>><a href="<?php if(APPTYPEID == 3) { ?>javascript:;<?php } else { ?>group.php<?php } ?>">球迷会</a></li>
    <!-- <li><a href="#">球迷联赛</a></li> -->
</ul>