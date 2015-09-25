<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class kami_adminorders extends extend_adminorders{
	public function view(){
		if($this->order['status']==3)return;
		$str ='<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_key'].' '.($this->order['status']!=1?$this->_lang['adminorders_lock']:'').'</th>
<td>'.$this->order['extdata']['key'].'</td>
</tr>
<tr>';
if($this->order['status']!=1){
$str .='
<th></th>
<td>
	<input class="pn pnc" name="submitbtn" value="'.$this->_lang['adminorders_cancelorder'].'" type="submit">'.$this->_lang['adminorders_cancelorder_msg'].'
</td>
</tr>';
}
$str .='</table>';
		return $str;
	}
	public function delete($oid){
		if(in_array($this->order['status'],array(1,3)))return false;
		C::t('#dc_mall#dc_mall_kami')->update($this->order['extdata']['id'],array('use'=>0,'oid'=>0));
		$keycount = C::t('#dc_mall#dc_mall_kami')->getcount(array('use'=>0,'gid'=>$this->order['gid']));
		C::t('#dc_mall#dc_mall_goods')->update($this->order['gid'],array('count'=>$keycount));
	}
	public function finish($oid){
		if(in_array($this->order['status'],array(1,3)))return false;
		$d = array(
			'status'=>3,
			'extdata'=>serialize(array()),
			'finishtime'=>TIMESTAMP,
		);
		C::t('#dc_mall#dc_mall_orders')->update($oid,$d);
		C::t('#dc_mall#dc_mall_kami')->update($this->order['extdata']['id'],array('use'=>0,'oid'=>0));
		$keycount = C::t('#dc_mall#dc_mall_kami')->getcount(array('use'=>0,'gid'=>$this->order['gid']));
		C::t('#dc_mall#dc_mall_goods')->update($this->order['gid'],array('count'=>$keycount));
	}
}
?>