<?php
if(!defined('IN_ADMINCP')) exit('Access Denied');
$sql = <<<EOF
DROP TABLE IF EXISTS pre_plugin_k_misign;
CREATE TABLE IF NOT EXISTS pre_plugin_k_misign (
  uid int(10) unsigned NOT NULL,
  `time` int(10) NOT NULL,
  days int(5) NOT NULL DEFAULT '0',
  lasted int(5) NOT NULL DEFAULT '0',
  mdays int(5) NOT NULL DEFAULT '0',
  reward int(12) NOT NULL DEFAULT '0',
  lastreward int(12) NOT NULL DEFAULT '0',
  `row` int(11) NOT NULL,
  PRIMARY KEY (uid),
  KEY `time` (`time`),
  KEY `row` (`row`)
) ENGINE=MyISAM;
DROP TABLE IF EXISTS pre_plugin_k_misignset;
CREATE TABLE IF NOT EXISTS pre_plugin_k_misignset (
  id int(10) unsigned NOT NULL,
  todayq int(10) NOT NULL DEFAULT '0',
  yesterdayq int(10) NOT NULL DEFAULT '0',
  highestq int(10) NOT NULL DEFAULT '0',
  qdtidnumber int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
INSERT INTO pre_plugin_k_misignset (id, todayq, yesterdayq, highestq, qdtidnumber) VALUES ('1', '0', '0', '0', '0');

EOF;
runquery($sql);
updatecache('setting');
$finish = TRUE;
?>