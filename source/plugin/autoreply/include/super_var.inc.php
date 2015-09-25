<?php
/**
 *	[自动回帖超人(autoreply.{modulename})] (C)2012-2099 Powered by 时创科技.
 *	Version: 1.0
 *	Date: 2014-02-20 13:50:58
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function _autoreply_get_super_var($message,$username,$author,$subject)
{
	$expression = array(':)', ':(', ':D', ':\'(', ':o', ':P', ':$', ';P', ':L', ':Q', ':lol', ':loveliness:', ':funk:', ':dizzy:');
	if (preg_match('/\{time\}/', $message)) {
		$message = preg_replace('/\{time\}/',date('H:i'), $message);
	}
	if (preg_match('/\{date\}/', $message)) {
		$message = preg_replace('/\{date\}/', date('Y-m-d'), $message);
	}
	if (preg_match('/\{username\}/', $message)) {
		$message = preg_replace('/\{username\}/', $username, $message);
	}
	if (preg_match('/\{author\}/', $message)) {
		$message = preg_replace('/\{author\}/', $author, $message);
	}
	if (preg_match('/\{subject\}/', $message)) {
		$message = preg_replace('/\{subject\}/', '<<'.$subject.'>>', $message);
	} 
	if (preg_match('/\{expression\}/', $message)) {
		$message = preg_replace('/\{expression\}/', $expression[mt_rand(0,count($expression)-1)], $message);
	}
	
    return $message;
}
