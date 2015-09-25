<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class credit_goods extends extend_goods{
	public function view(){
		global $_G;
		$extdata = dunserialize($this->goods['extdata']);
		if(!$_G['setting']['extcredits'][$extdata['id']])showmessage('dc_mall:error');
		$return = '<div>'.$this->_lang['goods_credit'].'<strong style="color:#FF0000; font-size:16px;">'.$extdata['credit'].'</strong>'.$_G['setting']['extcredits'][$extdata['id']]['title'].'</div>';
		$return .= '<div>'.$this->_lang['goods_maxbuy'].'<strong style="color:#FF0000; font-size:16px;">'.$this->goods['maxbuy'].'</strong> '.$this->_lang['goods_jian'].'</div>';
		$return .= '<div>'.$this->_lang['goods_allow'].($this->goods['buytimes']?$this->_lang['goods_buy'].' <strong style="color:#FF6600; font-size:16px;">'.$this->goods['buytimes'].'</strong> '.$this->_lang['goods_ci']:$this->_lang['goods_nolimit']).'</div>';
		$return .= parent::view();
		return $return;
	}
	public function paycheck(){//订单创建前检测
		$extdata = dunserialize($this->goods['extdata']);
		return $extdata;
	}
	public function finish($order){
		C::t('#dc_mall#dc_mall_orders')->update($order['id'],array('status'=>1,'finishtime'=>TIMESTAMP));
		updatemembercount($order['uid'], array('extcredits'.$order['extdata']['id'] => $order['extdata']['credit']), true, '', 0, '',$this->_lang['goods_sccredit'],$this->_lang['goods_sccredit_msg'].'<a href="plugin.php?id=dc_mall&action=goods&gid='.$order['gid'].'">'.$order['gname'].'</a>');
		

	}
}
?>