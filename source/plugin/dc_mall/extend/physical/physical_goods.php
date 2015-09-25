<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class physical_goods extends extend_goods{
	private $addr;
	public function view(){
		$return = '<div>'.$this->_lang['goods_maxbuy'].'<strong style="color:#FF0000; font-size:16px;">'.$this->goods['maxbuy'].'</strong> '.$this->_lang['goods_jian'].'</div>';
		$return .= '<div>'.$this->_lang['goods_allow'].($this->goods['buytimes']?$this->_lang['goods_buy'].' <strong style="color:#FF6600; font-size:16px;">'.$this->goods['buytimes'].'</strong> '.$this->_lang['goods_ci']:$this->_lang['goods_nolimit']).'</div>';
		$return .= parent::view();
		return $return;
	}
	public function payview(){
		global $_G;
		require_once libfile('function/profile');
		$values = array(0,0,0,0);
		$elems = array('resideprovince', 'residecity', 'residedist', 'residecommunity');
		$reside = '<p id="residedistrictbox">'.showdistrict($values, $elems, 'residedistrictbox', 1, 'reside').'</p>';
		$address = C::t('#dc_mall#dc_mall_address')->getbyuid($_G['uid']);
		$str = '
		<div>
		<div style="background: none repeat scroll 0% 0% #F80;padding: 4px;color:#FFF">'.$this->_lang['goods_sctaddr'].'</div>
		<ul>';
foreach($address as $v){
	$str .='<li class="vm">
				<input name="txt_addr" id="txt_addr_'.$v['id'].'" value="'.$v['id'].'" type="radio" onclick="selectaddr(this.value);" '.($v['default']?'checked':'').'><label for="txt_addr_'.$v['id'].'">'.$v['address'].' ('.$v['realname'].':'.$v['tel'].')</label>
			</li>';
}
	$str .='<li class="vm">
				<input name="txt_addr" id="txt_addr_0" value="0" type="radio" onclick="selectaddr(this.value);"'.(!$address?' checked':'').'><label for="txt_addr_0">'.$this->_lang['goods_sctoaddr'].'</label>
			</li>';
	$str .='		
			<li id="otheraddr"'.($address?'style="display:none;"':'').'>
				<table width="100%" cellpadding="0" id="newaddress" cellspacing="0" style="border:1px solid #E4E4E4;">
<tr>
<td align="right">'.$this->_lang['goods_addr'].'</td>
<td>'.$reside.'<input type="text" name="txt_address" id="txt_address"  size="50" value=""/></td>
</tr>
<tr>
<td align="right">'.$this->_lang['goods_name'].'</td>
<td><input type="text" name="txt_realname" id="txt_realname" value=""/></td>
</tr>
<tr>
<td align="right">'.$this->_lang['goods_tel'].'</td>
<td><input type="text" name="txt_tel" id="txt_tel" value=""/></td>
</tr>
</table>
			</li>
		</ul>
	</div>
	<script>
		function selectaddr(addrid){
			if(addrid==0)
				$("otheraddr").style.display="";
			else
				$("otheraddr").style.display="none";
			
		}
	</script>';
		
		return $str;
	}
	public function paycheck(){//订单创建前检测
		global $_G;
		if(isset($_GET['txt_addr'])){
			$addid = dintval($_GET['txt_addr']);
			if($addid){
				$addr = C::t('#dc_mall#dc_mall_address')->fetch($addid);
				if(!$addr||$addr['uid']!=$_G['uid'])showmessage($this->_lang['goods_sctaddrerror']);
				$this->addr = $addr['realname']."\t".$addr['address']."\t".$addr['tel'];
			}else{
				
				$strAddress  = trim($_GET['resideprovince']).trim($_GET['residecity']).trim($_GET['residedist']).trim($_GET['residecommunity']).trim($_GET['txt_address']);
				$strRealname  = trim($_GET['txt_realname']);
				$strTel  = trim($_GET['txt_tel']);
				if(!$strAddress||!$strRealname||!$strTel)showmessage($this->_lang['goods_addrerror']);
				$setarr = array(
					'uid' => $_G['uid'],
					'realname' => $strRealname,
					'address' => $strAddress,
					'tel' => $strTel,
					'default'=>0,
				);
				DB::insert('dc_mall_address', $setarr);
				$this->addr =  $strRealname."\t".$strAddress."\t".$strTel;
					
			}
			return;
		}
		showmessage($this->_lang['goods_sctaddrerror']);
	}
	
	public function payfinish($orderid){//订单完成后处理
		$data = array(
			'address'=>$this->addr,
		);
		C::t('#dc_mall#dc_mall_orders')->update($orderid,$data);
	}
	public function finish($order){ //完成后执行
		
	}
}
?>