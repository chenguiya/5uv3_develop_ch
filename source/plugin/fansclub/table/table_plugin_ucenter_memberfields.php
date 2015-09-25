<?php if(!defined('IN_DISCUZ')) { exit('Access Denied'); }

// ucenter 主从库问题 这个class不适用于外网，这个没有select功能 by zhangjh 2015-09-23
class table_plugin_ucenter_memberfields extends discuz_table
{
    public function __construct()
    {
        $this->_table = 'ucenter_memberfields';
        $this->_pk    = 'uid';
        parent::__construct();
    }
}
