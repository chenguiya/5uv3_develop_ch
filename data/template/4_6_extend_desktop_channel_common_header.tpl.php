<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<div class="bread">
<a href="/" target="_blank">5U体育</a>&nbsp;&gt;&nbsp;
<?php if($_GET['tpl']=='channel_index') { ?><a href="group/" target="_blank">球迷会广场</a>&nbsp;&gt;&nbsp;<?php } if($_GET['tpl']!='channel_index') { ?><a href="group/<?php echo $_G['fid'];?>/" target="_blank"><?php echo $_G['forum']['name'];?>频道</a>
<?php } else { ?><a href="group/<?php echo $_G['fid'];?>/" target="_self"><?php echo $_G['forum']['name'];?>频道</a><?php } if($_GET['tpl']=='channel_video') { ?><a href="<?php echo $_SERVER['REQUEST_URI'];?>" target="_self">&nbsp;&gt;&nbsp;<?php echo $_G['forum']['name'];?>视频</a><?php } if($_GET['tpl']=='channel_picture') { ?><a href="<?php echo $_SERVER['REQUEST_URI'];?>" target="_self">&nbsp;&gt;&nbsp;<?php echo $_G['forum']['name'];?>图片</a><?php } if($_GET['tpl']=='channel_news') { ?><a href="<?php echo $_SERVER['REQUEST_URI'];?>" target="_self">&nbsp;&gt;&nbsp;<?php echo $_G['forum']['name'];?>新闻</a><?php } if($_GET['id']=='fansclub:forumfansclub') { ?><a href="<?php echo $_SERVER['REQUEST_URI'];?>" target="_self">&nbsp;&gt;&nbsp;<?php echo $_G['forum']['name'];?>球迷会分会</a><?php } if($_GET['id']=='playerdata:playerdata' && $_GET['ac']=="data") { ?><a href="<?php echo $_SERVER['REQUEST_URI'];?>" target="_self">&nbsp;&gt;&nbsp;<?php echo $_G['forum']['name'];?>数据</a><?php } if($_GET['id']=='playerdata:playerdata' && $_GET['ac']=="formation") { ?><a href="<?php echo $_SERVER['REQUEST_URI'];?>" target="_self">&nbsp;&gt;&nbsp;<?php echo $_G['forum']['name'];?>阵容</a><?php } ?>
</div>
<!--共用头部-->
<div class="teamHead">
     <div class="teamImg"><?php if($_G['forum']['banner']) { ?><img src="<?php echo $_G['forum']['banner'];?>" width="1000" height="200"><?php } else { ?><img src="<?php echo $_G['config']['static'];?>/images/teambg.jpg" width="1000" height="200"><?php } ?></div>

 <div class="teamLogo"><img src="<?php echo $_G['setting']['attachurl'];?>common/<?php echo $_G['forum']['icon'];?>" width="110" height="110" alt="" title="" /></div>
     <div class="teamInfo">
    	<p class="teamName"><?php echo $_G['forum']['name'];?>频道</p>
    	<?php if($_G['forum']['type'] == 'forum') { ?>
    	<div class="teamul">
             <?php if($team_scoreboard->league_name && $team_scoreboard->ranking && $team_scoreboard->ranking) { ?><p><span>联赛：<?php echo $team_scoreboard->league_name;?></span><span>排名：<?php echo $team_scoreboard->ranking;?></span><span>主教练：<?php echo $coach->name;?></span></p><?php } ?>
             <p><span>总帖：<?php echo $_G['forum']['threads'];?></span><span>今日：<?php echo $_G['forum']['todayposts'];?></span><span>球迷会：<?php echo intval($branchnum);?></span></p>
         </div>
         <?php } elseif($_G['forum']['type'] == 'sub') { ?>
         <div class="teamul">
             <p><span>联赛：<?php echo $_G['forum']['league_name'];?></span><span>所属俱乐部：<?php echo $_G['forum']['fup_name'];?></span></p>
             <p><span>生日：<?php echo $player['birthday'];?></span><span>身高：<?php echo $player['height'];?></span></p>
         </div>
         <?php } ?>
    </div>
</div>
<!--足球圈或篮球圈的高亮-->
<script language="javascript">
jQuery(document).ready(function($) {
<?php if(in_array($_G['forum']['fup'], array(1,54,64,81,82,185))) { ?>
$(".chnav.cl").find("li").removeClass("a");
$(".chnav.cl").find("li:eq(1)").addClass("a");
<?php } elseif(in_array($_G['forum']['fup'], array(129,194))) { ?>
$(".chnav.cl").find("li").removeClass("a");
$(".chnav.cl").find("li:eq(2)").addClass("a");
<?php } ?>
});
</script>

<!--光用头部end-->
<div class="teamMenu">
<ul>		
<li <?php if(empty($_GET['typeid']) && $_SERVER['PHP_SELF'] != '/plugin.php' ) { ?>class="active"<?php } ?>>
<?php if($forum_is_open) { ?><a href="group/<?php echo $_G['fid'];?>/" title="论坛">首页</a>
<?php } else { ?><span>首页</span>
<?php } ?></li>	
<?php if($_G['forum']['threadtypes']) { if(is_array($_G['forum']['threadtypes']['types'])) foreach($_G['forum']['threadtypes']['types'] as $id => $name) { ?>                
                <!--
                <?php if($_GET['typeid'] == $id) { ?>
                <li class="active"><a href="forum.php?mod=forumdisplay&amp;fid=<?php echo $_G['fid'];?><?php if($_GET['sortid']) { ?>&amp;filter=sortid&amp;sortid=<?php echo $_GET['sortid'];?><?php } if($_GET['archiveid']) { ?>&amp;archiveid=<?php echo $_GET['archiveid'];?><?php } ?>"><?php if($_G['forum']['threadtypes']['icons'][$id] && $_G['forum']['threadtypes']['prefix'] == 2) { ?><img class="vm" src="<?php echo $_G['forum']['threadtypes']['icons'][$id];?>" alt="" /> <?php } ?><?php echo $name;?><?php if($showthreadclasscount['typeid'][$id]) { ?><span class="xg1 num"><?php echo $showthreadclasscount['typeid'][$id];?></span><?php } ?></a></li>
                <?php } else { ?>
                <li><a href="forum.php?mod=forumdisplay&amp;fid=<?php echo $_G['fid'];?>&amp;filter=typeid&amp;typeid=<?php echo $id;?><?php echo $forumdisplayadd['typeid'];?><?php if($_GET['archiveid']) { ?>&amp;archiveid=<?php echo $_GET['archiveid'];?><?php } ?>"><?php if($_G['forum']['threadtypes']['icons'][$id] && $_G['forum']['threadtypes']['prefix'] == 2) { ?><img class="vm" src="<?php echo $_G['forum']['threadtypes']['icons'][$id];?>" alt="" /> <?php } ?><?php echo $name;?><?php if($showthreadclasscount['typeid'][$id]) { ?><span class="xg1 num"><?php echo $showthreadclasscount['typeid'][$id];?></span><?php } ?></a></li>
                <?php } ?>
                -->
                
                <?php if($name=='视频') { ?>
                <li <?php if($_GET['typeid'] == $id) { ?>class="active"<?php } ?>>
                    <?php if($forum_is_open) { ?><a href="video/<?php echo $_G['fid'];?>/<?php echo $id;?>/" title="视频">视频</a>
                    <?php } else { ?><span>视频</span>
                    <?php } ?></li>
                <?php } ?>
                <?php if($name=='图片') { ?>
                <li <?php if($_GET['typeid'] == $id) { ?>class="active"<?php } ?>>
                    <?php if($forum_is_open) { ?><a href="pic/<?php echo $_G['fid'];?>/<?php echo $id;?>/" title="图片">图片</a>
                    <?php } else { ?><span>图片</span>
                    <?php } ?></li>
                <?php } ?>
                <?php if($name=='新闻') { ?>
                <li <?php if($_GET['typeid'] == $id) { ?>class="active"<?php } ?>>
                    <?php if($forum_is_open) { ?><a href="news/<?php echo $_G['fid'];?>/<?php echo $id;?>/" title="新闻">新闻</a>
                    <?php } else { ?><span>新闻</span>
                    <?php } ?></li>
                <?php } ?>
        
            <?php } } ?>

        <li <?php if($_GET['id']=='fansclub:forumfansclub') { ?>class="active"<?php } ?>>
<?php if($forum_is_open) { ?><a href="fansclub/<?php echo $_G['fid'];?>/" title="球迷会">球迷会</a>
<?php } else { ?><span>球迷会</span>
<?php } ?></li>
<?php if($_G['forum']['type'] == 'forum') { ?>
        <!-- <li><a href="#" target="_blank" title="球员">球员</a></li> -->
<?php } ?>
        <!-- <li><a href="#" target="_blank" title="赛程">赛程</a></li> -->

        <li <?php if($_GET['id']=='playerdata:playerdata' && $_GET['ac']=="data") { ?> class="active"<?php } ?> >
<?php if($groupid !="129") { ?><a href="data/<?php echo $_G['fid'];?>/" title="数据">数据</a>
<?php } else { ?><span>数据</span>
<?php } ?> </li>
        <?php if($_G['forum']['type'] == 'forum') { ?>
<li <?php if($_GET['id']=='playerdata:playerdata' && $_GET['ac']=="formation") { ?>class="active"<?php } ?> >
<?php if($groupid !="129") { ?>
<a  href="formation/<?php echo $_G['fid'];?>/" title="阵容" >阵容</a>
<?php } else { ?><span>阵容</span>
<?php } ?></li>
<?php } if($_G['forum']['type'] == 'sub') { ?>
<li <?php if($_GET['id']=='playerdata:playerdata' && $_GET['ac']=="playerinfo") { ?>class="active"<?php } ?> >
<?php if($groupid !="129") { ?><a href="playerinfo/<?php echo $_G['fid'];?>/" title="资料">资料</a>
<?php } else { ?><span>资料</span>
<?php } ?></li>
<?php } ?>
    </ul>
</div>
<div class="jianju20"></div>


