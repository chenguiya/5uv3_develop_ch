<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class extend_goods{
	protected $goods;
	protected $check;
	protected $_lang = array();
	protected $identify=null;
	public function __construct($goods){
		$this->goods = $goods;
		if($this->identify){
			$this->_lang = @include DISCUZ_ROOT.'./source/plugin/'.$this->identify.'/language/'.$this->getextend().'.'.currentlang().'.php';
			if(empty($this->_lang))$this->_lang = @include DISCUZ_ROOT.'./source/plugin/'.$this->identify.'/language/'.$this->getextend().'.php';
		}else{
			$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_mall/language/'.$this->getextend().'.'.currentlang().'.php';
			if(empty($this->_lang))$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_mall/language/'.$this->getextend().'.php';
		}
	}
	private function getextend(){
		if(preg_match('/^(\w+)\_goods$/',get_class($this),$a))
			return $a[1];
	}
	public function check(){ //�Ƿ�ɹ�����
		global $_G;
		$agids = dunserialize($this->goods['allowgroup']);
		if($this->check)
			return $this->check;
		if($this->goods['enddateline']&&$this->goods['enddateline']<TIMESTAMP){
			$this->check = 1;
		}elseif(!$this->goods['count']){
			$this->check = 2;
		}elseif(!$_G['dc_mall']['vip']['user']&&!in_array($_G['groupid'],$agids)&&!empty($this->goods['allowgroup'])){
			$this->check =  4;
		}elseif($_G['uid']&&$this->goods['buytimes']){
			$times = C::t('#dc_mall#dc_mall_orders')->count(array('uid'=>$_G['uid'],'gid'=>$this->goods['id']));
			if($times>=$this->goods['buytimes'])
				$this->check =  3;
		}

		if(!$this->check)$this->check = true;
		return $this->check;
	}
	public function view(){
		if(!$this->check)$this->check = $this->check();
		if($this->check===true)return;
		if($this->check==1)$str = lang('plugin/dc_mall','extend_goods_daoqi');
		if($this->check==2)$str = lang('plugin/dc_mall','对不起，礼品已兑换完');
		if($this->check==3)$str = lang('plugin/dc_mall','extend_goods_isbuy');
		if($this->check==4)$str = lang('plugin/dc_mall','您的用户组不允许兑换');

		return '<div style="color:#FF0000; font-size:16px;font-weight: bold;">'.$str.'</div>';
	}
	public function payview(){//���������²���ʾ����

	}
	public function paycheck(){//��������ǰ���,�ɷ���һ���飬�Զ���⵽ extdata

	}
	public function payfinish($orderid){//������ɺ���

	}
	public function finish($order){

	}
}
?>