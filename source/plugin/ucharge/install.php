<?php
if(!defined('IN_DISCUZ')) {
　　exit('Access Denied');
}

/*
CREATE TABLE `pre_plugin_ucharge_log` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '附加表ID',
  `orderid` char(32) NOT NULL COMMENT '订单号id，对应pre_forum_order.orderid',
  `status` int NOT NULL COMMENT '订单状态，1初始化，2已通知',
  `log_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单时间',
  `confirm_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通知时间',
  `amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数量，默认1',
  `price` float(7,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `fid` mediumint(8) unsigned NOT NULL COMMENT '购买版块ID',
  `charge_type` int(2) unsigned NOT NULL DEFAULT '0' COMMENT '充值类型，1表示vip1，2表示vip2，3表示vip3',
  `username` char(50) NOT NULL DEFAULT '' COMMENT '购买者账号',
  `email` char(50) NOT NULL DEFAULT '' COMMENT '购买者Email',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '购买者IP',
  `api_type` char(20) NOT NULL DEFAULT '' COMMENT 'api类型 alipay或tenpay',
  `trade_no` char(32) NOT NULL DEFAULT '' COMMENT '官方订单号',
  `subject` char(50) NOT NULL DEFAULT '' COMMENT '产品名称',
  `body` char(100) NOT NULL DEFAULT '' COMMENT '产品详细',
  `seller_email` char(50) NOT NULL DEFAULT '' COMMENT '销售者Email',
  `notify_url` char(200) NOT NULL DEFAULT '' COMMENT '通知地址',
  `return_url` char(200) NOT NULL DEFAULT '' COMMENT '返回地址',
  `show_url` char(200) NOT NULL DEFAULT '' COMMENT '显示地址',
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `orderid` (`orderid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='插件 - U充值记录附加表';
*/

//各种安装操作  
$sql = "show tables";
runquery($sql);
//或  
DB::query($sql);


$finish = TRUE;
