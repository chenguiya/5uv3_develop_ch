<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('index');?><?php include template('common/header'); include template('common/topnav'); ?>    <ul class="hsub_team">
    <?php if(is_array($showleagues)) foreach($showleagues as $league) { ?>    	<li id="league_<?php echo $league['fid'];?>"><a href="javascript:;"><?php echo $league['name'];?></a></li>
    	<!-- <li class="cur"><a href="javascript:;"><?php echo $vo['name'];?></a></li> -->
    	<?php } ?>        
    </ul>
    <?php if(is_array($showleagues)) foreach($showleagues as $league) { ?>    <div id="club_<?php echo $league['fid'];?>" class="hst_club swiper-container" style="<?php if($league['fid'] != 1) { ?>display:none;<?php } ?>height: 60px;overflow: hidden;">    	
        <div class="hclub_ul swiper-wrapper">
        <?php if(is_array($league['clubs'])) foreach($league['clubs'] as $club) { ?>            <!-- <li class="active"><a href=""><img src="template/usportstyle/touch/common/images/team_pic1.png" alt="" width="35" height="35"></a></li> -->
            <div id="club_<?php echo $club['fid'];?>" class="hclub_li swiper-slide">
                <a href="javascript:;" data-href="forum.php?mod=fansclub&amp;fid=<?php echo $club['fid'];?>"><img src="data/attachment/common/<?php echo $club['icon'];?>" alt="" width="35" height="35"></a>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    <div class="hsub_content">
        <ul>
        <?php if(is_array($fansclub)) foreach($fansclub as $vo) { ?>            <li>
                <a href="forum.php?mod=group&amp;fid=<?php echo $vo['fid'];?>&amp;mobile=2">
                    <div class="hsc_head"><img src="<?php echo $vo['icon'];?>" alt="<?php echo $vo['name'];?>"></div>
                    <div class="hsc_mes">
                        <div class="hsm_t"><?php echo $vo['name'];?><?php if($vo['verify_org'] == 3) { ?>&nbsp;<span class="certif">官</span><?php } elseif($vo['verify_5u'] == 3) { ?>&nbsp;<span>5U认证</span><?php } ?></div>
                        <div class="hsm_b">
                            <span>人数：</span><span><?php echo $vo['members'];?></span><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><span><span>地区：</span><span><?php echo $vo['province_name'];?> </span><?php echo $vo['city_name'];?></span>
                        </div>
                    </div>
                    <div class="hsc_right"></div>
                </a>
            </li>
            <?php } ?>            
        </ul>
    </div>
</div>
<script id="group-index-templ" type="text/x-dot-template">
    {{ for (var key in it) { }}
    <li>
        <a href="forum.php?mod=group&amp;fid={{=it[key].fid}}&amp;mobile=2">
            <div class="hsc_head"><img src="{{=it[key].icon}}" alt=""></div>
            <div class="hsc_mes">
                <div class="hsm_t">{{=it[key].name}}</div>
                <div class="hsm_b">
                    <span>人数：</span><span>{{=it[key].members}}</span><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><span><span>地区：</span><span>{{=it[key].province_name}}</span>{{=it[key].city_name}}</span>
                </div>
            </div>
            <div class="hsc_right"></div>
        </a>
    </li>
    {{ } }}
</script>
<script type="text/javascript">
    new Swiper('.swiper-container', {
        freenMode: true,
        slideActiveClass: 'slideActive',
        width: '50'
    })
</script>