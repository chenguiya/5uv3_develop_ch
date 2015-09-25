<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

header("Access-Control-Allow-Origin:*"); // 允许AJAX跨域

// 操作用户中心的接口
include_once DISCUZ_ROOT.'./source/function/function_member.php';
include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';   // 公共函数
include_once DISCUZ_ROOT.'./source/plugin/fansclub/function/function_passport.php'; // 用户处理函数

/*
DB 修改
alter table group_ucenter_memberfields add column mobile varchar(30) NOT NULL DEFAULT '' COMMENT '手机号码';
alter table group_ucenter_memberfields add column birthday date DEFAULT NULL COMMENT '生日';
alter table group_ucenter_memberfields add column sex tinyint(1) unsigned DEFAULT '0' COMMENT '性别，1男2女0保密';
alter table group_ucenter_memberfields add column signiture varchar(255) DEFAULT '' COMMENT '个性签名';
alter table group_ucenter_memberfields add column address varchar(255) DEFAULT '' COMMENT '地址';
alter table group_ucenter_memberfields add column newuser tinyint(1) unsigned DEFAULT '0' COMMENT '是否新用户名(手机注册和邮箱注册和第三方注册可以修一次)';
alter table group_ucenter_memberfields add column userfrom varchar(50) DEFAULT '' COMMENT '注册来源';
alter table group_ucenter_memberfields add column openid varchar(45) NOT NULL DEFAULT '' COMMENT 'openId微信用户与公众号之间的唯一凭证';

// 修改昵称 http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=passport&op=modnick&from=weixin&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&newnick=5uczt1&time=1442977333&sign=0061524467ff5dd87b4b790a18cb71eb
// 直接登录 http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=passport&op=directlogin&from=weixin&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&redirect=&time=1442977333&sign=378db974410c3052d80ce9af81904496

// 用户注册(可不用) http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=passport&op=register&email=czt@163.com&password=123456aa&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&sign=8ead54dadc9b826e4ca856cd22f1b6a6
// 登录(可不用)     http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=passport&op=login&name=czt@163.com&password=123456aa&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&sign=382e8c61869ee37cfec36d41ca5bd4b7
*/


// echo "<pre>";
$login_success_url = $_G['siteurl'].'home.php?mod=space&do=profile&mycenter=1&mobile=2';

$arr_return = array('success' => FALSE, 'message' => 'init');

$arr_param = array();
$arr_param['email'] = trim($_GET['email']);
$arr_param['password'] = trim($_GET['password']);
$arr_param['openid'] = trim($_GET['openid']);
$arr_param['sign'] = trim($_GET['sign']);
$arr_param['name'] = trim(arr_to_utf8($_GET['name']));
$arr_param['from'] = trim($_GET['from']);
$arr_param['time'] = trim($_GET['time']);
$arr_param['redirect'] = trim(urldecode($_GET['redirect']));
$arr_param['newnick'] = trim(arr_to_utf8($_GET['newnick']));

$op = trim($_GET['op']);
    
$bln_check = passport_check_sign($_GET);
if($bln_check === TRUE)
{
    if($op == 'register') // 可不用
    {
        $data = array();
        $data['type'] = 1;
        $data['email'] = $arr_param['email'];
        $data['password'] = $arr_param['password'];
        $data['openid'] = $arr_param['openid'];
        $data['username'] = passport_get_default_username();
        $arr_check_result = passport_can_register($data);
        
        if($arr_check_result['success'] === TRUE)
        {
            $arr_register_result = passport_register($data);
            $arr_return = $arr_register_result;
        }
        else
        {
            $arr_return['message'] = $arr_check_result['message'];
        }
    }
    elseif($op == 'login') // 可不用
    {
        $data = array();
        $data['name'] = $arr_param['name'];
        $data['password'] = $arr_param['password'];
        $data['openid'] = $arr_param['openid'];
        
        $arr_login_result = passport_login($data);
        $arr_return['message'] = $arr_login_result['message'];
        if($arr_login_result['success'] === TRUE)
        {
            $arr_return['success'] = TRUE;
            $arr_return['login_success_url'] = $login_success_url;
        }
    }
    elseif($op == 'modnick')
    {
        $data = array();
        $data['from'] = $arr_param['from'];
        $data['openid'] = $arr_param['openid'];
        $data['time'] = $arr_param['time'];
        $data['newnick'] = $arr_param['newnick'];
        $arr_return = passport_modnick($data);
        
    }
    elseif($op == 'directlogin')
    {
        $data = array();
        $data['from'] = $arr_param['from'];
        $data['openid'] = $arr_param['openid'];
        $data['time'] = $arr_param['time'];
        $data['redirect'] = $arr_param['redirect'];
        
        $arr_return = passport_directlogin($data);
        
        if($arr_return['success'] === TRUE)
        {
            if($arr_return['redirect'] != '')
            {
                header('Location: '.$arr_return['redirect']);
                exit;
            }
        }
    }
    else
    {
        $arr_return['message'] = '操作类型错误';
    }
}
else
{
    $arr_return['message'] = 'sign检验错误';
}

// print_r($arr_return);
die(json_encode($arr_return));
