<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_singcere_poll_seltype` (
  `stid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`stid`)
) ENGINE=MyISAM;


CREATE TABLE IF NOT EXISTS `pre_singcere_poll_attachment` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) DEFAULT NULL,
  `attachment` varchar(255) NOT NULL DEFAULT '',
  `remote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isimage` tinyint(1) NOT NULL DEFAULT '0',
  `width` smallint(6) unsigned NOT NULL DEFAULT '0',
  `thumb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `troduce` varchar(255) NOT NULL,
  `display` int(2) NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM;
        
CREATE TABLE IF NOT EXISTS `pre_singcere_poll_theme` (
  `pid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `troduce` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `times` int(2) NOT NULL,
  `nologin` tinyint(1) NOT NULL,
  `begin` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `dateline` int(11) NOT NULL,
  `uid` mediumint(8) NOT NULL,
  `period` int(3) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `muti` tinyint(1) NOT NULL,
  `fid` int(3) NOT NULL,
  `canmark` tinyint(1) NOT NULL,
  `color` int(1) NOT NULL,
  `keywords` varchar(60) NOT NULL,
  `descript` varchar(255) NOT NULL,
  `title` varchar(60) NOT NULL,
  `blank` tinyint(1) NOT NULL,
  `mark` tinyint(1) NOT NULL,
  `juli` int(4) NOT NULL,
  `nvtype` int(1) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM;
        
CREATE TABLE IF NOT EXISTS `pre_singcere_poll_selitem` (
  `sid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `tid` mediumint(8) NOT NULL,
  `troduce` text NOT NULL,
  `stid` mediumint(8) NOT NULL,
  `pid` mediumint(8) NOT NULL,
  `dateline` int(11) NOT NULL,
  `pnum` int(6) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM ;
        
  CREATE TABLE IF NOT EXISTS `pre_singcere_poll_recorder` (
  `rid` int(8) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(8) NOT NULL,
  `sid` mediumint(8) NOT NULL,
  `dateline` int(11) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `uid` mediumint(8) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM;
        
CREATE TABLE IF NOT EXISTS `pre_singcere_poll_remark` (
  `pid` mediumint(9) NOT NULL,
  `uid` mediumint(9) NOT NULL,
  `dateline` int(10) NOT NULL,
  `message` text NOT NULL,
  `rid` mediumint(9) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `hot` tinyint(1) NOT NULL,
  `hdateline` int(11) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_singcere_poll_nav` (
  `hid` mediumint(8) NOT NULL,
  `nav1` varchar(30) NOT NULL,
  `nav2` varchar(30) NOT NULL,
  `nav3` varchar(30) NOT NULL,
  `nav4` varchar(30) NOT NULL,
  `p_nav` varchar(30) NOT NULL,
  `c_nav` varchar(30) NOT NULL
) ENGINE=MyISAM;
EOF;
runquery($sql);

$finish = TRUE;
?>