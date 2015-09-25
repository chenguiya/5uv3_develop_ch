<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class usergroup_goods extends extend_goods{
	public function view(){
		$extdata = dunserialize($this->goods['extdata']);
		$usergroup = C::t('common_usergroup')->fetch($extdata['usergroup']);
		if(!$usergroup)showmessage('dc_mall:error');
		$return = '<div>'.$this->_lang['goods_usergroup'].'<strong style="color:#FF0000; font-size:16px;">'.$usergroup['grouptitle'].'</strong></div>';
		$return .= '<div>'.$this->_lang['goods_yxq'].'<strong style="color:#FF0000; font-size:16px;">'.($extdata['yxq']?$extdata['yxq'].'</strong> '.$this->_lang['goods_tian']:$this->_lang['goods_forever'].'</strong> ').'</div>';
		$return .= '<div>'.$this->_lang['goods_maxbuy'].'<strong style="color:#FF0000; font-size:16px;">'.$this->goods['maxbuy'].'</strong> '.$this->_lang['goods_jian'].'</div>';
		$return .= '<div>'.$this->_lang['goods_allow'].($this->goods['buytimes']?$this->_lang['goods_buy'].' <strong style="color:#FF6600; font-size:16px;">'.$this->goods['buytimes'].'</strong> '.$this->_lang['goods_ci']:$this->_lang['goods_nolimit']).'</div>';
		$return .= parent::view();
		return $return;
	}
	public function paycheck(){
		$extdata = dunserialize($this->goods['extdata']);
		$usergroup = C::t('common_usergroup')->fetch($extdata['usergroup']);
		if(!$usergroup)showmessage('dc_mall:error');
		return array('usergroup'=>$extdata['usergroup'],'yxq'=>$extdata['yxq']);
	}
	public function finish($order){
		C::t('#dc_mall#dc_mall_orders')->update($order['id'],array('status'=>1,'finishtime'=>TIMESTAMP));
		$cm = C::t('common_member')->fetch($order['uid']);
		$cnff = C::t('common_member_field_forum')->fetch($order['uid']);
		$gt=dunserialize($cnff['groupterms']);
		$extg=explode("\t",$cm['extgroupids']);
		if(empty($extg[0]))
			unset($extg[0]);
		if($order['extdata']['usergroup']!=$cm['groupid']){
			$extg[]=$cm['groupid'];
		}
		foreach($extg as $k=>$v){
			if($v==$order['extdata']['usergroup'])
				unset($extg[$k]);
		}
		$extgroupids=implode("\t",$extg);
		if($order['extdata']['yxq']){
			if(TIMESTAMP>$gt['ext'][$order['extdata']['usergroup']])
				$gt['ext'][$order['extdata']['usergroup']]=TIMESTAMP+(3600*24*$order['extdata']['yxq']);
			else
				$gt['ext'][$order['extdata']['usergroup']]=$gt['ext'][$order['extdata']['usergroup']]+(3600*24*$order['extdata']['yxq']);
			$groupexpiry=intval($gt['ext'][$order['extdata']['usergroup']]);
			$d = array(
				'groupid'=>$order['extdata']['usergroup'],
				'extgroupids'=>$extgroupids,
				'groupexpiry'=>$groupexpiry,
			);
			C::t('common_member')->update($order['uid'],$d);
			C::t('common_member_field_forum')->update($order['uid'],array('groupterms'=>serialize($gt)));
		}else{
			C::t('common_member')->update($order['uid'],array('groupid'=>$order['extdata']['usergroup'],'groupexpiry'=>0,'extgroupids'=>$extgroupids,));
			unset($gt['ext'][$order['extdata']['usergroup']]);
			C::t('common_member_field_forum')->update($order['uid'],array('groupterms'=>serialize($gt)));
		}
	}
}
?>