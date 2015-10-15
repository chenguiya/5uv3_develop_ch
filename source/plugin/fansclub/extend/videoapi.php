<?php
/**
 *  $video = new VideoApi();  
 *  $video->resizeimage($tmp_img, $new_w, $new_h, $cut, $img);  
 */

class VideoApi
{
    const USER_AGENT = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko)
        Chrome/8.0.552.224 Safari/534.10";
    const CHECK_URL_VALID = "/(tv\.cntv\.cn|video\.weibo\.com|pptv\.com|video\.qq\.com|youku\.com|tudou\.com|ku6\.com|56\.com|letv\.com|video\.sina\.com\.cn|(my\.)?tv\.sohu\.com|v\.qq\.com)/";

    private static $video_img_dir;
    private static $video_img_file;
    private static $video_img_url;
    
    static public function parse($url = '', $createObject = false, $tid = 0, $_G = array()) // 第三个参数是转传用的tid
    {
        // 查询tid是否已经有截图保存在服务器
        if($tid > 0)
        {
            self::$video_img_dir = $_G['setting']['attachdir'].'video/';
            
            self::$video_img_file = self::$video_img_dir.'tid_'.$tid.'.jpg';
            self::$video_img_url = $_G['setting']['attachurl'].'video/tid_'.$tid.'.jpg';
            if(file_exists(self::$video_img_file))
            {
                $data = array();
                $data['img'] = self::$video_img_url;
                return $data;
            }
        }
        
        $lowerurl = strtolower($url);
        preg_match(self::CHECK_URL_VALID, $lowerurl, $matches);
        if(!$matches) return false;
 
        switch($matches[1]){
        case 'youku.com':
            $data = self::_parseYouku($url);
            break;
        case 'tudou.com':
            $data = self::_parseTudou($url);
            break;
        // case 'ku6.com':
        //     $data = self::_parseKu6($url);
        //     break;
        // case '56.com':
        //     $data = self::_parse56($url);
        //     break;
        case 'letv.com':
             $data = self::_parseLetv($url);
             break;
        case 'video.sina.com.cn':
            $data = self::_parseSina($url);
            break;
        case 'video.weibo.com':
            $data = self::_parseWeibo($url);
            break;
        // case 'my.tv.sohu.com':
        // case 'tv.sohu.com':
        // case 'sohu.com':
        //     $data = self::_parseSohu($url);
        //     break;
        case 'video.qq.com':
        case 'v.qq.com':
             $data = self::_parseQq($url);
             break;
        case 'pptv.com':
            $data = self::_parsePptv($url);
            break;
        case 'tv.cntv.cn':
            $data = self::_parseCntv($url);
            break;
        default:
            $data = false;
        }
 
        if($data && $createObject)
            $data['object'] = "<embed src=\"{$data['swf']}\" quality=\"high\" width=\"480\" height=\"400\" align=\"middle\" allowNetworking=\"all\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\"></embed>";
        
        if($data['img'] != '')
        {
            self::save_to_local($data['img']);
        }
        
        return $data;
    }
    
    /**
     * cntv
     * http://tv.cntv.cn/video/VSET100152136340/e8d94da4920a419890d9a5fdc0da824b
     */
    private static function _parseCntv($url)
    {
        $html = self::_fget($url);
        $regex = "|flvImgUrl=\"(.*)\"|U";
        preg_match_all($regex, $html, $_tmp_arr, PREG_PATTERN_ORDER);
        
        if(empty($_tmp_arr[1]))
        {
            return false;
        }
        $data = array();
        $data['img'] = $_tmp_arr[1][0];

        return $data;
    }
    
    /**
     * miaopai
     * 1 http://www.miaopai.com/show/hKQXSYPOJgXET0oFTqdMWA__.swf
     * 2 http://wscdn.miaopai.com/splayer2.1.1.swf?scid=hKQXSYPOJgXET0oFTqdMWA__&&r=469845
     * 3 http://qncdn.miaopai.com/stream/hKQXSYPOJgXET0oFTqdMWA___m.jpg
     */
    
    
    /**
     * weibo
     * http://video.weibo.com/player/1034:394a0d3fca6fc8c3191c31c41f54f15b/v.swf
     * http://video.weibo.com/show?fid=1034:394a0d3fca6fc8c3191c31c41f54f15b
     */
    private static function _parseWeibo($url)
    {
        preg_match("#/player/(.*?)/v.swf#", $url, $matches);
        if(empty($matches))
        {
            return false;
        }
        $url = 'http://video.weibo.com/show?fid='.$matches[1];
        
        $html = self::_fget($url);
        $regex = "|<img src = \"(.*)\"|U";
        // echo $html;
        preg_match_all($regex, $html, $_tmp_arr, PREG_PATTERN_ORDER);
        
        if(empty($_tmp_arr[1]))
        {
            return false;
        }
        $data = array();
        $data['img'] = $_tmp_arr[1][0];

        return $data;
    }
    
    
    private static function save_to_local($url)
    {
        $data = '';
        $arr_return = array('success' => FALSE, 'message' => 'init');
        include_once(DISCUZ_ROOT.'./source/plugin/fansclub/extend/avatar.php');
        $avatar = new Avatar();
        $img = $avatar->myGetImageSize($url);
        
        if(count($img) > 0)
        {
            $data = $img['code'];
        }
        
        if($data == '')
        {
            $arr_return['message'] = '取不到图片';
            return $arr_return;
        }

        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $data, $result))
        {
            $bln_upload = $avatar->onuploadvideoimg($data, self::$video_img_file);
            if($bln_upload === TRUE)
            {
                $arr_return['success'] = TRUE;
                $arr_return['message'] = '成功';
            }
            else
            {
                $arr_return['message'] = '保存文件失败';
            }
        }
        else
        {
            $arr_return['message'] = '不是一个有效的图片编码';
        }
        return $arr_return;
    }
    
    /**
     * pptv 
     * http://player.pptv.com/v/iaagppg505CKFA2s.swf
       http://v.pptv.com/show/iaagppg505CKFA2s.html
     */
    private static function _parsePptv($url)
    {
        preg_match("#/v/(.*?)\.swf#", $url, $matches);
        if(empty($matches))
        {
            return false;
        }
        
        $url = 'http://v.pptv.com/show/'.$matches[1].'.html';
        $html = self::_fget($url);
        $regex = "|\"share_wx_image\":\"(.*)\"|U";
        preg_match_all($regex, $html, $_tmp_arr, PREG_PATTERN_ORDER);

        if(empty($_tmp_arr[1]))
        {
            return false;
        }
        $data = array();
        $data['img'] = str_replace('\/', '/', $_tmp_arr[1][0]);
        
        return $data;
    }
    
    /**
     * 新浪
     * http://video.sina.com.cn/view/249827089.html
     * http://video.sina.com.cn/share/video/249827089.swf
     */
     private static function _parseSina($url)
     {
        preg_match("#/share/video/(.*?)\.swf#", $url, $matches);
        if(empty($matches))
        {
            return false;
        }
        
        $url = 'http://video.sina.com.cn/view/'.$matches[1].'.html';
        
        $html = self::_fget($url);
        $regex = "|pic: '(.*)'|U";
        preg_match_all($regex, $html, $_tmp_arr, PREG_PATTERN_ORDER);

        if(empty($_tmp_arr[1]))
        {
            return false;
        }
        
        $data = array();
        $data['img'] = $_tmp_arr[1][0];

        return $data;
    }
        
    /**
     * 腾讯视频 
     * http://v.qq.com/cover/o/o9tab7nuu0q3esh.html?vid=97abu74o4w3_0
     * http://v.qq.com/play/97abu74o4w3.html
     * http://v.qq.com/cover/d/dtdqyd8g7xvoj0o.html
     * http://v.qq.com/cover/d/dtdqyd8g7xvoj0o/9SfqULsrtSb.html
     * http://imgcache.qq.com/tencentvideo_v1/player/TencentPlayer.swf?_v=20110829&vid=97abu74o4w3&autoplay=1&list=2&showcfg=1&tpid=23&title=%E7%AC%AC%E4%B8%80%E7%8E%B0%E5%9C%BA&adplay=1&cid=o9tab7nuu0q3esh
     * http://static.video.qq.com/TPout.swf?auto=1&vid=z0018oig45x zhangjh add
     * http://static.video.qq.com/TPout.swf?vid=i0017ww9n9k&auto=0
     */
    private static function _parseQq($url){
        if(preg_match("/\/play\//", $url)){
            $html = self::_fget($url);
            preg_match("/url=[^\"]+/", $html, $matches);
            if(!$matches); return false;
            $url = $matches[0];
        }
        
        //$url = 'http://static.video.qq.com/TPout.swf?auto=1&vid=z0018oig45x';
        preg_match("/vid=([^\_]+)\&/", $url, $matches);
        $vid = $matches[1];
        
        if($vid == '')
        {
            preg_match("/vid=([^\_]+)/", $url, $matches);
            $vid = $matches[1];
        }
        
        $html = self::_fget($url);
        // query
        preg_match("/flashvars\s=\s\"([^;]+)/s", $html, $matches);
        $query = $matches[1];
        if(!$vid){
            preg_match("/vid\s?=\s?vid\s?\|\|\s?\"(\w+)\";/i", $html, $matches);
            $vid = $matches[1];
        }
        $query = str_replace('"+vid+"', $vid, $query);
        parse_str($query, $output);
        $data['img'] = "http://vpic.video.qq.com/{$$output['cid']}/{$vid}_1.jpg";
        //$data['url'] = $url;
        //$data['title'] = $output['title'];
        //$data['swf'] = "http://imgcache.qq.com/tencentvideo_v1/player/TencentPlayer.swf?".$query;
        return $data;
    }
     
    /**
     * 乐视
     * http://i7.imgs.letv.com/player/swfPlayer.swf?autoPlay=0&id=23452267
     * http://www.letv.com/ptv/vplay/23432932.html
     */
    private static function _parseLetv($url)
    {
        preg_match("#id=(\d+)#", $url, $matches);
        if(empty($matches))
        {
            return false;
        }
        
        $url = 'http://www.letv.com/ptv/vplay/'.$matches[1].'.html';
        $html = self::_fget($url);
        // preg_match("#videoPic:\"(.*?)\"#", $html, $matches); // 2015-10-10 这个已经不适用了
        $regex = "|(?is)<div class=\"img-wrap\">(.*)<img src=\"(.*)\"|U";
        $regex = "|(?is)<div class=\"img-wrap\">(.*)<img data-original=\"(.*)\"|U";
        preg_match_all($regex, $html, $_tmp_arr, PREG_PATTERN_ORDER);
        
        if(empty($_tmp_arr[2]))
        {
            return false;
        }
        
        $data = array();
        $data['img'] = $_tmp_arr[2][0];
        return $data;
    }
    
    /**
     * 优酷
     * http://player.youku.com/player.php/sid/XMjU0NjI2Njg4/v.swf
     * doc_url http://open.youku.com/docs
     * 账号 myQQ client_id:bdd5b86929ce301f client_secret:63e5c711b78ce9f634358ef540545fce
     */
    private static function _parseYouku($url)
    {
        $client_id = 'bdd5b86929ce301f';
        preg_match("#sid\/(\w+)#", $url, $matches);
        
        if(empty($matches))
        {
            return false;
        }
        $video_id = $matches[1];
        
        $json_url = 'https://openapi.youku.com/v2/videos/show.json?client_id='.$client_id.'&video_id='.$video_id;
        $str_data = self::_cget($json_url);
        $arr_data = json_decode($str_data, true);
        
        $data = array();
        $data['img'] = $arr_data['bigThumbnail'];
        return $data;
    }
    
    /**
     * 土豆
     * http://www.tudou.com/a/_93DHcFhgf4/&iid=132528782&rpid=801544571&resourceId=801544571_04_05_99/v.swf
       http://www.tudou.com/v/w75uiyi42K0/&resourceId=0_04_05_99/v.swf
     
     * http://www.tudou.com/v/siuBXDL5nGs/v.swf
     * doc_url http://open.tudou.com/wiki
     * 账号 myQQ App Key:8492d7dc95dde778 App Secret:bb871864c9dd6a346fb0500b006acd4c
     */
    private static function _parseTudou($url)
    {
        $app_key = '8492d7dc95dde778';
        preg_match("#\/v\/(.*?)\/#", $url, $matches);
        
        if(empty($matches))
        {
            preg_match("#\&iid=(.*?)\&#", $url, $matches);
            
            if(empty($matches))
                return false;
        }
        $item_codes = $matches[1];
        
        $json_url = 'http://api.tudou.com/v6/video/info?app_key='.$app_key.'&itemCodes='.$item_codes;
        $str_data = self::_cget($json_url);
        $arr_data = json_decode($str_data, true);
        
        $data = array();
        $data['img'] = $arr_data['results'][0]['bigPicUrl'];
        return $data;
    }
    
    /**
     * 通过 file_get_contents 获取内容
     */
    private static function _fget($url=''){
        if(!$url) return false;
        $html = file_get_contents($url);
        
        // 判断是否gzip压缩
        if($dehtml = self::_gzdecode($html))
            return $dehtml;
        else
            return $html;
    }
    
    private static function _gzdecode($data) {
        $len = strlen ( $data );
        if ($len < 18 || strcmp ( substr ( $data, 0, 2 ), "\x1f\x8b" )) {
            return null; // Not GZIP format (See RFC 1952) 
        }
        $method = ord ( substr ( $data, 2, 1 ) ); // Compression method 
        $flags = ord ( substr ( $data, 3, 1 ) ); // Flags 
        if ($flags & 31 != $flags) {
            // Reserved bits are set -- NOT ALLOWED by RFC 1952 
            return null;
        }
        // NOTE: $mtime may be negative (PHP integer limitations) 
        $mtime = unpack ( "V", substr ( $data, 4, 4 ) );
        $mtime = $mtime [1];
        $xfl = substr ( $data, 8, 1 );
        $os = substr ( $data, 8, 1 );
        $headerlen = 10;
        $extralen = 0;
        $extra = "";
        if ($flags & 4) {
            // 2-byte length prefixed EXTRA data in header 
            if ($len - $headerlen - 2 < 8) {
                return false; // Invalid format 
            }
            $extralen = unpack ( "v", substr ( $data, 8, 2 ) );
            $extralen = $extralen [1];
            if ($len - $headerlen - 2 - $extralen < 8) {
                return false; // Invalid format 
            }
            $extra = substr ( $data, 10, $extralen );
            $headerlen += 2 + $extralen;
        }
      
        $filenamelen = 0;
        $filename = "";
        if ($flags & 8) {
            // C-style string file NAME data in header 
            if ($len - $headerlen - 1 < 8) {
                return false; // Invalid format 
            }
            $filenamelen = strpos ( substr ( $data, 8 + $extralen ), chr ( 0 ) );
            if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
                return false; // Invalid format 
            }
            $filename = substr ( $data, $headerlen, $filenamelen );
            $headerlen += $filenamelen + 1;
        }
      
        $commentlen = 0;
        $comment = "";
        if ($flags & 16) {
            // C-style string COMMENT data in header 
            if ($len - $headerlen - 1 < 8) {
                return false; // Invalid format 
            }
            $commentlen = strpos ( substr ( $data, 8 + $extralen + $filenamelen ), chr ( 0 ) );
            if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
                return false; // Invalid header format 
            }
            $comment = substr ( $data, $headerlen, $commentlen );
            $headerlen += $commentlen + 1;
        }
      
        $headercrc = "";
        if ($flags & 1) {
            // 2-bytes (lowest order) of CRC32 on header present 
            if ($len - $headerlen - 2 < 8) {
                return false; // Invalid format 
            }
            $calccrc = crc32 ( substr ( $data, 0, $headerlen ) ) & 0xffff;
            $headercrc = unpack ( "v", substr ( $data, $headerlen, 2 ) );
            $headercrc = $headercrc [1];
            if ($headercrc != $calccrc) {
                return false; // Bad header CRC 
            }
            $headerlen += 2;
        }
      
        // GZIP FOOTER - These be negative due to PHP's limitations 
        $datacrc = unpack ( "V", substr ( $data, - 8, 4 ) );
        $datacrc = $datacrc [1];
        $isize = unpack ( "V", substr ( $data, - 4 ) );
        $isize = $isize [1];
      
        // Perform the decompression: 
        $bodylen = $len - $headerlen - 8;
        if ($bodylen < 1) {
            // This should never happen - IMPLEMENTATION BUG! 
            return null;
        }
        $body = substr ( $data, $headerlen, $bodylen );
        $data = "";
        if ($bodylen > 0) {
            switch ($method) {
                case 8 :
                    // Currently the only supported compression method: 
                    $data = gzinflate ( $body );
                    break;
                default :
                    // Unknown compression method 
                    return false;
            }
        } else {
            //...
        }
      
        if ($isize != strlen ( $data ) || crc32 ( $data ) != $datacrc) {
            // Bad format!  Length or CRC doesn't match! 
            return false;
        }
        return $data;
    }
    
    /**
     * 通过 curl 获取内容
     */
    private static function _cget($url = '', $user_agent = ''){
        if(!$url) return;
 
        $user_agent = $user_agent ? $user_agent : self::USER_AGENT;
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if(strlen($user_agent)) curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
 
        ob_start();
        curl_exec($ch);
        $html = ob_get_contents();        
        ob_end_clean();
 
        if(curl_errno($ch)){
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        if(!is_string($html) || !strlen($html)){
            return false;
        }
        return $html;
        // 判断是否gzip压缩
        if($dehtml = self::_gzdecode($html))
            return $dehtml;
        else
            return $html;
    }
    
    private static function arr_to_utf8($arr) 
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
    
}
?>