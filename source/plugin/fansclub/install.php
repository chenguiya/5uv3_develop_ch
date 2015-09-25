<?php
/*
-- 触发器 开始 --
-- 大事记1用户申请
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_1 AFTER INSERT ON pre_plugin_fansclub_apply_log
	FOR EACH ROW 
	BEGIN
	insert into pre_plugin_fansclub_event_log (`fid`,`type`,`operator_id`,`log_time`,`ip`,`remark`,`relation_id`,`relation_amount`)
	select 0, 1, new.uid, new.log_time, new.ip, CONCAT(new.username,'|', new.credit_type,'|',new.fansclub_name), new.apply_id, new.credit_num;
	END;
|
DELIMITER;

-- 大事记2用户附议、3附议完成并创建
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_2_3 AFTER INSERT ON pre_plugin_fansclub_apply_support
	FOR EACH ROW 
	BEGIN
		INSERT INTO pre_plugin_fansclub_event_log (`fid`,`type`,`operator_id`,`log_time`,`ip`,`remark`,`relation_id`)
		SELECT 0, 2, new.uid, new.support_time, new.ip, new.username, new.apply_id;
		
		-- 如果附议完成，记录type3
		SELECT need_support INTO @need_support FROM pre_plugin_fansclub_apply_log WHERE apply_id = new.apply_id;
		SELECT COUNT(support_id) INTO @have_support FROM pre_plugin_fansclub_apply_support WHERE apply_id = new.apply_id;
		
		IF @have_support >= @need_support THEN
			insert into pre_plugin_fansclub_event_log (`fid`,`type`,`operator_id`,`log_time`,`ip`,`remark`,`relation_id`)
			select 0, 3, new.uid, new.support_time, new.ip, new.username, new.apply_id;
		END IF;
	END;
|
DELIMITER;

-- 大事记4球类会通过审核、5第一次访问,9球迷会升级，10球迷会降级
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_4_5_9_10 AFTER UPDATE ON pre_forum_forum
	FOR EACH ROW 
	BEGIN
		-- SELECT SUBSTRING_INDEX(host, ':', 1) as 'ip' into @ip FROM information_schema.processlist WHERE ID = connection_id();
		-- SELECT REVERSE(SUBSTRING_INDEX(REVERSE(USER()),'@',1)) as ip into @ip;
		-- SELECT SUBSTRING(USER(), LOCATE('@', USER())+1) as ip into @ip;
		
		SET @ip = '';
		SET @province = '';
    SET @city = '';
		SET @relation_name = '';
		IF new.level = '0' && old.level = '-1' && new.status = '3' THEN
			-- 通过申请人ID和群组名称，查申请ID
			select founderuid into @founderuid from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
			
			select apply_id, relation_fid, league_id, club_id, star_id, province_id, city_id,district_id,community_id,
				fansclub_logo,fansclub_brief,fansclub_rules into 
				@apply_id, @relation_fid, @league_id, @club_id, @star_id, @province_id, @city_id, @district_id, @community_id,
				@fansclub_logo, @fansclub_brief, @fansclub_rules
			from pre_plugin_fansclub_apply_log where uid = @founderuid and fansclub_name = new.name LIMIT 0, 1;
				
			insert into pre_plugin_fansclub_event_log (`fid`,`type`,`operator_id`,`log_time`,`ip`,`remark`,`relation_id`)
			select new.fid, 4, 0, UNIX_TIMESTAMP(), @ip, '', @apply_id;
			-- 更新大事记的记录
			update pre_plugin_fansclub_event_log set fid = new.fid where relation_id = @apply_id;
			
			-- 插入球迷会信息记录
			IF @province_id <> '' THEN
				select `name` into @province from pre_common_district where id = @province_id;
			END IF;

			IF @city_id <> '' THEN
				select `name` into @city from pre_common_district where id = @city_id;
			END IF;

			IF @relation_fid <> '' THEN
				select `name` into @relation_name from pre_forum_forum where fid = @relation_fid;
			END IF;

			insert into pre_plugin_fansclub_info (`fid`,`fup`,`name`,`relation_fid`,`relation_name`,`province_city`,`league_id`,`club_id`,
			`star_id`,`province_id`,`city_id`,`district_id`,`community_id`,`logo`,`brief`,`rules`)
			select new.fid, new.fup, new.name, @relation_fid, @relation_name, CONCAT(@province, ' ', @city),@league_id,@club_id,
			@star_id, @province_id, @city_id, @district_id, @community_id, @fansclub_logo, @fansclub_brief, @fansclub_rules;

			-- 图标更新
			update pre_forum_forumfield set icon = @fansclub_logo where fid = new.fid;
		END IF;
		
		-- 第一次访问
		IF new.level = '1' && old.level = '0' && new.status = '3' THEN
			insert into pre_plugin_fansclub_event_log (`fid`,`type`,`operator_id`,`log_time`, `ip`)
			select new.fid, 5, 0, UNIX_TIMESTAMP(), @ip;
		END IF;
		
		IF new.level > '0' && old.level > '0' && new.level != old.level && new.status = '3' THEN
			SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.fid LIMIT 0, 1;
			SELECT membernum into @members from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
			select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.fid LIMIT 0, 1;
			select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
			
			if new.level > old.level then
			insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`,
				contribution, balance, members, level, commoncredits, threads, posts, rank)
			select new.fid, 9, 0, UNIX_TIMESTAMP(), '', CONCAT(new.name,'|',old.level,'|',new.level), new.fid,
				@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank;
			end if;
			
			if old.level > new.level then
			insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`,
				contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
			select new.fid, 10, 0, UNIX_TIMESTAMP(), '', CONCAT(new.name,'|',old.level,'|',new.level), new.fid,
				@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
			end if;
		END IF;
		
	END;
|
DELIMITER;

-- 大事记6会员加入(审核通过)8成员变更
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_6_8 AFTER UPDATE ON pre_forum_groupuser
	FOR EACH ROW 
	BEGIN
		IF new.level = '4' && old.level = '0' THEN
			-- 加入后的各种数值
			SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.fid LIMIT 0, 1;
			SELECT membernum + 1 into @members from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
			select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.fid LIMIT 0, 1;
			select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
			
			insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`,
				contribution, balance, members, level, commoncredits, threads, posts, rank)
			select new.fid, 6, 0, UNIX_TIMESTAMP(), '', CONCAT(new.username,'|',new.joindateline), new.uid,
				@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank;
		END IF;
		IF new.level > '0' && old.level > '0' && old.level != new.level THEN
			SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.fid LIMIT 0, 1;
			SELECT membernum into @members from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
			select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.fid LIMIT 0, 1;
			select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
			
			insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`,
				contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
			select new.fid, 8, 0, UNIX_TIMESTAMP(), '', CONCAT(new.username,'|',old.level,'|',new.level), new.uid,
				@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
		END IF;
	END;
|
DELIMITER;

-- 大事记7会员退出
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_7 BEFORE delete ON pre_forum_groupuser
	FOR EACH ROW 
	BEGIN
		-- 加入后的各种数值
		SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = old.fid LIMIT 0, 1;
		SELECT membernum - 1 into @members from pre_forum_forumfield where fid = old.fid LIMIT 0, 1;
		select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = old.fid LIMIT 0, 1;
		select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
		
		insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`,
			contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
		select old.fid, 7, 0, UNIX_TIMESTAMP(), '', CONCAT(old.username,'|',UNIX_TIMESTAMP()), old.uid,
			@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
	END;
|
DELIMITER;

-- 大事记11会员发主题
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_11 AFTER INSERT ON pre_forum_thread
	FOR EACH ROW 
	BEGIN
		-- 加入后的各种数值
		SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.fid LIMIT 0, 1;
		SELECT membernum into @members from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
		select level, commoncredits, threads+1, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.fid LIMIT 0, 1;
		select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
		
		insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`,
			contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
		select new.fid, 11, new.authorid, UNIX_TIMESTAMP(), '', CONCAT(new.author,'|',new.subject), new.tid,
			@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
	END;
|
DELIMITER;

-- 大事记12会员发帖子
-- 2015-06-08 多加记录精华帖子
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_12 AFTER INSERT ON pre_forum_post
	FOR EACH ROW 
	BEGIN
		-- 加入后的各种数值
		SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.fid LIMIT 0, 1;
		SELECT membernum into @members from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
		select level, commoncredits, threads, posts+1, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.fid LIMIT 0, 1;
		select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
		
		IF new.first = '1' THEN
			SET @threads = @threads + 1;
		END IF;
		
		insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`,
			contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
		select new.fid, 12, new.authorid, UNIX_TIMESTAMP(), '', CONCAT(new.author,'|',new.tid), new.pid,
			@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
	END;
|
DELIMITER;

-- 大事记13会员充值
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_13 AFTER UPDATE ON pre_plugin_ucharge_log
	FOR EACH ROW 
	BEGIN
		-- 先查new.uid所在的fid
		SELECT fid into @fid from pre_forum_groupuser WHERE uid = new.uid LIMIT 0, 1;
		
		-- 加入后的各种数值
		SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.fid LIMIT 0, 1;
		SELECT membernum into @members from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
		select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.fid LIMIT 0, 1;
		select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
		
		insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`, `relation_amount`,
			contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
		select @fid, 13, new.uid, UNIX_TIMESTAMP(), new.ip, CONCAT(new.fid,'|',new.orderid,'|',new.trade_no), new.log_id, new.amount*new.price,
			@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
	END;
|
DELIMITER;

-- 大事记98删除群组
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_98 BEFORE delete ON pre_forum_forum
	FOR EACH ROW 
	BEGIN
		if old.status = '3' then
		-- 加入后的各种数值
		SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = old.fid LIMIT 0, 1;
		SELECT membernum into @members from pre_forum_forumfield where fid = old.fid LIMIT 0, 1;
		select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = old.fid LIMIT 0, 1;
		select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
		
		insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`,
			contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
		select old.fid, 98, 0, UNIX_TIMESTAMP(), '', CONCAT(old.name), 0,
			@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
		end if;
	END;
|
DELIMITER;

-- 14_15 5u认证，机构认证通过和取消
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_14_15 AFTER UPDATE ON pre_plugin_fansclub_level_apply_log
	FOR EACH ROW 
	BEGIN
		-- 加入后的各种数值
		SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.fid LIMIT 0, 1;
		SELECT membernum into @members from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
		select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.fid LIMIT 0, 1;
		select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
		
		SET @type = 0;
		
		-- 机构认证
		if new.level_type = '0' then
			SET @type = 14;
		end if;
		
		-- 5u认证
		if new.level_type = '1' then
			SET @type = 15;
		end if;
		
		insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`, `relation_amount`,
			contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
		select new.fid, @type, new.uid, UNIX_TIMESTAMP(), new.ip, CONCAT(new.fid,'|',new.relation_fid,'|',new.status,'|',new.active_month,'|',new.expired_time), new.apply_id, 0,
			@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
	END;
|
DELIMITER;


-- 16友情球迷会
-- 这个表小麦负责
CREATE TABLE `pre_plugin_grouplink` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `linkgid` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DELIMITER |
CREATE TRIGGER t_fansclub_event_type_16 AFTER UPDATE ON pre_plugin_grouplink
	FOR EACH ROW 
	BEGIN
		if old.status = '0' && new.status = '1' then
		-- 加入后的各种数值
		SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.gid LIMIT 0, 1;
		SELECT membernum into @members from pre_forum_forumfield where fid = new.gid LIMIT 0, 1;
		select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.gid LIMIT 0, 1;
		select count(tid) into @digests from pre_forum_thread where fid = new.gid AND digest = 1 LIMIT 0, 1;
		
		insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`, `relation_amount`,
			contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
		select new.gid, 16, 0, UNIX_TIMESTAMP(), '', CONCAT(new.gid,'|',new.linkgid,'|',new.status,'|',new.createtime), new.id, 0,
			@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
		end if;
	END;
|
DELIMITER;

-- 17球迷会转让
DELIMITER |
CREATE TRIGGER t_fansclub_event_type_17 AFTER UPDATE ON pre_forum_forumfield
	FOR EACH ROW 
	BEGIN
		if old.founderuid != new.founderuid then
		SELECT extendcredits1, extendcredits3 into @balance, @contribution from pre_plugin_fansclub_balance where relation_fid = new.fid LIMIT 0, 1;
		SELECT membernum into @members from pre_forum_forumfield where fid = new.fid LIMIT 0, 1;
		select level, commoncredits, threads, posts, rank into @level, @commoncredits, @threads, @posts, @rank from pre_forum_forum where fid = new.fid LIMIT 0, 1;
		select count(tid) into @digests from pre_forum_thread where fid = new.fid AND digest = 1 LIMIT 0, 1;
		
		insert into pre_plugin_fansclub_event_log (`fid`, `type`, `operator_id`, `log_time`, `ip`, `remark`, `relation_id`, `relation_amount`,
			contribution, balance, members, level, commoncredits, threads, posts, rank, digests)
		select new.fid, 17, 0, UNIX_TIMESTAMP(), old.founderuid, CONCAT(old.founderuid,'|',old.foundername,'|',new.founderuid,'|',new.foundername), new.founderuid, 0,
			@contribution, @balance, @members, @level, @commoncredits, @threads, @posts, @rank, @digests;
		end if;
	END;
|
DELIMITER;

pre_forum_forumfield


-- 触发器 结束 --

-- 建表 开始 --

pre_forum_groupuser
pre_forum_grouplevel

group_level_normal
group_level_special
group_active_time
user_level

-- 手机短信记录表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_sms_log` (
  `id` bigint(15) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) NOT NULL,
  `posttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提交时间',
  `content` varchar(300) NOT NULL COMMENT '短信内容',
  `return_msg` varchar(30) NOT NULL COMMENT '网关返回',
  `ip` varchar(60) NOT NULL COMMENT '用户IP',
  `user_name` varchar(10) NOT NULL COMMENT '网关账号',
  `money` int(10) NOT NULL COMMENT '网关账号短信剩余条数',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`,`posttime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='手机短信记录表';

-- 用户手机表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_mobile` (
`uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
`username` char(32) NOT NULL DEFAULT '' COMMENT '用户名',
`mobile` char(32) NOT NULL DEFAULT '' COMMENT '手机号码',
PRIMARY KEY (`uid`),
UNIQUE KEY `mobile` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户手机表';

-- 视频帖子截图表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_videoscreenshot` (
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '帖子id',
  `fid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '论坛id',
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '主题id',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  `pic_path` char(100) NOT NULL COMMENT '图片路径',
  `video_url` char(200) NOT NULL COMMENT '视频URL',
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='视频帖子截图表';

-- 球迷会信息表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_info` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fup` mediumint(8) NOT NULL COMMENT '上级群组ID',
  `name` char(50) NOT NULL COMMENT '球迷会名称',
  `relation_fid` mediumint(8) NOT NULL COMMENT '关联版块ID',
  `relation_name` char(30) NOT NULL COMMENT '关联版块名称',
  `province_city` char(100) NOT NULL COMMENT '省市名称',
  `league_id` mediumint(8) NOT NULL COMMENT '联赛ID',
  `club_id` mediumint(8) NOT NULL COMMENT '俱乐部ID',
  `star_id` mediumint(8) NOT NULL COMMENT '球星ID',
  `province_id` mediumint(8) NOT NULL COMMENT '省ID',
  `city_id` mediumint(8) NOT NULL COMMENT '市ID',
  `district_id` mediumint(8) NOT NULL COMMENT '区ID',
  `community_id` mediumint(8) NOT NULL COMMENT '街ID',
  `logo` char(255) NOT NULL DEFAULT '' COMMENT '申请时LOGO',
  `brief` char(255) NOT NULL COMMENT '简介',
  `rules` text COMMENT '管理章程',
  PRIMARY KEY (`fid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球迷会信息表';

-- 2015-08-05 zhangjh 增加三个字段
-- 测试数据库
alter table pre_plugin_fansclub_info add members smallint(8) default '0' COMMENT '用户数';
alter table pre_plugin_fansclub_info add contribution smallint(8) default '0' COMMENT '贡献值';
alter table pre_plugin_fansclub_info add posts smallint(8) default '0' COMMENT '帖子数';
alter table pre_plugin_fansclub_info add `level` smallint(6) default '0' COMMENT '群组等级';

-- 定时更新，减少联表查询
update pre_plugin_fansclub_info a, pre_plugin_fansclub_balance b set a.contribution = b.extendcredits3 where a.fid = b.relation_fid;
update pre_plugin_fansclub_info a, pre_forum_forum b set a.posts = b.posts,a.level = b.level where a.fid = b.fid;
update pre_plugin_fansclub_info a, pre_forum_forumfield b set a.members = b.membernum where a.fid = b.fid;


-- 球类会设置扩展表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_setting_ex` (
  `ex_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL COMMENT '英文名称',
  `title` char(30) NOT NULL COMMENT '中文标题',
  `policy` text COMMENT '设置策略',
  PRIMARY KEY (`ex_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球类会设置扩展表';

-- 初始化一些信息
INSERT INTO pre_plugin_fansclub_setting_ex (`name`, `title`) value ('group_level_normal', '普通等级设置');
INSERT INTO pre_plugin_fansclub_setting_ex (`name`, `title`) value ('group_level_special', '特殊等级设置');
INSERT INTO pre_plugin_fansclub_setting_ex (`name`, `title`) value ('group_active_time', '有效时间设置');
INSERT INTO pre_plugin_fansclub_setting_ex (`name`, `title`) value ('user_level', '会员等级设置');

-- 球类会事件记录表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_event_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(10) unsigned NOT NULL COMMENT '球类会ID，即pre_forum_forum.fid',
  `type` tinyint(2) NOT NULL COMMENT '事件类型，1用户申请，2用户附议，3附议完成并创建，4审核通过，5第一次访问，6会员加入，7会员退出，8成员变更，9球迷会升级，10球迷会降级，11会员发主题，12会员发帖子，13会员充值，...98解散',
  `operator_id` int(10) unsigned NOT NULL COMMENT '操作人ID',
  `log_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志时间',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `remark` char(255) NOT NULL DEFAULT '' COMMENT '备注',
  `relation_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '相关ID',
  `relation_amount` float(9,2) unsigned NOT NULL DEFAULT '0' COMMENT '相关数值',
  `contribution` float(9,2) unsigned DEFAULT '0' COMMENT '贡献值',
  `balance` float(9,2) unsigned DEFAULT '0' COMMENT '钱包余额',
  `members` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '球类会成员数',
  `level` smallint(6) DEFAULT '0' COMMENT '群组等级',
  `commoncredits` int(11) unsigned DEFAULT '0' COMMENT '群组公共积分',
  `threads` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '球类会主题数',
  `posts` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '球类会帖子数',
  `rank` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排名',
  `digests` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '精华帖子数',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球类会事件记录表';

alter table pre_plugin_fansclub_event_log modify level smallint(6) DEFAULT '0' COMMENT '群组等级';
alter table pre_plugin_fansclub_event_log add digests mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '精华帖子数';

-- 球类会钱包(余额、贡献值表)（大明要修改）
CREATE TABLE `pre_plugin_fansclub_balance` (
  `balance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `relation_fid` int(10) unsigned NOT NULL COMMENT '相关ID，pre_forum_forum.fid',
  `extendcredits1` float(9,2) NOT NULL COMMENT '余额',
  `extendcredits2` float(9,2) NOT NULL,
  `extendcredits3` float(9,2) NOT NULL COMMENT '贡献值',
  `extendcredits4` float(9,2) NOT NULL,
  `extendcredits5` float(9,2) NOT NULL,
  `extendcredits6` float(9,2) NOT NULL,
  PRIMARY KEY (`balance_id`),
  UNIQUE KEY `balance_type_relation` (`relation_fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='球类会钱包(余额、贡献值表)';

-- 球类会钱包日志表（大明要修改）
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_balance_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `balance_id` int(10) unsigned NOT NULL COMMENT '球类会余额ID',
  `type` tinyint(2) NOT NULL COMMENT '变动类型，1充值...',
  `amount` float(9,2) NOT NULL COMMENT '资金变动数目',
  `balance` float(9,2) NOT NULL COMMENT '变动后数目',
  `log_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日志时间',
  `relation_log_id` int(11) unsigned COMMENT '相关单号ID',
  `remark` char(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='球类会钱包日志表';

-- 球迷会申请记录表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_apply_log` (
  `apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL COMMENT '申请人ID',
  `username` char(32) NOT NULL COMMENT '申请人账号',
  `log_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请状态：0新建，1附议中，2附议完成，3审核通过，4审核不通过',
  `need_support` mediumint(8) NOT NULL DEFAULT '0' COMMENT '需要附议人数',
  `have_support` mediumint(8) NOT NULL DEFAULT '0' COMMENT '已经附议人数',
  `confirm_uid` int(10) NOT NULL DEFAULT '0' COMMENT '后台审核人ID',
  `confirm_time` int(10) unsigned DEFAULT NULL COMMENT '后台审核时间',
  `fansclub_name` char(50) NOT NULL DEFAULT '' COMMENT '球迷会名称',
  `relation_fid` mediumint(8) NOT NULL DEFAULT '0' COMMENT '关联版块ID，club_id或star_id的值',
  `league_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '联赛ID',
  `club_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '俱乐部ID',
  `star_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '球星ID',
  `range_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '范围ID，province_id、city_id、district_id或community_id的值',
  `province_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '省ID',
  `city_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '市ID',
  `district_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '区ID',
  `community_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '街ID',
  `fansclub_logo` char(255) NOT NULL DEFAULT '' COMMENT '球迷会LOGO',
  `fansclub_brief` char(255) NOT NULL DEFAULT '' COMMENT '球迷会简介',
  `fansclub_rules` text COMMENT '球迷会管理章程',
  `mobile` char(50) NOT NULL DEFAULT '' COMMENT '手机',
  `qq` char(50) NOT NULL DEFAULT '' COMMENT 'QQ',
  `email` char(50) NOT NULL DEFAULT '' COMMENT 'Email',
  `credit_type` char(32) DEFAULT '' COMMENT '需要积分类型，extcredits2等',
  `credit_num` mediumint(8) DEFAULT '0' COMMENT '需要积分数目',
  `credit_unit` char(8) DEFAULT '' COMMENT '需要积分单位',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `fid` mediumint(8) NOT NULL DEFAULT '0' COMMENT '申请成功后群组的fid',
  PRIMARY KEY (`apply_id`),
  UNIQUE KEY `fansclub_name` (`fansclub_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球迷会申请记录表';

-- alter table pre_plugin_fansclub_apply_log add ip char(15) NOT NULL DEFAULT '' COMMENT '操作IP';
-- alter table pre_plugin_fansclub_apply_log add fid mediumint(8) NOT NULL DEFAULT '0' COMMENT '申请成功后群组的fid';

-- 球迷会申请附议表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_apply_support` (
  `support_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) unsigned NOT NULL COMMENT '申请ID',
  `uid` int(10) NOT NULL COMMENT '附议人ID',
  `username` char(32) NOT NULL COMMENT '附议人账号',
  `support_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '附议时间',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  PRIMARY KEY (`support_id`),
  UNIQUE KEY `apply_id_uid` (`apply_id`, `uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球迷会申请附议表';

-- 球迷会等级认证申请表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_level_apply_log` (
  `apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '等级类型0机构认证，1官方认证',
  `fid` mediumint(8) NOT NULL DEFAULT '0' COMMENT '群组的fid',
  `relation_fid` mediumint(8) NOT NULL DEFAULT '0' COMMENT '关联版块ID，club_id或star_id的值',
  `uid` int(10) NOT NULL COMMENT '申请人ID',
  `username` char(32) NOT NULL COMMENT '申请人账号',
  `log_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `verify_pic` char(255) NOT NULL DEFAULT '' COMMENT '机构认证图片',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '申请IP',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请状态：0新建，3审核通过，4审核不通过，5认证过期',
  `confirm_uid` int(10) NOT NULL DEFAULT '0' COMMENT '后台审核人ID',
  `confirm_time` int(10) unsigned DEFAULT '0' COMMENT '后台审核时间',
  `confirm_remark` char(255) NOT NULL DEFAULT '' COMMENT '备注',
  `active_month` int(3) unsigned DEFAULT '0' COMMENT '有效月份',
  `expired_time` int(10) unsigned DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`apply_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球迷会等级认证申请表';

-- 文章转移记录表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_old_article_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` int(10) unsigned NOT NULL COMMENT '',
  `old_catid` int(10) unsigned NOT NULL COMMENT '',
  `old_url` char(255) NOT NULL COMMENT '',
  `old_detail` text COMMENT '详细记录',
  `title` char(255) NOT NULL COMMENT '',
  `thumb` char(255) NOT NULL COMMENT '',
  `keywords` char(255) NOT NULL COMMENT '',
  `type` tinyint(2) unsigned NOT NULL COMMENT '类型 1新闻 2图片 3视频',
  `new_tid` int(10) unsigned NOT NULL COMMENT '',
  `new_fid` int(10) unsigned NOT NULL COMMENT '',
  `new_detail` text COMMENT '新详细记录',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章转移记录表';

-- 球迷会api日志记录表
CREATE TABLE IF NOT EXISTS `pre_plugin_fansclub_api_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_type` char(32) NOT NULL DEFAULT '' COMMENT '类型eg.tuisong',
  `log_time` int(10) unsigned DEFAULT '0' COMMENT '时间',
  `log_text` char(255) NOT NULL DEFAULT '' COMMENT '内容',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='球迷会api日志记录表';
-- 建表 结束 --
*/