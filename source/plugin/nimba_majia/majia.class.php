<?php
/*
 * ��ҳ��http://addon.discuz.com/?@ailab
 * �˹�����ʵ���ң�Discuz!Ӧ������ʮ�����㿪���ߣ�
 * ������� ��ϵQQ594941227
 * From www.ailab.cn
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_nimba_majia {
	function __construct(){
		global $_G;
		loadcache('plugin');
		$this->newip=$_G['clientip'];
		$this->allow=$this->allow();
	}
	
	function allow(){
		loadcache('plugin');
		global $_G;
		$var= $_G['cache']['plugin']['nimba_majia'];
		$groups=unserialize($var['groups']);
		$return=in_array($_G['groupid'],$groups)? 1:0;
		return $return;
	}
	
    function global_usernav_extra2(){
		global $_G;
	    loadcache('plugin');
		if($this->allow) return '<span class="pipe">|</span><a href="home.php?mod=spacecp&ac=plugin&id=nimba_majia:admincp" target="_blank"><span style="font: bold Verdana; color: #f15a29;">'.lang('plugin/nimba_majia', 'appname').'</span></a>';
		else return '';
    }	

}

class plugin_nimba_majia_forum extends plugin_nimba_majia{

	function viewthread_fastpost_side_output() {//���Ӳ鿴ҳ���ײ����ٻظ�
		loadcache('plugin');
		global $_G;
		if($this->allow){
			$repeatusers = array();
			$query = DB::query("SELECT * FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]'");
			while($majia=DB::fetch($query)){
				$repeatusers[]=$majia;
			}
			include template('nimba_majia:viewthread_fastpost_side');
			return $return;
		}else return '';
	}

	function post_side_bottom_output() {//���淢��ҳ���Ҳ�ѡ��
		loadcache('plugin');
		global $_G;
		if($this->allow){
			$repeatusers = array();
			$query = DB::query("SELECT * FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]'");
			while($majia=DB::fetch($query)){
				$repeatusers[]=$majia;
			}
			include template('nimba_majia:post_side_bottom');
			return $return;
		}else return '';
	}

	function forumdisplay_fastpost_content(){//�����б�ҳ���ײ����ٷ���
		loadcache('plugin');
		global $_G;
		if($this->allow){
			$repeatusers = array();
			$query = DB::query("SELECT * FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]'");
			while($majia=DB::fetch($query)){
				$repeatusers[]=$majia;
			}
			//已加入的群组
			if(helper_access::check_module('group')) {
				$mygroups = $groupids = array();
				$groupids = C::t('forum_groupuser')->fetch_all_fid_by_uids($_G['uid']);
				array_slice($groupids, 0, 20);
				$query = C::t('forum_forum')->fetch_all_info_by_fids($groupids);
				foreach($query as $group) {
					$mygroups[$group['fid']] = $group['name'];
				}
			}
			
			include template('nimba_majia:forumdisplay_fastpost_conten');
			return $return;
		}else return '';
	}

	function post_infloat_top(){//ajax�������ڻظ�
		loadcache('plugin');
		global $_G;
		if($this->allow){
			$repeatusers = array();
			$query = DB::query("SELECT * FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]'");
			while($majia=DB::fetch($query)){
				$repeatusers[]=$majia;
			}
			include template('nimba_majia:post_infloat_top');
			return $return;
		}else return '';
	}
	
	function post_middle(){//>=X3.0
		loadcache('plugin');
		global $_G;
		require_once DISCUZ_ROOT.'./source/discuz_version.php';
		$right=1;
		if(strtolower(substr(DISCUZ_VERSION,0,2))=='x2') $right=0;
		if($right==1&&$this->allow){
			$repeatusers = array();
			$query = DB::query("SELECT * FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]'");
			while($majia=DB::fetch($query)){
				$repeatusers[]=$majia;
			}
			include template('nimba_majia:post_infloat_top');
			return $return;
		}else return '';	
	}
	
	function post_feedlog_message($var){
		global $_G,$thread,$tid,$pid;
		$tid = $var['param'][2]['tid'];
		$pid = $var['param'][2]['pid'];
		$action=$var['param'][0];
		/*
		@require_once libfile('function/cache');
		$cacheArray='';
		$cacheArray .= "\$var=".arrayeval($var).";\n";
		$cacheArray .= "\$_GET=".arrayeval($_GET).";\n";
		writetocache('m_'.$pid, $cacheArray);
		*/
		$_GET['majiauid']=intval($_GET['majiauid']);
		if(!$_GET['majiauid']||($_GET['action']=='reply'&&!$pid)||!$this->allow ) return '';//|| ($_GET['action']=='newthread' && !$tid)
		$uid=$_GET['majiauid'];
		if($uid==$_G['uid']) $username=$_G['username'];//����
		else $username=DB::result_first("SELECT username FROM ".DB::table('nimba_majia')." WHERE uid='$_G[uid]' and useruid='$uid'");//ʹ�����
		$username=daddslashes($username);
		$do=0;
		if($action=='post_reply_succeed'){//new reply
			DB::update('forum_post',array('authorid'=>$uid,'author'=>$username,'useip'=>$this->newip),array('pid'=>$pid));
			C::t('common_member_count')->increase(array($_G['uid']),array('posts'=>-1));
			C::t('common_member_count')->increase(array($uid),array('posts'=>1));
			$subject=htmlspecialchars_decode(DB::result_first("select subject from ".DB::table('forum_thread')." where tid='$tid'"));
			$lastpost = "$tid\t$subject\t".TIMESTAMP."\t".$username;
			C::t('forum_forum')->update(array('fid'=>$_GET['fid']),array('lastpost'=>$lastpost));
			C::t('forum_thread')->update($tid,array('lastposter=\''.$username.'\''),false,false,0,true);
			$reppid=intval($_GET['reppid']);
			if($reppid) $first=DB::result_first("select first from ".DB::table('forum_post')." where pid='$reppid'");
			else $first=1;//fastpost or fastpost's AdvanceMode has no $_GET['reppid']
			if(!$first){//reply other reply !$first||$_GET['noticetrimstr']
				$notice=DB::fetch_first("select id,note from ".DB::table('home_notification')." where new=1 and authorid='".$_G['uid']."' and type='post' and from_idtype='quote' order by dateline desc");
				$noteid=intval($notice['id']);
				$note=array();
				$note['note']=str_replace($_G['username'],$username,str_replace('uid='.$_G['uid'],'uid='.$uid,$notice['note']));
				$note['authorid']=$uid;
				$note['author']=$username;
				DB::update('home_notification',$note,array('id'=>$noteid));	
			}else{//reply thread
				$notice=DB::fetch_first("select id,note from ".DB::table('home_notification')." where new=1 and authorid='".$_G['uid']."' and type='post' and from_idtype='post' order by dateline desc");
				$noteid=intval($notice['id']);
				$note=array();
				$note['note']=str_replace($_G['username'],$username,str_replace('uid='.$_G['uid'],'uid='.$uid,$notice['note']));
				$note['authorid']=$uid;
				$note['author']=$username;
				DB::update('home_notification',$note,array('id'=>$noteid));
			}
			$attach_num=DB::result_first("select count(*) from ".DB::table('forum_attachment')." where uid='".$_G['uid']."' and tid='$tid' and pid='$pid'");
			if($attach_num){//update attachment
				DB::update('forum_attachment',array('uid'=>$uid),array('uid'=>$_G['uid'],'tid'=>$tid,'pid'=>$pid));
				$tableid=intval($tid%10);
				DB::update('forum_attachment_'.$tableid,array('uid'=>$uid),array('uid'=>$_G['uid'],'tid'=>$tid,'pid'=>$pid));
			}			
		}elseif($action=='post_newthread_succeed'){//newthread
			DB::update('forum_thread',array('authorid'=>$uid,'author'=>$username,'lastposter'=>$username),array('tid'=>$tid));
			DB::update('forum_post',array('authorid'=>$uid,'author'=>$username,'useip'=>$this->newip),array('pid'=>$pid));
			//add by Daming 2015/06/24	处理动态
			DB::update('home_feed', array('uid'=>$uid,'username'=>$username), array('id'=>$tid,'idtype'=>'tid'));
			
			C::t('common_member_count')->increase(array($_G['uid']),array('threads'=>-1,'posts'=>-1));
			C::t('common_member_count')->increase(array($uid),array('threads'=>1,'posts'=>1));
			$subject=htmlspecialchars_decode(DB::result_first("select subject from ".DB::table('forum_thread')." where tid='$tid'"));
			$lastpost = "$tid\t$subject\t".TIMESTAMP."\t".$username;
			C::t('forum_forum')->update(array('fid'=>$_GET['fid']),array('lastpost'=>$lastpost));
			$attach_num=DB::result_first("select count(*) from ".DB::table('forum_attachment')." where uid='".$_G['uid']."' and tid='$tid' and pid='$pid'");
			if($attach_num){//update attachment
				DB::update('forum_attachment',array('uid'=>$uid),array('uid'=>$_G['uid'],'tid'=>$tid,'pid'=>$pid));
				$tableid=intval($tid%10);
				DB::update('forum_attachment_'.$tableid,array('uid'=>$uid),array('uid'=>$_G['uid'],'tid'=>$tid,'pid'=>$pid));
			}
		}
		DB::update('common_member_status',array('lastvisit'=>TIMESTAMP,'lastactivity'=>TIMESTAMP,'lastpost'=>TIMESTAMP),array('uid'=>$uid));
	}
}
class plugin_nimba_majia_group extends plugin_nimba_majia_forum{
}
?>