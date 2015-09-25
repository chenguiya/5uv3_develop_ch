<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF

<div class="teamCon">
<div class="teamFans">
    	<span class="teamTag"><i class="icon-fans"></i>下属球迷会</span>
        <span>{$data['branchnum']}</span>
    </div>
    <div class="teamMember">
    	<span class="teamTag"><i class="icon-member"></i>会员</span>
        <span>{$data['membernum']}</span>
    </div>
    <div class="teamPosts">
    	<span class="teamTag"><i class="icon-post"></i>帖子数</span>
        <span>{$data['threadnum']}</span>
    </div>
</div>

EOF;
?>