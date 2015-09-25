<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$op = trim($_GET['op']);
if($op == 'changestate'){
	$gid = dintval($_GET['gid']);
	$goods = C::t('#dc_mall#dc_mall_goods')->fetch($gid);
	if(empty($goods))showmessage($_lang['admin_goods_nog']);
	if(submitcheck('submitcheck')){
		C::t('#dc_mall#dc_mall_goods')->update($gid,array('status'=>!$goods['status']));
		showmessage($_lang['succeed'],dreferer());
	}
}elseif($op == 'delete'){
	$gid = dintval($_GET['gid']);
	$goods = C::t('#dc_mall#dc_mall_goods')->fetch($gid);
	if(empty($goods))showmessage($_lang['admin_goods_nog']);
	if(submitcheck('submitcheck')){
		C::t('#dc_mall#dc_mall_goods')->delete($gid);
		if($goods['pic']){
			require_once libfile('function/home');
			pic_delete($goods['pic'], 'common', $goods['pic'].'.thumb.jpg', 0);
		}
		$extend = C::t('#dc_mall#dc_mall_extend')->fetch($goods['extid']);
		C::import('extend/admingoods','plugin/dc_mall');
		$identify = explode(':',$ext['identify']);
		if(count($identify)==2){
			$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
			C::import($identify[1].'_admingoods','plugin/'.$hook['directory'].'/dcmallextend',false);
			$modstr = $identify[1].'_admingoods';
		}else{
			C::import($ext['identify'].'/admingoods','plugin/dc_mall/extend',false);
			$modstr = $ext['identify'].'_admingoods';
		}
		if(class_exists($modstr,false)){
			$mobj = new $modstr($goods);
			$check = $mobj->delete($gid);
		}
		$admingoodsurl = getcookie('admingoodsurl');
		if(!$admingoodsurl)$admingoodsurl='plugin.php?id=dc_mall:admin&action=goods';
		showmessage($_lang['delsucceed'],$admingoodsurl);
	}
}elseif($op == "add"){
	$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
	if(submitcheck('submitchk')){
		require_once libfile('function/home');
		$name = getstr(trim(daddslashes($_GET['txt_name'])), 200, 1, 1, 1);
		if(strlen($name) < 1) {
			showmessage($_lang['admin_goods_notitle']);
		}
		$sortid = dintval($_GET['txt_sortid']);
		if(empty($mallnav[$sortid]))showmessage($_lang['admin_goods_nosort']);
		$creditid = dintval($_GET['txt_creditid']);
		$creditid = $_G['setting']['extcredits'][$creditid]?$creditid:99;
		$credit = dintval($_GET['txt_credit']);
		if($_G['dc_mall']['vip']['open'])
			$vipcredit = dintval($_GET['txt_vipcredit']);
		else
			$vipcredit = $credit;
		$hot = dintval($_GET['txt_hot']);
		$enddateline = strtotime($_GET['txt_enddateline']);
		$des = getstr(trim($_GET['txt_des']),10000, 1, 1, 1, 1);
		$extid = dintval($_GET['txt_extid']);
		$allowgids = dintval($_GET['txt_groupids'],true);
		if(!$extends[$extid])showmessage($_lang['admin_goods_noext']);
		C::import('extend/admingoods','plugin/dc_mall');
		$identify = explode(':',$extends[$extid]['identify']);
		if(count($identify)==2){
			$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
			C::import($identify[1].'_admingoods','plugin/'.$hook['directory'].'/dcmallextend',false);
			$modstr = $identify[1].'_admingoods';
		}else{
			C::import($extends[$extid]['identify'].'/admingoods','plugin/dc_mall/extend',false);
			$modstr = $extends[$extid]['identify'].'_admingoods';
		}
		if(class_exists($modstr,false)){
			$mobj = new $modstr();
		}else{
			showmessage($_lang['error']);
		}
		$check = $mobj->check();
		$maxbuy = $check['maxbuy'];
		$buytimes = $check['buytimes'];
		$count = $check['count'];
		$data = array(
			'name'=>$name,
			'sortid'=>$sortid,
			'creditid'=>$creditid,
			'credit'=>$credit,
			'vipcredit'=>$vipcredit,
			'count'=>$count,
			'hot'=>$hot,
			'enddateline'=>$enddateline,
			'des'=>$des,
			'maxbuy'=>$maxbuy?$maxbuy:1,
			'buytimes'=>$buytimes,
			'extid'=>$extid,
			'sales'=>0,
			'dateline'=>TIMESTAMP,
			'status'=>0,
			'allowgroup'=>serialize($allowgids),
			
		);
		if($_FILES['txt_pic']){
			if($files = pic_upload($_FILES['txt_pic'], 'common', 225, 180, 2)){
				$data['pic'] = "data/attachment/common/".$files['pic'];
			}
		}
		$gid = C::t('#dc_mall#dc_mall_goods')->insert($data,true);
		$mobj->finish($gid);
		$admingoodsurl = getcookie('admingoodsurl');
		if(!$admingoodsurl)$admingoodsurl='plugin.php?id=dc_mall:admin&action=goods';
		showmessage($_lang['admin_goods_addsucceed'],$admingoodsurl);
	}
	$allowgroupstr = '<select name="txt_groupids[]" size="10" multiple="multiple"><option value="">'.$_lang['empty'].'</option>';
	$query = C::t('common_usergroup')->range_orderby_credit();
	$groupselect = array();
	foreach($query as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groupselect[$group['type']] .= '<option value="'.$group['groupid'].'">'.$group['grouptitle'].'</option>';
	}
	$allowgroupstr .= '<optgroup label="'.$_lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
							($groupselect['special'] ? '<optgroup label="'.$_lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
							($groupselect['specialadmin'] ? '<optgroup label="'.$_lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
							'<optgroup label="'.$_lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup></select>';
}elseif($op == "update"){
	$gid = dintval($_GET['gid']);
	$goods = C::t('#dc_mall#dc_mall_goods')->fetch($gid);
	if(empty($goods))showmessage($_lang['admin_goods_nog']);
	$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
	$hookstr = '';
	C::import('extend/admingoods','plugin/dc_mall');
	$identify = explode(':',$extends[$goods['extid']]['identify']);
	if(count($identify)==2){
		$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
		C::import($identify[1].'_admingoods','plugin/'.$hook['directory'].'/dcmallextend',false);
		$modstr = $identify[1].'_admingoods';
	}else{
		C::import($extends[$goods['extid']]['identify'].'/admingoods','plugin/dc_mall/extend',false);
		$modstr = $extends[$goods['extid']]['identify'].'_admingoods';
	}
	if(class_exists($modstr,false)){
		$mobj = new $modstr($goods);
	}else{
		showmessage($_lang['error']);
	}
	if(submitcheck('submitchk')){
		require_once libfile('function/home');
		$name = getstr(trim(daddslashes($_GET['txt_name'])), 200, 1, 1, 1);
		if(strlen($name) < 1) {
			showmessage($_lang['admin_goods_notitle']);
		}
		$sortid = dintval($_GET['txt_sortid']);
		if(empty($mallnav[$sortid]))showmessage($_lang['admin_goods_nosort']);
		$creditid = dintval($_GET['txt_creditid']);
		$creditid = $_G['setting']['extcredits'][$creditid]?$creditid:99;
		$credit = dintval($_GET['txt_credit']);
		if($_G['dc_mall']['vip']['open'])
			$vipcredit = dintval($_GET['txt_vipcredit']);
		else
			$vipcredit = $credit;
		$hot = dintval($_GET['txt_hot']);
		$enddateline = strtotime($_GET['txt_enddateline']);
		$des = getstr(trim($_GET['txt_des']), 10000, 1, 1, 1, 1);
		$allowgids = dintval($_GET['txt_groupids'],true);
		$check = $mobj->check();
		$maxbuy = $check['maxbuy'];
		$buytimes = $check['buytimes'];
		$count = $check['count'];
		$data = array(
			'name'=>$name,
			'sortid'=>$sortid,
			'creditid'=>$creditid,
			'credit'=>$credit,
			'vipcredit'=>$vipcredit,
			'count'=>$count,
			'hot'=>$hot,
			'enddateline'=>$enddateline,
			'des'=>$des,
			'maxbuy'=>$maxbuy?$maxbuy:1,
			'buytimes'=>$buytimes,
			'allowgroup'=>serialize($allowgids),
		);
		if($_FILES['txt_pic']) {
			if($files = pic_upload($_FILES['txt_pic'], 'common', 225, 180, 2)){
				pic_delete($goods['pic'], 'common', $goods['pic'].'.thumb.jpg', 0);
				$data['pic'] = "data/attachment/common/".$files['pic'];
			}
		}
		C::t('#dc_mall#dc_mall_goods')->update($gid,$data);
		$mobj->finish($gid);
		$admingoodsurl = getcookie('admingoodsurl');
		if(!$admingoodsurl)$admingoodsurl='plugin.php?id=dc_mall:admin&action=goods';
		showmessage($_lang['admin_goods_editsucceed'],$admingoodsurl);
	}
	$goods['des'] = dstripslashes($goods['des']);
	$hookstr = $mobj->view();
	$gids = dunserialize($goods['allowgroup']);
	$allowgroupstr = '<select name="txt_groupids[]" size="10" multiple="multiple"><option value="" '.(@in_array(0, $gids) ? ' selected' : '').'>'.$_lang['empty'].'</option>';
	$query = C::t('common_usergroup')->range_orderby_credit();
	$groupselect = array();
	foreach($query as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groupselect[$group['type']] .= '<option value="'.$group['groupid'].'" '.(@in_array($group['groupid'], $gids) ? ' selected' : '').'>'.$group['grouptitle'].'</option>';
	}
	$allowgroupstr .= '<optgroup label="'.$_lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
							($groupselect['special'] ? '<optgroup label="'.$_lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
							($groupselect['specialadmin'] ? '<optgroup label="'.$_lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
							'<optgroup label="'.$_lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup></select>';
}elseif($op == "selectext"){
	$extid = dintval($_GET['extid']);
	$return = '';
	if($extid){
		$extend = C::t('#dc_mall#dc_mall_extend')->fetch($extid);
		if($extend){
			C::import('extend/admingoods','plugin/dc_mall');
			$identify = explode(':',$extend['identify']);
			if(count($identify)==2){
				$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
				C::import($identify[1].'_admingoods','plugin/'.$hook['directory'].'/dcmallextend',false);
				$modstr = $identify[1].'_admingoods';
			}else{
				C::import($extend['identify'].'/admingoods','plugin/dc_mall/extend',false);
				$modstr = $extend['identify'].'_admingoods';
			}
			if(class_exists($modstr,false)){
				$mobj = new $modstr();
				$return = $mobj->view();
			}
		}
	}
	if(!$return){
		$return = '<tr>
<th></th>
<td colspan="2">'.$_lang['admin_goods_noext'].'</td>
</tr>';
	}
	include template('common/header_ajax');
	echo $return;
	include template('common/footer_ajax');
}elseif($op == 'extend'){
	$gid = dintval($_GET['gid']);
	$do = trim($_GET['do']);
	if(empty($do))$do='run';
	$goods = C::t('#dc_mall#dc_mall_goods')->fetch($gid);
	if(empty($goods))showmessage($_lang['admin_goods_nog']);
	$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
	$hookstr = '';
	C::import('extend/adminextend','plugin/dc_mall');
	if(!C::import($extends[$goods['extid']]['identify'].'/adminextend','plugin/dc_mall/extend',false))showmessage($_lang['error']);
	$modstr = $extends[$goods['extid']]['identify'].'_adminextend';
	$loadtemp = 'extend/'.$extends[$goods['extid']]['identify'].'/adminextend';
	if(class_exists($modstr,false)){
		$mobj = new $modstr($goods);
		$method = 'do'.$do;
		if(in_array($method,get_class_methods($mobj))){
			$mobj->$method();
		}else{
			$mobj->dorun();
		}
	}else{
		showmessage($_lang['error']);
	}
	$_tlang = &$_G['dc_mall']['extend']['lang'];
	dsetcookie('adminordersurl','plugin.php?id=dc_mall:admin&action=goods&op=extend&do=list&gid='.$gid);
	
}else{
	$sortid = dintval($_GET['sortid']);
	$extid = dintval($_GET['extid']);
	$searchkeyword = trim(daddslashes($_GET['searchkeyword']));
	$orderby = in_array($_GET['orderby'],array('credit','sales','count'))?$_GET['orderby']:'id';
	$da = $_GET['da']=='asc'?'asc':'desc';
	$page = dintval($_GET['page']);
	$page = $page?$page:1;
	$perpage = 12;
	$start = ($page-1)*$perpage;
	$wherearr = array();
	if($sortid){
		$wherearr['sortid'] = $sortid;
	}
	if($extid){
		$wherearr['extid'] = $extid;
	}
	if($searchkeyword){
		$wherearr['name'] = $searchkeyword;
	}
	$goodslist = C::t('#dc_mall#dc_mall_goods')->range($start,$perpage,$wherearr,$orderby,$da);
	$count = C::t('#dc_mall#dc_mall_goods')->count($wherearr);
	$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
	foreach($extends as &$ev){
		$ev['data'] = dunserialize($ev['data']);
	}
	$wherestr='';
	foreach($wherearr as $k=>$v){
		if($k=='name')$k='searchkeyword';
		$wherestr.='&'.$k.'='.urlencode($v);
	}
	$multiurl = 'plugin.php?id=dc_mall:admin&action=goods'.$wherestr;
	$multiurl .= in_array($orderby,array('credit','sales','count'))?'&orderby='.$orderby:'';
	$multiurl .= $da=='asc'?'&da='.$da:'';
	$multi = multi($count, $perpage, $page, $multiurl);
	
	$commonurl = $multiurl.'&page='.$page;
	dsetcookie('admingoodsurl',$commonurl);
	$sid = $sortid;
	$sortid = 0;
}
?>