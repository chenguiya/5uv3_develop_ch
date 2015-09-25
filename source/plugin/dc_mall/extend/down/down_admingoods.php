<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class down_admingoods extends extend_admingoods{
	public function view(){
		if($this->goods['extdata'])
			$this->goods['extdata'] = dunserialize($this->goods['extdata']);
		$str = '
<tr>
<th>'.$this->_lang['admingoods_times'].'</th>
<td colspan="2"><input name="txt_buytimes" id="txt_buytimes" value="'.$this->goods['buytimes'].'" class="p_fre" type="text">'.$this->_lang['admingoods_times_msg'].'</td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_cunku'].'</th>
<td colspan="2"><input name="txt_count" id="txt_count" value="'.$this->goods['count'].'" class="p_fre" type="text"></td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_downurl'].'</th>
<td colspan="2"><input name="txt_downurl" id="txt_downurl" value="'.($this->goods['extdata']['downurl']?$this->goods['extdata']['downurl']:'http://').'" size="80" class="p_fre" type="text"></td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_downcode'].'</th>
<td colspan="2"><input name="txt_downcode" id="txt_downcode" value="'.$this->goods['extdata']['downcode'].'" class="p_fre" type="text"></td>
</tr>
';
		return $str;
	}
	public function check(){
		$count = dintval($_GET['txt_count']);
		$maxbuy = 1;
		$buytimes = dintval($_GET['txt_buytimes']);
		return array('count'=>$count,'maxbuy'=>$maxbuy,'buytimes'=>$buytimes);
	}
	public function finish($gid){
		$d=array('downurl'=>addslashes($_GET['txt_downurl']),'downcode'=>addslashes($_GET['txt_downcode']));
		$data['extdata']=serialize($d);
		C::t('#dc_mall#dc_mall_goods')->update($gid,$data);
	}
}
?>