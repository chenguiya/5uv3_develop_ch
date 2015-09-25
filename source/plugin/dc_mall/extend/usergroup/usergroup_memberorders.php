<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class usergroup_memberorders extends extend_memberorders{
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
</table>';
		return $str;
	}
}
?>