<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class extend_adminorders{
	protected $order;
	protected $_lang = array();
	protected $identify=null;
	public function __construct($order){
		$this->order = $order;
		if($this->identify){
			$this->_lang = @include DISCUZ_ROOT.'./source/plugin/'.$this->identify.'/language/'.$this->getextend().'.'.currentlang().'.php';
			if(empty($this->_lang))$this->_lang = @include DISCUZ_ROOT.'./source/plugin/'.$this->identify.'/language/'.$this->getextend().'.php';
		}else{
			$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_mall/language/'.$this->getextend().'.'.currentlang().'.php';
			if(empty($this->_lang))$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_mall/language/'.$this->getextend().'.php';
		}
	}
	private function getextend(){
		if(preg_match('/^(\w+)\_adminorders$/',get_class($this),$a))
			return $a[1];
	}
	public function view(){
		
		
	}
	public function finish($oid){

	}
	public function delete($oid){}
}
?>