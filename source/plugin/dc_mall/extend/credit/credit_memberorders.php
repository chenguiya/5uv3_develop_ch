<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class credit_memberorders extends extend_memberorders{
	public function view(){
		global $_G;
			$str ='<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_credit'].'</th>
<td>'.$this->order['extdata']['credit'].$_G['setting']['extcredits'][$this->order['extdata']['id']]['title'].'</td>
</tr>
<tr>
</table>';
		return $str;
	}
}
?>