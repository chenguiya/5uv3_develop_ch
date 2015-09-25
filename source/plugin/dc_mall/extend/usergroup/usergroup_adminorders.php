<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class usergroup_adminorders extends extend_adminorders{
	public function view(){
		$usergroup = C::t('common_usergroup')->fetch($this->order['extdata']['usergroup']);
		$str ='<table width="100%" cellpadding="0" cellspacing="0" class="orderinfo">
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_usergroup'].'</th>
<td>'.$usergroup['grouptitle'].'</td>
</tr>
<tr>
<th style="width:80px;">'.$this->_lang['memberorders_yxq'].'</th>
<td>'.($this->order['extdata']['yxq']?$this->order['extdata']['yxq'].$this->_lang['goods_tian']:$this->_lang['goods_forever']).'</td>
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