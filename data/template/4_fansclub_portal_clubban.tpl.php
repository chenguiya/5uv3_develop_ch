<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<div class="layout_con">
<div class="layout_hd"><h3>球迷会排行</h3></div>
  	<div class="rank_bd">
       <ul>
       	   
EOF;
 $i=1;
$return .= <<<EOF
       
EOF;
 if(is_array($fansclublists)) foreach($fansclublists as $val) { 
$return .= <<<EOF
       
EOF;
 if($i==1) { 
$return .= <<<EOF

       <li class="rank_A">
       <a class="rank_img" href="fans/topic/{$val['relation_fid']}/" target="_blank"><img src="{$val['icon']}"></a>
       <div class="rank_areaA">
        <h3><a href="fans/topic/{$val['relation_fid']}/" target="_blank">{$val['name']}</a></h3>
<p class="rank_address">{$val['area']}</p>
<p>球迷: {$val['membernum']}</p>
<p>总帖数: {$val['posts']}</p>
   </div>
   <span class="rank_text">{$val['desc']}</span>
   <span class="rank_num">{$i}</span>
   </li>
   
EOF;
 } else { 
$return .= <<<EOF

   <li class="rank_B">
       <a class="rank_img" href="fans/topic/{$val['relation_fid']}/" target="_blank"><img src="{$val['icon']}"></a>
       <div class="rank_areaB">
        <h3><a href="fans/topic/{$val['relation_fid']}/" target="_blank">{$val['name']}</a></h3>
<p class="rank_address">{$val['area']}</p>
<p>球迷: {$val['membernum']}</p>
   </div>
   <span class="rank_num">{$i}</span>
   </li>
   
EOF;
 } 
$return .= <<<EOF

   
EOF;
 $i++;
$return .= <<<EOF
       
EOF;
 } 
$return .= <<<EOF

   </ul>
  	</div>
</div>

EOF;
?>