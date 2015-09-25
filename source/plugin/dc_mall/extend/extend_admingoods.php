<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class extend_admingoods{
	protected $goods;
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
		if(preg_match('/^(\w+)\_admingoods$/',get_class($this),$a))
			return $a[1];
	}
	public function check(){
		return array('maxbuy'=>1,'buytimes'=>1,'count'=>1);
	}
	public function view(){
		
	}
	public function finish($gid){
	
	}
	public function delete($gid){
	
	}
	
}
?>