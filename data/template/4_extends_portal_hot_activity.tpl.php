<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<div class="slide_wrap">
<div class="slide_content">
<div class="module cl slidebox" style="display: block;">
<ul class="slideshow">
EOF;
 if(is_array($activitylists)) foreach($activitylists as $key => $val) { if($key%3==0) { 
$return .= <<<EOF
<li>
EOF;
 } 
$return .= <<<EOF

<a href="{$val['url']}"><img src="data/attachment/{$val['pic']}" width="328" height="210"><span class="title">{$val['title']}</span></a>

EOF;
 if($key%3==2) { 
$return .= <<<EOF
</li>
EOF;
 } } 
$return .= <<<EOF

</ul>
</div>
<script type="text/javascript">
runslideshow();
</script>
</div>
</div>

EOF;
?>