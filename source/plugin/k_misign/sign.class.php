<?php
!defined('IN_DISCUZ') && exit('Access Denied');

class plugin_k_misign{	
	function _k_misign_showbutton($width) {
		global $_G;
		if(empty($_GET['fid']) || intval($_GET['fid']) == 0){
			return '';
		}
        
		$setting = $_G['cache']['plugin']['k_misign'];
		$setting['groups'] = unserialize($setting['groups']);
		$setting['ban'] = explode(",",$setting['ban']);
		$setting['width'] = $setting['width'] ? $setting['width'] : 220;
		$setting['bcolor'] = $setting['bcolor'] ? $setting['bcolor'] : '#ff6f3d';
		$setting['hcolor'] = $setting['hcolor'] ? $setting['hcolor'] : '#ff7d49';
		
		$setting['width'] = $width ? $width : $setting['width'];
		$fid = htmlspecialchars($_GET['fid']);  //获取fid
		$stats = DB::fetch_first("SELECT * FROM ".DB::table('plugin_k_misignset')." WHERE fid={$fid}");
		$qiandaodb = DB::fetch_first("SELECT * FROM ".DB::table('plugin_k_misign')." WHERE  fid={$fid} and uid='$_G[uid]' order by time desc");
		$tdtime = gmmktime(0,0,0,dgmdate($_G['timestamp'], 'n',$setting['tos']),dgmdate($_G['timestamp'], 'j',$setting['tos']),dgmdate($_G['timestamp'], 'Y',$setting['tos'])) - $setting['tos']*3600;
		$allowmem = memory('check');
		$t_time=date('m月d日');
		if($qiandaodb){
			$lq_time = floor((time()-$qiandaodb['time'])/86400);
		}else{
			$lq_time = 0;
		}
		if((!in_array($_G['uid'], $setting['ban']) && in_array($_G['groupid'], $setting['groups'])) || !$_G['uid']) {
			if($allowmem && $setting['mcacheopen']){
				$signtime = memory('get', 'k_misign_'.$_G['uid']);
			}
			if(!$signtime){
				$htime = dgmdate($_G['timestamp'], 'H',$setting['tos']);
				if($qiandaodb){
					if($allowmem && $setting['mcacheopen']){
						memory('set', 'k_misign_'.$_G['uid'], $qiandaodb['time'], 86400);
					}
					if($qiandaodb['time'] < $tdtime){
						include template("k_misign:hook_indexside");
						return $return;
					}else{
						include template("k_misign:hook_indexside");
						return $return;
					}
				}else{
					include template("k_misign:hook_indexside");
					return $return;
				}
			}else{
				if($signtime < $tdtime){
					include template("k_misign:hook_indexside");
					return $return;
				}else{
					include template("k_misign:hook_indexside");
					return $return;
				}
			}
		}
		return '';
	}
	function global_qiandao(){
		global $_G;
		$setting = $_G['cache']['plugin']['k_misign'];
		$setting['modstatus'] = unserialize($setting['modstatus']);
		if($setting['modstatus']['group_viewthread']['status']){
			return $this->_k_misign_showbutton($setting['modstatus']['group_viewthread']['width']);
		}else{
			return '';
		}
	}
}

class plugin_k_misign_forum extends plugin_k_misign{
	function  forumdisplay_side_top_output(){
		global $_G;
		$setting = $_G['cache']['plugin']['k_misign'];
		$setting['modstatus'] = unserialize($setting['modstatus']);
		if($setting['modstatus']['group_viewthread']['status']){
			return $this->_k_misign_showbutton($setting['modstatus']['group_viewthread']['width']);
		}else{
			return;
		}
	}
	
}
class plugin_k_misign_group extends plugin_k_misign{
	
	function group_qiandao(){
		global $_G;
		$setting = $_G['cache']['plugin']['k_misign'];
		$setting['modstatus'] = unserialize($setting['modstatus']);
		if($setting['modstatus']['group_viewthread']['status']){
			return $this->_k_misign_showbutton($setting['modstatus']['group_viewthread']['width']);
		}else{
			return;
		}
	}
	function forumdisplay_qiandao() {
		global $_G;
		$setting = $_G['cache']['plugin']['k_misign'];
		$setting['modstatus'] = unserialize($setting['modstatus']);
		if($setting['modstatus']['group_forumdisplay']['status']){
			return $this->_k_misign_showbutton($setting['modstatus']['group_forumdisplay']['width']);
		}else{
			return;
		}
	}
}

?>