<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class rights_userbannerset {
	
	var $version = '1.0';
	var $name = 'userbannerset_name';
	var $description = 'userbannerset_desc';
	var $price = '10';
	var $targetgroupperm = true;
	var $copyright = '<a href="http://www.5usport.com" target="_blank">5U体育</a>';
	var $rights = array();
	var $parameters = array();
	
	function getsetting() {
		return array();
	}
	function setsetting() {
		return array();
	}
}