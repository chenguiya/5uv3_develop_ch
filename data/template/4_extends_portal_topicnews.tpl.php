<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($data['data']) { 
$return .= <<<EOF

<div class="today_bd">
<ul class="cl">
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
<li>
<div class="cl">
<a class="tody_img" href="{$val['url']}" target="_blank"><img src="data/attachment/{$val['pic']}" width="188" height="125" border="0"></a>
<div class="tody_areaA">
<p class="tody_name"><a href="{$val['url']}" target="_blank">{$val['title']}</a></p>
<p class="tody_text">{$val['summary']}</p>
</div>
</div>
<div class="cl">
<div class="tody_areaB">
<span class="tody_time">{$val['dateline']}</span>
<span class="tody_from">来自：<em><a href="group/{$val['fields']['fid']}" target="_blank">{$val['fields']['forumname']}</a></em></span>
<span class="y">
<i class="zan_A">{$val['recommend_add']}</i>
<i class="post_A">{$val['replies']}</i>
</span>
</div>
</div>
</li>

EOF;
 } 
$return .= <<<EOF

</ul>
</div>

EOF;
 } 
$return .= <<<EOF


EOF;
?>