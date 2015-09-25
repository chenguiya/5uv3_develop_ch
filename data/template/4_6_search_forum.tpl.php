<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('forum');
0
|| checktplrefresh('./template/usportstyle/search/forum.htm', './template/usportstyle/search/header.htm', 1443089910, '6', './data/template/4_6_search_forum.tpl.php', './template/usportstyle', 'search/forum')
|| checktplrefresh('./template/usportstyle/search/forum.htm', './template/usportstyle/search/pubsearch.htm', 1443089910, '6', './data/template/4_6_search_forum.tpl.php', './template/usportstyle', 'search/forum')
|| checktplrefresh('./template/usportstyle/search/forum.htm', './template/usportstyle/search/thread_list.htm', 1443089910, '6', './data/template/4_6_search_forum.tpl.php', './template/usportstyle', 'search/forum')
|| checktplrefresh('./template/usportstyle/search/forum.htm', './template/usportstyle/search/footer.htm', 1443089910, '6', './data/template/4_6_search_forum.tpl.php', './template/usportstyle', 'search/forum')
|| checktplrefresh('./template/usportstyle/search/forum.htm', './template/default/common/header_common.htm', 1443089910, '6', './data/template/4_6_search_forum.tpl.php', './template/usportstyle', 'search/forum')
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
<base href="<?php echo $_G['siteurl'];?>" /><link rel="stylesheet" type="text/css" href="data/cache/style_4_common.css?<?php echo VERHASH;?>" /><link rel="stylesheet" type="text/css" href="data/cache/style_4_search_forum.css?<?php echo VERHASH;?>" /><?php if($_G['uid'] && isset($_G['cookie']['extstyle']) && strpos($_G['cookie']['extstyle'], TPLDIR) !== false) { ?><link rel="stylesheet" id="css_extstyle" type="text/css" href="<?php echo $_G['cookie']['extstyle'];?>/style.css" /><?php } elseif($_G['style']['defaultextstyle']) { ?><link rel="stylesheet" id="css_extstyle" type="text/css" href="<?php echo $_G['style']['defaultextstyle'];?>/style.css" /><?php } ?><script type="text/javascript">var STYLEID = '<?php echo STYLEID;?>', STATICURL = '<?php echo STATICURL;?>', IMGDIR = '<?php echo IMGDIR;?>', VERHASH = '<?php echo VERHASH;?>', charset = '<?php echo CHARSET;?>', discuz_uid = '<?php echo $_G['uid'];?>', cookiepre = '<?php echo $_G['config']['cookie']['cookiepre'];?>', cookiedomain = '<?php echo $_G['config']['cookie']['cookiedomain'];?>', cookiepath = '<?php echo $_G['config']['cookie']['cookiepath'];?>', showusercard = '<?php echo $_G['setting']['showusercard'];?>', attackevasive = '<?php echo $_G['config']['security']['attackevasive'];?>', disallowfloat = '<?php echo $_G['setting']['disallowfloat'];?>', creditnotice = '<?php if($_G['setting']['creditnotice']) { ?><?php echo $_G['setting']['creditnames'];?><?php } ?>', defaultstyle = '<?php echo $_G['style']['defaultextstyle'];?>', REPORTURL = '<?php echo $_G['currenturl_encode'];?>', SITEURL = '<?php echo $_G['siteurl'];?>', JSPATH = '<?php echo $_G['setting']['jspath'];?>', CSSPATH = '<?php echo $_G['setting']['csspath'];?>', DYNAMICURL = '<?php echo $_G['dynamicurl'];?>';</script>
<script src="<?php echo $_G['setting']['jspath'];?>common.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<?php if(empty($_GET['diy'])) { $_GET['diy'] = '';?><?php } if(!isset($topic)) { $topic = array();?><?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo $_G['config']['static'];?>/stsearch.css">
</head>
<body id="nv_search" onkeydown="if(event.keyCode==27) return false;">
<div id="append_parent"></div><div id="ajaxwaitid"></div>
<?php if($_G['adminid']) { ?>
<div id="toptb" class="cl search-toolbar-top">
<div class="z">
<a href="./" id="navs" class="showmenu xi2" onMouseOver="showMenu(this.id)">返回首页<i></i></a>
</div>
<?php if($_G['setting']['navs']) { ?>
<ul class="p_pop h_pop p_sera" id="navs_menu" style="display: none;"><?php if(is_array($_G['setting']['navs'])) foreach($_G['setting']['navs'] as $nav) { $nav_showmenu = strpos($nav['nav'], 'onmouseover="showMenu(');?>    <?php $nav_navshow = strpos($nav['nav'], 'onmouseover="navShow(')?>    <?php if($nav_hidden !== false || $nav_navshow !== false) { $nav['nav'] = preg_replace("/onmouseover\=\"(.*?)\"/i", '',$nav['nav'])?>    <?php } if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?><li <?php echo $nav['nav'];?>></li><?php } } ?>
    <i></i>
</ul>
<?php } ?>     
</div>
<?php } if(!empty($_G['setting']['plugins']['jsmenu'])) { ?>
<ul class="p_pop h_pop" id="plugin_menu" style="display: none"><?php if(is_array($_G['setting']['plugins']['jsmenu'])) foreach($_G['setting']['plugins']['jsmenu'] as $module) { ?>     <?php if(!$module['adminid'] || ($module['adminid'] && $_G['adminid'] > 0 && $module['adminid'] >= $_G['adminid'])) { ?>
     <li><?php echo $module['url'];?></li>
     <?php } } ?>   
</ul>
<?php } ?>
<?php echo $_G['setting']['menunavs'];?>

<ul id="myspace_menu" class="p_pop" style="display:none;"><?php if(is_array($_G['setting']['mynavs'])) foreach($_G['setting']['mynavs'] as $nav) { if($nav['available'] && (!$nav['level'] || ($nav['level'] == 1 && $_G['uid']) || ($nav['level'] == 2 && $_G['adminid'] > 0) || ($nav['level'] == 3 && $_G['adminid'] == 1))) { ?>
<li><?php echo $nav['code'];?></li>
<?php } } ?>
</ul><div id="ct" class="cl w">
<div class="mw">
<form class="searchform" method="post" autocomplete="off" action="search.php?mod=forum" onsubmit="if($('scform_srchtxt')) searchFocus($('scform_srchtxt'));">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
   <?php $keywordenc = $keyword ? rawurlencode($keyword) : '';?><?php if($searchid || ($_GET['adv'] && CURMODULE == 'forum')) { ?>
<table id="scform" class="mbm search-result" cellspacing="0" cellpadding="0">
<tr>
<td><h1><a href="search.php" title="<?php echo $_G['setting']['bbname'];?>"><img src="<?php echo IMGDIR;?>/logo_sc_s.png" alt="<?php echo $_G['setting']['bbname'];?>" /></a></h1></td>
<td>
<div id="scform_tb" class="cl clearfix">
      <?php if(CURMODULE == 'forum') { ?>
<span class="y">
<a href="javascript:;" id="quick_sch" class="showmenu ch_sero" onmouseover="delayShow(this);">快速<i></i></a>
<?php if(CURMODULE == 'forum') { ?>
<a href="search.php?mod=forum&amp;adv=yes<?php if($keyword) { ?>&amp;srchtxt=<?php echo $keywordenc;?><?php } ?>">高级</a>
<?php } ?>
</span>
<?php } if($_G['setting']['portalstatus'] && $_G['setting']['search']['portal']['status'] && ($_G['group']['allowsearch'] & 1 || $_G['adminid'] == 1)) { ?><?php
$slist[portal] = <<<EOF
<a href="search.php?mod=portal
EOF;
 if($keyword) { 
$slist[portal] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[portal] .= <<<EOF
"
EOF;
 if(CURMODULE == 'portal') { 
$slist[portal] .= <<<EOF
 class="a"
EOF;
 } 
$slist[portal] .= <<<EOF
>文章</a>
EOF;
?><?php } if($_G['setting']['search']['forum']['status'] && ($_G['group']['allowsearch'] & 2 || $_G['adminid'] == 1)) { ?><?php
$slist[forum] = <<<EOF
<a href="search.php?mod=forum
EOF;
 if($keyword) { 
$slist[forum] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[forum] .= <<<EOF
"
EOF;
 if(CURMODULE == 'forum') { 
$slist[forum] .= <<<EOF
 class="a"
EOF;
 } 
$slist[forum] .= <<<EOF
>帖子</a>
EOF;
?><?php } ?><?php
$slist[user] = <<<EOF
<a href="search.php?mod=user
EOF;
 if($keyword) { 
$slist[user] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[user] .= <<<EOF
"
EOF;
 if(CURMODULE == 'user') { 
$slist[user] .= <<<EOF
 class="a"
EOF;
 } 
$slist[user] .= <<<EOF
>用户</a>
EOF;
?><?php echo implode("", $slist);; ?></div>
<div id="scform_form" class="clearfix">
     <span class="td_srchtxt"><input type="text" id="scform_srchtxt" name="srchtxt" size="45" maxlength="40" value="<?php echo $keyword;?>" tabindex="1" x-webkit-speech speech /><script type="text/javascript">initSearchmenu('scform_srchtxt');$('scform_srchtxt').focus();</script></span><span class="td_srchbtn"><input type="hidden" name="searchsubmit" value="yes" /><button type="submit" id="scform_submit" class="schbtn"><strong>搜索</strong></button></span>
</div>
</td>
</tr>
</table>
<?php } else { if(!empty($srchtype)) { ?><input type="hidden" name="srchtype" value="<?php echo $srchtype;?>" /><?php } if($srchtype != 'threadsort') { ?>
<div class="hm mtw ptw pbw"><h1 class="mtw ptw"><a href="./" title="<?php echo $_G['setting']['bbname'];?>"><img src="<?php echo IMGDIR;?>/logo_sc.png" alt="<?php echo $_G['setting']['bbname'];?>" /></a><br>因为喜欢相同的球星而欢聚一堂</h1></div>
<table id="scform" class="search-entry" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
<tr>
<td>
    <div id="scform_tb" class="xs2 clearfix">
    <?php if(CURMODULE == 'forum') { ?>
<span class="y xs1">
<a href="javascript:;" id="quick_sch" class="showmenu ch_sero" onmouseover="delayShow(this);">快速<i></i></a>
<?php if(CURMODULE == 'forum') { ?>
<a href="search.php?mod=forum&amp;adv=yes">高级</a>
<?php } ?>
</span>
<?php } if(helper_access::check_module('portal') && $_G['setting']['search']['portal']['status'] && ($_G['group']['allowsearch'] & 1 || $_G['adminid'] == 1)) { ?><?php
$slist[portal] = <<<EOF
<a href="search.php?mod=portal
EOF;
 if($keyword) { 
$slist[portal] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[portal] .= <<<EOF
"
EOF;
 if(CURMODULE == 'portal') { 
$slist[portal] .= <<<EOF
 class="a"
EOF;
 } 
$slist[portal] .= <<<EOF
>文章</a>
EOF;
?><?php } if($_G['setting']['search']['forum']['status'] && ($_G['group']['allowsearch'] & 2 || $_G['adminid'] == 1)) { ?><?php
$slist[forum] = <<<EOF
<a href="search.php?mod=forum
EOF;
 if($keyword) { 
$slist[forum] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[forum] .= <<<EOF
"
EOF;
 if(CURMODULE == 'forum') { 
$slist[forum] .= <<<EOF
 class="a"
EOF;
 } 
$slist[forum] .= <<<EOF
>帖子</a>
EOF;
?><?php } echo implode("", $slist);; ?><a href="search.php?mod=user<?php if($keyword) { ?>&amp;srchtxt=<?php echo $keywordenc;?>&amp;searchsubmit=yes<?php } ?>"
<?php if(CURMODULE == 'user') { ?> class="a"<?php } ?>>用户</a>
<a href="plugin.php?id=fansclub<?php if($keyword) { ?>&amp;search=<?php echo $keywordenc;?><?php } ?>"
<?php if(CURMODULE == 'fansclub') { ?> class="a"<?php } ?>>球迷会</a>
</div>
</td>
</tr>
<tr>
<td>
    <div id="scform_form" class="clearfix">
     <span class="td_srchtxt"><input type="text" id="scform_srchtxt" name="srchtxt" size="65" maxlength="40" value="<?php echo $keyword;?>" tabindex="1" /><script type="text/javascript">initSearchmenu('scform_srchtxt');$('scform_srchtxt').focus();</script></span><span class="td_srchbtn"><input type="hidden" name="searchsubmit" value="yes" /><button type="submit" id="scform_submit" value="true"><strong>搜索</strong></button></span>
</div>
</td>
</tr>
</table>
<?php } } if(CURMODULE == 'forum') { ?>
<ul id="quick_sch_menu" class="p_pop" style="display: none;">
<li><a href="search.php?mod=forum&amp;srchfrom=3600&amp;searchsubmit=yes">1 小时以内的新帖</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=14400&amp;searchsubmit=yes">4 小时以内的新帖</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=28800&amp;searchsubmit=yes">8 小时以内的新帖</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=86400&amp;searchsubmit=yes">24 小时以内的新帖</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=604800&amp;searchsubmit=yes">1 周内帖子</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=2592000&amp;searchsubmit=yes">1 月内帖子</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=15552000&amp;searchsubmit=yes">6 月内帖子</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=31536000&amp;searchsubmit=yes">1 年内帖子</a></li>
</ul>
<?php } ?><?php if(!empty($_G['setting']['pluginhooks']['forum_top'])) echo $_G['setting']['pluginhooks']['forum_top'];?><?php $policymsgs = $p = '';?><?php if(is_array($_G['setting']['creditspolicy']['search'])) foreach($_G['setting']['creditspolicy']['search'] as $id => $policy) { ?><?php
$policymsg = <<<EOF

EOF;
 if($_G['setting']['extcredits'][$id]['img']) { 
$policymsg .= <<<EOF
{$_G['setting']['extcredits'][$id]['img']} 
EOF;
 } 
$policymsg .= <<<EOF
{$_G['setting']['extcredits'][$id]['title']} {$policy} {$_G['setting']['extcredits'][$id]['unit']}
EOF;
?><?php $policymsgs .= $p.$policymsg;$p = ', ';?><?php } if($policymsgs) { ?><p>每进行一次搜索将扣除 <?php echo $policymsgs;?></p><?php } ?>
</form>

<?php if(!empty($searchid) && submitcheck('searchsubmit', 1)) { ?>
<div class="tl">
<div class="sttl mbn">
<h2><?php if($keyword) { ?>结果: <em>找到 “<span class="emfont"><?php echo $keyword;?></span>” 相关内容 <?php echo $index['num'];?> 个</em> <?php if($modfid) { ?><a href="forum.php?mod=modcp&amp;action=thread&amp;fid=<?php echo $modfid;?>&amp;keywords=<?php echo $modkeyword;?>&amp;submit=true&amp;do=search&amp;page=<?php echo $page;?>" target="_blank">进入管理面板</a><?php } } else { ?>结果: <em>找到相关主题 <?php echo $index['num'];?> 个</em><?php } ?></h2>
</div><?php echo adshow("search/y mtw");?><?php if(empty($threadlist)) { ?>
<p class="emp xs2 xg2">对不起，没有找到匹配结果。</p>
<?php } else { ?>
<div class="slst mtw" id="threadlist" <?php if($modfid) { ?> style="position: relative;"<?php } ?>>
<?php if($modfid) { ?>
<form method="post" autocomplete="off" name="moderate" id="moderate" action="forum.php?mod=topicadmin&amp;action=moderate&amp;fid=<?php echo $modfid;?>&amp;infloat=yes&amp;nopost=yes">
<?php } ?>
<ul><?php if(is_array($threadlist)) foreach($threadlist as $thread) { ?><li class="pbw" id="<?php echo $thread['tid'];?>">
<h3 class="xs3">
<?php if($modfid) { if($thread['fid'] == $modfid && ($thread['displayorder'] <= 3 || $_G['adminid'] == 1)) { ?>
<input onclick="tmodclick(this)" type="checkbox" name="moderate[]" value="<?php echo $thread['tid'];?>" />&nbsp;
<?php } else { ?>
<input type="checkbox" disabled="disabled" />&nbsp;
<?php } } ?>
<!--<a href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['realtid'];?>&amp;highlight=<?php echo $index['keywords'];?>" target="_blank" <?php echo $thread['highlight'];?>><?php echo $thread['subject'];?></a>-->
<a href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['realtid'];?>" target="_blank" <?php echo $thread['highlight'];?>><?php echo $thread['subject'];?></a>
</h3>
<p class="xg1"><?php echo $thread['replies'];?> 个回复 &nbsp;&nbsp; <?php echo $thread['views'];?> 次查看</p>
<p class="ch_stitle"><?php if(!$thread['price'] && !$thread['readperm']) { ?><?php echo $thread['message'];?><?php } else { ?>内容隐藏需要，请点击进去查看<?php } ?></p>
<p class="ch_sfit">
<span><?php echo $thread['dateline'];?></span>
 -
<span>
<?php if($thread['authorid'] && $thread['author']) { ?>
<a href="home.php?mod=space&amp;uid=<?php echo $thread['authorid'];?>" target="_blank"><?php echo $thread['author'];?></a>
<?php } else { if($_G['forum']['ismoderator']) { ?><a href="home.php?mod=space&amp;uid=<?php echo $thread['authorid'];?>" target="_blank">匿名</a><?php } else { ?>匿名<?php } } ?>
</span>
 -
<span><!--<a href="forum.php?mod=forumdisplay&amp;fid=<?php echo $thread['fid'];?>" target="_blank" class="xi1"><?php echo $thread['forumname'];?></a>-->

<a href="<?php if($_GET['mod'] == 'group') { ?>fans/topic/<?php echo $thread['fid'];?>/<?php } else { ?>group/<?php echo $thread['fid'];?>/<?php } ?>" target="_blank" class="xi1"><?php echo $thread['forumname'];?></a></span>
</p>
</li>
<?php } ?>
</ul>
<?php if($modfid) { ?>
</form>
<script src="<?php echo $_G['setting']['jspath'];?>forum_moderate.js?<?php echo VERHASH;?>" type="text/javascript"></script><?php include template('forum/topicadmin_modlayer'); } ?>
</div>
<?php } if(!empty($multipage)) { ?><div class="pgs cl mbm"><?php echo $multipage;?></div><?php } ?>
</div><?php } ?>
    
</div>
</div>
<?php if(!empty($_G['setting']['pluginhooks']['forum_bottom'])) echo $_G['setting']['pluginhooks']['forum_bottom'];?><?php $focusid = getfocus_rand($_G[basescript]);?><?php if($focusid !== null) { $focus = $_G['cache']['focus']['data'][$focusid];?><div class="focus" id="focus">
<div class="bm">
<div class="bm_h cl">
<a href="javascript:;" onclick="setcookie('nofocus_<?php echo $focusid;?>', 1, <?php echo $_G['cache']['focus']['cookie'];?>*3600);$('focus').style.display='none'" class="y" title="关闭">关闭</a>
<h2><?php if($_G['cache']['focus']['title']) { ?><?php echo $_G['cache']['focus']['title'];?><?php } else { ?>站长推荐<?php } ?></h2>
</div>
<div class="bm_c">
<dl class="xld cl bbda">
<dt><a href="<?php echo $focus['url'];?>" class="xi2" target="_blank"><?php echo $focus['subject'];?></a></dt>
<?php if($focus['image']) { ?>
<dd class="m"><a href="<?php echo $focus['url'];?>" target="_blank"><img src="<?php echo $focus['image'];?>" alt="<?php echo $focus['subject'];?>" /></a></dd>
<?php } ?>
<dd><?php echo $focus['summary'];?></dd>
</dl>
<p class="ptn hm"><a href="<?php echo $focus['url'];?>" class="xi2" target="_blank">查看 &raquo;</a></p>
</div>
</div>
</div>
<?php } ?><?php echo adshow("footerbanner/wp a_f hm/1");?><?php echo adshow("footerbanner/wp a_f hm/2");?><?php echo adshow("footerbanner/wp a_f hm/3");?><?php echo adshow("float/a_fl/1");?><?php echo adshow("float/a_fr/2");?><?php echo adshow("couplebanner/a_fl a_cb/1");?><?php echo adshow("couplebanner/a_fr a_cb/2");?><?php if(!empty($_G['setting']['pluginhooks']['global_footer'])) echo $_G['setting']['pluginhooks']['global_footer'];?>

<div id="ft" class="ch_footer cl">
     <div class="ch_copyright">
      <a href="http://www.5usport.com/about/aboutus/" target="_blank" rel="nofollow">关于我们</a>&nbsp;|&nbsp;<a href="http://www.5usport.com/about/advert/" target="_blank" rel="nofollow">广告服务</a>&nbsp;|&nbsp;<a href="http://www.5usport.com/about/hr/" target="_blank" rel="nofollow">招聘信息</a>&nbsp;|&nbsp;<a href="http://www.5usport.com/about/copyright/" target="_blank" rel="nofollow">法律声明</a>&nbsp;|&nbsp;<a href="http://www.5usport.com/about/contactus/" target="_blank" rel="nofollow">联系方式</a>&nbsp;|&nbsp;<a href="http://www.5usport.com/sitemap.html" target="_blank">网站地图</a>
  <!--&nbsp;|&nbsp;<a href="http://www.5usport.com/about/link/" target="_blank">友情链接</a>&nbsp;|&nbsp;<a href="http://www.5usport.com/dujia" target="_blank">体育新闻</a>--><br>
广州市晌网文化传播有限公司  <a href="http://gdcainfo.miitbeian.gov.cn/publish/query/indexFirst.action">粤ICP备11095914号-1</a> Copyright © 2009-2014 usportnews.com, All rights reserved.
 </div>
</div>
<?php if($_G['uid'] && !isset($_G['cookie']['checkpm'])) { ?>
<script src="home.php?mod=spacecp&ac=pm&op=checknewpm&rand=<?php echo $_G['timestamp'];?>" type="text/javascript"></script>
<?php } output();?></body>
</html>