<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('mobile');
0
|| checktplrefresh('./source/plugin/mobile/template/mobile.htm', './template/usportstyle/common/header.htm', 1442988968, 'mobile', './data/template/4_mobile_mobile.tpl.php', './source/plugin/mobile/template', 'mobile')
|| checktplrefresh('./source/plugin/mobile/template/mobile.htm', './template/usportstyle/common/footer.htm', 1442988968, 'mobile', './data/template/4_mobile_mobile.tpl.php', './source/plugin/mobile/template', 'mobile')
|| checktplrefresh('./source/plugin/mobile/template/mobile.htm', './template/default/common/header_common.htm', 1442988968, 'mobile', './data/template/4_mobile_mobile.tpl.php', './source/plugin/mobile/template', 'mobile')
|| checktplrefresh('./source/plugin/mobile/template/mobile.htm', './template/default/common/header_qmenu.htm', 1442988968, 'mobile', './data/template/4_mobile_mobile.tpl.php', './source/plugin/mobile/template', 'mobile')
|| checktplrefresh('./source/plugin/mobile/template/mobile.htm', './template/usportstyle/common/pubsearchform.htm', 1442988968, 'mobile', './data/template/4_mobile_mobile.tpl.php', './source/plugin/mobile/template', 'mobile')
;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<?php if($_G['config']['output']['iecompatible']) { ?><meta http-equiv="X-UA-Compatible" content="IE=EmulateIE<?php echo $_G['config']['output']['iecompatible'];?>" /><?php } ?>
<title><?php if(!empty($navtitle)) { ?><?php echo $navtitle;?><?php } if(empty($nobbname)) { ?> - <?php echo $_G['setting']['bbname'];?><?php } ?></title>
<?php echo $_G['setting']['seohead'];?>
<meta name="keywords" content="<?php if($metakeywords=='首页' || $metakeywords=='') { ?>足球明星,篮球明星,体育明星,球队<?php } else { if(!empty($metakeywords)) { echo dhtmlspecialchars($metakeywords); } } ?>" />
<meta name="description" content="<?php if($metadescription=='首页' || $metadescription=='') { ?>5U体育是以体育新闻、NBA、CBA、英超、西甲、中超、中国足球等的垂直体育社群门户，体育明星在线互动，独特的体育观点与草根体育专栏，尽在你我的体育社区。 <?php } else { if(!empty($metadescription)) { echo dhtmlspecialchars($metadescription); ?> <?php } if(empty($nobbname)) { ?>,<?php echo $_G['setting']['bbname'];?><?php } } ?>" />
<meta name="MSSmartTagsPreventParsing" content="True" />
<meta http-equiv="MSThemeCompatible" content="Yes" />
<base href="<?php echo $_G['siteurl'];?>" /><link rel="stylesheet" type="text/css" href="data/cache/style_4_common.css?<?php echo VERHASH;?>" /><?php if($_G['uid'] && isset($_G['cookie']['extstyle']) && strpos($_G['cookie']['extstyle'], TPLDIR) !== false) { ?><link rel="stylesheet" id="css_extstyle" type="text/css" href="<?php echo $_G['cookie']['extstyle'];?>/style.css" /><?php } elseif($_G['style']['defaultextstyle']) { ?><link rel="stylesheet" id="css_extstyle" type="text/css" href="<?php echo $_G['style']['defaultextstyle'];?>/style.css" /><?php } ?><script type="text/javascript">var STYLEID = '<?php echo STYLEID;?>', STATICURL = '<?php echo STATICURL;?>', IMGDIR = '<?php echo IMGDIR;?>', VERHASH = '<?php echo VERHASH;?>', charset = '<?php echo CHARSET;?>', discuz_uid = '<?php echo $_G['uid'];?>', cookiepre = '<?php echo $_G['config']['cookie']['cookiepre'];?>', cookiedomain = '<?php echo $_G['config']['cookie']['cookiedomain'];?>', cookiepath = '<?php echo $_G['config']['cookie']['cookiepath'];?>', showusercard = '<?php echo $_G['setting']['showusercard'];?>', attackevasive = '<?php echo $_G['config']['security']['attackevasive'];?>', disallowfloat = '<?php echo $_G['setting']['disallowfloat'];?>', creditnotice = '<?php if($_G['setting']['creditnotice']) { ?><?php echo $_G['setting']['creditnames'];?><?php } ?>', defaultstyle = '<?php echo $_G['style']['defaultextstyle'];?>', REPORTURL = '<?php echo $_G['currenturl_encode'];?>', SITEURL = '<?php echo $_G['siteurl'];?>', JSPATH = '<?php echo $_G['setting']['jspath'];?>', CSSPATH = '<?php echo $_G['setting']['csspath'];?>', DYNAMICURL = '<?php echo $_G['dynamicurl'];?>';</script>
<script src="<?php echo $_G['setting']['jspath'];?>common.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php if(empty($_GET['diy'])) { $_GET['diy'] = '';?><?php } if(!isset($topic)) { $topic = array();?><?php } ?>
<meta name="application-name" content="<?php echo $_G['setting']['bbname'];?>" />
<meta name="msapplication-tooltip" content="<?php echo $_G['setting']['bbname'];?>" />
<meta name="renderer" content="webkit" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
<?php if($_G['setting']['portalstatus']) { ?><meta name="msapplication-task" content="name=<?php echo $_G['setting']['navs']['1']['navname'];?>;action-uri=<?php echo !empty($_G['setting']['domain']['app']['portal']) ? 'http://'.$_G['setting']['domain']['app']['portal'] : $_G['siteurl'].'portal.php'; ?>;icon-uri=<?php echo $_G['siteurl'];?><?php echo IMGDIR;?>/portal.ico" /><?php } ?>
<meta name="msapplication-task" content="name=<?php echo $_G['setting']['navs']['2']['navname'];?>;action-uri=<?php echo !empty($_G['setting']['domain']['app']['forum']) ? 'http://'.$_G['setting']['domain']['app']['forum'] : $_G['siteurl'].'forum.php'; ?>;icon-uri=<?php echo $_G['siteurl'];?><?php echo IMGDIR;?>/bbs.ico" />
<?php if($_G['setting']['groupstatus']) { ?><meta name="msapplication-task" content="name=<?php echo $_G['setting']['navs']['3']['navname'];?>;action-uri=<?php echo !empty($_G['setting']['domain']['app']['group']) ? 'http://'.$_G['setting']['domain']['app']['group'] : $_G['siteurl'].'group.php'; ?>;icon-uri=<?php echo $_G['siteurl'];?><?php echo IMGDIR;?>/group.ico" /><?php } if(helper_access::check_module('feed')) { ?><meta name="msapplication-task" content="name=<?php echo $_G['setting']['navs']['4']['navname'];?>;action-uri=<?php echo !empty($_G['setting']['domain']['app']['home']) ? 'http://'.$_G['setting']['domain']['app']['home'] : $_G['siteurl'].'home.php'; ?>;icon-uri=<?php echo $_G['siteurl'];?><?php echo IMGDIR;?>/home.ico" /><?php } if($_G['basescript'] == 'forum' && $_G['setting']['archiver']) { ?>
<link rel="archives" title="<?php echo $_G['setting']['bbname'];?>" href="<?php echo $_G['siteurl'];?>archiver/" />
<?php } if(!empty($rsshead)) { ?><?php echo $rsshead;?><?php } if(widthauto()) { ?>
<link rel="stylesheet" id="css_widthauto" type="text/css" href='<?php echo $_G['setting']['csspath'];?><?php echo STYLEID;?>_widthauto.css?<?php echo VERHASH;?>' />
<script type="text/javascript">HTMLNODE.className += ' widthauto'</script>
<?php } if($_G['basescript'] == 'forum' || $_G['basescript'] == 'group') { ?>
<script src="<?php echo $_G['setting']['jspath'];?>forum.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php } elseif($_G['basescript'] == 'home' || $_G['basescript'] == 'userapp') { ?>
<script src="<?php echo $_G['setting']['jspath'];?>home.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php } elseif($_G['basescript'] == 'portal') { ?>
<script src="<?php echo $_G['setting']['jspath'];?>portal.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php } if($_G['basescript'] != 'portal' && $_GET['diy'] == 'yes' && check_diy_perm($topic)) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>portal.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php } if($_GET['diy'] == 'yes' && check_diy_perm($topic)) { ?>
<link rel="stylesheet" type="text/css" id="diy_common" href="<?php echo $_G['setting']['csspath'];?><?php echo STYLEID;?>_css_diy.css?<?php echo VERHASH;?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo $_G['config']['static'];?>/main.css">
<script src="http://s.csbew.com/k.js" type="text/javascript"></script>
<script src="http://static.5usport.com/sea.js" type="text/javascript"></script>
<script src="<?php echo $_G['config']['static'];?>/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $_G['config']['static'];?>/masonry.pkgd.min.js" type="text/javascript"></script>
<script src="<?php echo $_G['config']['static'];?>/imagesloaded.pkgd.min.js" type="text/javascript"></script>
<script src="<?php echo $_G['config']['static'];?>/main.js" type="text/javascript"></script>
<script type="text/javascript">
            var jq=jQuery.noConflict();
        </script>
</head>
<body id="nv_<?php echo $_G['basescript'];?>" class="pg_<?php echo CURMODULE;?><?php if($_G['basescript'] === 'portal' && CURMODULE === 'list' && !empty($cat)) { ?> <?php echo $cat['bodycss'];?><?php } ?>" onkeydown="if(event.keyCode==27) return false;">
<div id="append_parent"></div><div id="ajaxwaitid"></div>
<?php if($_GET['diy'] == 'yes' && check_diy_perm($topic)) { include template('common/header_diy'); } if(check_diy_perm($topic)) { include template('common/header_diynav'); } if(CURMODULE == 'topic' && $topic && empty($topic['useheader']) && check_diy_perm($topic)) { ?>
<?php echo $diynav;?>
<?php } if(empty($topic) || $topic['useheader']) { if($_G['setting']['mobile']['allowmobile'] && (!$_G['setting']['cacheindexlife'] && !$_G['setting']['cachethreadon'] || $_G['uid']) && ($_GET['diy'] != 'yes' || !$_GET['inajax']) && ($_G['mobile'] != '' && $_G['cookie']['mobile'] == '' && $_GET['mobile'] != 'no')) { ?>
<div class="xi1 bm bm_c">
    请选择 <a href="<?php echo $_G['siteurl'];?>forum.php?mobile=yes">进入手机版</a> <span class="xg1">|</span> <a href="<?php echo $_G['setting']['mobile']['nomobileurl'];?>">继续访问电脑版</a>
</div>
<?php } if($_G['setting']['shortcut'] && $_G['member']['credits'] >= $_G['setting']['shortcut']) { ?>
<div id="shortcut">
<span><a href="javascript:;" id="shortcutcloseid" title="关闭">关闭</a></span>
您经常访问 <?php echo $_G['setting']['bbname'];?>，试试添加到桌面，访问更方便！
<a href="javascript:;" id="shortcuttip">添加 <?php echo $_G['setting']['bbname'];?> 到桌面</a>
</div>
<script type="text/javascript">setTimeout(setShortcut, 2000);</script>
<?php } if($_G['adminid'] ) { ?>
<div id="toptb" class="cl">
<?php if(!empty($_G['setting']['pluginhooks']['global_cpnav_top'])) echo $_G['setting']['pluginhooks']['global_cpnav_top'];?>
<div class="wp">
<div class="z"><?php if(is_array($_G['setting']['topnavs']['0'])) foreach($_G['setting']['topnavs']['0'] as $nav) { if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?><?php echo $nav['code'];?><?php } } ?>
<?php if(!empty($_G['setting']['pluginhooks']['global_cpnav_extra1'])) echo $_G['setting']['pluginhooks']['global_cpnav_extra1'];?>
</div>
<div class="y">
<a id="switchblind" href="javascript:;" onClick="toggleBlind(this)" title="开启辅助访问" class="switchblind">开启辅助访问</a>
<?php if(!empty($_G['setting']['pluginhooks']['global_cpnav_extra2'])) echo $_G['setting']['pluginhooks']['global_cpnav_extra2'];?><?php if(is_array($_G['setting']['topnavs']['1'])) foreach($_G['setting']['topnavs']['1'] as $nav) { if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?><?php echo $nav['code'];?><?php } } if(empty($_G['disabledwidthauto']) && $_G['setting']['switchwidthauto']) { ?>
<a href="javascript:;" onClick="widthauto(this)"><?php if(widthauto()) { ?>切换到窄版<?php } else { ?>切换到宽版<?php } ?></a>
<?php } if($_G['uid'] && !empty($_G['style']['extstyle'])) { ?><a id="sslct" href="javascript:;" onMouseOver="delayShow(this, function() {showMenu({'ctrlid':'sslct','pos':'34!'})});">切换风格</a><?php } if(check_diy_perm($topic)) { ?>
<?php echo $diynav;?>
<?php } ?>
</div>
</div>
</div>
<?php } if(!IS_ROBOT) { if($_G['uid']) { ?>
<div>
<!--
<ul id="myprompt_menu" class="p_pop" style="display: none;">
<li><a href="home.php?mod=space&amp;do=pm" id="pm_ntc" style="background-repeat: no-repeat; background-position: 0 50%;"><em class="prompt_news<?php if(empty($_G['member']['newpm'])) { ?>_0<?php } ?>"></em>消息</a></li>
<li><a href="home.php?mod=follow&amp;do=follower"><em class="prompt_follower<?php if(empty($_G['member']['newprompt_num']['follower'])) { ?>_0<?php } ?>"></em>新听众<?php if($_G['member']['newprompt_num']['follower']) { ?>(<?php echo $_G['member']['newprompt_num']['follower'];?>)<?php } ?></a></li>
<?php if($_G['member']['newprompt'] && $_G['member']['newprompt_num']['follow']) { ?>
<li><a href="home.php?mod=follow"><em class="prompt_concern"></em>我关注的(<?php echo $_G['member']['newprompt_num']['follow'];?>)</a></li>
<?php } if($_G['member']['newprompt']) { if(is_array($_G['member']['category_num'])) foreach($_G['member']['category_num'] as $key => $val) { ?><li><a href="home.php?mod=space&amp;do=notice&amp;view=<?php echo $key;?>"><em class="notice_<?php echo $key;?>"></em><?php echo lang('template', 'notice_'.$key); ?>(<span class="rq"><?php echo $val;?></span>)</a></li>
<?php } } if(empty($_G['cookie']['ignore_notice'])) { ?>
<li class="ignore_noticeli"><a href="javascript:;" onClick="setcookie('ignore_notice', 1);hideMenu('myprompt_menu')" title="暂不提醒"><em class="ignore_notice"></em></a></li>
<?php } ?>
<i></i>
</ul>-->
</div>
<?php } if($_G['uid'] && !empty($_G['style']['extstyle'])) { ?>
<div id="sslct_menu" class="cl p_pop" style="display: none;">
<?php if(!$_G['style']['defaultextstyle']) { ?><span class="sslct_btn" onClick="extstyle('')" title="默认"><i></i></span><?php } if(is_array($_G['style']['extstyle'])) foreach($_G['style']['extstyle'] as $extstyle) { ?><span class="sslct_btn" onClick="extstyle('<?php echo $extstyle['0'];?>')" title="<?php echo $extstyle['1'];?>"><i style='background:<?php echo $extstyle['2'];?>'></i></span>
<?php } ?>
</div>
<?php } if($_G['uid']) { ?>
<ul id="myitem_menu" class="p_pop" style="display: none;">
<li><a href="forum.php?mod=guide&amp;view=my">帖子</a></li>
<li><a href="home.php?mod=space&amp;do=favorite&amp;view=me">收藏</a></li>
<li><a href="home.php?mod=space&amp;do=friend">好友</a></li>
<?php if(!empty($_G['setting']['pluginhooks']['global_myitem_extra'])) echo $_G['setting']['pluginhooks']['global_myitem_extra'];?>
<i></i>
</ul>
<?php } ?><div id="qmenu_menu" class="p_pop <?php if(!$_G['uid']) { ?>blk<?php } ?>" style="display: none;">
<?php if(!empty($_G['setting']['pluginhooks']['global_qmenu_top'])) echo $_G['setting']['pluginhooks']['global_qmenu_top'];?>
<?php if($_G['uid']) { ?>
<ul class="cl nav"><?php if(is_array($_G['setting']['mynavs'])) foreach($_G['setting']['mynavs'] as $nav) { if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?>
<li><?php echo $nav['code'];?></li>
<?php } } ?>
</ul>
<?php } elseif($_G['connectguest']) { ?>
<div class="ptm pbw hm">
请先<br /><a class="xi2" href="member.php?mod=connect"><strong>完善帐号信息</strong></a> 或 <a href="member.php?mod=connect&amp;ac=bind" class="xi2 xw1"><strong>绑定已有帐号</strong></a><br />后使用快捷导航
</div>
<?php } else { ?>
<div class="ptm pbw hm">
请 <a href="javascript:;" class="xi2" onclick="lsSubmit()"><strong>登录</strong></a> 后使用快捷导航<br />没有帐号？<a href="member.php?mod=<?php echo $_G['setting']['regname'];?>" class="xi2 xw1"><?php echo $_G['setting']['reglinkname'];?></a>
</div>
<?php } if($_G['setting']['showfjump']) { ?><div id="fjump_menu" class="btda"></div><?php } ?>
<?php if(!empty($_G['setting']['pluginhooks']['global_qmenu_bottom'])) echo $_G['setting']['pluginhooks']['global_qmenu_bottom'];?>
</div><?php } ?><?php echo adshow("headerbanner/wp a_h");?><div class="chheader">
<div class="wp_main">
<div class="chtop cl"><?php $mnid = getcurrentnav();?><h2><?php if(!isset($_G['setting']['navlogos'][$mnid])) { ?><a href="<?php if($_G['setting']['domain']['app']['default']) { ?>http://<?php echo $_G['setting']['domain']['app']['default'];?>/<?php } else { ?>./<?php } ?>" title="<?php echo $_G['setting']['bbname'];?>"><?php echo $_G['style']['boardlogo'];?></a><?php } else { ?><?php echo $_G['setting']['navlogos'][$mnid];?><?php } ?></h2><?php if($_G['setting']['search']) { $slist = array();?><?php if($_G['fid'] && $_G['forum']['status'] != 3 && $mod != 'group') { ?><?php
$slist[forumfid] = <<<EOF
<li><a href="javascript:;" rel="curforum" fid="{$_G['fid']}" >本版</a></li>
EOF;
?><?php } if($_G['setting']['portalstatus'] && $_G['setting']['search']['portal']['status'] && ($_G['group']['allowsearch'] & 1 || $_G['adminid'] == 1)) { ?><?php
$slist[portal] = <<<EOF
<li><a href="javascript:;" rel="article">文章</a></li>
EOF;
?><?php } if($_G['setting']['search']['forum']['status'] && ($_G['group']['allowsearch'] & 2 || $_G['adminid'] == 1)) { ?><?php
$slist[forum] = <<<EOF
<li><a href="javascript:;" rel="forum" class="curtype">帖子</a></li>
EOF;
?><?php } if(helper_access::check_module('blog') && $_G['setting']['search']['blog']['status'] && ($_G['group']['allowsearch'] & 4 || $_G['adminid'] == 1)) { ?><?php
$slist[blog] = <<<EOF
<li><a href="javascript:;" rel="blog">日志</a></li>
EOF;
?><?php } if(helper_access::check_module('album') && $_G['setting']['search']['album']['status'] && ($_G['group']['allowsearch'] & 8 || $_G['adminid'] == 1)) { ?><?php
$slist[album] = <<<EOF
<li><a href="javascript:;" rel="album">相册</a></li>
EOF;
?><?php } if($_G['setting']['groupstatus'] && $_G['setting']['search']['group']['status'] && ($_G['group']['allowsearch'] & 16 || $_G['adminid'] == 1)) { ?><?php
$slist[group] = <<<EOF
<li><a href="javascript:;" rel="group">{$_G['setting']['navs']['3']['navname']}</a></li>
EOF;
?><?php } ?><?php
$slist[user] = <<<EOF
<li><a href="javascript:;" rel="user">用户</a></li>
EOF;
?>
<?php } if($_G['setting']['search'] && $slist) { ?>
<div id="scbar" class="<?php if($_G['setting']['srchhotkeywords'] && count($_G['setting']['srchhotkeywords']) > 5) { ?>scbar_narrow <?php } ?>cl">
<form id="scbar_form" method="<?php if($_G['fid'] && !empty($searchparams['url'])) { ?>get<?php } else { ?>post<?php } ?>" autocomplete="off" onsubmit="searchFocus($('scbar_txt'))" action="<?php if($_G['fid'] && !empty($searchparams['url'])) { ?><?php echo $searchparams['url'];?><?php } else { ?>search.php?searchsubmit=yes<?php } ?>" target="_blank">
<input type="hidden" name="mod" id="scbar_mod" value="search" />
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<input type="hidden" name="srchtype" value="title" />
<input type="hidden" name="srhfid" value="<?php echo $_G['fid'];?>" />
<input type="hidden" name="srhlocality" value="<?php echo $_G['basescript'];?>::<?php echo CURMODULE;?>" />
<?php if(!empty($searchparams['params'])) { if(is_array($searchparams['params'])) foreach($searchparams['params'] as $key => $value) { $srchotquery .= '&' . $key . '=' . rawurlencode($value);?><input type="hidden" name="<?php echo $key;?>" value="<?php echo $value;?>" />
<?php } ?>
<input type="hidden" name="source" value="discuz" />
<input type="hidden" name="fId" id="srchFId" value="<?php echo $_G['fid'];?>" />
<input type="hidden" name="q" id="cloudsearchquery" value="" />

<style>
#scbar { overflow: visible; position: relative; }
#sg{ background: #FFF; width:456px; border: 1px solid #B2C7DA; }
.scbar_narrow #sg { width: 316px; }
#sg li { padding:0 8px; line-height:30px; font-size:14px; }
#sg li span { color:#999; }
.sml { background:#FFF; cursor:default; }
.smo { background:#E5EDF2; cursor:default; }
            </style>
            <div style="display: none; position: absolute; top:37px; left:44px;" id="sg">
                <div id="st_box" cellpadding="2" cellspacing="0"></div>
            </div>
<?php } ?>
<div class="chsearch cl">
     <span style="display:none;"><a href="javascript:;" id="scbar_type" class="xg1" onclick="showMenu(this.id)" hidefocus="true">搜索</a></span>
     <span><input type="text" name="srchtxt" id="scbar_txt" value="请输入搜索内容" autocomplete="off" x-webkit-speech speech /></span>
 <span class="chsubmit"><button type="submit" name="searchsubmit" id="scbar_btn" sc="1" value="true"></button></span>
</div>
</form>
</div>
<ul id="scbar_type_menu" class="p_pop" style="display: none;"><?php echo implode('', $slist);; ?></ul>
<script type="text/javascript">
initSearchmenu('scbar', '<?php echo $searchparams['url'];?>');
</script>
<?php } include template('common/header_userstatus'); ?></div>
<!--<span class="chuser cl"><a href="" class="chupic"></a><a href="" class="chnews"></a><a href="" class="chpers"></a></span>--><?php $mnid = getcurrentnav();?><ul class="chnav cl"><?php if(is_array($_G['setting']['navs'])) foreach($_G['setting']['navs'] as $nav) { if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?><li <?php if($mnid == $nav['navid']) { ?>class="a" <?php } if($nav['navname']=='天猫商城') $nav['nav'] = str_replace('<a href=', '<a rel="nofollow" href=', $nav['nav']);?><?php echo $nav['nav'];?>></li><?php } } ?>
    </ul>
    <?php if(!empty($_G['setting']['pluginhooks']['global_nav_extra'])) echo $_G['setting']['pluginhooks']['global_nav_extra'];?>
<?php if(!empty($_G['setting']['plugins']['jsmenu'])) { ?>
<ul class="p_pop h_pop" id="plugin_menu" style="display: none"><?php if(is_array($_G['setting']['plugins']['jsmenu'])) foreach($_G['setting']['plugins']['jsmenu'] as $module) { ?> <?php if(!$module['adminid'] || ($module['adminid'] && $_G['adminid'] > 0 && $module['adminid'] >= $_G['adminid'])) { ?>
 <li><?php echo $module['url'];?></li>
 <?php } } ?>
</ul>
<?php } ?>
<?php echo $_G['setting']['menunavs'];?>
<div id="mu" class="cl">
<?php if($_G['setting']['subnavs']) { if(is_array($_G['setting']['subnavs'])) foreach($_G['setting']['subnavs'] as $navid => $subnav) { if($_G['setting']['navsubhover'] || $mnid == $navid) { ?>
<ul class="cl <?php if($mnid == $navid) { ?>current<?php } ?>" id="snav_<?php echo $navid;?>" style="display:<?php if($mnid != $navid) { ?>none<?php } ?>">
<?php echo $subnav;?>
</ul>
<?php } } } ?>
</div><?php echo adshow("subnavbanner/a_mu");?></div>
</div>
<?php if(!empty($_G['setting']['pluginhooks']['global_header'])) echo $_G['setting']['pluginhooks']['global_header'];?>
<?php } ?>

<div id="wp" class="wp">
<link rel="stylesheet" type="text/css" href="source/plugin/mobile/template/style.css" />
<?php if($_G['adminid'] == 1) { ?>
<script type="text/javascript">
function hiddentips() {
setcookie("mobiletips", "1");
display('admintips');
}
</script>
<?php } ?>
<div id="ct" class="wp cl ptw pbw">
<?php if($_G['cache']['mobileoem_data']['iframeUrl']) { ?>
<iframe frameborder="0" width="100%" scrolling="no" height="810" src="<?php echo $_G['cache']['mobileoem_data']['iframeUrl'];?>"></iframe>
<?php } else { if($_G['adminid'] == 1 && !$_G['cookie']['mobiletips']) { ?>
  <div class="wrap">
<div id="admintips" class="tips"><span class="y"><a href="javascript:hiddentips();" class="close">关闭</a></span>尊敬的站长，您可以在Discuz!程序根目录上传"mobile.png"图片(尺寸178x178)，以确保论坛客户端能成功获得到论坛的logo</div>
    <?php } ?>
<div class="content">
<ul class="platform">
<li><a href="http://www.discuz.net/mobile.php?platform=ios"><img src="./source/plugin/mobile/template/image/iphone.jpg" alt="iphone-适用于苹果手机"/></a></li>
<li><a href="http://www.discuz.net/mobile.php?platform=android"><img src="./source/plugin/mobile/template/image/android.jpg" alt="Android-适用于装有安卓系统的三星/HTC/小米等手机"/></a></li>
<li><a href="http://www.discuz.net/mobile.php?platform=windowsphone"><img src="./source/plugin/mobile/template/image/wp7.jpg" alt="WinodwsPhone-适用于装有微软WindowsPhone7 系统的手机"/></a></li>
</ul>
<div class="inner cl">
<div class="intro z">
<h2><img src="./source/plugin/mobile/template/image/title_intro.jpg" alt="掌上论坛优势"></h2>
<dl>
<dt>随时随地，快速访问</dt>
<dd>只要手机在手，您都可以快速、方便地看贴发帖，与论坛好友收发短消息。</dd>
<dt>极致优化，畅快"悦"读</dt>
<dd>独有的论坛界面和触屏设计，手机论坛也变得赏心悦目，操作自如。</dd>
<dt>即拍即发，分享生活</dt>
<dd>不管是风景图画，还是新闻现场，拍照发帖一气呵成，让您在论坛出尽风头。</dd>
</dl>
</div>
<div class="code y">
<dl>
<dt class="shoot">下载客户端后，拍摄二维码快速访问本站:</dt>
<dd class="code_img"><img src="data/cache/mobile_siteqrcode.png" alt="二维码"/></dd>
<dt>或者通过以下地址访问:</dt>
<dd class="url"><a href="<?php echo $_G['siteurl'];?>" target="_blank"><?php echo $_G['siteurl'];?></a></dd>
</dl>
</div>
</div>
</div>
<?php } ?>
</div>
</div>	</div><?php $focusid = getfocus_rand($_G[basescript]);?><?php if($focusid !== null) { $focus = $_G['cache']['focus']['data'][$focusid];?><?php $focusnum = count($_G['setting']['focus'][$_G[basescript]]);?><div class="focus" id="sitefocus">
<div class="bm">
<div class="bm_h cl">
<a href="javascript:;" onclick="setcookie('nofocus_<?php echo $_G['basescript'];?>', 1, <?php echo $_G['cache']['focus']['cookie'];?>*3600);$('sitefocus').style.display='none'" class="y" title="关闭">关闭</a>
<h2>
<?php if($_G['cache']['focus']['title']) { ?><?php echo $_G['cache']['focus']['title'];?><?php } else { ?>站长推荐<?php } ?>
<span id="focus_ctrl" class="fctrl"><img src="<?php echo IMGDIR;?>/pic_nv_prev.gif" alt="上一条" title="上一条" id="focusprev" class="cur1" onclick="showfocus('prev');" /> <em><span id="focuscur"></span>/<?php echo $focusnum;?></em> <img src="<?php echo IMGDIR;?>/pic_nv_next.gif" alt="下一条" title="下一条" id="focusnext" class="cur1" onclick="showfocus('next')" /></span>
</h2>
</div>
<div class="bm_c" id="focus_con">
</div>
</div>
</div><?php $focusi = 0;?><?php if(is_array($_G['setting']['focus'][$_G['basescript']])) foreach($_G['setting']['focus'][$_G['basescript']] as $id) { ?><div class="bm_c" style="display: none" id="focus_<?php echo $focusi;?>">
<dl class="xld cl bbda">
<dt><a href="<?php echo $_G['cache']['focus']['data'][$id]['url'];?>" class="xi2" target="_blank"><?php echo $_G['cache']['focus']['data'][$id]['subject'];?></a></dt>
<?php if($_G['cache']['focus']['data'][$id]['image']) { ?>
<dd class="m"><a href="<?php echo $_G['cache']['focus']['data'][$id]['url'];?>" target="_blank"><img src="<?php echo $_G['cache']['focus']['data'][$id]['image'];?>" alt="<?php echo $_G['cache']['focus']['data'][$id]['subject'];?>" /></a></dd>
<?php } ?>
<dd><?php echo $_G['cache']['focus']['data'][$id]['summary'];?></dd>
</dl>
<p class="ptn cl"><a href="<?php echo $_G['cache']['focus']['data'][$id]['url'];?>" class="xi2 y" target="_blank">查看 &raquo;</a></p>
</div><?php $focusi ++;?><?php } ?>
<script type="text/javascript">
var focusnum = <?php echo $focusnum;?>;
if(focusnum < 2) {
$('focus_ctrl').style.display = 'none';
}
if(!$('focuscur').innerHTML) {
var randomnum = parseInt(Math.round(Math.random() * focusnum));
$('focuscur').innerHTML = Math.max(1, randomnum);
}
showfocus();
var focusautoshow = window.setInterval('showfocus(\'next\', 1);', 5000);
</script>
<?php } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && $_G['setting']['showpatchnotice'] == 1) { ?>
<div class="focus patch" id="patch_notice"></div>
<?php } ?><?php echo adshow("footerbanner/wp a_f/1");?><?php echo adshow("footerbanner/wp a_f/2");?><?php echo adshow("footerbanner/wp a_f/3");?><?php echo adshow("float/a_fl/1");?><?php echo adshow("float/a_fr/2");?><?php echo adshow("couplebanner/a_fl a_cb/1");?><?php echo adshow("couplebanner/a_fr a_cb/2");?><?php echo adshow("cornerbanner/a_cn");?><?php if(!empty($_G['setting']['pluginhooks']['global_footer'])) echo $_G['setting']['pluginhooks']['global_footer'];?>
<div class="chfooter">
<div id="ft" class="wp cl">
<div class="footer_beta">
 <a href="javascript:void(0);" class="ch_weibo"></a>
 <a href="javascript:void(0);" class="ch_weixin"></a>
 <a href="javascript:void(0);" class="ch_ipad"></a>
 <div class="openBox sinaBox ch_weibo-box">
            	<!-- <a href="http://weibo.com/usportnews" target="_blank" title="新浪微博" rel="nofollow"><span style=" background:#FFF"><iframe width="220" height="120" class="share_self"  frameborder="0" scrolling="no" src="http://widget.weibo.com/weiboshow/index.php?language=&amp;width=220&amp;height=150&amp;fansRow=2&amp;ptype=1&amp;speed=0&amp;skin=1&amp;isTitle=1&amp;noborder=1&amp;isWeibo=0&amp;isFans=0&amp;uid=1735856841&amp;verifier=a36ceb4f&amp;dpc=1"></iframe></span></a> -->
            	<div class="openBg code_xl"><img src="<?php echo $_G['config']['static'];?>/images/code_wb.jpg" width="100" height="100"/><p>关注5U新浪微博</p><div><a href="http://www.weibo.com/usportnews" target="_blank"><strong>+</strong>关注</a></div></div>
            </div>
        	<div class="openBox code_wx ch_weixin-box">
            	<div class="openBg"><img src="<?php echo $_G['config']['static'];?>/images/code_wx.jpg" width="100" height="100"/><p>关注5U微信</p></div>
            </div>
            <div class="openBox code_sj ch_ipad-box">
            	<div class="openBg"><img src="<?php echo $_G['config']['static'];?>/images/code_sj.jpg" width="100" height="100"/><p>下载聊球APP</p></div>
            </div>
 <p class="foot_logo"><a href="<?php echo $_G['setting']['siteurl'];?>" target="_blank"><img src="<?php echo $_G['config']['static'];?>/images/logo_white.png" width="175" height="45" border="0"></a></p>
</div>

<div id="frt">
     <p class="foot_nav">
<a href="about/aboutus/" target="_blank" title="关于我们">关于我们</a>|
<a href="about/advert/" target="_blank" title="广告服务">广告服务</a>|
<a href="about/hr/" target="_blank" title="招聘信息">招聘信息</a>|
<a href="about/copyright/" target="_blank" title="法律声明">法律声明</a>|
<a href="about/contactus/" target="_blank" title="联系方式">联系方式</a>|
<a href="about/sitemap/" target="_blank" title="网站地图">网站地图</a><!--|
<a href="http://about.5usport.com/about/link/" target="_blank" title="友情链接">友情链接</a>-->
</p>
<p>Copyright © 2009-2014 5usport.com, All rights reserved. 广州市晌网文化传播有限公司 <a href="http://gdcainfo.miitbeian.gov.cn/publish/query/indexFirst.action" target="_blank" class="fo_color" rel="nofollow">粤ICP备11095914号</a></p>
<p>免责声明：本站部分文章作品转载自合作网站，如有侵权问题，请及时联系，我们将尽快处理。</p>
</div><?php updatesession();?><?php if($_G['uid'] && $_G['group']['allowinvisible']) { ?>
<script type="text/javascript">
var invisiblestatus = '<?php if($_G['session']['invisible']) { ?>隐身<?php } else { ?>在线<?php } ?>';
var loginstatusobj = $('loginstatusid');
if(loginstatusobj != undefined && loginstatusobj != null) loginstatusobj.innerHTML = invisiblestatus;
</script>
<?php } ?>
</div>
</div>


<?php if(!$_G['setting']['bbclosed'] && !$_G['member']['freeze'] && !$_G['member']['groupexpiry']) { if($_G['uid'] && !isset($_G['cookie']['checkpm'])) { ?>
<script src="home.php?mod=spacecp&ac=pm&op=checknewpm&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } if($_G['uid'] && helper_access::check_module('follow') && !isset($_G['cookie']['checkfollow'])) { ?>
<script src="home.php?mod=spacecp&ac=follow&op=checkfeed&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } if(!isset($_G['cookie']['sendmail'])) { ?>
<script src="home.php?mod=misc&ac=sendmail&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && !isset($_G['cookie']['checkpatch'])) { ?>
<script src="misc.php?mod=patch&action=checkpatch&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } } if($_GET['diy'] == 'yes') { if(check_diy_perm($topic) && (empty($do) || $do != 'index')) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>common_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script src="<?php echo $_G['setting']['jspath'];?>portal_diy<?php if(!check_diy_perm($topic, 'layout')) { ?>_data<?php } ?>.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php } if($space['self'] && CURMODULE == 'space' && $do == 'index') { ?>
<script src="<?php echo $_G['setting']['jspath'];?>common_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script src="<?php echo $_G['setting']['jspath'];?>space_diy.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php } } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && $_G['setting']['showpatchnotice'] == 1) { ?>
<script type="text/javascript">patchNotice();</script>
<?php } if($_G['uid'] && $_G['member']['allowadmincp'] == 1 && empty($_G['cookie']['pluginnotice'])) { ?>
<div class="focus plugin" id="plugin_notice"></div>
<script type="text/javascript">pluginNotice();</script>
<?php } if(!$_G['setting']['bbclosed'] && !$_G['member']['freeze'] && !$_G['member']['groupexpiry'] && $_G['setting']['disableipnotice'] != 1 && $_G['uid'] && !empty($_G['cookie']['lip'])) { ?>
<div class="focus plugin" id="ip_notice"></div>
<script type="text/javascript">ipNotice();</script>
<?php } if($_G['member']['newprompt'] && (empty($_G['cookie']['promptstate_'.$_G['uid']]) || $_G['cookie']['promptstate_'.$_G['uid']] != $_G['member']['newprompt']) && $_GET['do'] != 'notice') { ?>
<script type="text/javascript">noticeTitle();</script>
<?php } if(($_G['member']['newpm'] || $_G['member']['newprompt']) && empty($_G['cookie']['ignore_notice'])) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>html5notification.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script type="text/javascript">
var h5n = new Html5notification();
if(h5n.issupport()) {
<?php if($_G['member']['newpm'] && $_GET['do'] != 'pm') { ?>
h5n.shownotification('pm', '<?php echo $_G['siteurl'];?>home.php?mod=space&do=pm', '<?php echo avatar($_G[uid],small,true);?>', '新的短消息', '有新的短消息，快去看看吧');
<?php } if($_G['member']['newprompt'] && $_GET['do'] != 'notice') { if(is_array($_G['member']['category_num'])) foreach($_G['member']['category_num'] as $key => $val) { $noticetitle = lang('template', 'notice_'.$key);?>h5n.shownotification('notice_<?php echo $key;?>', '<?php echo $_G['siteurl'];?>home.php?mod=space&do=notice&view=<?php echo $key;?>', '<?php echo avatar($_G[uid],small,true);?>', '<?php echo $noticetitle;?> (<?php echo $val;?>)', '有新的提醒，快去看看吧');
<?php } } ?>
}
</script>
<?php } userappprompt();?><?php if($_G['basescript'] != 'userapp') { ?>
<div class="ch_scrollbtn" id="rightSideScrollTopBtn">
 <?php if($_G['fid']) { ?><a id="newspecialtmp" <?php if(!$_G['forum']['allowspecialonly'] && empty($_G['forum']['picstyle']) && !$_G['forum']['threadsorts']['required']) { ?> onclick="showWindow('newthread', '<?php echo $_G['siteurl'];?>forum.php?mod=post&action=newthread&fid=<?php echo $_G['fid'];?>')"<?php } else { ?> onclick="location.href='<?php echo $_G['siteurl'];?>forum.php?mod=post&action=newthread&fid=<?php echo $_G['fid'];?>';return false;"<?php } ?> href="javascript:;" title="发帖" class="thread_ch">发帖</a><?php } ?>
 <a href="download/app/liaoqiu/" class="app_load" target="_blank">APP下载</a>
 <a href="thread-247.html" class="feedback" target="_blank">问题反馈</a>
 <a title="返回顶部" onclick="window.scrollTo('0','0')" class="scrolltops">top</a>
</div>

<!--热门频道-->
<?php if(!empty($_G['setting']['pluginhooks']['global_hot_forum'])) echo $_G['setting']['pluginhooks']['global_hot_forum'];?>
<!--热门频道end-->
<script type="text/javascript">_attachEvent(window, 'scroll', function () { showTopLink(); });checkBlind();</script>
<?php } if(isset($_G['makehtml'])) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>html2dynamic.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script type="text/javascript">
var html_lostmodify = <?php echo TIMESTAMP;?>;
htmlGetUserStatus();
<?php if(isset($_G['htmlcheckupdate'])) { ?>
htmlCheckUpdate();
<?php } ?>
</script>
<?php } output();?><div style="display:none;"><?php if($_G['setting']['statcode']) { ?><?php echo $_G['setting']['statcode'];?><?php } ?></div>

</body>
</html>
