<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<div class="layout_con">
<div class="layout_hd"><h3>贡献达人</h3>	</div>
<div class="layout_bd">
     <ul class="sident_ul">
     
EOF;
 if(is_array($fanslists)) foreach($fanslists as $fans) { 
$return .= <<<EOF
     	
 <li class="cl">
     <a class="sident_img" href="home.php?mod=space&amp;uid={$fans['uid']}" target="_blank"><img src="{$fans['avatar']}"></a>
 <div class="sident_bd">
      <h3>{$fans['username']}</h3>
  <p>[<a href="group/{$fans['mainclub_fid']}/" target="_blank">{$fans['mainclub_name']}</a>]</p>
  <span>贡献度：{$fans['total']}</span>
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