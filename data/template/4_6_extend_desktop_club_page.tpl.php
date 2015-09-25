<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<div class="blank20"></div>
<div class="ad_banner">
<!-- 108811：首页-频道-列表-内容-顶部通栏 类型：固定广告位 尺寸：1000x0-->
<script type="text/javascript">
_acK({aid:108811,format:0,mode:1,gid:1,serverbaseurl:"afp.csbew.com/"});
</script>
</div>
<div class="wp ball_main clearfix" id="wp">
<div class="ball_left fl clearfix">
<div class="ball_club">
<h1 class="bt fl clearfix">推荐球迷会</h1>
<!--<div class="fr clearfix">
地区：<input type="text" id="" name="" value="黔西南布依族苗族自治州" placeholder="黔西南布依族苗族自治州"><i class="clubbtn"></i>
</div>-->
</div>
<div class="club_cont"><?php if(is_array($arr_group_show)) foreach($arr_group_show as $key => $value) { ?><div class="cc_unit fl clearfix">
<div class="cc_cell">
<a href="fans/topic/<?php echo $value['fid'];?>/" target="_blank" title="<?php echo $value['name'];?>"><img src="<?php echo $value['icon'];?>" width="110" height="110"></a>
<p><a href="fans/topic/<?php echo $value['fid'];?>/" target="_blank" title="<?php echo $value['name'];?>" <?php if($value['specialclub']) { ?> class="colorRed"<?php } ?>><?php echo $value['name'];?></a></p>
<span>地区：<?php echo $value['province_city'];?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span>人数：<?php echo $value['members'];?></span>
<h4>简介：<?php echo $value['description_short'];?></h4>
</div>
</div>
<?php if($key % 2 == 0 && $key == count($arr_group_show) - 1) { ?>
<div class="cc_unit fl clearfix">
<div class="cc_cell"><img style="visibility:hidden;" src="" width="110" height="110">
<p>&nbsp;</p>
<span>&nbsp;</span><span>&nbsp;</span>
<h4>&nbsp;</h4>
</div>
</div>
<?php } } ?>
</div>
<div>
<?php echo $multipage;?>
</div>
</div>
<div class="ball_right fr clearfix">
<div class="bg_publish"><div class="apbld"><a href="http://www.5usport.com/thread-33146.html" target="_blank"><i></i>申请建立</a></div></div>
<div class="chComd clearfix">
<div class="chHeader">
<h1 class="fl bt chComd_title">优秀会长</h1>
</div>
<div class="chBlock fl clearfix"><?php if(is_array($chairmanlists)) foreach($chairmanlists as $val) { ?><div class="chBoard">
<div class="board_c">
<div class="board_l fl clearfix"><a href="home.php?mod=space&amp;uid=<?php echo $val['uid'];?>" target="_blank"><img src="<?php echo $val['avatar'];?>" width="50" height="50"></a></div>
<div class="board_r fl clearfix">
<div class="r_up" >
<span><?php echo $val['username'];?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span><a <?php if($val['checkrights']) { ?>style="color:red;"<?php } ?> href="fans/topic/<?php echo $val['fid'];?>/" target="_blank"><?php echo $val['name'];?></a></span>
</div>
<div class="r_down">
<span>简介：<?php echo $val['sign'];?></span>
</div>
</div>
</div>
</div>
<?php } ?>
</div>
</div><?php include template('extend/desktop/sub_activity'); ?></div>
</div>
<div class="blank15"></div>
<div class="ad_banner">
<!-- 108817：首页-频道-列表-内容-底部通栏 类型：固定广告位 尺寸：1000x0-->
<script type="text/javascript">
_acK({aid:108817,format:0,mode:1,gid:1,serverbaseurl:"afp.csbew.com/"});
</script>
</div>
<div class="blank15"></div>