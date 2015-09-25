<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
class usergroup_install extends extend_install{
	public function __construct(){
		parent::__construct();
		$this->title=$this->_lang['install_title'];
		$this->des=$this->_lang['install_des'];
		$this->author=$this->_lang['install_author'];
		$this->version='v1.0.1';
		$this->data=array();
	}

}
?>