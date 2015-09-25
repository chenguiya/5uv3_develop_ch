<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class usergroup_admingoods extends extend_admingoods{
	public function view(){
		$this->goods['extdata'] = dunserialize($this->goods['extdata']);
		$query = C::t('common_usergroup')->fetch_all_by_type('special','0');
		$groupselect;
		foreach($query as $group) {
			if($group['groupid'] == $this->goods['extdata']['usergroup']) {
				$groupselect .= "<option value=\"$group[groupid]\" selected>$group[grouptitle]</option>\n";
			} else {
				$groupselect .= "<option value=\"$group[groupid]\">$group[grouptitle]</option>\n";
			}
		}
		
		$str = '
<tr>
<th>'.$this->_lang['admingoods_usergroup'].'</th>
<td colspan="2"><select name="txt_usergroup" id="txt_usergroup">'.$groupselect.'</select></td>
</tr>
<tr>
<th>'.$this->_lang['admingoods_yxq'].'</th>
<td colspan="2"><input name="txt_yxq" id="txt_yxq" value="'.$this->goods['extdata']['yxq'].'" class="p_fre" type="text">'.$this->_lang['admingoods_yxq_msg'].'</td>
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
		$maxbuy = 1;
		$buytimes = dintval($_GET['txt_buytimes']);
		$count = dintval($_GET['txt_count']);
		return array('count'=>$count,'maxbuy'=>1,'buytimes'=>$buytimes);
	}
	public function finish($gid){
		$usergroup = dintval($_GET['txt_usergroup']);
		$yxq = dintval($_GET['txt_yxq']);
		$extdata = array(
			'usergroup'=>$usergroup,
			'yxq'=>$yxq,
		);
		$data['extdata'] = serialize($extdata);
		C::t('#dc_mall#dc_mall_goods')->update($gid,$data);
	}
}
?>