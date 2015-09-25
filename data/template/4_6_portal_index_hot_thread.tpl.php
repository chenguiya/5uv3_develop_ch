<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<div class="modech_left">
<div class="in_hdbox"><span class="hdspan">热门帖子</span></div>
<div class="indexThread">
<ul id='newslistflag'>
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
<li class="cl">
<div class="avatar">
<a href="home.php?mod=space&amp;uid={$val['fields']['authorid']}" target="_blank"><img src="{$val['fields']['avatar_middle']}"></a>
</div>
<div class="inde_info">
<p class="in_name"><a href="{$val['url']}" target="_blank">{$val['title']}</a></p>
<p class="in_title">{$val['summary']['message']}</p>


EOF;
 if($val['summary']['img']) { 
$return .= <<<EOF

<p class="in_img">
EOF;
 if(is_array($val['summary']['img'])) foreach($val['summary']['img'] as $v) { 
$return .= <<<EOF
<a href="{$val['url']}" target="_blank"><img src="{$v}" width="auto" height="140"></a>

EOF;
 } 
$return .= <<<EOF

</p>

EOF;
 } elseif($val['summary']['media']) { 
$return .= <<<EOF

<p class="in_img"><embed width="230" height="140" allownetworking="internal" allowscriptaccess="never" src="{$val['summary']['media']}" quality="high" bgcolor="#ffffff" wmode="transparent" allowfullscreen="true" type="application/x-shockwave-flash"></embed></p>

EOF;
 } 
$return .= <<<EOF

<div class="in_meta">
                        <span><a href="home.php?mod=space&amp;uid={$val['fields']['authorid']}" target="_blank">{$val['fields']['author']}</a>&nbsp;&nbsp;{$val['dateline']} &nbsp;&nbsp;&nbsp;来自<a href="group/{$val['fid']}/" target="_blank">{$val['fields']['forumname']}</a></span>
                        <span class="y sta_lun">{$val['replies']}</span>
                        <span class="y sta_zan">{$val['support_num']}</span>
</div>
</div>

EOF;
 if($val['displayorder'] == 1) { 
$return .= <<<EOF
<span class="inshot"></span>
EOF;
 } 
$return .= <<<EOF

</li>

EOF;
 } 
$return .= <<<EOF

          </ul>
     </div>
     
EOF;
 if($maxpage > 1) { 
$return .= <<<EOF

 <!--<br>
 <a class="bm_h" style="border: 1px solid #e9e9e9;font: normal 14px/28px 'Microsoft Yahei', sans-serif;width: 100%;height: 40px;line-height: 40px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;display: inline-block;background: -webkit-linear-gradient(top, #ffffff 0%, #f2f2f2 100%);text-align: center;" onclick="getmoreload('{$multipage_more}', '#newslistflag', '#newslistflag');" page="
EOF;
 $page = $page + 1;
$return .= <<<EOF
{$page}" href="javascript:void(0);">加载更多</a>
 -->
     <!--<a class="bm_h" href="javascript:;" rel="{$multipage_more}" curpage="{$page}" id="autopbn" totalpage="{$maxpage}"
style="border: 1px solid #e9e9e9;font: normal 14px/28px 'Microsoft Yahei', sans-serif;width: 500px;height: 40px;line-height: 40px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;display: inline-block;background: -webkit-linear-gradient(top, #ffffff 0%, #f2f2f2 100%);">加载更多</a>-->
     <!--<div class="load_more"><a href="" target="_blank">加载中</a></div>-->
     
EOF;
 } 
$return .= <<<EOF

 
 <script>
function getmoreload(url,sourceSelector,targetSelector){
var url,sourceSelector,targetSelector,num;
var page=parseInt(jq('.bm_h').attr('page'));
url=url+'&page='+page;
jq.get(url, function (html) {
jq(targetSelector).append(jq(html).find(targetSelector));
num=page+1;
jq('.bm_h').attr('page',num);
});

}

</script>
</div>

EOF;
?>