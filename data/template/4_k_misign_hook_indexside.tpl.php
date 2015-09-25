<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$__FORMHASH = FORMHASH;$return = <<<EOF

<script type="text/javascript">
function cjdsign(){
//document.getElementById("JD_sign").className = 'midaben_signpanel JD_sign visted';	
document.getElementById("JD_sign").href = 'javascript:void(0);';
document.getElementById("JD_sign").class = 'btn-sign-over';
document.getElementById("JD_sign").html = '已签到';
document.getElementById("JD_sign").onclick = '';
location.reload();
} 
</script>
<a class="
EOF;
 if($qiandaodb['time'] >= $tdtime) { 
$return .= <<<EOF
btn-sign-over
EOF;
 } else { 
$return .= <<<EOF
btn-sign
EOF;
 } 
$return .= <<<EOF
"  id="JD_sign"  
EOF;
 if(empty($_G['uid'])) { 
$return .= <<<EOF
 onclick="showWindow('login', this.href);return false;" href="member.php?mod=logging&amp;action=login"
EOF;
 } elseif($qiandaodb['time'] >= $tdtime) { 
$return .= <<<EOF
  href="javascript:void(0);" 
EOF;
 } else { 
$return .= <<<EOF
 href="plugin.php?id=k_misign:sign&amp;operation=qiandao&amp;formhash={$__FORMHASH}&amp;fid={$_G['fid']}" onclick="ajaxget(this.href, this.id, '', '', '', 'cjdsign();');return false;"
EOF;
 } 
$return .= <<<EOF
>
EOF;
 if($qiandaodb['time'] >= $tdtime) { 
$return .= <<<EOF
已签到
EOF;
 } else { 
$return .= <<<EOF
签到
EOF;
 } 
$return .= <<<EOF
</a>

EOF;
?><?php return $return;?>