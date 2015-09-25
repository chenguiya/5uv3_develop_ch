<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class extend_adminextend{
	protected $goods;
	protected $_lang = array();
	public function __construct($goods){
		global $_G;
		$this->goods = $goods;
		$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_mall/language/'.$this->getextend().'.'.currentlang().'.php';
		if(empty($this->_lang))$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_mall/language/'.$this->getextend().'.php';
		$_G['dc_mall']['extend']['lang'] = &$this->_lang;
	}
	private function getextend(){
		if(preg_match('/^(\w+)\_adminextend$/',get_class($this),$a))
			return $a[1];
	}
	public function dorun(){
	
	}
}
?>