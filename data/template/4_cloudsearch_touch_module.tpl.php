<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); function tpl_global_header_mobile($searchparams, $srchotquery) {
global $_G;?><?php
$__CURMODULE = CURMODULE;$__FORMHASH = FORMHASH;$return = <<<EOF


EOF;
 if(!empty($searchparams['url'])) { 
$return .= <<<EOF

<style>
.keyword {padding:0 10px;margin-bottom: 100px;}
.keyword a{ background:#FFF;border: 1px solid #DDDDDD; color: #336699; display: inline-block; margin-bottom: 10px; margin-right: 10px; padding: 3px 10px; white-space: nowrap;}
.keyword a:hover {background-color: none;}
</style>
<script type="text/javascript">
$(document).ready(function() {
var obj = $('#searchform');
if(obj.length > 0) {
var getstr = {mod:'search', srchtype:'title', srhfid:0, srhlocality:'{$_G['basescript']}::{$__CURMODULE}', source:'txt.form_
EOF;
 if($_G['setting']['mobile']['mobilesimpletype']) { 
$return .= <<<EOF
sim
EOF;
 } else { 
$return .= <<<EOF
nor
EOF;
 } 
$return .= <<<EOF
.a', wap:'yes', fId:0};
EOF;
 if(is_array($searchparams['params'])) foreach($searchparams['params'] as $key => $value) { 
$return .= <<<EOF
getstr.{$key} = '{$value}';

EOF;
 } 
$return .= <<<EOF

var str = '';
for(var i in getstr) {
str += '<input type="hidden" name="'+ i +'" value="'+ getstr[i] +'">';
}
obj.append(str);
obj.attr('action', '{$searchparams['url']}').attr('method', 'get');

obj.on('submit', function() {
obj.append('<input type="hidden" name="q" value="' + $('#scform_srchtxt').val() + '">');
});


EOF;
 if($_G['setting']['srchhotkeywords']) { 
$return .= <<<EOF

var str = '';
str += '<div class="keyword cl">';
EOF;
 if(is_array($_G['setting']['srchhotkeywords'])) foreach($_G['setting']['srchhotkeywords'] as $val) { if($val=trim($val)) { } if($encodeval=rawurlencode(trim($val))) { if(!empty($searchparams['url'])) { 
$return .= <<<EOF

str += '<a href="{$searchparams['url']}?q={$encodeval}&source=txt.hotsearch_
EOF;
 if($_G['setting']['mobile']['mobilesimpletype']) { 
$return .= <<<EOF
sim
EOF;
 } else { 
$return .= <<<EOF
nor
EOF;
 } 
$return .= <<<EOF
.a{$srchotquery}&wap=yes" target="_blank" sc="1">{$val}</a>';

EOF;
 } else { 
$return .= <<<EOF

str += '<a href="search.php?mod=forum&amp;srchtxt={$encodeval}&amp;formhash={$__FORMHASH}&amp;searchsubmit=true&amp;source=txt.hotsearch_
EOF;
 if($_G['setting']['mobile']['mobilesimpletype']) { 
$return .= <<<EOF
sim
EOF;
 } else { 
$return .= <<<EOF
nor
EOF;
 } 
$return .= <<<EOF
.a&amp;wap=yes" target="_blank" class="xi2" sc="1">{$val}</a>';

EOF;
 } } } 
$return .= <<<EOF

str += '</div>';
obj.after(str);

EOF;
 } 
$return .= <<<EOF

}
});
</script>

EOF;
 } 
$return .= <<<EOF



EOF;
?><?php return $return;?><?php }?>