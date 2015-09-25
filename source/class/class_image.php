<?php
//set_time_limit(600); // 
/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: class_image.php 34673 2014-06-26 02:55:52Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class image {

	var $source = '';
	var $target = '';
	var $imginfo = array();
	var $imagecreatefromfunc = '';
	var $imagefunc = '';
	var $tmpfile = '';
	var $libmethod = 0;
	var $param = array();
	var $errorcode = 0;

	function image() {
		global $_G;
		$this->param = array(
			'imagelib'		=> $_G['setting']['imagelib'],
			'imageimpath'		=> $_G['setting']['imageimpath'],
			'thumbquality'		=> $_G['setting']['thumbquality'],
			'watermarkstatus'	=> dunserialize($_G['setting']['watermarkstatus']),
			'watermarkminwidth'	=> dunserialize($_G['setting']['watermarkminwidth']),
			'watermarkminheight'	=> dunserialize($_G['setting']['watermarkminheight']),
			'watermarktype'		=> $_G['setting']['watermarktype'],
			'watermarktext'		=> $_G['setting']['watermarktext'],
			'watermarktrans'	=> dunserialize($_G['setting']['watermarktrans']),
			'watermarkquality'	=> dunserialize($_G['setting']['watermarkquality']),
		);
	}


	function Thumb($source, $target, $thumbwidth, $thumbheight, $thumbtype = 1, $nosuffix = 0) {
		$return = $this->init('thumb', $source, $target, $nosuffix);
		if($return <= 0) {
			return $this->returncode($return);
		}

		if($this->imginfo['animated']) {
			return $this->returncode(0);
		}
		$this->param['thumbwidth'] = intval($thumbwidth);
		if(!$thumbheight || $thumbheight > $this->imginfo['height']) {
			$thumbheight = $thumbwidth > $this->imginfo['width'] ? $this->imginfo['height'] : $this->imginfo['height']*($thumbwidth/$this->imginfo['width']);
		}
		$this->param['thumbheight'] = intval($thumbheight);
		$this->param['thumbtype'] = $thumbtype;
		if($thumbwidth < 100 && $thumbheight < 100) {
			$this->param['thumbquality'] = 100;
		}

		$return = !$this->libmethod ? $this->Thumb_GD() : $this->Thumb_IM();
		$return = !$nosuffix ? $return : 0;

		return $this->sleep($return);
	}

	function Cropper($source, $target, $dstwidth, $dstheight, $srcx = 0, $srcy = 0, $srcwidth = 0, $srcheight = 0) {

		$return = $this->init('thumb', $source, $target, 1);
		if($return <= 0) {
			return $this->returncode($return);
		}
		if($dstwidth < 0 || $dstheight < 0) {
			return $this->returncode(false);
		}
		$this->param['dstwidth'] = intval($dstwidth);
		$this->param['dstheight'] = intval($dstheight);
		$this->param['srcx'] = intval($srcx);
		$this->param['srcy'] = intval($srcy);
		$this->param['srcwidth'] = intval($srcwidth ? $srcwidth : $dstwidth);
		$this->param['srcheight'] = intval($srcheight ? $srcheight : $dstheight);

		$return = !$this->libmethod ? $this->Cropper_GD() : $this->Cropper_IM();
	}

	function Watermark($source, $target = '', $type = 'forum') {
		$return = $this->init('watermask', $source, $target);
		if($return <= 0) {
			return $this->returncode($return);
		}

		if(!$this->param['watermarkstatus'][$type] || ($this->param['watermarkminwidth'][$type] && $this->imginfo['width'] <= $this->param['watermarkminwidth'][$type] && $this->param['watermarkminheight'][$type] && $this->imginfo['height'] <= $this->param['watermarkminheight'][$type])) {
			return $this->returncode(0);
		}
		$this->param['watermarkfile'][$type] = './static/image/common/'.($this->param['watermarktype'][$type] == 'png' ? 'watermark.png' : 'watermark.gif');
		if(!is_readable($this->param['watermarkfile'][$type]) || ($this->param['watermarktype'][$type] == 'text' && (!file_exists($this->param['watermarktext']['fontpath'][$type]) || !is_file($this->param['watermarktext']['fontpath'][$type])))) {
			return $this->returncode(-3);
		}
		
		$return = !$this->libmethod ? $this->Watermark_GD($type) : $this->Watermark_IM($type);
		
		return $this->sleep($return);
	}

	function error() {
		return $this->errorcode;
	}

	function init($method, $source, $target, $nosuffix = 0) {
		global $_G;

		$this->errorcode = 0;
		if(empty($source)) {
			return -2;
		}
		$parse = parse_url($source);
		if(isset($parse['host'])) {
			if(empty($target)) {
				return -2;
			}
			$data = dfsockopen($source);
			$this->tmpfile = $source = tempnam($_G['setting']['attachdir'].'./temp/', 'tmpimg_');
			if(!$data || $source === FALSE) {
				return -2;
			}
			file_put_contents($source, $data);
		}
		if($method == 'thumb') {
			$target = empty($target) ? (!$nosuffix ? getimgthumbname($source) : $source) : $_G['setting']['attachdir'].'./'.$target;
		} elseif($method == 'watermask') {
			$target = empty($target) ?  $source : $_G['setting']['attachdir'].'./'.$target;
		}
		$targetpath = dirname($target);
		dmkdir($targetpath);

		clearstatcache();
		if(!is_readable($source) || !is_writable($targetpath)) {
			return -2;
		}

		$imginfo = @getimagesize($source);
		if($imginfo === FALSE) {
			return -1;
		}

		$this->source = $source;
		$this->target = $target;
		$this->imginfo['width'] = $imginfo[0];
		$this->imginfo['height'] = $imginfo[1];
		$this->imginfo['mime'] = $imginfo['mime'];
		$this->imginfo['size'] = @filesize($source);
		$this->libmethod = $this->param['imagelib'] && $this->param['imageimpath'];

		if(!$this->libmethod) {
			switch($this->imginfo['mime']) {
				case 'image/jpeg':
					$this->imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
					$this->imagefunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
					break;
				case 'image/gif':
					$this->imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
					$this->imagefunc = function_exists('imagegif') ? 'imagegif' : '';
					break;
				case 'image/png':
					$this->imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
					$this->imagefunc = function_exists('imagepng') ? 'imagepng' : '';
					break;
			}
		} else {
			$this->imagecreatefromfunc = $this->imagefunc = TRUE;
		}

		if(!$this->libmethod && $this->imginfo['mime'] == 'image/gif') {
			if(!$this->imagecreatefromfunc) {
				return -4;
			}
			if(!($fp = @fopen($source, 'rb'))) {
				return -2;
			}
			$content = fread($fp, $this->imginfo['size']);
			fclose($fp);
			$this->imginfo['animated'] = strpos($content, 'NETSCAPE2.0') === FALSE ? 0 : 1;
		}

		return $this->imagecreatefromfunc ? 1 : -4;
	}

	function sleep($return) {
		if($this->tmpfile) {
			@unlink($this->tmpfile);
		}
		$this->imginfo['size'] = @filesize($this->target);
		return $this->returncode($return);
	}

	function returncode($return) {
		if($return > 0 && file_exists($this->target)) {
			return true;
		} else {
			if($this->tmpfile) {
				@unlink($this->tmpfile);
			}
			$this->errorcode = $return;
			return false;
		}
	}

	function sizevalue($method) {
		$x = $y = $w = $h = 0;
		if($method > 0) {
			$imgratio = $this->imginfo['width'] / $this->imginfo['height'];
			$thumbratio = $this->param['thumbwidth'] / $this->param['thumbheight'];
			if($imgratio >= 1 && $imgratio >= $thumbratio || $imgratio < 1 && $imgratio > $thumbratio) {
				$h = $this->imginfo['height'];
				$w = $h * $thumbratio;
				$x = ($this->imginfo['width'] - $thumbratio * $this->imginfo['height']) / 2;
			} elseif($imgratio >= 1 && $imgratio <= $thumbratio || $imgratio < 1 && $imgratio <= $thumbratio) {
				$w = $this->imginfo['width'];
				$h = $w / $thumbratio;
			}
		} else {
			$x_ratio = $this->param['thumbwidth'] / $this->imginfo['width'];
			$y_ratio = $this->param['thumbheight'] / $this->imginfo['height'];
			if(($x_ratio * $this->imginfo['height']) < $this->param['thumbheight']) {
				$h = ceil($x_ratio * $this->imginfo['height']);
				$w = $this->param['thumbwidth'];
			} else {
				$w = ceil($y_ratio * $this->imginfo['width']);
				$h = $this->param['thumbheight'];
			}
		}
		return array($x, $y, $w, $h);
	}

	function loadsource() {
		$imagecreatefromfunc = &$this->imagecreatefromfunc;
		$im = @$imagecreatefromfunc($this->source);
		if(!$im) {
			if(!function_exists('imagecreatefromstring')) {
				return -4;
			}
			$fp = @fopen($this->source, 'rb');
			$contents = @fread($fp, filesize($this->source));
			fclose($fp);
			$im = @imagecreatefromstring($contents);
			if($im == FALSE) {
				return -1;
			}
		}
		return $im;
	}

	function Thumb_GD() {
		if(!function_exists('imagecreatetruecolor') || !function_exists('imagecopyresampled') || !function_exists('imagejpeg') || !function_exists('imagecopymerge')) {
			return -4;
		}

		$imagefunc = &$this->imagefunc;
		$attach_photo = $this->loadsource();
		if($attach_photo < 0) {
			return $attach_photo;
		}
		$copy_photo = imagecreatetruecolor($this->imginfo['width'], $this->imginfo['height']);
		imagecopy($copy_photo, $attach_photo ,0, 0, 0, 0, $this->imginfo['width'], $this->imginfo['height']);
		$attach_photo = $copy_photo;

		$thumb_photo = null;
		switch($this->param['thumbtype']) {
			case 'fixnone':
			case 1:
				if($this->imginfo['width'] >= $this->param['thumbwidth'] || $this->imginfo['height'] >= $this->param['thumbheight']) {
					$thumb = array();
					list(,,$thumb['width'], $thumb['height']) = $this->sizevalue(0);
					$cx = $this->imginfo['width'];
					$cy = $this->imginfo['height'];
					$thumb_photo = imagecreatetruecolor($thumb['width'], $thumb['height']);
					imagecopyresampled($thumb_photo, $attach_photo ,0, 0, 0, 0, $thumb['width'], $thumb['height'], $cx, $cy);
				}
				break;
			case 'fixwr':
			case 2:
				if(!($this->imginfo['width'] <= $this->param['thumbwidth'] || $this->imginfo['height'] <= $this->param['thumbheight'])) {
					list($startx, $starty, $cutw, $cuth) = $this->sizevalue(1);
					$dst_photo = imagecreatetruecolor($cutw, $cuth);
					imagecopymerge($dst_photo, $attach_photo, 0, 0, $startx, $starty, $cutw, $cuth, 100);
					$thumb_photo = imagecreatetruecolor($this->param['thumbwidth'], $this->param['thumbheight']);
					imagecopyresampled($thumb_photo, $dst_photo ,0, 0, 0, 0, $this->param['thumbwidth'], $this->param['thumbheight'], $cutw, $cuth);
				} else {
					$thumb_photo = imagecreatetruecolor($this->param['thumbwidth'], $this->param['thumbheight']);
					$bgcolor = imagecolorallocate($thumb_photo, 255, 255, 255);
					imagefill($thumb_photo, 0, 0, $bgcolor);
					$startx = ($this->param['thumbwidth'] - $this->imginfo['width']) / 2;
					$starty = ($this->param['thumbheight'] - $this->imginfo['height']) / 2;
					imagecopymerge($thumb_photo, $attach_photo, $startx, $starty, 0, 0, $this->imginfo['width'], $this->imginfo['height'], 100);
				}
				break;
		}
		clearstatcache();
		if($thumb_photo) {
			if($this->imginfo['mime'] == 'image/jpeg') {
				@$imagefunc($thumb_photo, $this->target, $this->param['thumbquality']);
			} else {
				@$imagefunc($thumb_photo, $this->target); 
			}
			return 1;
		} else {
			return 0;
		}
	}

	function Thumb_IM() {
		switch($this->param['thumbtype']) {
			case 'fixnone':
			case 1:
				if($this->imginfo['width'] >= $this->param['thumbwidth'] || $this->imginfo['height'] >= $this->param['thumbheight']) {
					$exec_str = $this->param['imageimpath'].'/convert -quality '.intval($this->param['thumbquality']).' -geometry '.$this->param['thumbwidth'].'x'.$this->param['thumbheight'].' '.$this->source.' '.$this->target;
					$return = exec($exec_str);
					if(!file_exists($this->target)) {
						return -3;
					}
				}
				break;
			case 'fixwr':
			case 2:
				if(!($this->imginfo['width'] <= $this->param['thumbwidth'] || $this->imginfo['height'] <= $this->param['thumbheight'])) {
					list($startx, $starty, $cutw, $cuth) = $this->sizevalue(1);
					$exec_str = $this->param['imageimpath'].'/convert -quality '.intval($this->param['thumbquality']).' -crop '.$cutw.'x'.$cuth.'+'.$startx.'+'.$starty.' '.$this->source.' '.$this->target;
					exec($exec_str);
					if(!file_exists($this->target)) {
						return -3;
					}
					$exec_str = $this->param['imageimpath'].'/convert -quality '.intval($this->param['thumbquality']).' -thumbnail \''.$this->param['thumbwidth'].'x'.$this->param['thumbheight'].'\' -resize '.$this->param['thumbwidth'].'x'.$this->param['thumbheight'].' -gravity center -extent '.$this->param['thumbwidth'].'x'.$this->param['thumbheight'].' '.$this->target.' '.$this->target;
					exec($exec_str);
					if(!file_exists($this->target)) {
						return -3;
					}
				} else {
					$startx = -($this->param['thumbwidth'] - $this->imginfo['width']) / 2;
					$starty = -($this->param['thumbheight'] - $this->imginfo['height']) / 2;
					$exec_str = $this->param['imageimpath'].'/convert -quality '.intval($this->param['thumbquality']).' -crop '.$this->param['thumbwidth'].'x'.$this->param['thumbheight'].'+'.$startx.'+'.$starty.' '.$this->source.' '.$this->target;
					exec($exec_str);
					if(!file_exists($this->target)) {
						return -3;
					}
					$exec_str = $this->param['imageimpath'].'/convert -quality '.intval($this->param['thumbquality']).' -thumbnail \''.$this->param['thumbwidth'].'x'.$this->param['thumbheight'].'\' -gravity center -extent '.$this->param['thumbwidth'].'x'.$this->param['thumbheight'].' '.$this->target.' '.$this->target;
					exec($exec_str);
					if(!file_exists($this->target)) {
						return -3;
					}
				}
				break;
		}
		return 1;
	}

	function Cropper_GD() {
		$image = $this->loadsource();
		if($image < 0) {
			return $image;
		}
		$newimage = imagecreatetruecolor($this->param['dstwidth'], $this->param['dstheight']);
		imagecopyresampled($newimage, $image, 0, 0, $this->param['srcx'], $this->param['srcy'], $this->param['dstwidth'], $this->param['dstheight'], $this->param['srcwidth'], $this->param['srcheight']);
		ImageJpeg($newimage, $this->target, 100);
		imagedestroy($newimage);
		imagedestroy($image);
		return true;
	}
	function Cropper_IM() {
		$exec_str = $this->param['imageimpath'].'/convert -quality 100 '.
			'-crop '.$this->param['srcwidth'].'x'.$this->param['srcheight'].'+'.$this->param['srcx'].'+'.$this->param['srcy'].' '.
			'-geometry '.$this->param['dstwidth'].'x'.$this->param['dstheight'].' '.$this->source.' '.$this->target;
		exec($exec_str);
		if(!file_exists($this->target)) {
			return -3;
		}
	}
	
	function Watermark_GD($type = 'forum') {
		if(!function_exists('imagecreatetruecolor')) {
			return -4;
		}

		$imagefunc = &$this->imagefunc;

		if($this->param['watermarktype'][$type] != 'text') {
			if(!function_exists('imagecopy') || !function_exists('imagecreatefrompng') || !function_exists('imagecreatefromgif') || !function_exists('imagealphablending') || !function_exists('imagecopymerge')) {
				return -4;
			}
			$watermarkinfo = @getimagesize($this->param['watermarkfile'][$type]);
			if($watermarkinfo === FALSE) {
				return -3;
			}
			$watermark_logo	= $this->param['watermarktype'][$type] == 'png' ? @imageCreateFromPNG($this->param['watermarkfile'][$type]) : @imageCreateFromGIF($this->param['watermarkfile'][$type]);
			if(!$watermark_logo) {
				return 0;
			}
			list($logo_w, $logo_h) = $watermarkinfo;
		} else {
			if(!function_exists('imagettfbbox') || !function_exists('imagettftext') || !function_exists('imagecolorallocate')) {
				return -4;
			}
			if(!class_exists('Chinese')) {
				include libfile('class/chinese');
			}

			$watermarktextcvt = pack("H*", $this->param['watermarktext']['text'][$type]);
			$box = imagettfbbox($this->param['watermarktext']['size'][$type], $this->param['watermarktext']['angle'][$type], $this->param['watermarktext']['fontpath'][$type], $watermarktextcvt);
			$logo_h = max($box[1], $box[3]) - min($box[5], $box[7]);
			$logo_w = max($box[2], $box[4]) - min($box[0], $box[6]);
			$ax = min($box[0], $box[6]) * -1;
			$ay = min($box[5], $box[7]) * -1;
		}
		$wmwidth = $this->imginfo['width'] - $logo_w;
		$wmheight = $this->imginfo['height'] - $logo_h;

		if($wmwidth > 10 && $wmheight > 10 && !$this->imginfo['animated']) {
			switch($this->param['watermarkstatus'][$type]) {
				case 1:
					$x = 5;
					$y = 5;
					break;
				case 2:
					$x = ($this->imginfo['width'] - $logo_w) / 2;
					$y = 5;
					break;
				case 3:
					$x = $this->imginfo['width'] - $logo_w - 5;
					$y = 5;
					break;
				case 4:
					$x = 5;
					$y = ($this->imginfo['height'] - $logo_h) / 2;
					break;
				case 5:
					$x = ($this->imginfo['width'] - $logo_w) / 2;
					$y = ($this->imginfo['height'] - $logo_h) / 2;
					break;
				case 6:
					$x = $this->imginfo['width'] - $logo_w;
					$y = ($this->imginfo['height'] - $logo_h) / 2;
					break;
				case 7:
					$x = 5;
					$y = $this->imginfo['height'] - $logo_h - 5;
					break;
				case 8:
					$x = ($this->imginfo['width'] - $logo_w) / 2;
					$y = $this->imginfo['height'] - $logo_h - 5;
					break;
				case 9:
					$x = $this->imginfo['width'] - $logo_w - 5;
					$y = $this->imginfo['height'] - $logo_h - 5;
					break;
			}
			if($this->imginfo['mime'] != 'image/png') {
				$color_photo = imagecreatetruecolor($this->imginfo['width'], $this->imginfo['height']);
			}
			$dst_photo = $this->loadsource();
			if($dst_photo < 0) {
				return $dst_photo;
			}
			imagealphablending($dst_photo, true);
			imagesavealpha($dst_photo, true);
			if($this->imginfo['mime'] != 'image/png') {
				imageCopy($color_photo, $dst_photo, 0, 0, 0, 0, $this->imginfo['width'], $this->imginfo['height']);
				$dst_photo = $color_photo;
			}
			if($this->param['watermarktype'][$type] == 'png') {
				imageCopy($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h);
			} elseif($this->param['watermarktype'][$type] == 'text') {
				if(($this->param['watermarktext']['shadowx'][$type] || $this->param['watermarktext']['shadowy'][$type]) && $this->param['watermarktext']['shadowcolor'][$type]) {
					$shadowcolorrgb = explode(',', $this->param['watermarktext']['shadowcolor'][$type]);
					$shadowcolor = imagecolorallocate($dst_photo, $shadowcolorrgb[0], $shadowcolorrgb[1], $shadowcolorrgb[2]);
					imagettftext($dst_photo, $this->param['watermarktext']['size'][$type], $this->param['watermarktext']['angle'][$type], $x + $ax + $this->param['watermarktext']['shadowx'][$type], $y + $ay + $this->param['watermarktext']['shadowy'][$type], $shadowcolor, $this->param['watermarktext']['fontpath'][$type], $watermarktextcvt);
				}

				$colorrgb = explode(',', $this->param['watermarktext']['color'][$type]);
				$color = imagecolorallocate($dst_photo, $colorrgb[0], $colorrgb[1], $colorrgb[2]);
				imagettftext($dst_photo, $this->param['watermarktext']['size'][$type], $this->param['watermarktext']['angle'][$type], $x + $ax, $y + $ay, $color, $this->param['watermarktext']['fontpath'][$type], $watermarktextcvt);
			} else {
				imageAlphaBlending($watermark_logo, true);
				imageCopyMerge($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h, $this->param['watermarktrans'][$type]);
			}

			clearstatcache();
			if($this->imginfo['mime'] == 'image/jpeg') {
				@$imagefunc($dst_photo, $this->target, $this->param['watermarkquality'][$type]);
			} else {
				@$imagefunc($dst_photo, $this->target);
			}
		}
		elseif($wmwidth > 10 && $wmheight > 10 && $this->imginfo['animated'] && FALSE) // zhangjh 2015-07-28 添加支持动态git加水印的支持
		{
			switch($this->param['watermarkstatus'][$type]) {
				case 1:
					$x = 5;
					$y = 5;
					break;
				case 2:
					$x = ($this->imginfo['width'] - $logo_w) / 2;
					$y = 5;
					break;
				case 3:
					$x = $this->imginfo['width'] - $logo_w - 5;
					$y = 5;
					break;
				case 4:
					$x = 5;
					$y = ($this->imginfo['height'] - $logo_h) / 2;
					break;
				case 5:
					$x = ($this->imginfo['width'] - $logo_w) / 2;
					$y = ($this->imginfo['height'] - $logo_h) / 2;
					break;
				case 6:
					$x = $this->imginfo['width'] - $logo_w;
					$y = ($this->imginfo['height'] - $logo_h) / 2;
					break;
				case 7:
					$x = 5;
					$y = $this->imginfo['height'] - $logo_h - 5;
					break;
				case 8:
					$x = ($this->imginfo['width'] - $logo_w) / 2;
					$y = $this->imginfo['height'] - $logo_h - 5;
					break;
				case 9:
					$x = $this->imginfo['width'] - $logo_w - 5;
					$y = $this->imginfo['height'] - $logo_h - 5;
					break;
			}
			if($this->imginfo['mime'] != 'image/png') {
				$color_photo = imagecreatetruecolor($this->imginfo['width'], $this->imginfo['height']);
			}
			$dst_photo = $this->loadsource();
			if($dst_photo < 0) {
				return $dst_photo;
			}
			
			$image = new Timagick();
			if($this->param['watermarktype'][$type] == 'text')
			{
				// $image->add_text($text, $x, $y, $angle, $style) // 文字水印没有加入，他们需要就加吧
			}
			else
			{
				$image->open($this->source);
				$image->add_watermark($this->param['watermarkfile'][$type], $x, $y);
				$image->save_to($this->target);
			}
		}
		return 1;
	}

	function Watermark_IM($type = 'forum') {
		switch($this->param['watermarkstatus'][$type]) {
			case 1:
				$gravity = 'NorthWest';
				break;
			case 2:
				$gravity = 'North';
				break;
			case 3:
				$gravity = 'NorthEast';
				break;
			case 4:
				$gravity = 'West';
				break;
			case 5:
				$gravity = 'Center';
				break;
			case 6:
				$gravity = 'East';
				break;
			case 7:
				$gravity = 'SouthWest';
				break;
			case 8:
				$gravity = 'South';
				break;
			case 9:
				$gravity = 'SouthEast';
				break;
		}

		if($this->param['watermarktype'][$type] != 'text') {
			$exec_str = $this->param['imageimpath'].'/composite'.
				($this->param['watermarktype'][$type] != 'png' && $this->param['watermarktrans'][$type] != '100' ? ' -watermark '.$this->param['watermarktrans'][$type] : '').
				' -quality '.$this->param['watermarkquality'][$type].
				' -gravity '.$gravity.
				' '.$this->param['watermarkfile'][$type].' '.$this->source.' '.$this->target;
		} else {
			$watermarktextcvt = escapeshellcmd(pack("H*", $this->param['watermarktext']['text'][$type]));
			$angle = -$this->param['watermarktext']['angle'][$type];
			$translate = $this->param['watermarktext']['translatex'][$type] || $this->param['watermarktext']['translatey'][$type] ? ' translate '.$this->param['watermarktext']['translatex'][$type].','.$this->param['watermarktext']['translatey'][$type] : '';
			$skewX = $this->param['watermarktext']['skewx'][$type] ? ' skewX '.$this->param['watermarktext']['skewx'][$type] : '';
			$skewY = $this->param['watermarktext']['skewy'][$type] ? ' skewY '.$this->param['watermarktext']['skewy'][$type] : '';
			$exec_str = $this->param['imageimpath'].'/convert'.
				' -quality '.$this->param['watermarkquality'][$type].
				' -font "'.$this->param['watermarktext']['fontpath'][$type].'"'.
				' -pointsize '.$this->param['watermarktext']['size'][$type].
				(($this->param['watermarktext']['shadowx'][$type] || $this->param['watermarktext']['shadowy'][$type]) && $this->param['watermarktext']['shadowcolor'][$type] ?
					' -fill "rgb('.$this->param['watermarktext']['shadowcolor'][$type].')"'.
					' -draw "'.
						' gravity '.$gravity.$translate.$skewX.$skewY.
						' rotate '.$angle.
						' text '.$this->param['watermarktext']['shadowx'][$type].','.$this->param['watermarktext']['shadowy'][$type].' \''.$watermarktextcvt.'\'"' : '').
				' -fill "rgb('.$this->param['watermarktext']['color'][$type].')"'.
				' -draw "'.
					' gravity '.$gravity.$translate.$skewX.$skewY.
					' rotate '.$angle.
					' text 0,0 \''.$watermarktextcvt.'\'"'.
				' '.$this->source.' '.$this->target;
		}
		exec($exec_str);
		if(!file_exists($this->target)) {
			return -3;
		}
		return 1;
	}

}

/*
@版本日期: 版本日期: 2012年1月18日
@著作权所有: 1024 intelligence ( http://www.1024i.com )
 
获得使用本类库的许可, 您必须保留著作权声明信息.
报告漏洞，意见或建议, 请联系 Lou Barnes(iua1024@gmail.com)
*/
class Timagick
{
	private $image = null;
	private $type = null;
	
	// 构造函数
	public function __construct(){}
	
	// 析构函数
	public function __destruct()
	{
	  if($this->image!==null) $this->image->destroy(); 
	}
 
	// 载入图像
	public function open($path)
	{
		$this->image = new Imagick( $path );
		if($this->image)
		{
			$this->type = strtolower($this->image->getImageFormat());
		}
		return $this->image;
	}
	
	public function crop($x=0, $y=0, $width=null, $height=null)
	{
		if($width==null) $width = $this->image->getImageWidth()-$x;
		if($height==null) $height = $this->image->getImageHeight()-$y;
		if($width<=0 || $height<=0) return;
		
		if($this->type=='gif')
		{
			$image = $this->image;
			$canvas = new Imagick();
			
			$images = $image->coalesceImages();
			foreach($images as $frame)
			{
				$img = new Imagick();
				$img->readImageBlob($frame);
				$img->cropImage($width, $height, $x, $y);
				
				$canvas->addImage( $img );
				$canvas->setImageDelay( $img->getImageDelay() );
				$canvas->setImagePage($width, $height, 0, 0);
			}
			$image->destroy();
			$this->image = $canvas;
		}
		else
		{
			$this->image->cropImage($width, $height, $x, $y);
		}
	}

	/*
	* 更改图像大小
	$fit: 适应大小方式
	'force': 把图片强制变形成 $width X $height 大小
	'scale': 按比例在安全框 $width X $height 内缩放图片, 输出缩放后图像大小 不完全等于 $width X $height
	'scale_fill': 按比例在安全框 $width X $height 内缩放图片，安全框内没有像素的地方填充色, 使用此参数时可设置背景填充色 $bg_color = array(255,255,255)(红,绿,蓝, 透明度) 透明度(0不透明-127完全透明))
	其它: 智能模能 缩放图像并载取图像的中间部分 $width X $height 像素大小
	$fit = 'force','scale','scale_fill' 时： 输出完整图像
	$fit = 图像方位值 时, 输出指定位置部分图像 
	字母与图像的对应关系如下:
	north_west   north   north_east
	
	west         center        east
	
	south_west   south   south_east
	
	*/
  public function resize_to($width = 100, $height = 100, $fit = 'center', $fill_color = array(255,255,255,0) )
  {
       
      switch($fit)
      {
          case 'force':
              if($this->type=='gif')
              {
                  $image = $this->image;
                  $canvas = new Imagick();
                   
                  $images = $image->coalesceImages();
                  foreach($images as $frame){
                      $img = new Imagick();
                      $img->readImageBlob($frame);
                        $img->thumbnailImage( $width, $height, false );
 
                        $canvas->addImage( $img );
                        $canvas->setImageDelay( $img->getImageDelay() );
                    }
                    $image->destroy();
                  $this->image = $canvas;
              }
              else
              {
                  $this->image->thumbnailImage( $width, $height, false );
              }
              break;
          case 'scale':
              if($this->type=='gif')
              {
                  $image = $this->image;
                  $images = $image->coalesceImages();
                  $canvas = new Imagick();
                  foreach($images as $frame){
                      $img = new Imagick();
                      $img->readImageBlob($frame);
                        $img->thumbnailImage( $width, $height, true );
 
                        $canvas->addImage( $img );
                        $canvas->setImageDelay( $img->getImageDelay() );
                    }
                    $image->destroy();
                  $this->image = $canvas;
              }
              else
              {
                  $this->image->thumbnailImage( $width, $height, true );
              }
              break;
          case 'scale_fill':
              $size = $this->image->getImagePage(); 
              $src_width = $size['width'];
              $src_height = $size['height'];
               
                $x = 0;
                $y = 0;
                 
                $dst_width = $width;
                $dst_height = $height;
 
          if($src_width*$height > $src_height*$width)
        {
          $dst_height = intval($width*$src_height/$src_width);
          $y = intval( ($height-$dst_height)/2 );
        }
        else
        {
          $dst_width = intval($height*$src_width/$src_height);
          $x = intval( ($width-$dst_width)/2 );
        }
 
                $image = $this->image;
                $canvas = new Imagick();
                 
                $color = 'rgba('.$fill_color[0].','.$fill_color[1].','.$fill_color[2].','.$fill_color[3].')';
              if($this->type=='gif')
              {
                  $images = $image->coalesceImages();
                  foreach($images as $frame)
                  {
                      $frame->thumbnailImage( $width, $height, true );
 
                      $draw = new ImagickDraw();
                        $draw->composite($frame->getImageCompose(), $x, $y, $dst_width, $dst_height, $frame);
 
                        $img = new Imagick();
                        $img->newImage($width, $height, $color, 'gif');
                        $img->drawImage($draw);
 
                        $canvas->addImage( $img );
                        $canvas->setImageDelay( $img->getImageDelay() );
                        $canvas->setImagePage($width, $height, 0, 0);
                    }
              }
              else
              {
                  $image->thumbnailImage( $width, $height, true );
                   
                  $draw = new ImagickDraw();
                    $draw->composite($image->getImageCompose(), $x, $y, $dst_width, $dst_height, $image);
                     
                  $canvas->newImage($width, $height, $color, $this->get_type() );
                    $canvas->drawImage($draw);
                    $canvas->setImagePage($width, $height, 0, 0);
              }
              $image->destroy();
              $this->image = $canvas;
              break;
      default:
        $size = $this->image->getImagePage(); 
          $src_width = $size['width'];
              $src_height = $size['height'];
               
                $crop_x = 0;
                $crop_y = 0;
                 
                $crop_w = $src_width;
                $crop_h = $src_height;
                 
            if($src_width*$height > $src_height*$width)
        {
          $crop_w = intval($src_height*$width/$height);
        }
        else
        {
            $crop_h = intval($src_width*$height/$width);
        }
                 
          switch($fit)
              {
            case 'north_west':
                $crop_x = 0;
                $crop_y = 0;
                break;
              case 'north':
                  $crop_x = intval( ($src_width-$crop_w)/2 );
                  $crop_y = 0;
                  break;
              case 'north_east':
                  $crop_x = $src_width-$crop_w;
                  $crop_y = 0;
                  break;
              case 'west':
                  $crop_x = 0;
                  $crop_y = intval( ($src_height-$crop_h)/2 );
                  break;
              case 'center':
                  $crop_x = intval( ($src_width-$crop_w)/2 );
                  $crop_y = intval( ($src_height-$crop_h)/2 );
                  break;
              case 'east':
                  $crop_x = $src_width-$crop_w;
                  $crop_y = intval( ($src_height-$crop_h)/2 );
                  break;
              case 'south_west':
                  $crop_x = 0;
                  $crop_y = $src_height-$crop_h;
                  break;
              case 'south':
                  $crop_x = intval( ($src_width-$crop_w)/2 );
                  $crop_y = $src_height-$crop_h;
                  break;
              case 'south_east':
                  $crop_x = $src_width-$crop_w;
                  $crop_y = $src_height-$crop_h;
                  break;
              default:
                  $crop_x = intval( ($src_width-$crop_w)/2 );
                  $crop_y = intval( ($src_height-$crop_h)/2 );
              }
               
              $image = $this->image;
              $canvas = new Imagick();
               
            if($this->type=='gif')
              {
                  $images = $image->coalesceImages();
                  foreach($images as $frame){
                      $img = new Imagick();
                      $img->readImageBlob($frame);
                        $img->cropImage($crop_w, $crop_h, $crop_x, $crop_y);
                        $img->thumbnailImage( $width, $height, true );
                         
                        $canvas->addImage( $img );
                        $canvas->setImageDelay( $img->getImageDelay() );
                        $canvas->setImagePage($width, $height, 0, 0);
                    }
              }
              else
              {
                  $image->cropImage($crop_w, $crop_h, $crop_x, $crop_y);
                  $image->thumbnailImage( $width, $height, true );
                  $canvas->addImage( $image );
                  $canvas->setImagePage($width, $height, 0, 0);
              }
              $image->destroy();
              $this->image = $canvas;
      }
       
  }
 
  // 添加水印图片
  public function add_watermark($path, $x = 0, $y = 0)
  {
        $watermark = new Imagick($path);
        $draw = new ImagickDraw();
        $draw->composite($watermark->getImageCompose(), $x, $y, $watermark->getImageWidth(), $watermark->getimageheight(), $watermark);
 
      if($this->type=='gif')
      {
          $image = $this->image;
          $canvas = new Imagick();
          $images = $image->coalesceImages();
          foreach($image as $frame)
          {
                $img = new Imagick();
                $img->readImageBlob($frame);
                $img->drawImage($draw);
                 
                $canvas->addImage( $img );
                $canvas->setImageDelay( $img->getImageDelay() );
            }
            $image->destroy();
          $this->image = $canvas;
      }
      else
      {
          $this->image->drawImage($draw);
      }
  }
 
   
  // 添加水印文字
  public function add_text($text, $x = 0 , $y = 0, $angle=0, $style=array())
  {
        $draw = new ImagickDraw();
        if(isset($style['font'])) $draw->setFont($style['font']);
        if(isset($style['font_size'])) $draw->setFontSize($style['font_size']);
      if(isset($style['fill_color'])) $draw->setFillColor($style['fill_color']);
      if(isset($style['under_color'])) $draw->setTextUnderColor($style['under_color']);
       
      if($this->type=='gif')
      {
          foreach($this->image as $frame)
          {
              $frame->annotateImage($draw, $x, $y, $angle, $text);
          }
      }
      else
      {
          $this->image->annotateImage($draw, $x, $y, $angle, $text);
      }
  }
   
   
  // 保存到指定路径
  public function save_to( $path )
  {
      if($this->type=='gif')
      {
          $this->image->writeImages($path, true);
      }
      else
      {
          $this->image->writeImage($path);
      }
  }
 
  // 输出图像
  public function output($header = true)
  {
      if($header) header('Content-type: '.$this->type);
      echo $this->image->getImagesBlob();   
  }
 
   
  public function get_width()
  {
        $size = $this->image->getImagePage(); 
        return $size['width'];
  }
   
  public function get_height()
  {
      $size = $this->image->getImagePage(); 
        return $size['height'];
  }
 
  // 设置图像类型， 默认与源类型一致
  public function set_type( $type='png' )
  {
      $this->type = $type;
        $this->image->setImageFormat( $type );
  }
 
  // 获取源图像类型
  public function get_type()
  {
    return $this->type;
  }
 
 
  // 当前对象是否为图片
  public function is_image()
  {
    if( $this->image )
      return true;
    else
      return false;
  }
   
 
 
  public function thumbnail($width = 100, $height = 100, $fit = true){ $this->image->thumbnailImage( $width, $height, $fit );} // 生成缩略图 $fit为真时将保持比例并在安全框 $width X $height 内生成缩略图片
 
  /*
  添加一个边框
  $width: 左右边框宽度
  $height: 上下边框宽度
  $color: 颜色: RGB 颜色 'rgb(255,0,0)' 或 16进制颜色 '#FF0000' 或颜色单词 'white'/'red'...
  */
  public function border($width, $height, $color='rgb(220, 220, 220)')
  {
    $color=new ImagickPixel();
    $color->setColor($color);
    $this->image->borderImage($color, $width, $height);
  }
   
  public function blur($radius, $sigma){$this->image->blurImage($radius, $sigma);} // 模糊
  public function gaussian_blur($radius, $sigma){$this->image->gaussianBlurImage($radius, $sigma);} // 高斯模糊
  public function motion_blur($radius, $sigma, $angle){$this->image->motionBlurImage($radius, $sigma, $angle);} // 运动模糊
  public function radial_blur($radius){$this->image->radialBlurImage($radius);} // 径向模糊
 
  public function add_noise($type=null){$this->image->addNoiseImage($type==null?imagick::NOISE_IMPULSE:$type);} // 添加噪点
   
  public function level($black_point, $gamma, $white_point){$this->image->levelImage($black_point, $gamma, $white_point);} // 调整色阶
  public function modulate($brightness, $saturation, $hue){$this->image->modulateImage($brightness, $saturation, $hue);} // 调整亮度、饱和度、色调
 
  public function charcoal($radius, $sigma){$this->image->charcoalImage($radius, $sigma);} // 素描
  public function oil_paint($radius){$this->image->oilPaintImage($radius);} // 油画效果
   
  public function flop(){$this->image->flopImage();} // 水平翻转
  public function flip(){$this->image->flipImage();} // 垂直翻转
 
}