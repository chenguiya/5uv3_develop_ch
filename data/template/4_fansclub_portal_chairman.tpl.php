<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<div class="layout_con">
<div class="layout_hd"><h3>优秀会长</h3>	</div>
<div class="layout_bd">
     <ul class="sident_ul">
     
EOF;
 if(is_array($chairmanlists)) foreach($chairmanlists as $val) { 
$return .= <<<EOF
     	<li class="cl">
     <a class="sident_img" href="home.php?mod=space&amp;uid={$val['uid']}" target="_blank"><img src="{$val['avatar']}"></a>
 <div class="sident_bd">
      <h3>{$val['username']} <em>优</em></h3>
  <p><a 
EOF;
 if($val['checkrights']) { 
$return .= <<<EOF
style="color:red;"
EOF;
 } 
$return .= <<<EOF
 href="group/{$val['relation_fid']}/" target="_blank">{$val['name']}</a></p>
  <span>{$val['sign']}</span>
 </div>
 </li>
     	
EOF;
 } 
$return .= <<<EOF
		     
 </ul>
</div>
</div>

EOF;
?>