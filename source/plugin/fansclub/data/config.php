<?php
//plugin mall config check file, DO NOT modify me!
//Identify: c504d7de00c9d6ccd29b971c72c8bf18

return array (
	'version' => 'Ver 1.0.0',
	
	// 申请球迷会状态
	'apply_status' => array('0' => '新建',
							'1' => '附议中',
							'2' => '附议完成',
							'3' => '审核通过',
							'4' => '审核不通过'),
	
	// 球迷会等级(默认名称)
	'group_level' => array('1' => '铁靴',
						   '2' => '铜靴',
						   '3' => '银靴',
						   '4' => '金靴',
						   '5' => '钻靴'),
	
	// 两个特殊的等级
	'group_level_special' => array('0' => '机构认证',
								   '1' => '5U认证'),
	
	// 球迷会会员头衔(等级)
	'group_user_level' => array('0' => '待审核',
								'1' => '群主',
								'2' => '副群主',
								'3' => '明星成员',
								'4' => '普通成员',
								'6' => '理事'),
	
	// 用户管理员等级，要多修改 ./source/function/function_group.php 的 update_groupmoderators
	'group_user_level_moderator' => array('1',
										  '2',
										  '6'),
	
	// 暂定两个会员权限
	'group_user_right' => array('allowmember' => '允许审核会员',
								'settitle' => '设置下级头衔'),
	
	// 事件类型(大事记用)
	'event_type' => array('1'  => '用户申请',
						  '2'  => '用户附议',
						  '3'  => '附议完成并创建',
						  '4'  => '审核通过',
						  '5'  => '第一次访问',
						  '6'  => '会员加入',
						  '7'  => '会员退出',
						  '8'  => '成员变更',
						  '9'  => '球迷会升级',
						  '10' => '球迷会降级',
						  '11' => '会员发主题',
						  '12' => '会员发帖子',
						  '13' => '会员充值',
						  '14' => '机构认证',
						  '15' => '5U认证',
						  '16' => '球迷会转让',
						  '17' => '友情球迷会',
						  '98' => '球迷会解散'),
	// 联赛ID对应名					  
	'arr_league' => array('1' => '英超',
						  '15' => '西甲',
						  '13' => '中超',
						  '41' => '亚冠',
						  '21' => '意甲',
						  '2' => '德甲',
						  '3' => '法甲',
						  '7' => 'NBA',
						  '8' => 'CBA',
						  '100' => '其它'),
);


