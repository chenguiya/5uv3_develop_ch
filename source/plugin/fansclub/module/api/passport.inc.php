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

// 直接登录 http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=passport&op=directlogin&from=weixin&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&wxhead=http%3A%2F%2Fwx.qlogo.cn%2Fmmopen%2Fg3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe%2F0&redirect=http%3A%2F%2Fzhangjh.usport.com.cn%2Fhome.php%3Fmod%3Dspace%26do%3Dprofile%26mycenter%3D1%26mobile%3D2&wxnick=admin&can_change_nickname=1&time=1442977333&sign=6f450ac6c604a2df4f2748c309eb5084

// 修改昵称(可不用) http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=passport&op=modnick&from=weixin&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&newnick=5uczt1&time=1442977333&sign=0061524467ff5dd87b4b790a18cb71eb
// 用户注册(可不用) http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=passport&op=register&email=czt@163.com&password=123456aa&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&sign=8ead54dadc9b826e4ca856cd22f1b6a6
// 登录(可不用)     http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=passport&op=login&name=czt@163.com&password=123456aa&openid=otiS-uNL79pw1lCLtR_zcQHSkuyU&sign=382e8c61869ee37cfec36d41ca5bd4b7
*/

// echo "<pre>";
$login_success_url = $_G['siteurl'].'home.php?mod=space&do=profile&mycenter=1&mobile=2';
$change_nickname_url = $_G['siteurl'].'home.php?mod=spacecp&ac=profile&op=password&sub=nickname&mobile=2';

$arr_return = array('success' => FALSE, 'message' => 'init');

$arr_param = array();
$arr_param['email'] = trim($_GET['email']);
$arr_param['password'] = trim($_GET['password']);
$arr_param['openid'] = trim($_GET['openid']);
$arr_param['sign'] = trim($_GET['sign']);
$arr_param['name'] = trim(arr_to_utf8($_GET['name']));
$arr_param['from'] = trim($_GET['from']);
$arr_param['time'] = trim($_GET['time']);
$arr_param['wxnick'] = trim(urldecode($_GET['wxnick']));
$arr_param['redirect'] = trim(urldecode($_GET['redirect']));
$arr_param['newnick'] = trim(arr_to_utf8($_GET['newnick']));
$arr_param['wxhead'] = trim(urldecode($_GET['wxhead']));
$arr_param['can_change_nickname'] = trim($_GET['can_change_nickname']);

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
    elseif($op == 'modnick') // 可不用
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
        $data['wxnick'] = $arr_param['wxnick'];
        $data['wxhead'] = $arr_param['wxhead'];
        $data['can_change_nickname'] = $arr_param['can_change_nickname'];
        
        $arr_return = passport_directlogin($data);
        
        if($arr_return['success'] === TRUE)
        {
            if($data['can_change_nickname'] == '1' && $arr_return['redirect'] != '') // 需要修改昵称
            {
                if($arr_return['redirect'] != '')
                {
                    $change_nickname_url .= '&redirect='.urlencode($arr_return['redirect']);
                }
                
                $change_nickname_url .= '&openid='.$arr_return['openid'].'&time='.$arr_return['time'].'&from='.$arr_return['from'];
                
                header('Location: '.$change_nickname_url);
                exit;
            }
            
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

die(json_encode($arr_return));
