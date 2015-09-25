<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class down_adminorders extends extend_adminorders{
	public function view(){
		$str ='<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_downurl'].'</th>
<td><a href="'.$this->order['extdata']['downurl'].'" target="_blank">'.$this->_lang['memberorders_downclick'].'<a/></td>
</tr>
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_downcode'].'</th>
<td>'.$this->order['extdata']['downcode'].'</td>
</tr>
<tr>
</table>';
		return $str;
	}
	
}
?>