<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($data['data']) { 
$return .= <<<EOF

<div class="scroll_code" id="leftSideScrollCode">
    
EOF;
 if(is_array($data['data'])) foreach($data['data'] as $val) { 
$return .= <<<EOF
    <a href="group/{$val['id']}" name="{$val['name']}"><img src="{$val['fields']['icon']}"></a>
    
EOF;
 } 
$return .= <<<EOF

    <a href="football" class="hotcode">热门频道<i class="icon"></i></a>
</div>

EOF;
 } 
$return .= <<<EOF


EOF;
?>