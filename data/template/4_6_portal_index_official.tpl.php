<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($data['data']) { 
$return .= <<<EOF

<div class="releaseBox">
<div class="release_hd"><span>官方发布</span></div>
<div class="release_bd">
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { if($val['displayorder'] == 1) { 
$return .= <<<EOF

<p><a href="{$val['url']}" target="_blank"><img src="data/attachment/{$val['thumbpath']}" width="280" height="125"></a></p>
<dl>
<dt><a href="{$val['url']}" target="_blank">{$val['title']}</a></dt>
<dd>{$val['summary']}</dd>

EOF;
 } else { 
$return .= <<<EOF

<dt><a href="{$val['url']}" target="_blank">{$val['title']}</a></dt>

EOF;
 } } 
$return .= <<<EOF

</dl>
</div>
</div>

EOF;
 } 
$return .= <<<EOF


EOF;
?>