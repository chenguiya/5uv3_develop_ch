<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class physical_admingoods extends extend_admingoods{
	public function view(){
		$str = '
<tr>
<th>'.$this->_lang['admingoods_num'].'<font color="#FF0000">*</font></th>
<td colspan="2"><input name="txt_maxbuy" id="txt_maxbuy" value="'.$this->goods['maxbuy'].'" class="p_fre" type="text">'.$this->_lang['admingoods_num_msg'].'</td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_times'].'</th>
<td colspan="2"><input name="txt_buytimes" id="txt_buytimes" value="'.$this->goods['buytimes'].'" class="p_fre" type="text">'.$this->_lang['admingoods_times_msg'].'</td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_cunku'].'</th>
<td colspan="2"><input name="txt_count" id="txt_count" value="'.$this->goods['count'].'" class="p_fre" type="text"></td>
</tr>';
		return $str;
	}
	public function check(){
		$count = dintval($_GET['txt_count']);
		$maxbuy = dintval($_GET['txt_maxbuy']);
		$buytimes = dintval($_GET['txt_buytimes']);
		return array('count'=>$count,'maxbuy'=>$maxbuy,'buytimes'=>$buytimes);
	}
}
?>