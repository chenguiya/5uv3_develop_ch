<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('showmessage');?>
<?php $space['isfriend'] = $space['self'];
if(in_array($_G['uid'], (array)$space['friends'])) $space['isfriend'] = 1;
space_merge($space, 'count');?><?php if($param['login']) { if($_G['inajax']) { dheader('Location:member.php?mod=logging&action=login&inajax=1&infloat=1');exit;?><?php } else { dheader('Location:member.php?mod=logging&action=login');exit;?><?php } } include template('common/header'); if($_G['inajax']) { ?>
<div class="tip">
<dt id="messagetext">
<p><?php echo $show_message;?></p>
        <?php if($_G['forcemobilemessage']) { ?>
        	<p>
            	<a href="<?php echo $_G['setting']['mobile']['pageurl'];?>" class="mtn">继续访问</a><br />
                <a href="javascript:history.back();">返回上一页</a>
            </p>
        <?php } if($url_forward && !$_GET['loc']) { ?>
<!--<p><a class="grey" href="<?php echo $url_forward;?>">点击此链接进行跳转</a></p>-->
<script type="text/javascript">
setTimeout(function() {
window.location.href = '<?php echo $url_forward;?>';
}, '3000');
</script>
<?php } elseif($allowreturn) { ?>
<p><input type="button" class="button" onclick="popup.close();" value="关闭"></p>
<?php } ?>
</dt>
</div>
<?php } else { ?>

<!-- header start -->
<nav class="navbar">
    <div class="inner">
        <div class="left"><a class="logo" href="forum.php?mod=group&amp;fid=1367&amp;mobile=2">
            <img src="template/usportstyle/touch/common/images/logo-03.png" alt="#"></a>
        </div>
        <div class="right">
            <a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile&amp;mycenter=1"><i class="iconfont icon-me"></i></a>
        </div>
    </div>
</nav>
<!-- header end -->

<!-- main jump start -->
<center style="padding-top:80px;">
        <img src="template/usportstyle/touch/common/images/zizi_mian.png">
</center>
<div class="jump_c">
<p><?php echo $show_message;?></p>
    <?php if($_G['forcemobilemessage']) { ?>
<p>
            <a href="<?php echo $_G['setting']['mobile']['pageurl'];?>" class="mtn">继续访问</a><br />
            <a href="javascript:history.back();">返回上一页</a>
        </p>
    <?php } if($url_forward) { ?>
<p><a class="grey" href="<?php echo $url_forward;?>">点击此链接进行跳转</a></p>
<?php } elseif($allowreturn) { ?>
<p><a class="grey" href="javascript:history.back();">[ 点击这里返回上一页 ]</a></p>
<?php } ?>
</div>
<!-- main jump end -->

<?php } include template('common/footer'); ?>