<?php
/**
 *  $video = new VideoApi();  
 *  $video->resizeimage($tmp_img, $new_w, $new_h, $cut, $img);  
 */

class VideoApi
{
    const USER_AGENT = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko)
        Chrome/8.0.552.224 Safari/534.10";
    const CHECK_URL_VALID = "/(youku\.com|tudou\.com|ku6\.com|56\.com|letv\.com|video\.sina\.com\.cn|(my\.)?tv\.sohu\.com|v\.qq\.com)/";

    static public function parse($url = '', $createObject = false)
    {
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
        case 'ku6.com':
            $data = self::_parseKu6($url);
            break;
        case '56.com':
            $data = self::_parse56($url);
            break;
        case 'letv.com':
            $data = self::_parseLetv($url);
            break;
        case 'video.sina.com.cn':
            $data = self::_parseSina($url);
            break;
        case 'my.tv.sohu.com':
        case 'tv.sohu.com':
        case 'sohu.com':
            $data = self::_parseSohu($url);
            break;
        case 'v.qq.com':
            $data = self::_parseQq($url);
            break;
        default:
            $data = false;
        }
 
        if($data && $createObject)
            $data['object'] = "<embed src=\"{$data['swf']}\" quality=\"high\" width=\"480\" height=\"400\" align=\"middle\" allowNetworking=\"all\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\"></embed>";
        
        return $data;
    }
    
    /**
     * 新浪
     */
     
    /**
     * 腾讯
     */
     
    /**
     * 乐视
     * http://i7.imgs.letv.com/player/swfPlayer.swf?autoPlay=0&id=23452267
     * http://www.letv.com/ptv/vplay/23432932.html
     */
    private static function _parseLetv($url)
    {
        
        echo $url;
        // 'http://i7.imgs.letv.com/player/swfPlayer.swf?autoPlay=0&id=23452267'
        preg_match("#id=(\d+)#", $url, $matches);
        if(empty($matches))
        {
            return false;
        }
        
        $url = 'http://www.letv.com/ptv/vplay/'.$matches[1].'.html';
        $html = self::_fget($url);
        
        preg_match("#videoPic:\"(.*?)\"#", $html, $matches);
        if(empty($matches))
        {
            return false;
        }
        
        $data = array();
        $data['img'] = $matches[1];
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
     * http://www.tudou.com/v/siuBXDL5nGs/v.swf
     * doc_url http://open.tudou.com/wiki
     * 账号 myQQ App Key:8492d7dc95dde778 App Secret:bb871864c9dd6a346fb0500b006acd4c
     */
    private static function _parseTudou($url)
    {
        $app_key = '8492d7dc95dde778';
        preg_match("#\/v\/(.*?)\/v\.swf#", $url, $matches);
        
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