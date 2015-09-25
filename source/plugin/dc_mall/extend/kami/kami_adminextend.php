<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class kami_adminextend extends extend_adminextend{
	public function dorun(){
		if(submitcheck('submitchk')){
			$keys=explode("\n",str_replace("\r\n","\n",daddslashes(trim($_GET['txt_keys']))));
			$i=0;
			foreach($keys as $k){
				$k = trim($k);
				if($k&&!C::t('#dc_mall#dc_mall_kami')->fetchbykey($k,$this->goods['id'])){
					C::t('#dc_mall#dc_mall_kami')->insert(array('key'=>$k,'gid'=>$this->goods['id'],'dateline'=>TIMESTAMP));
					$i++;
				}
			}
			$keycount = C::t('#dc_mall#dc_mall_kami')->getcount(array('use'=>0,'gid'=>$this->goods['id']));
			C::t('#dc_mall#dc_mall_goods')->update($this->goods['id'],array('count'=>$keycount));
			showmessage($this->_lang['extend_import_msg'],'plugin.php?id=dc_mall:admin&action=goods&op=extend&do=list&gid='.$this->goods['id'],array('goodsname'=>$this->goods['name'],'count'=>$i));
		}
	}
	public function dolist(){
		global $_G;
		$act = in_array($_GET['act'],array('use','nouse','lock'))?$_GET['act']:'';
		$page = dintval($_GET['page']);
		$page = $page?$page:1;
		$perpage = 20;
		$start = ($page-1)*$perpage;
		$wherearr = array();
		$wherearr['gid'] = $this->goods['id'];
		if($act){
			$acturl='&act='.$act;
			if($act=='use')
				$wherearr['use']=1;
			elseif($act=='lock')
				$wherearr['use']=2;
			else
				$wherearr['use']=0;
		}
		$_G['dc_mall']['keys'] = C::t('#dc_mall#dc_mall_kami')->getrange($wherearr,$start,$perpage);
		$count = C::t('#dc_mall#dc_mall_kami')->getcount($wherearr);
		$_G['dc_mall']['multi'] = multi($count, $perpage, $page, 'plugin.php?id=dc_mall:admin&action=goods&op=extend&do=list'.$acturl.'&gid='.$this->goods['id']);
	}
	public function dodelete(){
		$kid = dintval($_GET['kid'],true);
		if(submitcheck('submitchk')){
			C::t('#dc_mall#dc_mall_kami')->deleteids($kid,$this->goods['id']);
			$keycount = C::t('#dc_mall#dc_mall_kami')->getcount(array('use'=>0,'gid'=>$this->goods['id']));
			C::t('#dc_mall#dc_mall_goods')->update($this->goods['id'],array('count'=>$keycount));
			showmessage($this->_lang['extend_delete_succeed'],'plugin.php?id=dc_mall:admin&action=goods&page=1&op=extend&do=list&gid='.$this->goods['id']);
		}
		
		
	}
}
?>