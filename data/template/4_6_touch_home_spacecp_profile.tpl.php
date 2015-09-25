<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('spacecp_profile');
0
|| checktplrefresh('./template/usportstyle/touch/home/spacecp_profile.htm', './template/default/home/spacecp_footer.htm', 1443065653, '6', './data/template/4_6_touch_home_spacecp_profile.tpl.php', './template/usportstyle', 'touch/home/spacecp_profile')
|| checktplrefresh('./template/usportstyle/touch/home/spacecp_profile.htm', './template/usportstyle/touch/home/spacecp_password.htm', 1443065653, '6', './data/template/4_6_touch_home_spacecp_profile.tpl.php', './template/usportstyle', 'touch/home/spacecp_profile')
;?><?php include template('common/header'); if($validate) { ?>
<p class="tbmu mbm">管理员否决了您的注册申请，请完善注册原因，重新提交申请</p>
<form action="member.php?mod=regverify" method="post" autocomplete="off">
<input type="hidden" value="<?php echo FORMHASH;?>" name="formhash" />
<table summary="个人资料" cellspacing="0" cellpadding="0" class="tfm">
<tr>
<th>否决原因</th>
<td><?php echo $validate['remark'];?></td>
<td>&nbsp;</td>
</tr>
<tr>
<th>注册原因</th>
<td><input type="text" class="px" name="regmessagenew" value="" /></td>
<td>&nbsp;</td>
</tr>
<tr>
<th>&nbsp;</th>
<td colspan="2">
<button type="submit" name="verifysubmit" value="true" class="pn pnc" /><strong>重新提交申请</strong></button>
</td>
</tr>
</table>
</div></div>
<div class="appl"><div class="tbn">
<h2 class="mt bbda">设置</h2>
<ul>
<li<?php echo $actives['avatar'];?>><a href="home.php?mod=spacecp&amp;ac=avatar">修改头像</a></li>			
<?php if(!empty($_G['setting']['pluginhooks']['global_personal_pic'])) echo $_G['setting']['pluginhooks']['global_personal_pic'];?>
<li<?php echo $actives['profile'];?>><a href="home.php?mod=spacecp&amp;ac=profile">个人资料</a></li>
<?php if($_G['setting']['verify']['enabled'] && allowverify() || $_G['setting']['my_app_status'] && $_G['setting']['videophoto']) { ?>
<li<?php echo $actives['verify'];?>><a href="<?php if($_G['setting']['verify']['enabled']) { ?>home.php?mod=spacecp&ac=profile&op=verify<?php } else { ?>home.php?mod=spacecp&ac=videophoto<?php } ?>">认证</a></li>
<?php } ?>
<li<?php echo $actives['credit'];?>><a href="home.php?mod=spacecp&amp;ac=credit">积分</a></li>
<li<?php echo $actives['usergroup'];?>><a href="home.php?mod=spacecp&amp;ac=usergroup">用户组</a></li>
<li<?php echo $actives['privacy'];?>><a href="home.php?mod=spacecp&amp;ac=privacy">隐私筛选</a></li>

<?php if($_G['setting']['sendmailday']) { ?><li<?php echo $actives['sendmail'];?>><a href="home.php?mod=spacecp&amp;ac=sendmail">邮件提醒</a></li><?php } ?>
<li<?php echo $actives['password'];?>><a href="home.php?mod=spacecp&amp;ac=profile&amp;op=password">密码安全</a></li>

<?php if($_G['setting']['creditspolicy']['promotion_visit'] || $_G['setting']['creditspolicy']['promotion_register']) { ?>
<li<?php echo $actives['promotion'];?>><a href="home.php?mod=spacecp&amp;ac=promotion">访问推广</a></li>
<?php } if(!empty($_G['setting']['plugins']['spacecp'])) { if(is_array($_G['setting']['plugins']['spacecp'])) foreach($_G['setting']['plugins']['spacecp'] as $id => $module) { if(!$module['adminid'] || ($module['adminid'] && $_G['adminid'] > 0 && $module['adminid'] >= $_G['adminid'])) { ?><li<?php if($_GET['id'] == $id) { ?> class="a"<?php } ?>><a href="home.php?mod=spacecp&amp;ac=plugin&amp;id=<?php echo $id;?>"><?php echo $module['name'];?></a></li><?php } } } ?>
</ul>
</div></div>
<?php } else { if($operation == 'password') { ?>
<header class="header">
    <div class="nav">
        <div class="category">
        	修改密码
        	<div id="elecnation_nav_left">
                <a href="javascript:;" onclick="history.go(-1)" class="head_back"></a>
        	</div>
        </div>
</div>
</header>

<div class="modefBox">
    <form action="home.php?mod=spacecp&amp;ac=profile&amp;op=password&amp;mobile=2" method="post" autocomplete="off">
        <input type="hidden" value="<?php echo FORMHASH;?>" name="formhash" />
        <ul>
            <li><input type="password" value="" tabindex="1" class="px p_fre" size="30" autocomplete="off" name="oldpassword" id="oldpassword" placeholder="原密码"></li>
            <li><input type="password" value="" tabindex="2" class="px p_fre" size="30" autocomplete="off" name="newpassword" id="newpassword" placeholder="新密码"></li>
            <li><input type="password" value="" tabindex="3" class="px p_fre" size="30" autocomplete="off" name="newpassword2" id="newpassword2" placeholder="确认新密码"></li>
            <?php if($_GET['from'] == 'contact') { ?>
            <li><input type="text" tabindex="4" class="px p_fre" size="30" autocomplete="off" name="emailnew" value="<?php echo $space['email'];?>" id="emailnew" placeholder="Email"></li>
            <?php } else { ?>
            <input type="hidden" name="emailnew" id="emailnew" value="<?php echo $space['email'];?>" />
            <?php } ?>
        </ul>
        <div class="btn_saved">
        
        <input type="hidden" name="passwordsubmit" value="true" />
        <button tabindex="5" value="true" name="pwdsubmit" value="true" type="submit" class="pn1"><span>保&nbsp;&nbsp;存</span></button></div>
    </form>
</div>


<?php } else { ?>
<?php if(!empty($_G['setting']['pluginhooks']['spacecp_profile_top'])) echo $_G['setting']['pluginhooks']['spacecp_profile_top'];?>
    <?php if($vid) { ?>
<p class="tbms mtm <?php if(!$showbtn) { ?>tbms_r<?php } ?>"><?php if($showbtn) { ?>以下信息通过审核后将不能再次修改，提交后请耐心等待核查 <?php } else { ?>恭喜您，您的认证审核已经通过，下面的资料项已经不允许被修改 <?php } ?></p>
<?php } ?>
<iframe id="frame_profile" name="frame_profile" style="display: none"></iframe>
<header class="header">
    <div class="nav">
        <div class="category">
        	修改资料
        	<div id="elecnation_nav_left">
                <a href="javascript:;" onclick="history.go(-1)" class="head_back"></a>
        	</div>
        </div>
</div>
</header>
            <div class="modefBox">
            <form action="home.php?mod=spacecp&amp;ac=profile&amp;mobile=2" method="post" autocomplete="off">
<input type="hidden" value="<?php echo FORMHASH;?>" name="formhash" />

                    <div class="boxCont">
<div class="boxC_l">用户名:</div>
<div class="boxC_r"><?php echo $_G['member']['username'];?></div>
</div>
                    <?php if(is_array($settings)) foreach($settings as $key => $value) { ?>                    <?php if($value['available']) { ?>
                    <div class="boxCont">
<div class="boxC_l"><?php if($value['required']) { ?><span class="rq" title="必填">*</span><?php } ?><?php echo $value['title'];?>:</div>
<div class="boxC_r"><?php echo $htmls[$key];?></div>
                    </div>
                    <?php } ?>
                    <?php } ?>
                    <div class="boxCont">
<div class="boxC_l">E-mail:</div>
<div class="boxC_r"><?php echo $space['email'];?>&nbsp;(<a href="home.php?mod=spacecp&amp;ac=profile&amp;op=password&amp;from=contact&amp;mobile=2#contact">修改</a>)</div>
</div>
<div class="btn_saved">
                    <input type="hidden" name="profilesubmit" value="true" />
                    <button tabindex="4" value="true" name="profilesubmitbtn" value="profilesubmitbtn" type="submit" class="pn1"><span>保&nbsp;&nbsp;存</span></button>
                    </div>
            </form>
</div>
<script type="text/javascript">
/*function show_error(fieldid, extrainfo) {
var elem = $('th_'+fieldid);
if(elem) {
elem.className = "rq";
fieldname = elem.innerHTML;
extrainfo = (typeof extrainfo == "string") ? extrainfo : "";
$('showerror_'+fieldid).innerHTML = "请检查该资料项 " + extrainfo;
$(fieldid).focus();
}
}
function show_success(message) {
message = message == '' ? '资料更新成功' : message;
showDialog(message, 'right', '提示信息', function(){
top.window.location.href = top.window.location.href;
}, 0, null, '', '', '', '', 3);
}
function clearErrorInfo() {
var spanObj = $('profilelist').getElementsByTagName("div");
for(var i in spanObj) {
if(typeof spanObj[i].id != "undefined" && spanObj[i].id.indexOf("_")) {
var ids = explode('_', spanObj[i].id);
if(ids[0] == "showerror") {
spanObj[i].innerHTML = '';
$('th_'+ids[1]).className = '';
}
}
}
}*/
</script>
<?php } ?>
</div>
</div>

<?php } ?>
</div>


