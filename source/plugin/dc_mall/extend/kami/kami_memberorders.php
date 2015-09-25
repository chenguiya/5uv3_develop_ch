<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class kami_memberorders extends extend_memberorders{
	public function view(){
		if($this->order['status']==1){
			$str ='<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_key'].'</th>
<td>'.$this->order['extdata']['key'].'</td>
</tr>
<tr>
</table>';
		return $str;
		}
	}
}
?>