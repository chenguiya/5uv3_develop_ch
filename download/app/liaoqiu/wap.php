<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta charset="utf-8" >
<title>聊球WAP下载页</title>
<meta  content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;"  name="viewport">
<link href="http://www.5usport.com/ad/liaoqiu/wap/download/wap_wx.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="mob_main">
     <div class="mob_content1 cl">
          <div class="wap_l">
               <p><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap1_limg.png"></p>
               <div class="box4_img box1_button">
               <a id="downloadBtn" class="btn btn-green" href="<?=$array_download_url['download_url']?>"><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap_button.png" width="180"/></a>
               </div>
          </div>
          <div class="wap_r"><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap1_rimg.png"/></div>
		  
	 </div>
	 <div class="mob_content2">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
		         <tr>
				     <td><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap2_img.png"></td>
				 </tr>
		  </table>

	 </div>
	 <div class="mob_content3">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                 <tr>
                     <td><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap3_limg.png"></td>
                     <td><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap3_rimg.png"></td>
                 </tr>
          </table>
	 </div>
	 <div class="mob_content4 cl">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
		         <tr>
				     <td><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap4_limg.png"></td>
					 <td><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap4_rimg.png"></td>
				 </tr>
		  </table>

	 </div>
	 <div class="mob_content5 cl">
	       <table border="0" cellpadding="0" cellspacing="0" width="100%">
		         <tr>
				     <td><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap5_limg.png"></td>
					 <td><img src="http://www.5usport.com/ad/liaoqiu/wap/download/wap5_rimg.png"></td>
				 </tr>
		  </table>
	 </div>
</div>
<script>
    var isWeiXin = navigator.userAgent.toLowerCase().indexOf("micromessenger") != -1;
    var bd = document.body;
    var downloadBtn = document.getElementById("downloadBtn");
    //在微信内打开详情页进行下载时，引导到浏览器打开
    var guildDownload = function(e) {
        if (isWeiXin) {
            if(!document.getElementById("wxTip")) {
                window.scrollTo(0, 0);
                var dom = document.createElement("div");
                dom.className = "modal-backdrop";
                dom.id = "wxTip";
                dom.innerHTML = '<div class="download-arrow"></div><div class="download-txt"></div>';
                bd.appendChild(dom);
                dom.addEventListener("touchstart", clearWxTip, false);
                downloadBtn.classList.add("download-btn");
            }
            return false;
        } else {
            downloadBtn.classList.add("btn-gray");
            downloadBtn.classList.remove("btn-green");
            downloadBtn.innerHTML = "正在下载…";
        }
    };
    function clearWxTip() {
        var wxTip = document.getElementById("wxTip");
        wxTip.removeEventListener("touchstart", clearWxTip, false);
        bd.removeChild(wxTip);
        downloadBtn.classList.remove("download-btn");
        downloadBtn.onclick = guildDownload;
    }
    downloadBtn.onclick = guildDownload;

    // 统计
    var urlParam = location.search.substr(1); //获取url中search字段
    if (urlParam.length != 0) {
        urlParam = '&' + urlParam;
    }
    var urlParamEncode = encodeURIComponent(urlParam);
    downloadBtn.href = downloadBtn.href + urlParam;
</script>
<script>
 var _hmt = _hmt || [];
 (function() {
   var hm = document.createElement("script");
   hm.src = "//hm.baidu.com/hm.js?08e23d5339d9237a04872314bc969c39";
   var s = document.getElementsByTagName("script")[0]; 
   s.parentNode.insertBefore(hm, s);
 })();
 </script> 
</body>
</html>
