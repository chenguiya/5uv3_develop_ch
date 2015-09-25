<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); if(CURMODULE != 'logging') { ?>
<script src="<?php echo $_G['setting']['jspath'];?>logging.js?<?php echo VERHASH;?>" type="text/javascript"></script>

<form class="chformlogin" method="post" autocomplete="off" id="lsform" action="member.php?mod=logging&amp;action=login&amp;loginsubmit=yes&amp;infloat=yes&amp;lssubmit=yes" onsubmit="<?php if($_G['setting']['pwdsafety']) { ?>pwmd5('ls_password');<?php } ?>return lsSubmit();">
    <div class="chlogin">
<button id='btn_login_simple' type="submit" class="" tabindex="904">登录</button><a id="btn_register_simple" href="member.php?mod=<?php echo $_G['setting']['regname'];?>">注册</a>
</div>
<div class="fastlg cl">
<span id="return_ls" style="display:none"></span>
<div class="y pns" style="display:none;">
<table cellspacing="0" cellpadding="0">
<tr>
<?php if(!$_G['setting']['autoidselect']) { ?>
<td>
<span class="ftid">
<select name="fastloginfield" id="ls_fastloginfield" width="40" tabindex="900">
<option value="username">用户名</option>
<?php if(getglobal('setting/uidlogin')) { ?>
<option value="uid">UID</option>
<?php } ?>
<option value="email">Email</option>
</select>
</span>
<script type="text/javascript">simulateSelect('ls_fastloginfield')</script>
</td>
<td><input type="text" name="username" id="ls_username" autocomplete="off" class="px vm" tabindex="901" /></td>
<?php } else { ?>
<td><label for="ls_username">帐号</label></td>
<td><input type="text" name="username" id="ls_username" class="px vm xg1" <?php if($_G['setting']['autoidselect']) { ?> value="<?php if(getglobal('setting/uidlogin')) { ?>UID/<?php } ?>用户名/Email" onfocus="if(this.value == '<?php if(getglobal('setting/uidlogin')) { ?>UID/<?php } ?>用户名/Email'){this.value = '';this.className = 'px vm';}" onblur="if(this.value == ''){this.value = '<?php if(getglobal('setting/uidlogin')) { ?>UID/<?php } ?>用户名/Email';this.className = 'px vm xg1';}"<?php } ?> tabindex="901" /></td>
<?php } ?>
</tr>

</table>
<input type="hidden" name="quickforward" value="yes" />
<input type="hidden" name="handlekey" value="ls" />
</div>
<!--hook/global_login_extra-->
</div>
</form>

<?php if($_G['setting']['pwdsafety']) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>md5.js?<?php echo VERHASH;?>" type="text/javascript" reload="1"></script>
<?php } } $_referer = urlencode($_G['siteurl'].substr($_SERVER['REQUEST_URI'], 1));?><!--<div class="chsimp">-->
     <!--<span>关联账号登录：</span>-->
 <!--<a href="plugin.php?id=mpage_weibo:login" class="ch_webo"></a>-->
 <!--<a href="connect.php?mod=login&amp;op=init&amp;referer=<?php echo $_referer;?>&amp;statfrom=login" class="ch_QQ"></a>-->
<!--</div>-->
