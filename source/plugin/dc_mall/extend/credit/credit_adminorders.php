<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class credit_adminorders extends extend_adminorders{
	public function view(){
		global $_G;
		$str ='<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['adminorders_credit'].'</th>
<td>'.$this->order['extdata']['credit'].$_G['setting']['extcredits'][$this->order['extdata']['id']]['title'].'</td>
</tr>
';
if(!in_array($this->order['status'],array(1,3))){
$str .='<tr>
<th></th>
<td>
	<input class="pn pnc" name="submitbtn" value="'.$this->_lang['adminorders_cancelorder'].'" type="submit">
</td>
</tr>';
}
$str .='</table>';
		return $str;
	}
	public function finish($oid){
		if(in_array($this->order['status'],array(1,3)))return false;
		$d = array(
			'status'=>3,
			'finishtime'=>TIMESTAMP,
		);
		C::t('#dc_mall#dc_mall_orders')->update($oid,$d);
	}
}
?>