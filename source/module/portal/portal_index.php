<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: portal_index.php 31313 2012-08-10 03:51:03Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

list($navtitle, $metadescription, $metakeywords) = get_seosetting('portal');
if(!$navtitle) {
	$navtitle = $_G['setting']['navs'][1]['navname'];
	$nobbname = false;
} else {
	$nobbname = true;
}
if(!$metakeywords) {
	$metakeywords = $_G['setting']['navs'][1]['navname'];
}
if(!$metadescription) {
	$metadescription = $_G['setting']['navs'][1]['navname'];
}

if(isset($_G['makehtml'])){
	helper_makehtml::portal_index();
}
$data['chairman'] = index_chairman();

include_once template('diy:portal/index');

//��ҳ����᳤
function index_chairman(){
                                             global $_G;
                                            $chairmanlists = C::t('#fansclub#plugin_fansclub_info')->fetch_all_for_search('1 = 1', 0, 3, 'members'); // �б�
                                            
                                            for($i = 0; $i < count($chairmanlists); $i++)
                                            {
                                                    //��ȡ���Ի���Ϣ
                                                    $clubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_fid($chairmanlists[$i]['fid']);

                                                    //��ȡ���Ի�᳤��Ϣ
                                                    $userinfo = DB::fetch_first('SELECT * FROM '.DB::table('forum_groupuser').' WHERE fid='.$chairmanlists[$i]['fid']." AND level=1");
                                                    
                                                    $chairmanlists[$i]['name'] = $clubinfo['name'];			
                                                    $chairmanlists[$i]['uid'] = $userinfo['uid'];			//�᳤�û�id
                                                    $chairmanlists[$i]['avatar'] = avatar($userinfo['uid'], 'small', true);     //�᳤��ͷ��
                                                    $chairmanlists[$i]['username'] = $userinfo['username'];		//�᳤�û���
                                                    
                                                    $userfieldinfo = DB::fetch_first("SELECT * FROM ".DB::table('common_member_profile')." WHERE uid=".intval($userinfo['uid']));//�᳤���
                                                    $chairmanlists[$i]['bio'] = $userfieldinfo['bio'];
                                                    
                                            }
 		return $chairmanlists;
                       }
?>