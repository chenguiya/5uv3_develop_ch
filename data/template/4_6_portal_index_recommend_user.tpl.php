<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($data['data']) { 
$return .= <<<EOF

<div class="modul_con">
<div class="in_hdbox">
     <span class="hdspan">认证用户</span>
     <a href="home.php?mod=spacecp&amp;ac=profile&amp;op=verify" class="aplicat" target="_blank">申请认证</a>
</div>
<div class="aplicat_Con">
<ul class="cl">
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
<li class="cl">
    <a href="home.php?mod=space&amp;uid={$val['id']}" class="apl_img" target="_blank"><img src="{$val['fields']['avatar_middle']}">
EOF;
 if(is_verify($val['id'])) { 
$return .= <<<EOF
<span class="icon_middle">
EOF;
 } 
$return .= <<<EOF
</span></a>
    <div class="apl_box">
         <div class="apl_hd">
              <p class="apl_name"><a href="home.php?mod=space&amp;uid={$val['id']}" target="_blank">{$val['title']}</a></p>
              <p class="apl_tit">[{$val['fields']['position']}]</p>			              
              
EOF;
 if(follow_check($val['id'])) { 
$return .= <<<EOF

      <a href="javascript:;" onclick="showDialog('确定取消关注吗？','confirm','',function(){location.href='home.php?mod=spacecp&ac=follow&op=del&fuid={$val['id']}'});" class="atten atten_over"></a>
      
EOF;
 } else { 
$return .= <<<EOF

      <a  id="a_followmod_{$val['id']}" href="home.php?mod=spacecp&amp;ac=follow&amp;op=add&amp;fuid={$val['id']}" class="atten atten_on"></a>
      
EOF;
 } 
$return .= <<<EOF
		              
         </div>
         <div class="apl_bds">
         
EOF;
 if(is_array($val['lastthread'])) foreach($val['lastthread'] as $v) { 
$return .= <<<EOF
<span><i>--</i> <a href="forum.php?mod=viewthread&amp;tid={$v['tid']}" target="_blank">{$v['subject']}</a></span>
            
EOF;
 } 
$return .= <<<EOF

         </div>
    </div>
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