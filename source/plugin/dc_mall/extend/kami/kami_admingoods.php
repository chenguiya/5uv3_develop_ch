<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class kami_admingoods extends extend_admingoods{
	public function view(){
		$str = '
<tr>
<th>'.$this->_lang['admingoods_times'].'</th>
<td colspan="2"><input name="txt_buytimes" id="txt_buytimes" value="'.$this->goods['buytimes'].'" class="p_fre" type="text">&nbsp;<em>'.$this->_lang['admingoods_times_msg'].'</em></td>
</tr>
';
		return $str;
	}
	public function check(){
		$maxbuy = 1;
		$buytimes = dintval($_GET['txt_buytimes']);
		$count = C::t('#dc_mall#dc_mall_kami')->getcount(array('use'=>0,'gid'=>$this->goods['id']));
		return array('count'=>$count,'maxbuy'=>$maxbuy,'buytimes'=>$buytimes);
	}
	public function delete($gid){
		C::t('#dc_mall#dc_mall_kami')->deletebygid($gid);
	}
}
?>