<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('space_profile');?>
<?php if($_GET['mycenter'] && !$_G['uid']) { dheader('Location:member.php?mod=logging&action=login');exit;?><?php } include template('common/header'); if(!$_GET['mycenter']) { ?>

<!-- header start -->
<header class="header">
    <div class="nav">
        <div class="category">
            <?php if($_G['uid'] == $space['uid']) { ?>
            我的资料
            <?php } else { ?>
            <?php echo $space['username'];?> 的资料
            <?php } ?>
       
        <div id="elecnation_nav_left">
            <?php if($_G['uid'] == $space['uid']) { ?>
            <a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile&amp;mycenter=1"><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_center.png" /></a>
            <?php } else { ?>
            <a href="javascript:;" onclick="history.go(-1)"><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_back.png" /></a>
            <?php } ?>
        </div>
        </div>
    </div>
</header>
<!-- header end -->
<!-- userinfo start -->
<div class="userinfo wp">
<div class="user_avatar">
<div class="avatar_m"><span><img src="<?php echo avatar($space[uid], middle, true);?>" /></span></div>
<h2 class="name"><?php echo $space['username'];?></h2>
</div>
<div class="user_box">
<ul>
<li><span><?php echo $space['credits'];?></span>积分</li><?php if(is_array($_G['setting']['extcredits'])) foreach($_G['setting']['extcredits'] as $key => $value) { if($value['title']) { ?>
<li><span><?php echo $space["extcredits$key"];?> <?php echo $value['unit'];?></span><?php echo $value['title'];?></li>
<?php } } ?>
</ul>
</div>
<?php if($space['uid'] == $_G['uid']) { ?>
<div class="btn_exit"><a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>" style="color:#FFFFFF;">退出登录</a></div>
    <?php } else { ?>
    <div class="btn_exit"><a href="home.php?mod=space&amp;do=pm&amp;subop=view&amp;touid=<?php echo $space['uid'];?>" style="color:#FFFFFF;">发消息</a></div>
<?php } ?>
</div>
<!-- userinfo end -->
<?php } else { ?>
<div class="pic_edit_11111" id="sub_div" style="display:none;">
    <div id="clipArea">
        <div class="photo-clip-view">
             <div class="photo-clip-moveLayer">
                  <div class="photo-clip-rotateLayer"></div>
             </div>
        </div>
        <div class="photo-clip-mask"></div>
        <div class="photo-clip-mask-right"></div>
        <div class="photo-clip-mask-top"></div>
        <div class="photo-clip-mask-bottom"></div>
        <div class="photo-clip-area"></div>
    </div>
    
    <!--div id="plan1"><center><img src="<?php echo avatar($_G[uid], big, true);?>"></center></div-->
    
    <center><button id="upload2">选择图片</button>
    <button id="clipBtn">上传图片</button></center>
    <input type="file" id="file" style="opacity: 0;position: fixed;bottom: -100px" accept="image/*">

</div>
<img data-src="" filename="" id="hit" style="display: none; z-index: 9; background-color: rgb(102, 102, 102); background-size: contain; background-position: 50% 50%; background-repeat: no-repeat;">

<script src="<?php echo STATICURL;?>js/mobile/hammer.js?<?php echo VERHASH;?>" type="text/javascript"></script>
<script>
var hammer = '';
var currentIndex = 0;
var body_width = $('body').width();
var body_height = $('body').height();

function upload_pic()
{
    $('#file').click();
    
}

$("#clipArea").photoClip({
    width: body_width * 0.8125,
    height: body_width * 0.8125,
    file: "#file",
    view: "#hit",
    ok: "#clipBtn",
    loadComplete: function (){
        $("#sub_div").show();
        $("#main_div").hide();
    },
    clipFinish: function (dataURL){
        $('#hit').attr('data-src', dataURL);
        saveImageInfo();
    }
});

//图片上传
function saveImageInfo() {
    var filename = $('#hit').attr('fileName');
    var img_data = $('#hit').attr('data-src');
    if(img_data==""){alert('null');}
    render(img_data); 
    
    $.post('plugin.php?id=fansclub:api&ac=upload_avatar&uid=<?php echo $_G["uid"];?>', {image: img_data}, function (data) {
        if (data != '') {
        // console.info(data);
        // alert(data);
            location.href='home.php?mod=space&uid=<?php echo $_G["uid"];?>&do=profile&mycenter=1&mobile=2';
        }
    });
}

/*获取文件拓展名*/
function getFileExt(str) {
    var d = /\.[^\.]+$/.exec(str);
    return d;
}

//图片上传结束
$(function () {
    $('#upload2').on('touchstart', function () {
        //图片上传按钮
        //$('#plan1').hide();
        //$("#clipArea").show();
        //$("#clipBtn").show();
        $('#file').click();
    });
})


function Close(){
// $('#plan').hide();
}

// 渲染 Image 缩放尺寸  
function render(src){  
     var MAX_HEIGHT = 256;  //Image 缩放尺寸 
    // 创建一个 Image 对象  
    var image = new Image();  
    
    // 绑定 load 事件处理器，加载完成后执行  
    image.onload = function(){  
        // 获取 canvas DOM 对象  
        var canvas = document.getElementById("myCanvas"); 
        // 如果高度超标  
        if(image.height > MAX_HEIGHT) {  
            // 宽度等比例缩放 *=  
            image.width *= MAX_HEIGHT / image.height;  
            image.height = MAX_HEIGHT;  
        }  
        // 获取 canvas的 2d 环境对象,  
        // 可以理解Context是管理员，canvas是房子  
        //var ctx = canvas.getContext("2d");  
        // canvas清屏  
        //ctx.clearRect(0, 0, canvas.width, canvas.height); 
        //canvas.width = image.width;        // 重置canvas宽高  
        //canvas.height = image.height;  
        // 将图像绘制到canvas上  
        //ctx.drawImage(image, 0, 0, image.width, image.height);  
        // !!! 注意，image 没有加入到 dom之中  
        
     //var dataurl = canvas.toDataURL("image/jpeg");  
     //var imagedata =  encodeURIComponent(dataurl); 
        // $('#plan').attr('data-src',dataurl); 
        // $('#plan').show();
    };  
    // 设置src属性，浏览器会自动加载。  
    // 记住必须先绑定render()事件，才能设置src属性，否则会出同步问题。  
    image.src = src;    
};  
</script>

<div id="main_div">
<header class="cenhead">
        <a href="javascript:;" onclick="history.go(-1)" class="head_back"></a>
        <div class="cen_img">
             <p><a href="javascript:void(0);" onClick="upload_pic();"><img src="<?php echo avatar($_G[uid], big, true);?>" alt="<?php echo $discuz_userss;?>" /><?php if($space['gender']==1) { ?><i class="fontweb_icon ic_nan"></i><?php } elseif($space['gender']==2) { ?><i class="fontweb_icon ic_nv"></i><?php } elseif($space['gender']==0) { ?><i class="fontweb_icon ic_secret"></i><?php } ?></a></p>   
        </div>
        <div class="cen_names"><?php echo $_G['username'];?></div>
        <div class="cen_introd"><?php if($space['bio']) { ?>简介：<?php echo $space['bio'];?><?php } else { ?>简介：这家伙很懒，什么都没有留下。<?php } ?></div>
</header>
<section class="user_wraps wp">
        <ul class="user_seul">
            <li><a href="home.php?mod=spacecp&amp;ac=profile&amp;op=password&amp;mobile=2">修改密码</a></li>
            <li><a href="home.php?mod=spacecp&amp;mobile=2">修改资料</a></li>
            <li><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;&amp;do=thread&amp;from=space&amp;mobile=2">我的帖子</a></li>
            <li><a href="home.php?mod=space&amp;do=group&amp;mycenter=1&amp;mobile=2">加入的球迷会</a></li>
            <li><a href="home.php?mod=space&amp;do=notice&amp;view=mypost&amp;type=post&amp;mobile=2">回复我的 <?php if($_G['member']['newprompt_num']['post']) { ?><i></i><?php } ?></a></li>
            <li><a href="home.php?mod=space&amp;do=notice&amp;view=mypost&amp;type=activity&amp;mobile=2">活动提醒<?php if($_G['member']['newprompt_num']['activity']) { ?><i></i><?php } ?></a></li>
        </ul>
</section>
<section class="user_logout">
         <a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>" class="dialog" title="退出">退出</a>
</section>
</div>

<?php } include template('common/footer'); ?>