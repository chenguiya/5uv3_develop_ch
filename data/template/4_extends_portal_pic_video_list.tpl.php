<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<div class="layout_hd"><h3>视频图片</h3></div>					     
<div class="layout_bd">
<ul class="video_photo cl">
EOF;
 if(is_array($data)) foreach($data as $val) { 
$return .= <<<EOF
<li>
<a class="photo_img" href="thread-{$val['id']}.html" target="_blank"><img src="data/attachment/{$val['pic']}"></a>
<a class="photo_text" href="thread-{$val['id']}.html" target="_blank">{$val['title']}</a>

EOF;
 if($val['attachment']==3) { 
$return .= <<<EOF

<a href="thread-{$val['id']}.html" target="_blank"><span class="icon_video_i"></span></a>
<i class="ico_video"></i>

EOF;
 } elseif($val['attachment']==2) { 
$return .= <<<EOF

<i class="ico_photo"></i>

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
?>