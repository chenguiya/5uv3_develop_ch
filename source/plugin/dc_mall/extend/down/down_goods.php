<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class down_goods extends extend_goods{
	private $addr;
	public function view(){
		$return = '<div>'.$this->_lang['goods_maxbuy'].'<strong style="color:#FF0000; font-size:16px;">'.$this->goods['maxbuy'].'</strong> '.$this->_lang['goods_jian'].'</div>';
		$return .= '<div>'.$this->_lang['goods_allow'].($this->goods['buytimes']?$this->_lang['goods_buy'].' <strong style="color:#FF6600; font-size:16px;">'.$this->goods['buytimes'].'</strong> '.$this->_lang['goods_ci']:$this->_lang['goods_nolimit']).'</div>';
		$return .= parent::view();
		return $return;
	}
	public function paycheck(){//订单创建前检测
		$extdata = dunserialize($this->goods['extdata']);
		return $extdata;
	}
	public function finish($order){ //完成后执行
		C::t('#dc_mall#dc_mall_orders')->update($order['id'],array('status'=>1,'finishtime'=>TIMESTAMP));
	}
}
?>