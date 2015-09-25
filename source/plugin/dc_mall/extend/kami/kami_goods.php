<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class kami_goods extends extend_goods{
	private $key;
	public function view(){
		$return = '<div>'.$this->_lang['goods_maxbuy'].'<strong style="color:#FF0000; font-size:16px;">'.$this->goods['maxbuy'].'</strong> '.$this->_lang['goods_jian'].'</div>';
		$return .= '<div>'.$this->_lang['goods_allow'].($this->goods['buytimes']?$this->_lang['goods_buy'].' <strong style="color:#FF6600; font-size:16px;">'.$this->goods['buytimes'].'</strong> '.$this->_lang['goods_ci']:$this->_lang['goods_nolimit']).'</div>';
		$return .= parent::view();
		return $return;
	}
	public function check(){
		if(parent::check()===true){
			$keycount = C::t('#dc_mall#dc_mall_kami')->getcount(array('use'=>0,'gid'=>$this->goods['id']));
			if(!$keycount){
				$this->check=2;
				C::t('#dc_mall#dc_mall_goods')->update($this->goods['id'],array('count'=>0));
			}
		}
		return $this->check;
		
	}
	public function paycheck(){//订单创建前检测
		$this->key = C::t('#dc_mall#dc_mall_kami')->fetchbyone($this->goods['id']);
		return array('id'=>$this->key['id'],'key'=>$this->key['key']);
	}
	public function payfinish($orderid){//订单完成后处理
		C::t('#dc_mall#dc_mall_kami')->update($this->key['id'],array('use'=>2,'oid'=>$orderid));
	}
	public function finish($order){
		C::t('#dc_mall#dc_mall_orders')->update($order['id'],array('status'=>1,'finishtime'=>TIMESTAMP));
		C::t('#dc_mall#dc_mall_kami')->update($order['extdata']['id'],array('use'=>1,'usetime'=>TIMESTAMP,'useuid'=>$order['uid'],'usename'=>$order['username']));
	}
}
?>