<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($data['data']) { 
$return .= <<<EOF

<ul class="today_bd xin_ul cl">
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
<li>
<p class="tody_name"><a href="{$val['url']}" target="_blank">{$val['title']}</a></p>
<p class="tody_text">{$val['summary']}</p>
<div class="tody_areaB">
<span class="tody_time">{$val['dateline']}</span>
<span class="tody_from">来自：<em><a href="group/{$val['fields']['fid']}" target="_blank">{$val['fields']['forumname']}</a></em></span>
<span class="y">	
<i class="zan_A">{$val['recommend_add']}</i>
<i class="post_A">{$val['replies']}</i>
</span>
</div>
</li>

EOF;
 } 
$return .= <<<EOF

</ul>

EOF;
 } 
$return .= <<<EOF


EOF;
?>