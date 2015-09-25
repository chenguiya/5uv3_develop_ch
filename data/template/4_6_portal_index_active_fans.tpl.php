<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$__FORMHASH = FORMHASH;$return = <<<EOF

<div class="modul_con">
     <div class="in_hdbox">
          <span class="hdspan">活跃粉丝</span>
          <!--<a href="plugin.php?id=extends&amp;action=userchange&amp;hash={$__FORMHASH}" onclick="changeBody(this);" class="mores">换一换</a>-->
     </div>
     <div class="active_Con">
<ul class="cl" id="avtiveFans">
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
<li class="cl">
<span class="actico act{$val['displayorder']}"></span>
<a href="{$val['url']}" class="act_img" target="_blank"><img src="{$val['fields']['avatar_middle']}">
EOF;
 if(is_verify($val['id'])) { 
$return .= <<<EOF
<span class="icon_small"></span>
EOF;
 } 
$return .= <<<EOF
</a>
<div class="act_middle">
<p class="actname"><a href="{$val['url']}" target="_blank">{$val['title']}</a>&nbsp;&nbsp;&nbsp;<em>积分：</em>{$val['fields']['credits']}</p>
<p class="acttit">{$val['fields']['bio']}</p>
</div>				
<span class="y">
      
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

</span>
</li>

EOF;
 } 
$return .= <<<EOF
            
</ul>
     </div>
</div>


EOF;
?>
