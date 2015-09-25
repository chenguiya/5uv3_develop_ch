<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($data['data']) { 
$return .= <<<EOF

<div class="modul_con">
<div class="in_hdbox">
<span class="hdspan">热门活动</span>
<a href="activity" class="mores" target="_blank">更多</a>
</div>
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
<div class="modul_con">
<div class="cell">
<a href="{$val['url']}" target="_blank"><img src=
EOF;
 if($val['thumbpath']) { 
$return .= <<<EOF
"data/attachment/{$val['thumbpath']}"
EOF;
 } else { 
$return .= <<<EOF
"data/attachment/{$val['pic']}"
EOF;
 } 
$return .= <<<EOF
 width="320" height="170" alt="{$val['title']}" class="img" /></a>
<div class="desc">
<p class="brief"><a href="{$val['url']}" target="_blank">{$val['title']}</a></p>
<span class="time">{$val['starttimefrom']}
EOF;
 if($val['starttimeto']) { 
$return .= <<<EOF
&nbsp;-&nbsp;{$val['starttimeto']}
EOF;
 } 
$return .= <<<EOF
</span>

EOF;
 if($val['status']) { 
$return .= <<<EOF

<a href="{$val['url']}" class="btn-join" target="_blank">立即参加</a>

EOF;
 } else { 
$return .= <<<EOF

<a href="{$val['url']}" class="btn-over" target="_blank">已结束</a>

EOF;
 } 
$return .= <<<EOF

</div>
</div>
</div>

EOF;
 } 
$return .= <<<EOF

</div>

EOF;
 } 
$return .= <<<EOF


EOF;
?>