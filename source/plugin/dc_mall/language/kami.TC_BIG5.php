<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
return array(
	'install_title'=>'卡密',
	'install_des'=>'支持各種卡密，如遊戲激活卡、話費充值卡等等',
	'install_author'=>'大創網絡',
	'install_goods_str'=>'<a href="plugin.php?id=dc_mall:admin&action=goods&op=extend&gid=[gid]">卡密管理</a>',
	'extend_import_msg'=>'共向商品【{goodsname}】導入{count}個卡密',
	'extend_delete_succeed'=>'刪除卡密成功！',
	'extend_isuse'=>'已使用',
	'extend_nouse'=>'未使用',
	'extend_lock'=>'已鎖定',
	'extend_add'=>'添加卡密',
	'extend_list'=>'卡密列表',
	'extend_addmsg'=>'壹行壹個卡密,建議每次導入量不要超過500個，具體看服務器性能',
	'extend_tijiao'=>'提交',
	'extend_kami'=>'卡密',
	'extend_ctime'=>'生成時間',
	'extend_btime'=>'購買時間',
	'extend_bname'=>'購買者',
	'extend_del'=>'刪?',
	'extend_status'=>'狀態',
	'goods_maxbuy'=>'壹次最多可購買：',
	'goods_jian'=>'件',
	'goods_allow'=>'此商品每個賬戶可',
	'goods_buy'=>'購買',
	'goods_ci'=>'次。',
	'goods_nolimit'=>'不限量購買。',
	'memberorders_key'=>'卡密內容',
	'adminorders_key'=>'卡密內容',
	'adminorders_lock'=>'<font color="#FF0000">鎖</font>',
	'adminorders_cancelorder'=>'關閉訂單',
	'adminorders_cancelorder_msg'=>'訂單關閉，自動釋放鎖定的卡密',
	'admingoods_times'=>'下單次數：',
	'admingoods_times_msg'=>'最大可下單次數,不限制留空或0',
);
?>