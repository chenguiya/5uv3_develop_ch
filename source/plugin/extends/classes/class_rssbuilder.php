<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class rssbuilder {
	var $rss_ver = "2.0";
	var $channel_title = '';
	var $channel_link = '';
	var $channel_description = '';
	var $language = 'zh_CN';
	var $copyright = '';
	var $webMaster = '';
	var $pubDate = '';
	var $lastBuildDate = '';
	var $generator = 'RedFox RSS Generator';
	
	var $content = '';
	var $items = array();
	
	/**************************************************************************/
	// 函数名: RSS
	// 功能: 构造函数
	// 参数: $title
	// $link
	// $description
	/**************************************************************************/
	function RSS($title, $link, $description) {
		$this->channel_title = $title;
		$this->channel_link = $link;
		$this->channel_description = $description;
		$this->pubDate = Date('Y-m-d H:i:s',time());
		$this->lastBuildDate = Date('Y-m-d H:i:s',time());
	}
	/**************************************************************************/
	// 函数名: AddItem
	// 功能: 添加一个节点
	// 参数: $title
	// $link
	// $description   $pubDate
	/**************************************************************************/
	function AddItem($title, $link, $description, $author, $category, $pubDate) {
		$this->items[] = array('title' => $title ,
				'link' => $link,
				'description' => $description,
				'author' => $author,
				'category' => $category,
				'pubDate' => $pubDate);
	}
	/**************************************************************************/
	// 函数名: BuildRSS
	// 功能: 生成rss xml文件内容
	/**************************************************************************/
	function BuildRSS($tile, $url, $description) {
		header('Content-Type: text/xml');
		$s = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
\n<rss version=\"2.0\">\n";
		// start channel
		$s .= "<channel>\n";
		$s .= "<title>{$tile}</title>\n";
		$s .= "<link>{$url}</link>\n";
		$s .= "<description>{$description}</description>\n";
		$s .= "<language>{$this->language}</language>\n";
		if (!empty($this->copyright)) {
			$s .= "<copyright><![CDATA[{$this->copyright}]]></copyright>\n";
		}
		if (!empty($this->webMaster)) {
			$s .= "<webMaster><![CDATA[{$this->webMaster}]]></webMaster>\n";
		}
		if (!empty($this->pubDate)) {
			$s .= "<pubDate>{$this->pubDate}</pubDate>\n";
		}
	
		if (!empty($this->lastBuildDate)) {
			$s .= "<lastBuildDate>{$this->lastBuildDate}</lastBuildDate>\n";
		}
	
		if (!empty($this->generator)) {
			$s .= "<generator>{$this->generator}</generator>\n";
		}
	
		// start items
		for ($i=0;$i<count($this->items);$i++) {
			$s .= "<item>\n";
			$s .= "<title><![CDATA[{$this->items[$i]['title']}]]></title>\n";
			$s .= "<link><![CDATA[{$this->items[$i]['link']}]]></link>\n";
			$s .= "<description><![CDATA[{$this->items[$i]['description']}]]></description>\n";
			$s .= "<pubDate>{$this->items[$i]['pubDate']}</pubDate>\n";
			$s .= "</item>\n";
		}
	
		// close channel
		$s .= "</channel>\n</rss>";
		$this->content = $s;
	}
	/**************************************************************************/
	// 函数名: BuildRSS
	// 功能: 生成rss xml文件内容
	/**************************************************************************/
	function BuildRSSForZark($title, $url, $description) {
		header('Content-Type: text/xml');
		$s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n";
		// start channel
		$s .= "<channel>\n";
		$s .= "<title>{$title}</title>\n";
		$s .= "<link>{$url}</link>\n";
		$s .= "<description>{$description}</description>\n";
	
		// start items
		for ($i=0;$i<count($this->items);$i++) {
			$s .= "<item>\n";
			$s .= "<title>{$this->items[$i]['title']}</title>\n";
			$s .= "<link>{$this->items[$i]['link']}</link>\n";
			$s .= "<description><![CDATA[{$this->items[$i]['description']}]]></description>\n";
			$s .= "<author>{$this->items[$i]['author']}</author>\n";
			$s .= "<category>{$this->items[$i]['category']}</category>\n";
			$s .= "<pubDate>{$this->items[$i]['pubDate']}</pubDate>\n";
			$s .= "</item>\n";
		}
	
		// close channel
		$s .= "</channel>\n</rss>";
		$this->content = $s;
	}
	/**************************************************************************/
	// 函数名: BuildRSS
	// 功能: 生成rss xml文件内容
	/**************************************************************************/
	function BuildRSSForWyy($title, $url, $description) {
		header('Content-Type: text/xml');
		$title = 'U体育_24小时全球体坛情报中心';
		$url = 'http://www.usportnews.com/index.php?m=content&c=usportrss&a=wyy_rss';
		$description = 'U体育是国内专业的体育资讯门户网站，以体育情报为特色打造“24小时体坛情报中心”，搜罗全球最新、最劲爆的体育情报，为用户提供新闻资讯、传闻消息、赛事前瞻、赛情播报、直播视讯以及情报分析和比分推介等专业体育情报信息服务。';
		$s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\" xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\" xmlns:dcterms=\"http://purl.org/dc/terms/\">\n";
		// start channel
		$s .= "<channel>\n";
		$s .= "<title><![CDATA[$tile]]></title>\n";
		$s .= "<link>$url</link>\n";
		$s .= "<description><![CDATA[$description]]></description>\n";
		$s .= "<language>{$this->language}</language>\n";
		if (!empty($this->copyright)) {
			$s .= "<copyright><![CDATA[{$this->copyright}]]></copyright>\n";
		}
		if (!empty($this->webMaster)) {
			$s .= "<webMaster><![CDATA[{$this->webMaster}]]></webMaster>\n";
		}
		if (!empty($this->pubDate)) {
			$s .= "<pubDate>{$this->pubDate}</pubDate>\n";
		}
	
		if (!empty($this->lastBuildDate)) {
			$s .= "<lastBuildDate>{$this->lastBuildDate}</lastBuildDate>\n";
		}
	
		if (!empty($this->generator)) {
			$s .= "<generator>{$this->generator}</generator>\n";
		}
	
		// start items
		for ($i=0;$i<count($this->items);$i++) {
			$s .= "<item>\n";
			$s .= "<title><![CDATA[{$this->items[$i]['title']}]]></title>\n";
			$s .= "<link><![CDATA[{$this->items[$i]['link']}]]></link>\n";
			$s .= "<description><![CDATA[{$this->items[$i]['description']}]]></description>\n";
			$s .= "<pubDate>{$this->items[$i]['pubDate']}</pubDate>\n";
			$s .= "</item>\n";
		}
	
		// close channel
		$s .= "</channel>\n</rss>";
		$this->content = $s;
	}
	/**************************************************************************/
	// 函数名: Show
	// 功能: 将产生的rss内容直接打印输出
	/**************************************************************************/
	function Show() {
		if (empty($this->content)) $this->BuildRSS();
		echo($this->content);
	}
	/**************************************************************************/
	// 函数名: Show_wyy
	// 功能: 将产生的rss内容直接打印输出
	/**************************************************************************/
	function Show_wyy() {
		if (empty($this->content)) $this->BuildRSSForWyy();
		echo($this->content);
	}
	/**************************************************************************/
	// 函数名: SaveToFile
	// 功能: 将产生的rss内容保存到文件
	// 参数: $fname 要保存的文件名
	/**************************************************************************/
	function SaveToFile($fname) {
		$handle = fopen($fname, 'wb');
		if ($handle === false)   return false;
		fwrite($handle, $this->content);
		fclose($handle);
	}
}
?>