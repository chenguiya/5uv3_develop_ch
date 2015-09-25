<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class physical_memberorders extends extend_memberorders{
	public function view(){
			$str ='<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_userinfo'].'</th>
<td>'.$this->order['address'].'</td>
</tr>
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_express'].'</th>
<td>'.$this->order['extdata']['express'].'</td>
</tr>
<tr>
</table>';
		return $str;
	}
}
?>