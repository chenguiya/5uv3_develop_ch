<?php
$action = $_REQUEST['action'];
$data = $_REQUEST['data'];
$arr_return = array('success' => FALSE, 'message' => '初始化');

if($action == 'videoscreenshot')
{
	$url = $data; // 用户提交的视频URL
	
	// 取真正的视频地址
	$flvcd_url = 'http://www.flvcd.com/parse.php?format=&kw='.$url.'&sbt=%BF%AA%CA%BCGO%21&go=1';
	$body = curl_access($flvcd_url);
	$content = arr_to_utf8($body);

	$regex = "|<a href=\"(.*)\" target=\"_blank\" class=\"link\"|U"; // 只有一段的
	preg_match_all($regex, $content, $_tmp_arr, PREG_PATTERN_ORDER);
	if(count(@$_tmp_arr[1]) == 0) 
	{
		$regex = "|<a href=\"(.*)\" target=\"_blank\"(.*)_alert|U"; // 有多段的
		preg_match_all($regex, $content, $_tmp_arr, PREG_PATTERN_ORDER);
	}
	

	//$jump = http_header('http://www.tudou.com/l/mfQXfumwiew/&iid=229612555&resourceId=0_04_05_99/v.swf');
	//echo "<pre>";
	//print_r($jump);
	//http://js.tudouui.com/bin/player2/olc_8.swf?iid=229612555&swfPath=http://js.tudouui.com/bin/lingtong/SocialPlayer_158.swf&lshare=1&listOwner=444701543&tvcCode=-1&tag=%E7%8C%B4%E5%AD%90%2C%E4%B8%AD%E6%8C%87%2C%E5%B0%91%E5%B9%B4&title=%E5%8D%B0%E5%BA%A6%E4%B8%80%E5%B0%91%E5%B9%B4%E5%90%91%E7%8C%B4%E5%AD%90%E7%AB%96%E4%B8%AD%E6%8C%87+%E9%81%AD%E7%8C%B4%E5%AD%90%E9%A3%9E%E8%84%9A%E8%B8%B9%E8%84%B8&mediaType=vi&totalTime=65000&hdType=3&hasPassword=0&nWidth=-1&isOriginal=0&channelId=29&nHeight=-1&banPublic=false&videoOwner=324480270&videoOwner=324480270&ocode=kcIc1c9Kjlw&tict=3&hasWaterMark=1&totalTime=65000&channelId=29&cs=&k=%E7%8C%B4%E5%AD%90|%E4%B8%AD%E6%8C%87|%E5%B0%91%E5%B9%B4&code=gZVJhEkO5W0&panelRecm=http://css.tudouui.com/bin/lingtong/PanelRecm_9.swz&panelDanmu=http://css.tudouui.com/bin/lingtong/PanelDanmu_18.swz&panelEnd=http://css.tudouui.com/bin/lingtong/PanelEnd_13.swz&pepper=http://css.tudouui.com/bin/binder/pepper_17.png&panelShare=http://css.tudouui.com/bin/lingtong/PanelShare_7.swz&panelCloud=http://css.tudouui.com/bin/lingtong/PanelCloud_12.swz&autoPlay=false&listType=1&rurl=&resourceId=0_04_05_99&autostart=false&lid=21305435&lCode=mfQXfumwiew&snap_pic=http%3A%2F%2Fg4.tdimg.com%2Fce7566603c8a36bb99673d2e250b9976%2Fw_2.jpg&aopRate=0.01&p2pRate=0.95&adSourceId=99999&yjuid=null&yseid=null&yseidtimeout=null&yseidcount=null&uid=null&juid=null&vip=0
	
	
	// \/f4v\/11\/229325411.h264_1.04000201005539FE767A024FBDBF05851187F9-1E71-0937-6D7A-000305160635.f4v

	//$jump = curl_access('http://js.tudouui.com/bin/player2/olc_8.swf?iid=229612555&swfPath=http://js.tudouui.com/bin/lingtong/SocialPlayer_158.swf&lshare=1&listOwner=444701543&tvcCode=-1&tag=%E7%8C%B4%E5%AD%90%2C%E4%B8%AD%E6%8C%87%2C%E5%B0%91%E5%B9%B4&title=%E5%8D%B0%E5%BA%A6%E4%B8%80%E5%B0%91%E5%B9%B4%E5%90%91%E7%8C%B4%E5%AD%90%E7%AB%96%E4%B8%AD%E6%8C%87+%E9%81%AD%E7%8C%B4%E5%AD%90%E9%A3%9E%E8%84%9A%E8%B8%B9%E8%84%B8&mediaType=vi&totalTime=65000&hdType=3&hasPassword=0&nWidth=-1&isOriginal=0&channelId=29&nHeight=-1&banPublic=false&videoOwner=324480270&videoOwner=324480270&ocode=kcIc1c9Kjlw&tict=3&hasWaterMark=1&totalTime=65000&channelId=29&cs=&k=%E7%8C%B4%E5%AD%90|%E4%B8%AD%E6%8C%87|%E5%B0%91%E5%B9%B4&code=gZVJhEkO5W0&panelRecm=http://css.tudouui.com/bin/lingtong/PanelRecm_9.swz&panelDanmu=http://css.tudouui.com/bin/lingtong/PanelDanmu_18.swz&panelEnd=http://css.tudouui.com/bin/lingtong/PanelEnd_13.swz&pepper=http://css.tudouui.com/bin/binder/pepper_17.png&panelShare=http://css.tudouui.com/bin/lingtong/PanelShare_7.swz&panelCloud=http://css.tudouui.com/bin/lingtong/PanelCloud_12.swz&autoPlay=false&listType=1&rurl=&resourceId=0_04_05_99&autostart=false&lid=21305435&lCode=mfQXfumwiew&snap_pic=http%3A%2F%2Fg4.tdimg.com%2Fce7566603c8a36bb99673d2e250b9976%2Fw_2.jpg&aopRate=0.01&p2pRate=0.95&adSourceId=99999&yjuid=null&yseid=null&yseidtimeout=null&yseidcount=null&uid=null&juid=null&vip=0');
	//echo "<pre>";
	//print_r($jump);
	
	//exit;
	//$ffmpegInstance = new ffmpeg_movie('http://js.tudouui.com/bin/player2/olc_8.swf?iid=229612555&swfPath=http://js.tudouui.com/bin/lingtong/SocialPlayer_158.swf&lshare=1&listOwner=444701543&tvcCode=-1&tag=%E7%8C%B4%E5%AD%90%2C%E4%B8%AD%E6%8C%87%2C%E5%B0%91%E5%B9%B4&title=%E5%8D%B0%E5%BA%A6%E4%B8%80%E5%B0%91%E5%B9%B4%E5%90%91%E7%8C%B4%E5%AD%90%E7%AB%96%E4%B8%AD%E6%8C%87+%E9%81%AD%E7%8C%B4%E5%AD%90%E9%A3%9E%E8%84%9A%E8%B8%B9%E8%84%B8&mediaType=vi&totalTime=65000&hdType=3&hasPassword=0&nWidth=-1&isOriginal=0&channelId=29&nHeight=-1&banPublic=false&videoOwner=324480270&videoOwner=324480270&ocode=kcIc1c9Kjlw&tict=3&hasWaterMark=1&totalTime=65000&channelId=29&cs=&k=%E7%8C%B4%E5%AD%90|%E4%B8%AD%E6%8C%87|%E5%B0%91%E5%B9%B4&code=gZVJhEkO5W0&panelRecm=http://css.tudouui.com/bin/lingtong/PanelRecm_9.swz&panelDanmu=http://css.tudouui.com/bin/lingtong/PanelDanmu_18.swz&panelEnd=http://css.tudouui.com/bin/lingtong/PanelEnd_13.swz&pepper=http://css.tudouui.com/bin/binder/pepper_17.png&panelShare=http://css.tudouui.com/bin/lingtong/PanelShare_7.swz&panelCloud=http://css.tudouui.com/bin/lingtong/PanelCloud_12.swz&autoPlay=false&listType=1&rurl=&resourceId=0_04_05_99&autostart=false&lid=21305435&lCode=mfQXfumwiew&snap_pic=http%3A%2F%2Fg4.tdimg.com%2Fce7566603c8a36bb99673d2e250b9976%2Fw_2.jpg&aopRate=0.01&p2pRate=0.95&adSourceId=99999&yjuid=null&yseid=null&yseidtimeout=null&yseidcount=null&uid=null&juid=null&vip=0');
	//$duration = $ffmpegInstance->getDuration(); // the duration of a movie or audio file in seconds
	//$frameCount = $ffmpegInstance->getFrameCount(); // number of frames in a movie or audio file
	//$frameRate = $ffmpegInstance->getFrameRate(); // the frame rate of a movie in fps
	//echo $duration."|".$frameCount."|".$frameRate;
					
	//exit;
	if(count(@$_tmp_arr[1]) > 0)
	{
		$new_w = 320; //设定的宽
		$new_h = 320; //设定的高
		$cut = 0;
		$file_name = md5($url)."_".$new_w."x".$new_h."_".$cut.".jpg";
		$img = './images/'.$file_name; //要生成图片的路径
		
		if(file_exists($img)) // 如果已经存在，不用做图片处理
		{
			$arr_return['success'] = TRUE;
			$arr_return['message'] = '截图已经存在';
			$arr_return['img_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/images/'.$file_name;
			$image_info = getimagesize($img);
			$base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($img)));
			$arr_return['img_code'] = $base64_image_content;
		}
		else
		{
			for($i = 0; $i < count($_tmp_arr[1]); $i++)
			{
				if($i > 0) break; // 取一段视频吧
				
				$video_url = $_tmp_arr[1][$i];
				$jump = http_header($video_url);
				$arr_jump = explode("\n", $jump);
				$real_video = '';
				for($j = 0; j < count($arr_jump); $j++)
				{
					if(stripos($arr_jump[$j], 'Location:') !== FALSE)
					{
						$_tmp = explode('Location:', $arr_jump[$j]);
						$real_video = trim($_tmp[1]);
						break;
					}
				}
				
				if($real_video != '')
				{
					$ffmpegInstance = new ffmpeg_movie($real_video);
					$duration = $ffmpegInstance->getDuration(); // the duration of a movie or audio file in seconds
					$frameCount = $ffmpegInstance->getFrameCount(); // number of frames in a movie or audio file
					$frameRate = $ffmpegInstance->getFrameRate(); // the frame rate of a movie in fps
					
					$ff_frame = $ffmpegInstance->getFrame(20); //截取视频第20帧的图像
					$gd_image = $ff_frame->toGDImage();
					$tmp_img = './images/'.md5($data).".jpg"; //要生成图片的路径
					
					imagejpeg($gd_image, $tmp_img); //创建jpg图像   
					
					$thumbnail = new ImageResize();  
					$thumbnail->resizeimage($tmp_img, $new_w, $new_h, $cut, $img);  
					@unlink($tmp_img);
					imagedestroy($gd_image); //销毁一图像
					
					if(file_exists($img))
					{
						$arr_return['success'] = TRUE;
						$arr_return['message'] = '生成截图成功';
						$arr_return['img_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/images/'.$file_name;
						$image_info = getimagesize($img);
						$base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($img)));
						$arr_return['img_code'] = $base64_image_content;
					}
					else
					{
						$arr_return['message'] = '生成截图失败';
					}
				}
				else
				{
					$arr_return['message'] = '没有返回视频';
				}
			}
		}
	}
}
elseif($action == 'showimg')
{
	$url = urldecode($data);
	$show = curl_access($url);
	echo $show;
	exit;
}
else
{
	$arr_return['message'] = '没有这个action';
}

echo json_encode($arr_return);
exit;

// 图片修改大小类
class ImageResize {  
     
    //图片类型  
    var $type;  
     
    //实际宽度  
    var $width;  
     
    //实际高度  
    var $height;  
     
    //改变后的宽度  
    var $resize_width;  
     
    //改变后的高度  
    var $resize_height;  
     
    //是否裁图  
    var $cut;  
     
    //源图象  
    var $srcimg;  
     
    //目标图象地址  
    var $dstimg;  
     
    //临时创建的图象  
    var $im;  
  
	function resizeimage($img, $wid, $hei,$c,$dstpath) {  
        $this->srcimg = $img;  
        $this->resize_width = $wid;  
        $this->resize_height = $hei;  
        $this->cut = $c;  
         
        //图片的类型  
        $this->type = strtolower(substr(strrchr($this->srcimg,"."),1));  
         
        //初始化图象  
        $this->initi_img();  
         
        //目标图象地址  
        $this -> dst_img($dstpath);  
         
        //--  
        $this->width = imagesx($this->im);  
        $this->height = imagesy($this->im);  
         
        //生成图象  
        $this->newimg();  
         
        ImageDestroy ($this->im);  
    }  
  
	function newimg() {  
  
		//改变后的图象的比例  
        $resize_ratio = ($this->resize_width)/($this->resize_height);  
  
		//实际图象的比例  
        $ratio = ($this->width)/($this->height);  
  
	if(($this->cut)=="1") {  
            //裁图 高度优先  
            if($ratio>=$resize_ratio){  
                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);  
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width,$this->resize_height, (($this->height)*$resize_ratio), $this->height);  
                ImageJpeg ($newimg,$this->dstimg);  
            }  
             
            //裁图 宽度优先  
            if($ratio<$resize_ratio) {  
                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);  
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, $this->resize_height, $this->width, (($this->width)/$resize_ratio));  
                ImageJpeg ($newimg,$this->dstimg);  
            }  
        } else {  
            //不裁图  
            if($ratio>=$resize_ratio) {  
                $newimg = imagecreatetruecolor($this->resize_width,($this->resize_width)/$ratio);  
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, ($this->resize_width)/$ratio, $this->width, $this->height);  
                ImageJpeg ($newimg,$this->dstimg);  
            }  
            if($ratio<$resize_ratio) {  
                $newimg = imagecreatetruecolor(($this->resize_height)*$ratio,$this->resize_height);  
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, ($this->resize_height)*$ratio, $this->resize_height, $this->width, $this->height);  
                ImageJpeg ($newimg,$this->dstimg);  
            }  
        }  
    }  
  
	//初始化图象  
    function initi_img() {  
        if($this->type=="jpg") {  
            $this->im = imagecreatefromjpeg($this->srcimg);  
        }  
         
        if($this->type=="gif") {  
            $this->im = imagecreatefromgif($this->srcimg);  
        }  
         
        if($this->type=="png") {  
            $this->im = imagecreatefrompng($this->srcimg);  
        }  
         
        if($this->type=="bmp") {  
            $this->im = $this->imagecreatefrombmp($this->srcimg);  
        }  
    }  
  
	//图象目标地址  
    function dst_img($dstpath) {  
        $full_length  = strlen($this->srcimg);  
        $type_length  = strlen($this->type);  
        $name_length  = $full_length-$type_length;  
        $name = substr($this->srcimg,0,$name_length-1);  
        $this->dstimg = $dstpath;  
        //echo $this->dstimg;  
    }  
     
    function ConvertBMP2GD($src, $dest = false) {  
        if(!($src_f = fopen($src, "rb"))) {  
            return false;  
        }  
        if(!($dest_f = fopen($dest, "wb"))) {  
            return false;  
        }  
        $header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f,14));  
        $info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant", fread($src_f, 40));  
         
        extract($info);  
        extract($header);  
         
        if($type != 0x4D42) { // signature "BM"  
            return false;  
        }  
         
        $palette_size = $offset - 54;  
        $ncolor = $palette_size / 4;  
        $gd_header = "";  
        // true-color vs. palette  
        $gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";  
        $gd_header .= pack("n2", $width, $height);  
        $gd_header .= ($palette_size == 0) ? "\x01" : "\x00";  
        if($palette_size) {  
            $gd_header .= pack("n", $ncolor);  
        }  
        // no transparency  
        $gd_header .= "\xFF\xFF\xFF\xFF";  
  
		fwrite($dest_f, $gd_header);  
  
		if($palette_size) {  
            $palette = fread($src_f, $palette_size);  
            $gd_palette = "";  
            $j = 0;  
            while($j < $palette_size) {  
                $b = $palette{$j++};  
                $g = $palette{$j++};  
                $r = $palette{$j++};  
                $a = $palette{$j++};  
                $gd_palette .= "$r$g$b$a";  
            }  
            $gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);  
            fwrite($dest_f, $gd_palette);  
        }  
  
		$scan_line_size = (($bits * $width) + 7) >> 3;  
        $scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03) : 0;  
  
		for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {  
            // BMP stores scan lines starting from bottom  
            fseek($src_f, $offset + (($scan_line_size + $scan_line_align) * $l));  
            $scan_line = fread($src_f, $scan_line_size);  
            if($bits == 24) {  
                $gd_scan_line = "";  
                $j = 0;  
                while($j < $scan_line_size) {  
                    $b = $scan_line{$j++};  
                    $g = $scan_line{$j++};  
                    $r = $scan_line{$j++};  
                    $gd_scan_line .= "\x00$r$g$b";  
                }  
            }  
            else if($bits == 8) {  
                $gd_scan_line = $scan_line;  
            }  
            else if($bits == 4) {  
                $gd_scan_line = "";  
                $j = 0;  
                while($j < $scan_line_size) {  
                    $byte = ord($scan_line{$j++});  
                    $p1 = chr($byte >> 4);  
                    $p2 = chr($byte & 0x0F);  
                    $gd_scan_line .= "$p1$p2";  
                }  
                $gd_scan_line = substr($gd_scan_line, 0, $width);  
            }  
            else if($bits == 1) {  
                $gd_scan_line = "";  
                $j = 0;  
                while($j < $scan_line_size) {  
                    $byte = ord($scan_line{$j++});  
                    $p1 = chr((int) (($byte & 0x80) != 0));  
                    $p2 = chr((int) (($byte & 0x40) != 0));  
                    $p3 = chr((int) (($byte & 0x20) != 0));  
                    $p4 = chr((int) (($byte & 0x10) != 0));  
                    $p5 = chr((int) (($byte & 0x08) != 0));  
                    $p6 = chr((int) (($byte & 0x04) != 0));  
                    $p7 = chr((int) (($byte & 0x02) != 0));  
                    $p8 = chr((int) (($byte & 0x01) != 0));  
                    $gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";  
                }  
                $gd_scan_line = substr($gd_scan_line, 0, $width);  
            }  
            fwrite($dest_f, $gd_scan_line);  
        }  
        fclose($src_f);  
        fclose($dest_f);  
        return true;  
    }  
  
	function imagecreatefrombmp($filename) {  
			$tmp_name = tempnam("/tmp", "GD");  
			if($this->ConvertBMP2GD($filename, $tmp_name)) {  
				$img = imagecreatefromgd($tmp_name);  
				unlink($tmp_name);  
				return $img;  
			}  
			return false;  
		}
}

function is_gbk($str)
{
    if(!preg_match("/^[".chr(0xa1)."-".chr(0xff)."a-za-z0-9_]+$/", $str))   // gb2312汉字字母数字下划线正则表达式 
    {
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

function is_utf8($str)
{
    if(!preg_match("/^[x{4e00}-x{9fa5}a-za-z0-9_]+$/u", $str))  // utf-8汉字字母数字下划线正则表达式 
    {
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

function arr_to_gbk($arr) 
{
    if (is_array($arr))
    {
        foreach($arr as $k => $v)
        {
            $_k = arr_to_gbk($k);
            $arr[$_k] = arr_to_gbk($v);
            
            if ($k != $_k)
                unset($arr[$k]);
        }
    }
    else
    {
        $arr = iconv('UTF-8', 'GBK', $arr);
    }
    return $arr;
}

function arr_to_utf8($arr) 
{
    if (is_array($arr))
    {
        foreach($arr as $k => $v)
        {
            $_k = arr_to_utf8($k);
            $arr[$_k] = arr_to_utf8($v);
            
            if ($k != $_k)
                unset($arr[$k]);
        }
    }
    else
    {
        $arr = iconv('GBK', 'UTF-8', $arr);
    }
    return $arr;
}

function http_header($str_url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $str_url);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);  //表示需要response header
	curl_setopt($ch, CURLOPT_NOBODY, FALSE); //表示需要response body
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);

	$result = curl_exec($ch);

	// if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200')
	return $result;
}

function curl_access($str_url, $str_query = '', $method = '', $str_referer = '', $cookie_file = '')
{
    $obj_ch = curl_init();
    curl_setopt($obj_ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($obj_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');

    if ($cookie_file != '')
    {
        if(file_exists($cookie_file))
        {
            curl_setopt($obj_ch, CURLOPT_COOKIEFILE, $cookie_file);
        }
        curl_setopt($obj_ch, CURLOPT_COOKIEJAR, $cookie_file);
    }

    if ($str_referer != '')
    {
        curl_setopt($obj_ch, CURLOPT_REFERER, $str_referer);
    }

    if ($method == 'post')
    {
        curl_setopt($obj_ch, CURLOPT_URL, $str_url);
        curl_setopt($obj_ch, CURLOPT_POST, 1);
        curl_setopt($obj_ch, CURLOPT_POSTFIELDS, $str_query);
    }
    else
    {
        curl_setopt($obj_ch, CURLOPT_URL, $str_url.($str_query?'?'.$str_query:''));
        curl_setopt($obj_ch, CURLOPT_HTTPGET, 1);
    }

    if (strpos($str_url, 'https') !== false)
    {
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($obj_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }

    @curl_setopt($obj_ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
    $str = curl_exec($obj_ch);
    curl_close($obj_ch);

    return trim($str);
}
