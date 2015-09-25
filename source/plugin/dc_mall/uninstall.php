<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
C::import('extend/install','plugin/dc_mall',false);
$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
foreach($extends as $ext){
	if(C::import($ext['identify'].'/install','plugin/dc_mall/extend',false)){
		$modstr = $ext['identify'].'_install';
		if (class_exists($modstr,false)){
			$mobj = new $modstr();
			if(in_array('uninstall',get_class_methods($mobj))){
				$mobj->uninstall();
			}
		}
	}
}
$sql = <<<EOF
DROP TABLE IF EXISTS `pre_dc_mall_extend`;
DROP TABLE IF EXISTS `pre_dc_mall_sort`;
DROP TABLE IF EXISTS `pre_dc_mall_goods`;
DROP TABLE IF EXISTS `pre_dc_mall_orders`;
DROP TABLE IF EXISTS `pre_dc_mall_address`;
EOF;
runquery($sql);
$finish = TRUE;
?>