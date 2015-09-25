<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$op = empty($_GET['op']) ? '' : $_GET['op'];

if($op == 'district') {
	$container = $_GET['container'];
	$showlevel = intval($_GET['level']);
	$showlevel = $showlevel >= 1 && $showlevel <= 4 ? $showlevel : 4;
	$values = array(intval($_GET['pid']), intval($_GET['cid']), intval($_GET['did']), intval($_GET['coid']));
	$containertype = in_array($_GET['containertype'], array('birth', 'reside', 'fansclub'), true) ? $_GET['containertype'] : 'birth';
	$level = 1;
	if($values[0]) {
		$level++;
	} else if($_G['uid'] && !empty($_GET['showdefault'])) {

		space_merge($_G['member'], 'profile');
		$district = array();
		if($containertype == 'birth') {
			if(!empty($_G['member']['birthprovince'])) {
				$district[] = $_G['member']['birthprovince'];
				if(!empty($_G['member']['birthcity'])) {
					$district[] = $_G['member']['birthcity'];
				}
				if(!empty($_G['member']['birthdist'])) {
					$district[] = $_G['member']['birthdist'];
				}
				if(!empty($_G['member']['birthcommunity'])) {
					$district[] = $_G['member']['birthcommunity'];
				}
			}
		} else if($containertype == 'reside') {
			if(!empty($_G['member']['resideprovince'])) {
				$district[] = $_G['member']['resideprovince'];
				if(!empty($_G['member']['residecity'])) {
					$district[] = $_G['member']['residecity'];
				}
				if(!empty($_G['member']['residedist'])) {
					$district[] = $_G['member']['residedist'];
				}
				if(!empty($_G['member']['residecommunity'])) {
					$district[] = $_G['member']['residecommunity'];
				}
			}
		} else if($containertype == 'fansclub') {	// 2015-04-07 新增加的
			if(!empty($_G['member']['resideprovince'])) {
				$district[] = $_G['member']['resideprovince'];
				if(!empty($_G['member']['residecity'])) {
					$district[] = $_G['member']['residecity'];
				}
				if(!empty($_G['member']['residedist'])) {
					$district[] = $_G['member']['residedist'];
				}
				if(!empty($_G['member']['residecommunity'])) {
					$district[] = $_G['member']['residecommunity'];
				}
			}
		}
		if(!empty($district)) {
			foreach(C::t('common_district')->fetch_all_by_name($district) as $value) {
				$key = $value['level'] - 1;
				$values[$key] = $value['id'];
			}
			$level++;
		}
	}
	if($values[1]) {
		$level++;
	}
	if($values[2]) {
		$level++;
	}
	if($values[3]) {
		$level++;
	}
	$showlevel = $level;
	$elems = array();
	if($_GET['province']) {
		$elems = array($_GET['province'], $_GET['city'], $_GET['district'], $_GET['community']);
	}
	
	$html = fansclub_showdistrict($values, $elems, $container, $showlevel, $containertype);
}

include template('home/misc_ajax');