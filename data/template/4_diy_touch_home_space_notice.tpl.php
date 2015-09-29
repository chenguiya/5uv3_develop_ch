<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('space_notice');?>
<?php $_G['home_tpl_titles'] = array('提醒');?><?php include template('common/header'); ?><header class="header">
    <div class="nav">
        <div class="category">
            <?php if($type_wap == 'post') { ?>回复我的<?php } else { ?>活动提醒<?php } ?>
            <div id="elecnation_nav_left">
                <a href="javascript:;" onclick="history.go(-1)" class="head_back"></a>
            </div>

        </div>
    </div>
</header>

<style id="diy_style" type="text/css"></style>
<div class="wp">
    <!--[diy=diy1]--><div id="diy1" class="area"></div><!--[/diy]-->
</div>

<div id="ct" class="ct2_a wp cl">
    <div class="mn">
        <div class="bm bw0">
            <!-- <li><a href="javascript:"><img src="template/usportstyle/touch/common/images/demo/3.png" alt=""></a></li> -->



            <?php if($view=='userapp') { ?>

            <script type="text/javascript">
                function manyou_add_userapp(hash, url) {
                    if (isUndefined(url)) {
                        $(hash).innerHTML = "<tr><td colspan=\"2\">成功忽略了该条应用消息</td></tr>";
                    } else {
                        $(hash).innerHTML = "<tr><td colspan=\"2\">正在引导您进入……</td></tr>";
                    }
                    var x = new Ajax();
                    x.get('home.php?mod=misc&ac=ajax&op=deluserapp&hash=' + hash, function (s) {
                        if (!isUndefined(url)) {
                            location.href = url;
                        }
                    });
                }
            </script>

            <div class="ct_vw cl">
                <div class="ct_vw_sd">
                    <ul class="mtw">
                        <?php if($list) { ?><li><a href="home.php?mod=space&amp;do=notice&amp;view=userapp">全部应用消息</a></li><?php } ?>
                        <?php if(is_array($apparr)) foreach($apparr as $type => $val) { ?>                        <li class="mtn">
                            <a href="home.php?mod=userapp&amp;id=<?php echo $val['0']['appid'];?>&amp;uid=<?php echo $space['uid'];?>" title="<?php echo $val['0']['typename'];?>"><img src="http://appicon.manyou.com/icons/<?php echo $val['0']['appid'];?>" alt="<?php echo $val['0']['typename'];?>" class="vm" /></a>
                            <a href="home.php?mod=space&amp;do=notice&amp;view=userapp&amp;type=<?php echo $val['0']['appid'];?>"> <?php echo count($val);?> 个 <?php echo $val['0']['typename'];?> <?php if($val['0']['type']) { ?>请求<?php } else { ?>邀请<?php } ?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="ct_vw_mn">
                    <?php if($list) { ?>
                    <?php if(is_array($list)) foreach($list as $key => $invite) { ?>                    <h4 class="mtw mbm">
                        <a href="home.php?mod=space&amp;do=notice&amp;view=userapp&amp;op=del&amp;appid=<?php echo $invite['0']['appid'];?>" class="y xg1">忽略该应用的所有邀请</a>
                        <a href="home.php?mod=userapp&amp;id=<?php echo $invite['0']['appid'];?>&amp;uid=<?php echo $space['uid'];?>" title="<?php echo $apparr[$invite['0']['appid']];?>"><img src="http://appicon.manyou.com/icons/<?php echo $invite['0']['appid'];?>" alt="<?php echo $apparr[$invite['0']['appid']];?>" class="vm" /></a>
                        您有 <?php echo count($invite);?> 个 <?php echo $invite['0']['typename'];?> <?php if($invite['0']['type']) { ?>请求<?php } else { ?>邀请<?php } ?>
                    </h4>
                    <div class="xld xlda">
                        <?php if(is_array($invite)) foreach($invite as $value) { ?>                        <dl class="bbda cl">
                            <dd class="m avt mbn">
                                <a href="home.php?mod=space&amp;uid=<?php echo $value['fromuid'];?>"><?php echo avatar($value[fromuid],small);?></a>
                            </dd>
                            <dt id="<?php echo $value['hash'];?>">
                                <div class="xw0 xi3"><?php echo $value['myml'];?></div>
                            </dt>
                        </dl>
                        <?php } ?>
                    </div>
                    <?php } ?>
                    <?php if($multi) { ?><div class="pgs cl"><?php echo $multi;?></div><?php } ?>
                    <?php } else { ?>
                    <div class="emp">没有新的应用请求或邀请</div>
                    <?php } ?>
                </div>
            </div>

            <?php } else { ?>
                <?php if(empty($arr_)&&$type_wap == 'post') { ?>
                <div class="emp mtw ptw hm xs2">
                    <?php if($new == 1) { ?>
                    暂时没有新提醒，<a href="home.php?mod=space&amp;do=notice&amp;isread=1">点此查看已读提醒</a>
                    <?php } else { ?>
                    暂时没有提醒内容
                    <?php } ?>
                </div>
                <?php } ?>
              <?php if(empty($a_arr_)&&$type_wap == 'activity') { ?>
                <div class="emp mtw ptw hm xs2">
                    <?php if($new == 1) { ?>
                    暂时没有新提醒，<a href="home.php?mod=space&amp;do=notice&amp;isread=1">点此查看已读提醒</a>
                    <?php } else { ?>
                    暂时没有提醒内容
                    <?php } ?>
                </div>
                <?php } ?>

                <script type="text/javascript">

                    function deleteQueryNotice(uid, type) {
                        var dlObj = $(type + '_' + uid);
                        if (dlObj != null) {
                            var id = dlObj.getAttribute('notice');
                            var x = new Ajax();
                            x.get('home.php?mod=misc&ac=ajax&op=delnotice&inajax=1&id=' + id, function (s) {
                                dlObj.parentNode.removeChild(dlObj);
                            });
                        }
                    }

                    function errorhandle_pokeignore(msg, values) {
                        deleteQueryNotice(values['uid'], 'pokeQuery');
                    }
                </script>

                <?php if($arr_||$a_arr_) { ?>
                    <div class="xld xlda">
                        <div class="nts hmes">
                            <ul class="cl <?php if($key==1) { ?>bw0<?php } ?>" <?php echo $value['rowid'];?> notice="<?php echo $value['id'];?>" id="notice_box">

                               <?php if($type_wap == 'post') { ?>
                                            <?php if(is_array($arr_)) foreach($arr_ as $key => $value) { ?>                                                    <li class="hmesLi">
                                                        <a href="forum.php?mod=viewthread&amp;tid=<?php echo $value['tid'];?>" target="_self">
                                                                <div class="m avt mbn hmes_left">
                                                                    <img src="<?php echo avatar($value[authorid], small, true);?>" alt="systempm" />
                                                                </div>
                                                                <div class="hmes_right">
                                                                    <div class="hmes_rt">
                                                                        <span class="xg1 xw0 hrt_s1"><?php echo $value['author'];?></span>
                                                                        <span class="colb5 hrt_s2"><?php echo $value['dateline'];?></span>
                                                                    </div>
                                                                    <div class="ntc_body hmes_rb" style="">
                                                                        <span class="hrt_s3">回复了您</span>
                                                                        <span class="col2d hrt_s4">"<?php echo $value['subject'];?>"</span>
                                                                    </div>
                                                                </div>
                                                          </a>
                                                    </li>
                                            <?php } ?>
                                <?php } else { ?>
                                        <?php if(is_array($a_arr_)) foreach($a_arr_ as $key => $value) { ?>                                                <li class="hmesLi">
                                                    <a href="forum.php?mod=viewthread&amp;tid=<?php echo $value['tid'];?>" target="_self">
                                                            <div class="m avt mbn hmes_left">
                                                                <img src="<?php echo avatar($value[userid], small, true);?>" alt="systempm" />
                                                            </div>
                                                            <div class="hmes_right">
                                                                <div class="hmes_rt">
                                                                    <span class="xg1 xw0 hrt_s1"><?php if($type_ma == 0) { ?><?php echo $value['author'];?><?php } else { ?><?php echo $value['username'];?><?php } ?></span>
                                                                    <span class="colb5 hrt_s2"><?php echo $value['dateline'];?></span>
                                                                </div>
                                                                <div class="ntc_body hmes_rb" style="">
                                                                    <span class="hrt_s3"><?php if($type_ma == 0) { ?>通过你的活动申请:<?php } else { ?>申请加入您举办的活动:<?php } ?></span>
                                                                    <span class="col2d hrt_s4"><?php echo $value['subject'];?></span>
                                                                </div>
                                                            </div>
                                                      </a>
                                                </li>
                                        <?php } ?>
                                <?php } ?>


                            </ul>
                        </div>
                    </div>

                    <?php if($view!='userapp' && $space['notifications']) { ?>
                    <div class="mtm mbm"><a href="home.php?mod=space&amp;do=notice&amp;ignore=all">还有 <?php echo $value['from_num'];?> 个相同通知被忽略 <em>&rsaquo;</em></a></div>
                    <?php } ?>

                    <?php if($maxpage > 2) { ?><div class="act_page"><a href="javascript:void(0);" page="2" totalpage="<?php echo $maxpage;?>" class="act_more" id="notice_More">加载更多</a></div><?php } ?>
                <?php } ?>

            <?php } ?>
        </div>
    </div>
    <div class="appl">


        <div class="drag">
            <!--[diy=diy2]--><div id="diy2" class="area"></div><!--[/diy]-->
        </div>

    </div>
</div>

<div class="wp mtn">
    <!--[diy=diy3]--><div id="diy3" class="area"></div><!--[/diy]-->
</div><?php include template('common/footer'); ?>