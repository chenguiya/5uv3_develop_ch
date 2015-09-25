<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($activemembers) { 
$return .= <<<EOF

<div class="inter_bdA cl">
<span class="ative_head">最新活跃</span>
<ul class="ative_list cl">
EOF;
 if(is_array($activemembers)) foreach($activemembers as $vo) { 
$return .= <<<EOF
<li><a href="home.php?mod=space&amp;uid={$vo['uid']}" target="_blank"><img src="{$vo['avatar']}"></a></li>

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