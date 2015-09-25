<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Cache-control"
          content="<?php if($_G['setting']['mobile']['mobilecachetime'] > 0) { ?><?php echo $_G['setting']['mobile']['mobilecachetime'];?><?php } else { ?>no-cache<?php } ?>"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="keywords" content="<?php if(!empty($metakeywords)) { echo dhtmlspecialchars($metakeywords); } ?>"/>
    <meta name="description"
          content="<?php if(!empty($metadescription)) { echo dhtmlspecialchars($metadescription); ?> <?php } ?>,<?php echo $_G['setting']['bbname'];?>"/>
    <title><?php if(!empty($navtitle)) { ?><?php echo $navtitle;?> - <?php } if(empty($nobbname)) { ?> <?php echo $_G['setting']['bbname'];?> -
        <?php } ?> 手机版</title>
    <link href="http://www.5usport.com/favicon.ico" rel="shortcut icon"/>
    <link rel="apple-touch-icon-precomposed" href="http://www.5usport.com/mbbicon.png"/>
    <link rel="stylesheet" href="template/usportstyle/touch/common/fontawesome/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="template/usportstyle/touch/common/swiper.3.1.2.min.css" type="text/css" media="all">
    <link rel="stylesheet" href="template/usportstyle/touch/common/touch_style.css" type="text/css" media="all">
    <link rel="stylesheet" href="template/usportstyle/touch/common/font/5ufont.css" type="text/css" media="all">
    <!--<script src="<?php echo STATICURL;?>js/mobile/zepto.min.js?<?php echo VERHASH;?>" type="text/javascript"></script>-->
    <script src="<?php echo STATICURL;?>js/mobile/jquery-1.8.3.min.js?<?php echo VERHASH;?>" type="text/javascript"></script>
    <script type="text/javascript">
        var STYLEID = '<?php echo STYLEID;?>',
                STATICURL = '<?php echo STATICURL;?>',
                IMGDIR = '<?php echo IMGDIR;?>',
                VERHASH = '<?php echo VERHASH;?>',
                charset = '<?php echo CHARSET;?>',
                discuz_uid = '<?php echo $_G['uid'];?>',
                cookiepre = '<?php echo $_G['config']['cookie']['cookiepre'];?>',
                cookiedomain = '<?php echo $_G['config']['cookie']['cookiedomain'];?>',
                cookiepath = '<?php echo $_G['config']['cookie']['cookiepath'];?>',
                showusercard = '<?php echo $_G['setting']['showusercard'];?>',
                attackevasive = '<?php echo $_G['config']['security']['attackevasive'];?>',
                disallowfloat = '<?php echo $_G['setting']['disallowfloat'];?>',
                creditnotice = '<?php if($_G[' setting '][' creditnotice ']) { ?><?php echo $_G;?>[' setting '][' creditnames ']<?php } ?>',
                defaultstyle = '<?php echo $_G['style']['defaultextstyle'];?>',
                REPORTURL = '<?php echo $_G['currenturl_encode'];?>',
                SITEURL = '<?php echo $_G['siteurl'];?>',
                JSPATH = '<?php echo $_G['setting']['jspath'];?>';
    </script>
    <script src="<?php echo STATICURL;?>js/mobile/common.js?<?php echo VERHASH;?>" type="text/javascript" charset="<?php echo CHARSET;?>"></script>
    <script src="template/usportstyle/touch/common/js/doT.min.js" type="text/javascript"></script>
    <script src="template/usportstyle/touch/common/js/swiper.3.1.2.jquery.min.js" type="text/javascript"></script>
    <script src="template/usportstyle/touch/common/js/common.js" type="text/javascript"></script>
</head>
<body>