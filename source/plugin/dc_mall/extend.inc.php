<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_mall');
if($_GET['act']=='install'){
	$f=trim($_GET['f']);
	if(submitcheck('confirmed')){
		if(!$f||!isfile($f)||!install($f))
			cpmsg($_lang['extend_installerror'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'error');
		cpmsg($_lang['extend_installsucceed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'succeed',array('identify' =>$f));
	}
	cpmsg($_lang['extend_intstallcheck'],'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend&act=install&f='.$f,'form', array('identify' => $f));
}elseif($_GET['act']=='uninstall'){
	$id=dintval($_GET['id']);
	$ext = C::t('#dc_mall#dc_mall_extend')->fetch($id);
	if(!$ext)
		cpmsg($_lang['error'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'error');
	if(C::t('#dc_mall#dc_mall_goods')->goodscheck($id))
		cpmsg($_lang['extend_uninstallgoodserr'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'error');
	if(submitcheck('confirmed')){
		if(!$ext||!isfile($ext['identify'])||!uninstall($ext))
			cpmsg($_lang['extend_uninstallerror'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'error');
		cpmsg($_lang['extend_uninstallsucceed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'succeed',array('title' =>$ext['title']));
	}
	cpmsg($_lang['extend_unintstallcheck'],'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend&act=uninstall&id='.$id,'form', array('title' =>$ext['title']));
}elseif($_GET['act']=='upgrade'){
	$id=dintval($_GET['id']);
	$ext = C::t('#dc_mall#dc_mall_extend')->fetch($id);
	if(!$ext)
		cpmsg($_lang['error'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'error');
	if(submitcheck('confirmed')){
		if(!$ext||!isfile($ext['identify'])||!upgrade($ext))
			cpmsg($_lang['extend_ugradeerror'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'error');
		cpmsg($_lang['extend_ugradesucceed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend', 'succeed',array('title' =>$ext['title']));
	}
	cpmsg($_lang['extend_ugradecheck'],'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend&act=upgrade&id='.$id,'form', array('title' =>$ext['title']));
}
$extarr = array();
$entrydir = DISCUZ_ROOT.'./source/plugin/dc_mall/extend';
if(file_exists($entrydir)) {
	$d = dir($entrydir);
	C::import('extend/install','plugin/dc_mall',false);
	while($f = $d->read()) {
		if($f!='.'&&$f!='..'&&is_dir($entrydir.'/'.$f)){
			C::import($f.'/install','plugin/dc_mall/extend',false);
			$modstr = $f.'_install';
			if (class_exists($modstr,false)){
				$mobj = new $modstr();
				$extarr[$f] = array(
					'title'=>$mobj->title,
					'des'=>$mobj->des,
					'author'=>$mobj->author,
					'version'=>$mobj->version,
				);
			}
		}
	}
}
$hooks = C::t('common_plugin')->fetch_all_data(true);
foreach($hooks as $hook){
	if($hook['identifier']=='dc_mall')continue;
	$hookpath = DISCUZ_ROOT.'./source/plugin/'.$hook['directory'].'mall.config.php';
	if(file_exists($hookpath)){
		$conf = @include $hookpath;
		if(is_array($conf)&&$conf['identify']){
			C::import($conf['identify'].'_install','plugin/'.$hook['directory'].'dcmallextend',false);
			$modstr = $conf['identify'].'_install';
			
			if (class_exists($modstr,false)){
				$mobj = new $modstr();
				$extarr[$hook['identifier'].':'.$conf['identify']] = array(
					'title'=>lang('plugin/'.$hook['identifier'],$mobj->title),
					'des'=>lang('plugin/'.$hook['identifier'],$mobj->des),
					'author'=>lang('plugin/'.$hook['identifier'],$mobj->author),
					'version'=>lang('plugin/'.$hook['identifier'],$mobj->version),
				);
			}
		}
	}
}
$extends = C::t('#dc_mall#dc_mall_extend')->getdata();
showtableheader($_lang['extend_isinstall'], '');
showsubtitle($_lang['extend_subtitle']);
foreach($extends as $v){
	showtablerow('', array('class="td25"'), array('',$v['title'],$v['des'],$v['version'],$v['author'],($extarr[$v['identify']]['version']>$v['version']?'<a href="?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend&act=upgrade&id='.$v['id'].'">'.$_lang['upgrade'].'</a> ':'').'<a href="?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend&act=uninstall&id='.$v['id'].'">'.$_lang['uninstall'].'</a>'));
	unset($extarr[$v['identify']]);
}
showtableheader($_lang['extend_isnotinstall'], '');
showsubtitle($_lang['extend_subtitle']);
foreach($extarr as $k =>$v){
	showtablerow('', array('class="td25"'), array('',$v['title'],$v['des'],$v['version'],$v['author'],'<a href="?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_mall&pmod=extend&act=install&f='.$k.'">'.$_lang['install'].'</a>'));
}
showtablefooter();
function isfile($str){
	$identify = explode(':',$str);
	if(count($identify)==2)
		return file_exists(DISCUZ_ROOT.'/source/plugin/'.$identify[0].'/dcmallextend/'.$identify[1].'_install.php');
	else
		return file_exists(DISCUZ_ROOT.'/source/plugin/dc_mall/extend/'.$str.'/'.$str.'_install.php');
}
function uninstall($ext){
	C::import('extend/install','plugin/dc_mall',false);
	$flag = false;
	$identify = explode(':',$ext['identify']);
	if(count($identify)==2){
		$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
		$flag = C::import($identify[1].'_install','plugin/'.$hook['directory'].'/dcmallextend',false);
		$modstr = $identify[1].'_install';
	}else{
		$flag = C::import($ext['identify'].'/install','plugin/dc_mall/extend',false);
		$modstr = $ext['identify'].'_install';
	}
	if($flag){
		if (class_exists($modstr,false)){
			$mobj = new $modstr();
			if(in_array('uninstall',get_class_methods($mobj))){
				if($mobj->uninstall()===false)
					return false;
			}
			C::t('#dc_mall#dc_mall_extend')->delete($ext['id']);
		}
	}
	return true;
}
function install($name){
	if(C::t('#dc_mall#dc_mall_extend')->getdata($name))
		return;
	C::import('extend/install','plugin/dc_mall',false);
	$flag = false;
	$identify = explode(':',$name);
	if(count($identify)==2){
		$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
		$flag = C::import($identify[1].'_install','plugin/'.$hook['directory'].'/dcmallextend',false);
		$modstr = $identify[1].'_install';
	}else{
		$flag = C::import($name.'/install','plugin/dc_mall/extend',false);
		$modstr = $name.'_install';
	}
	if($flag){
		if (class_exists($modstr,false)){
			$mobj = new $modstr();
			if(in_array('install',get_class_methods($mobj))){
				if($mobj->install()===false)
					return false;
			}
			$d['title']=$mobj->title;
			$d['des']=$mobj->des;
			$d['author']=$mobj->author;
			$d['version']=$mobj->version;
			$d['data']=serialize($mobj->data);
			$d['identify']=$name;
			C::t('#dc_mall#dc_mall_extend')->insert($d);
		}
	}
	return true;
}
function upgrade($ext){
	C::import('extend/install','plugin/dc_mall',false);
	$flag = false;
	$identify = explode(':',$ext['identify']);
	if(count($identify)==2){
		$hook = C::t('common_plugin')->fetch_by_identifier($identify[0]);
		$flag = C::import($identify[1].'_install','plugin/'.$hook['directory'].'/dcmallextend',false);
		$modstr = $identify[1].'_install';
	}else{
		$flag = C::import($ext['identify'].'/install','plugin/dc_mall/extend',false);
		$modstr = $ext['identify'].'_install';
	}
	if($flag){
		if (class_exists($modstr,false)){
			$mobj = new $modstr();
			if($mobj->version<=$ext['version'])
				return false;
			if(in_array('upgrade',get_class_methods($mobj))){
				if($mobj->upgrade($ext['version'])===false)
					return false;
			}
			$d['title']=$mobj->title;
			$d['des']=$mobj->des;
			$d['author']=$mobj->author;
			$d['version']=$mobj->version;
			$d['data']=serialize($mobj->data);
			$d['identify']=$ext['identify'];
			C::t('#dc_mall#dc_mall_extend')->update($ext['id'],$d);
		}
	}
	return true;
}
?>