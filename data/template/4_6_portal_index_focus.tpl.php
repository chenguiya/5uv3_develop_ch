<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($data['data']) { 
$return .= <<<EOF

<div class="slide_content">
<div class="module cl slidebox">
<ul class="slideshow">
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
<li><a href="{$val['url']}" target="_blank"><img src="data/attachment/{$val['pic']}" width="660" height="350"><span class="title">{$val['title']}</span></a></li>

EOF;
 } 
$return .= <<<EOF
			
</ul>
</div>
<script type="text/javascript">runslideshow();</script>
</div>

EOF;
 } 
$return .= <<<EOF


EOF;
?>