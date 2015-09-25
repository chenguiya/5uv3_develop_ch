<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class credit_admingoods extends extend_admingoods{
	public function view(){
		global $_G;
		$this->goods['extdata'] = dunserialize($this->goods['extdata']);
		$creditstr = '<select id="txt_creditidk" name="txt_creditidk" class="p_fre">';
		$creditstr .= '<option value="" >'.$this->_lang['admingoods_selcredit'].'</option>';
foreach($_G['setting']['extcredits'] as $k=>$ext){
$creditstr .= '<option value="'.$k.'" '.($this->goods['extdata']['id']==$k?'selected':'').'>'.$ext['title'].'</option>';
}
$creditstr .= '</select>';
		$str = '
<tr>
<th>'.$this->_lang['admingoods_credit'].'<font color="#FF0000">*</font></th>
<td colspan="2">'.$creditstr.$this->_lang['admingoods_credit_msg'].'</td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_creditnum'].'</th>
<td colspan="2"><input name="txt_creditnum" id="txt_creditnum" value="'.$this->goods['extdata']['credit'].'" class="p_fre" type="text"></td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_times'].'</th>
<td colspan="2"><input name="txt_buytimes" id="txt_buytimes" value="'.$this->goods['buytimes'].'" class="p_fre" type="text">'.$this->_lang['admingoods_times_msg'].'</td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_kucun'].'</th>
<td colspan="2"><input name="txt_kucun" id="txt_kucun" value="'.$this->goods['count'].'" class="p_fre" type="text"></td>
</tr>
';
		return $str;
	}
	public function check(){
		global $_G;
		$creditid = dintval($_GET['txt_creditidk']);
		print_r($creditid);
		if(!$_G['setting']['extcredits'][$creditid])showmessage('dc_mall:error');
		$maxbuy = 1;
		$buytimes = dintval($_GET['txt_buytimes']);
		$count = dintval($_GET['txt_kucun']);
		return array('count'=>$count,'maxbuy'=>$maxbuy,'buytimes'=>$buytimes);
	}
	public function finish($gid){
		$d=array('id'=>dintval($_GET['txt_creditidk']),'credit'=>dintval($_GET['txt_creditnum']));
		$data['extdata']=serialize($d);
		C::t('#dc_mall#dc_mall_goods')->update($gid,$data);
	}
}
?>