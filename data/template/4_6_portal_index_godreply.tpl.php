<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<div class="modul_con">
<div class="in_hdbox">
     <span class="hdspan">神回复</span>
     <a href="plugin.php?id=extends&amp;action=replychange" onclick="changeGodReply(this)" class="mores">换一换</a>
</div>
<div class="reply_Con">
     <ul id="replyByGod">
     
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
     	<li>
    <div class="reply_box cl">
         <a href="home.php?mod=space&amp;uid={$val['authorid']}" target="_blank">{$val['avatar']} &nbsp;{$val['author']}</a>
         <span class="y sta_zan">
EOF;
 if($val['support'] > 0) { 
$return .= <<<EOF
{$val['support']}
EOF;
 } else { 
$return .= <<<EOF
0
EOF;
 } 
$return .= <<<EOF
</span>
    </div>
    <p class="rep_title"><a target="_blank" href="thread-{$val['tid']}.html#pid{$val['pid']}" >{$val['message']}</a></p>
    <p class="rep_retitle"><i class="rep_icon"></i><a target="_blank" href="thread-{$val['tid']}.html">《{$val['subject']}》</a></p>
</li>
     	
EOF;
 } 
$return .= <<<EOF

     </ul>
</div>
</div>

EOF;
?>