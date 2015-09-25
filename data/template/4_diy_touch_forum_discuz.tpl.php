<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('discuz');?><?php include template('common/header'); include template('common/topnav'); ?>    <div class="swiper-container ybanner">
        <div class="swiper-wrapper">
            <?php if(is_array($focus)) foreach($focus as $vo) { ?>            <div class="swiper-slide">
                <a href="forum.php?mod=viewthread&amp;tid=<?php echo $vo['id'];?>"><img src="data/attachment/<?php echo $vo['pic'];?>" alt="<?php echo $vo['title'];?>"></a>

                <div class="swiper-title"><?php echo $vo['title'];?></div>
            </div>
            <?php } ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <!-- <section class="ch_activity">
        <ul id="ch_actbox">
            <?php if(is_array($activity)) foreach($activity as $vo) { ?>            <li style="border-bottom: 0;">
                <a href="forum.php?mod=viewthread&amp;tid=<?php echo $vo['id'];?>&amp;extra=<?php echo $extra;?>" class="m_actbox">
                    <span class="act_imgshow"><img src="<?php echo $vo['pic'];?>"></span>

                    <div class="act_info">
                        <h3><?php echo $vo['title'];?></h3>

                        <p class="act_time"><?php echo $vo['starttimefrom'];?>&nbsp;至<br>&nbsp;<?php echo $vo['starttimeto'];?></p>
                        <?php if($vo['status']) { ?><p class="act_btn">立即参加</p><?php } else { ?><p class="act_over">已经结束</p>
                        <?php } ?>
                    </div>
                </a>
            </li>
            <?php } ?>
        </ul>
    </section> -->
    <ul id="forum-discuz" class="ylist ylist-padding">
        <?php if(is_array($threadlist)) foreach($threadlist as $vo) { ?>        <li class="row" href="<?php echo $vo['url'];?>">
            <div class="author-thumb">
                <img src="<?php echo $vo['avatar'];?>" alt="<?php echo $vo['author'];?>"/>
            </div>
            <div class="row-item">
                <div class="m"><?php echo $vo['author'];?></div>
                <div class="s"><?php echo $vo['dateline'];?></div>
            </div>
            <div class="row-badge-wrap">
                <!-- <div class="ybadge ybadge-4">置顶</div> -->
                <span class="social"><i class="iconfont icon-replay"></i><?php echo $vo['replies'];?></span>
            </div>
        </li>
        <div class="title"><?php echo $vo['title'];?></div>
        <div class="images" href="<?php echo $vo['url'];?>">
            <?php if(is_array($vo['attachment'])) foreach($vo['attachment'] as $key => $attach) { ?>            <img src="<?php echo $attach;?>" alt="<?php echo $vo['title'];?>_<?php echo $key;?>"/>
            <?php } ?>
        </div>
        <?php } ?>
    </ul>
    <div style="padding: 0 8px;">
        <a id="load-more-forum-discuz" href="javascript:;" class="ybtn loadmore">加载更多</a>
    </div>
</div>
<script id="load-more-forum-discuz-templ" type="text/x-dot-template">
    {{ for (var key in it) { }}
    <li class="row" href="{{=it[key].url}}">
        <div class="author-thumb">
            <img src="{{=it[key].avatar}}" alt="{{=it[key].author}}"/>
        </div>
        <div class="row-item">
            <div class="m">{{=it[key].author}}</div>
            <div class="s">{{=it[key].dateline}}</div>
        </div>
        <div class="row-badge-wrap">
            <span class="social"><i class="iconfont icon-replay"></i>{{=it[key].replies}}</span>
        </div>
    </li>
    <div class="title">{{=it[key].title}}</div>
    {{? it[key].attachment }}
    <div class="images" href="{{=it[key].url}}">
        {{~it[key].attachment :value:index}}
        <img src="{{=value}}" alt='{{=it[key].title}}'/>
        {{~}}
    </div>
    {{?}}
    {{ } }}
</script>
<script type="text/javascript">
    $(function () {
        new Swiper('.swiper-container', {
            direction: 'horizontal',
            loop: true,
            pagination: '.swiper-pagination'
        });

        $('#forum-discuz').on('click', '.row, .title, .images', function () {
            var me = $(this);
            var url = me.attr('href');
            if (!url) url = me.prev().attr('href');
            location.href = url;
        });

//        $('#forum-discuz>.row, #forum-discuz>.title, #forum-discuz>.images').click(function () {
//            var me = $(this);
//            var url = me.attr('href');
//            if (!url) url = me.prev().attr('href');
//            location.href = url;
//        });
    });
</script>
