<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class physical_adminorders extends extend_adminorders{
	public function view(){
		if($this->order['status'] == 1){
			$str ='<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['adminorders_addr'].'</th>
<td>'.$this->order['address'].'</td>
</tr>
<tr>
<th style="width:80px;">'.$this->_lang['adminorders_express'].'</th>
<td>'.$this->order['extdata']['express'].'</td>
</tr>
<tr>
</table>';
			
		}else{
		$str = '
	<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['adminorders_addr'].'</th>
<td>'.$this->order['address'].'</td>
</tr>';
$str .= $this->order['status']!=3?'<tr>
<th style="width:80px;">'.$this->_lang['adminorders_express'].'</th>
<td><input name="txt_express" id="txt_express" value="" type="text" style="width:280px;"></td>
</tr>
<tr>
<tr>
<th></th>
<td>
	<input class="pn pnc" name="submitbtn" value="'.(!$this->order['status']?$this->_lang['adminorders_finishorder']:$this->_lang['adminorders_cancelorder']).'" type="submit">'.(!$this->order['status']?$this->_lang['adminorders_finishorder_msg']:'').'
</td>
</tr>':'';
$str .= '</table>';
		}
		return $str;
	}
	public function finish($oid){
		if($this->order['status']==1)return false;
		if($this->order['status']){
			$d = array(
				'status'=>3,
				'extdata'=>serialize(array()),
				'finishtime'=>TIMESTAMP,
			);
		}else{
			$express = trim(daddslashes($_GET['txt_express']));
			$d = array(
				'status'=>1,
				'extdata'=>serialize(array('express'=>$express)),
				'finishtime'=>TIMESTAMP,
			);
		}
		C::t('#dc_mall#dc_mall_orders')->update($oid,$d);
	}
}
?>