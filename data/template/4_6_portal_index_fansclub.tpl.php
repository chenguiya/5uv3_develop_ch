<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($data['data']) { 
$return .= <<<EOF

<div class="indexFans">
<div class="in_hdbox">
<span class="hdspan">推荐球迷会</span>
<!--<a href="/group" class="mores" target="_blank">更多</a>-->
</div>
<div class="Fans_box">
<ul class="cl">
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
<li>
    <a class="fans_img" href="fans/topic/{$val['id']}/" target="_blank"><img src="{$val['fields']['icon']}"></a>
    <p class="fans_name"><a href="fans/topic/{$val['id']}/" target="_blank">{$val['title']}</a></p>
    <p class="fans_num">地区：{$val['area']} &nbsp;&nbsp;&nbsp;&nbsp;人数：{$val['fields']['membernum']}</p>
    
EOF;
 if($val['user_status']) { 
$return .= <<<EOF

    <p class="fans_btn"><a class="attes_on" href="javascript:;" onclick="showDialog('确定退出球迷会吗？','confirm','',function(){location.href='forum.php?mod=group&action=out&fid={$val['id']}'});"></a></p>
    
EOF;
 } else { 
$return .= <<<EOF

    <p class="fans_btn"><a class="attes" href="forum.php?mod=group&amp;action=join&amp;fid={$val['id']}&amp;forward=current"></a>
</p>
    
EOF;
 } 
$return .= <<<EOF

</li>

EOF;
 } 
$return .= <<<EOF

</ul>
</div>
</div>

EOF;
 } 
$return .= <<<EOF


EOF;
?>