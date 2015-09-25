<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<?php if(!empty($_G['setting']['pluginhooks']['global_footer_mobile'])) echo $_G['setting']['pluginhooks']['global_footer_mobile'];?><?php $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);$clienturl = 'http://www.discuz.net/mobile.php?platform=android';?><?php if(strpos($useragent, 'iphone') !== false || strpos($useragent, 'ios') !== false) { $clienturl = 'http://www.discuz.net/mobile.php?platform=ios';?><?php } elseif(strpos($useragent, 'android') !== false) { $clienturl = 'http://www.ituiz.com/mobile.php?platform=android';?><?php } elseif(strpos($useragent, 'windows phone') !== false) { $clienturl = 'http://www.ituiz.com/mobile.php?platform=windowsphone';?><?php } ?>

<div id="mask" style="display:none;"></div>
<script src="<?php echo STATICURL;?>js/mobile/mobile.js?<?php echo VERHASH;?>" type="text/javascript"></script>
</body>
</html><?php updatesession();?><?php if(defined('IN_MOBILE')) { output();?><?php } else { output_preview();?><?php } ?>