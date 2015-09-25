<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
class kami_install extends extend_install{
	public function __construct(){
		parent::__construct();
		$this->title=$this->_lang['install_title'];
		$this->des=$this->_lang['install_des'];
		$this->author=$this->_lang['install_author'];
		$this->version='v1.0.1';
		$this->data=array(
			'admin'=>array(
					'goods'=>$this->_lang['install_goods_str'],
				),
			);
	}
	public function install(){
		$sql = <<<EOF
		CREATE TABLE IF NOT EXISTS `pre_dc_mall_kami` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `key` varchar(45) NOT NULL,
		  `dateline` int(11) NOT NULL DEFAULT '0',
		  `gid` int(11) NOT NULL DEFAULT '0',
		  `use` int(1) NOT NULL DEFAULT '0',
		  `usetime` int(11) NOT NULL DEFAULT '0',
		  `useuid` int(11) DEFAULT NULL,
		  `usename` varchar(45) DEFAULT NULL,
		  `oid` INT(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		);
EOF;
		runquery($sql);
		return true;
	}
	
	public function uninstall(){
		$sql = <<<EOF
		DROP TABLE IF EXISTS `pre_dc_mall_kami`;
EOF;
		runquery($sql);
	}
	
	public function upgrade($version){
		return true;
	}

}
?>