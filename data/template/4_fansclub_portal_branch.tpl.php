<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<ul class="rank_bd cl">
EOF;
 if(is_array($result)) foreach($result as $val) { 
$return .= <<<EOF
 <li class="rank_B">
 <a class="rank_img" href="fans/topic/{$val['id']}/"><img src="
EOF;
 if($val['info']['icon']) { 
$return .= <<<EOF
{$val['info']['icon']}
EOF;
 } else { 
$return .= <<<EOF
{$_G['style']['tpldir']}/common/images/default_icon.jpg
EOF;
 } 
$return .= <<<EOF
"></a>
 <div class="rank_areaB">
  <h3><a href="fans/topic/{$val['id']}/" target="_blank">{$val['title']}</a></h3>
  <p class="rank_address">{$val['province']}&nbsp;&nbsp;{$val['city']}</p>
  <p>球迷: {$val['info']['membernum']}</p>
  <p>总帖数: {$val['info']['threads']}</p>
 </div>
 </li>

EOF;
 } 
$return .= <<<EOF

</ul>

EOF;
?>