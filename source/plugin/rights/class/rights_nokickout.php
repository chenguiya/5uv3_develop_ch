<?php
if (!defined('IN_DISCUZ')) {
	exit('Acces Denied');
}

class rights_nokickout {

	var $version = '1.0';
	var $name = 'nokickout_name';
	var $description = 'nokickout_desc';
	var $price = '10';
	var $targetgroupperm = true;
	var $copyright = '<a href="http://www.5usport.com" target="_blank">5U体育</a>';
	var $rights = array();
	var $parameters = array();

	function getsetting(&$rights) {
		return array();
	}
	
	function setsetting(&$rightsnew, &$parameters) {
		return array();
	}
}