<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
return array(
	'install_title'=>'卡密',
	'install_des'=>'支持各种卡密，如游戏激活卡、话费充值卡等等',
	'install_author'=>'大创网络',
	'install_goods_str'=>'<a href="plugin.php?id=dc_mall:admin&action=goods&op=extend&gid=[gid]">卡密管理</a>',
	'extend_import_msg'=>'共向礼品【{goodsname}】导入{count}个卡密',
	'extend_delete_succeed'=>'删除卡密成功！',
	'extend_isuse'=>'已使用',
	'extend_nouse'=>'未使用',
	'extend_lock'=>'已锁定',
	'extend_add'=>'添加卡密',
	'extend_list'=>'卡密列表',
	'extend_addmsg'=>'一行一个卡密,建议每次导入量不要超过500个，具体看服务器性能',
	'extend_tijiao'=>'提交',
	'extend_kami'=>'卡密',
	'extend_ctime'=>'生成时间',
	'extend_btime'=>'兑换时间',
	'extend_bname'=>'兑换者',
	'extend_del'=>'删?',
	'extend_status'=>'状态',
	'goods_maxbuy'=>'一次最多可兑换：',
	'goods_jian'=>'件',
	'goods_allow'=>'此礼品每个账户可',
	'goods_buy'=>'兑换',
	'goods_ci'=>'次。',
	'goods_nolimit'=>'不限量兑换。',
	'memberorders_key'=>'卡密内容',
	'adminorders_key'=>'卡密内容',
	'adminorders_lock'=>'<font color="#FF0000">锁</font>',
	'adminorders_cancelorder'=>'关闭订单',
	'adminorders_cancelorder_msg'=>'订单关闭，自动释放锁定的卡密',
	'admingoods_times'=>'下单次数：',
	'admingoods_times_msg'=>'最大可下单次数,不限制留空或0',
);
?>