<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

loaducenter();

// 修改昵称
function passport_modnick($data)
{
    $arr_return = array('success' => FALSE, 'message' => '');
    
    $openid = $data['openid'];
    $time = $data['time'];
    $from = $data['from'];
    $newnick = $data['newnick'];
    
    $uid = uc_get_uid_by_openid($openid);
    
    if($uid > 0)
    {
        $user = uc_get_user($uid, 1);
        if(is_array($user))
        {
            $old_username = $user[1];
            
            $arr_param = array();
            $arr_param['username'] = $newnick;
            $arr_param['only_check_username'] = 1;
            
            $arr_check_result = passport_can_register($arr_param);
            
            if($arr_check_result['success'] === TRUE)
            {
                $arr_result = uc_renameuser($uid, $old_username, $newnick);
                if($arr_result['success'] === TRUE)
                {
                    $arr_return['success'] = TRUE;
                    $arr_return['message'] = '修改成功';
                }
                else
                {
                    $arr_return['message'] = $arr_result['message'];
                }
            }
            else
            {
                $arr_return = $arr_check_result;
            }
        }
        else
        {
            $arr_return['message'] = '账号记录有误2';
        }
    }
    else
    {
        $arr_return['message'] = '账号记录有误';
    }
    return $arr_return;
}

// 直接登录
function passport_directlogin($data)
{
    $arr_return = array('success' => FALSE, 'message' => '');
    $openid = $data['openid'];
    $time = $data['time'];
    $from = $data['from'];
    $redirect = $data['redirect'];
    $wxnick = $data['wxnick'];
    $wxhead = $data['wxhead'];
    
    if($openid != '' && $time != '' && $from != '')
    {
        // 查询openid
        $uid = uc_get_uid_by_openid($openid);
        if(intval($uid) > 0) // 已有这个openid
        {
            $member = getuserbyuid(intval($uid));
            if(count($member) > 0)
            {
                setloginstatus($member, 2592000);
                $arr_return['success'] = TRUE;
                $arr_return['message'] = '登录成功';
                $arr_return['uid'] = intval($member['uid']);
                $arr_return['username'] = trim($member['username']);
                $arr_return['email'] = trim($member['email']);
                $arr_return['redirect'] = $redirect;
                $arr_return['wxhead'] = $wxhead;
                $arr_return['nickname'] = $wxnick;
            }
            else
            {
                $arr_return['message'] = '账号记录有误';
            }
        }
        else // 没有openid的
        {
            $arr_param = array();
            $arr_param['type'] = 1;
            $arr_param['email'] = passport_get_default_email();
            $arr_param['password'] = passport_get_default_password();
            $arr_param['openid'] = $openid;
            $arr_param['username'] = ($wxnick == '') ? passport_get_default_username() : $wxnick;
            
            $arr_check_result = passport_can_register($arr_param);
           
            if($wxnick != '' && $arr_check_result['success'] === FALSE && 
               ($arr_check_result['message'] == lang('message', 'register_check_found') ||      // 该用户名已注册，请更换用户名或...
                $arr_check_result['message'] == lang('message', 'profile_username_illegal') ||  // 用户名包含敏感字符
                $arr_check_result['message'] == lang('message', 'profile_username_protect')))   // 用户名包含被系统屏蔽的字符
            {
                if($arr_check_result['message'] == lang('message', 'register_check_found'))
                {
                    $arr_param['username'] = $arr_param['username'].'_'.strtolower(passport_create_randomstr(5)); // 已经注册过
                }
                else
                {
                    $arr_param['username'] = passport_get_default_username();
                }
                
                $arr_check_result = passport_can_register($arr_param);
                $can_change_nickname = TRUE;
            }
            else
            {
                $can_change_nickname = FALSE;
            }
            
            if($arr_check_result['success'] === TRUE)
            {
                $arr_register_result = passport_register($arr_param);
                
                if($arr_register_result['success'] === TRUE) // 注册成功
                {
                    // 登录
                    $member = getuserbyuid(intval($arr_register_result['uid']));
                
                    if(count($member) > 0)
                    {
                        setloginstatus($member, 2592000);
                        $arr_return['success'] = TRUE;
                        $arr_return['message'] = '登录成功';
                        $arr_return['uid'] = intval($member['uid']);
                        $arr_return['username'] = trim($member['username']);
                        $arr_return['email'] = trim($member['email']);
                        $arr_return['redirect'] = $redirect;
                        $arr_return['wxhead'] = $wxhead;
                        $arr_return['nickname'] = $wxnick;
                        $arr_return['can_change_nickname'] = $can_change_nickname;
                        $arr_return['openid'] = $openid;
                        $arr_return['time'] = $time;
                        $arr_return['from'] = $from;
                    }
                    else
                    {
                        $arr_return['message'] = '账号记录有误2';
                    }
                }
                else
                {
                    $arr_return = $arr_register_result;
                }
            }
            else
            {
                $arr_return['message'] = '系统错误请重试|'.$arr_check_result['message'];
            }
        }
    }
    else
    {
        $arr_return['message'] = '参数不完整';
    }
    return $arr_return;
}

function passport_login($data)
{
    $arr_return = array('success' => FALSE, 'message' => '');
    
    $password = $data['password'];
    $openid = $data['openid'];
    
    if(is_email($data['name']))
    {
        //echo "Email\n";
        $email = $data['name'];
        
        // 根据Email查用户名
        $arr_member = uc_get_member_by_email($email);
        
        if(count($arr_member) > 0)
        {
            // 查询openid
            $uid = uc_get_uid_by_openid($openid);
            if(count($arr_member) > 0)
            {
                if($uid == $arr_member['uid'])
                {
                    $username = $arr_member['username'];
                    $result = userlogin($username, $password);
                    if($result['status'] === 1)
                    {
                        setloginstatus($result['member'], 2592000);
                        $arr_return['success'] = TRUE;
                        $arr_return['message'] = '登录成功';
                    }
                    else
                    {
                        switch($result['ucresult']['uid'])
                        {
                            case "-1" : $arr_return['message'] = '登录失败:UC_USER_CHECK_USERNAME_FAILED'; break;
                            case "-2" : $arr_return['message'] = '登录失败:UC_USER_USERNAME_BADWORD'; break;
                            case "-3" : $arr_return['message'] = '登录失败:UC_USER_USERNAME_EXISTS'; break;
                            case "-4" : $arr_return['message'] = '登录失败:UC_USER_EMAIL_FORMAT_ILLEGAL'; break;
                            case "-5" : $arr_return['message'] = '登录失败:UC_USER_EMAIL_ACCESS_ILLEGAL'; break;
                            case "-6" : $arr_return['message'] = '登录失败:UC_USER_EMAIL_EXISTS'; break;
                            default   : $arr_return['message'] = '登录失败:UC_USER_OTHER_FAILED';
                        }
                    }
                }
                else
                {
                    $arr_return['message'] = 'openid不匹配uid';
                }
            }
            else
            {
                $arr_return['message'] = '没有找到openid';
            }
        }
        else
        {
            $arr_return['message'] = '没有找到账号';
        }
    }
    else
    {
        $arr_return['message'] = '账号格式不正确';
    }
    
    return $arr_return;
}

function passport_register($data)
{
    global $_G;
    
    $arr_return = array('success' => FALSE, 'message' => '', 'uid' => 0, 'username' => '', 'email' => '');

    $username = trim(@$data['username']);
    $password = trim(@$data['password']);
    $email = trim(@$data['email']);
    $openid = trim(@$data['openid']);
    
    $arr_return = array('success' => FALSE, 'message' => '');
    
    $uid = uc_user_register(addslashes($username), $password, $email, '', '', $_G['clientip']);
    if($uid <= 0)
    {
        if($uid == -1)
        {
            $arr_return['message'] = lang('message', 'profile_username_illegal');
        }
        elseif($uid == -2)
        {
            $arr_return['message'] = lang('message', 'profile_username_protect');
        }
        elseif($uid == -3)
        {
            $arr_return['message'] = lang('message', 'profile_username_duplicate');
        }
        elseif($uid == -4)
        {
            $arr_return['message'] = lang('message', 'profile_email_illegal');
        }
        elseif($uid == -5)
        {
            $arr_return['message'] = lang('message', 'profile_email_domain_illegal');
        }
        elseif($uid == -6)
        {
            $arr_return['message'] = lang('message', 'profile_email_duplicate');
        }
        else
        {
            $arr_return['message'] = lang('message', 'undefined_action');
        }
    }
    else
    {
        $arr_return['success'] = TRUE;
        $arr_return['message'] = '注册成功';
        $arr_return['uid'] = $uid;
        $arr_return['username'] = $username;
        $arr_return['email'] = $email;
        
        C::t('#fansclub#plugin_ucenter_memberfields')->update($uid, array('userfrom' => 'weixin', 'openid' => $openid)); // 这个写法update没有问题，fetch可能有问题
        
        $member = getuserbyuid(intval($uid));
        if(count($member) == 0)
        {
            // 激活用户
            $init_arr = explode(',', $_G['setting']['initcredits']);
            
            DB::insert('common_member', array(
                'uid' => $uid,
                'username' => $username,
                'password' => md5(random(10)),
                'email' => $email,
                'adminid' => 0,
                'groupid' => $_G['setting']['regverify'] ? 8 : $_G['setting']['newusergroupid'],
                'regdate' => TIMESTAMP,
                'credits' => $init_arr[0],
                'timeoffset' => 9999
            ));
            
            DB::insert('common_member_status', array(
                'uid' => $uid,
                'regip' => $_G['clientip'],
                'lastip' => $_G['clientip'],
                'lastvisit' => TIMESTAMP,
                'lastactivity' => TIMESTAMP,
                'lastpost' => 0,
                'lastsendmail' => 0
            ));
            
            DB::insert('common_member_profile', array('uid' => $uid));
            DB::insert('common_member_field_forum', array('uid' => $uid));
            DB::insert('common_member_field_home', array('uid' => $uid));
            DB::insert('common_member_count', array(
                'uid' => $uid,
                'extcredits1' => $init_arr[1],
                'extcredits2' => $init_arr[2],
                'extcredits3' => $init_arr[3],
                'extcredits4' => $init_arr[4],
                'extcredits5' => $init_arr[5],
                'extcredits6' => $init_arr[6],
                'extcredits7' => $init_arr[7],
                'extcredits8' => $init_arr[8]
            ));
            manyoulog('user', $uid, 'add');
        }
    }
    
    return $arr_return;
}

// 检查sign
function passport_check_sign($data)
{
    $sn_key = '9lF4BbQaDvJMFGLp';
    $your_sing = '';
    
    $arr_sign = array();
    foreach($data as $key => $value)
    {
        if($key == 'sign')
        {
            $your_sing = $value;
            continue;
        }
        $arr_sign[] = $key.'='.urldecode($value);
    }
    $arr_sign[] = 'sn_key='.$sn_key;
    asort($arr_sign);
    $str_param = implode('&', $arr_sign);
    $my_sign = md5($str_param);
    if($my_sign === $your_sing)
    {
        return TRUE;
    }
    else
    {
        //echo $my_sign;
        return FALSE;
    }
}

// 返回是否可以注册 arr
function passport_can_register($data)
{
    global $_G;
    
    $arr_return = array('success' => FALSE, 'message' => '');
    
    $type = intval(@$data['type']); // 1Email注册,2手机注册
    $email = trim(@$data['email']);
    $mobile = trim(@$data['mobile']);
    $password = trim(@$data['password']);
    $username = trim(@$data['username']);
    $no_password = intval(@$data['no_password']);
    $only_check_username = intval(@$data['only_check_username']);
    
    if($only_check_username == 1)
    {
        $type = 1;
    }
    
    if($type == 0)
    {
        $arr_return['message'] = '注册类型错误'; 
    }

    if($type == 1) // Email注册
    {
        if($only_check_username == 0)
        {
            // Email是否已注册
            if($arr_return['message'] == '' && $email == '')
            {
                $arr_return['message'] = 'Email没有填写'; 
            }
            
            if($arr_return['message'] == '' && !is_email($email))
            {
                $arr_return['message'] = '不是一个有效的Email';
            }
        }
        
        if($arr_return['message'] == '')
        {
            $ucresult = uc_user_checkname($username);
            
            if($ucresult == -1)
            {
                $arr_return['message'] = lang('message', 'profile_username_illegal');
            }
            elseif($ucresult == -2)
            {
                $arr_return['message'] = lang('message', 'profile_username_protect');
            }
            elseif($ucresult == -3)
            {
                if(C::t('common_member')->fetch_by_username($username) || C::t('common_member_archive')->fetch_by_username($username))
                {
                    $arr_return['message'] = lang('message', 'register_check_found');
                }
                else
                {
                    $arr_return['message'] = lang('message', 'register_activation');
                }
            }

            if($arr_return['message'] == '')
            {
                $censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($_G['setting']['censoruser'] = trim($_G['setting']['censoruser'])), '/')).')$/i';
                if($_G['setting']['censoruser'] && @preg_match($censorexp, $username))
                {
                    $arr_return['message'] = lang('message', 'profile_username_protect');
                }
            }

            if($arr_return['message'] == '' && $only_check_username == 0)
            {
                $ucresult = uc_user_checkemail($email);
                if($ucresult == -4)
                {
                    $arr_return['message'] = lang('message', 'profile_email_illegal');
                }
                elseif($ucresult == -5)
                {
                    $arr_return['message'] = lang('message', 'profile_email_domain_illegal');
                }
                elseif($ucresult == -6)
                {
                    $arr_return['message'] = lang('message', 'profile_email_duplicate');
                }
            }
        }
    }
    elseif($type == 2) // 手机注册
    {
        $arr_return['message'] == '手机注册暂不支持';
        
        /*
        if($arr_return['message'] == '' && $mobile == '')
        {
            $arr_return['message'] = '手机号码没有填写'; 
        }
        
        if($arr_return['message'] == '' && !is_mobile($mobile))
        {
            $arr_return['message'] = '不是一个有效的手机号码';
        }
        
        // 手机是否已注册
        if($arr_return['message'] == '')
        {
            //$_CI =& get_instance();
            //$_CI->load->model('group/member_model');
            //$arr_mobile = $_CI->member_model->getMemberByMobile($mobile);
            //$arr_user = $_CI->member_model->getMemberByUsername($mobile);
            if(count($arr_mobile) != 0 || count($arr_user) != 0)
            {
                $arr_return['message'] = '手机号码已经被注册';
            }
        }
        */
    }
    else
    {
        $arr_return['message'] = '注册类型不被支持';
    }

    if($no_password == 0 && $only_check_username == 0)
    {
        if($arr_return['message'] == '' && !is_password($password))
        {
            $arr_return['message'] = '密码格式有误';
        }
    }

    if($arr_return['message'] == '')
    {
        $arr_return['success'] = TRUE;
        $arr_return['message'] = '可以注册';
    }
    
	return $arr_return;
}

// 返回一个默认用户名 一共15位
function passport_get_default_username()
{
    return '微信_'.strtolower(passport_create_randomstr(3)).substr(time(), -7);
}

// 返回一个默认Email
function passport_get_default_email() 
{
    return 'wx_'.strtolower(passport_create_randomstr(6)).substr(time(), -7).'@example.com';
}

// 返回一个默认密码
function passport_get_default_password()
{
    return passport_create_randomstr(3).'1'.passport_create_randomstr(6).'a'.passport_create_randomstr(3);
}

// 生成随机字符串
function passport_create_randomstr($lenth = 6)
{
    $characters = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
    $string = '';
    for($i = 0; $i < $lenth; $i++)
    {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

// =========================================== 下面的暂时没有使用 ===========================================
// 返回相册URL arr
function passport_get_album($uid, $album_url)
{
    $_CI =& get_instance();
    $_CI->load->model('group/home_model');

    $user_album = $_CI->home_model->getAblum($uid);
    $temp_album = array();
    for($j = 0; $j < count($user_album); $j++)
    {
        $_tmp = array();
        if($user_album[$j]['thumb'] == 1) // 是否有缩略图
        {
            $_tmp['img_thumb_url'] = $album_url.$user_album[$j]['filepath'].'.thumb.jpg';
        } else {
            $_tmp['img_thumb_url'] = '';
        }
        $_tmp['img_url'] = $album_url.$user_album[$j]['filepath'];
        $temp_album[] = $_tmp;
    }
    return $temp_album;
}

// 返回头像URL str
function passport_get_avatar($uid, $size, $avatar_url)
{
    $uid = abs(intval($uid));
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);

    $url = $avatar_url.'data/avatar/'.$dir1.DS.$dir2.DS.$dir3.DS.substr($uid, -2)."_avatar_$size.jpg";
    $no_url = $avatar_url.'images/noavatar_'.$size.'.gif';
    $bln_exists = url_file_exists($url);

    return $bln_exists ? $url : $no_url;
}

// 生成token的值
function passport_make_token($data)
{
    list($usec, $sec) = explode(" ", microtime());
    $microtime = ((float)$usec + (float)$sec);
    return md5(http_build_query($data).$microtime.rand(10000, 99999));
}

// 生成手机注册验证码并发短信
function passport_mobile_create_verify_code($mobile)
{
    $arr_return = array('success' => FALSE, 'message' => '', 'verify_sms_code' => '');
    // 生成校验码
    $how = 6;
    // $alpha = 'abcdefghijkmnpqrstuvwxyz';
    $number = '1234567890';
    $verify_sms_code = '';

    for($i = 0; $i < $how; $i++)  
    {     
        // $alpha_or_number = mt_rand(0, 1);
        // $str = $alpha_or_number ? $alpha : $number;
        $str = $number;
        $which = mt_rand(0, strlen($str)-1);
        $code = substr($str, $which, 1);
        $j = !$i ? 4 : $j+15;
        $verify_sms_code .= $code;
    }

    if(is_mobile($mobile))
    {
        $content = '手机注册验证，您的短信验证码是：'.$verify_sms_code;
        $send_seccess = passport_write_sms_log($mobile, $content);
        
        if($send_seccess === TRUE)
        {
            $arr_return['success'] = TRUE;
            $arr_return['message'] = '发送成功，请输入短信验证码';
            $arr_return['verify_sms_code'] = $verify_sms_code;
        }
        else
        {
            $arr_return['message'] = $send_seccess;
        }
    }
    else
    {
        $arr_return['message'] = '不是一个有效的手机号码，请重新填写';
    }
    return $arr_return;
}

// app手机分步注册时，操作token表
function passport_token($data)
{
    $arr_return = array('success' => FALSE, 'message' => '');
    $_CI =& get_instance();
    $_CI->load->model('group/member_model');

    // 如果是注册
    if($data['method'] == 'register')
    {
        // 检查是否已经注册了
        $arr_param = array();
        $arr_param['type'] = 2; // 手机注册
        $arr_param['mobile'] = $data['mobile'];
        $arr_param['no_password'] = 1; // 是否没有密码
        $arr_register = passport_can_register($arr_param);
        if($arr_register['success'] === TRUE) // 手机号码可以注册
        {}
        else
        {
            return $arr_register;
        }
        
        // 查询是否之前有记录
        $arr_param = array();
        $arr_param['method'] = $data['method'];
        $arr_param['is_deleted'] = '0';
        $arr_param['mobile'] = $data['mobile'];
        $row = $_CI->member_model->getPassportTokenTmp($arr_param);
        
        if(count($row) > 0) // 如果之前有记录，返回这个token，暂时没有时间限制
        {
            $need_send_new = FALSE;
            $timestamp = trim(@$data['timestamp']) + 0;
            $log_time = $row[0]['log_time'];
            $my_time = time();
            $cha = abs($log_time - $my_time);
            if($cha > 60 && FALSE) // 检查 timestamp 60s 内重新发sms // todo 不重发
            {
                $arr_param = array();
                $arr_param['is_deleted'] = '1'; // 作废之前的记录
                $_CI->member_model->editPassportTokenTmp($row[0]['token'], $arr_param);
                $need_send_new = TRUE;
            }
        }
        else
        {
            $need_send_new = TRUE;
        }
        
        if($need_send_new) // 是否需要新发sms
        {
            $return = passport_mobile_create_verify_code($data['mobile']);
            if($return['success'] === TRUE)
            {
                // 写token记录
                $arr_token_log = array();
                $arr_token_log['token'] = passport_make_token($data);
                $arr_token_log['is_deleted'] = '0';
                $arr_token_log['log_time'] = time();
                $arr_token_log['method'] = $data['method'];
                $arr_token_log['step'] = '1';
                $arr_token_log['from'] = 'api';
                $arr_token_log['connectid'] = '';
                $arr_token_log['remote_avatar'] = '';
                $arr_token_log['username'] = '';
                $arr_token_log['mobile'] = $data['mobile'];
                $arr_token_log['remote_avatar'] = '';
                $arr_token_log['sms_verify'] = $return['verify_sms_code'];
                $id = $_CI->member_model->setPassportTokenTmp($arr_token_log);
                $row = $_CI->member_model->getPassportTokenTmp(array('id' => $id));
            }
            else
            {
                $arr_return['message'] = $return['message'];
                return $arr_return;
            }
        }
        
        if(count($row) == 1)
        {
            $arr_return['success'] = TRUE;
            $arr_return['message'] = $row[0]['token'];
        }
        else
        {
            $arr_return['message'] = '请重新获取验证码';
        }
    }
    return $arr_return;
}

// 会员中心短信日志
function passport_write_sms_log($mobile, $content)
{
    $_CI =& get_instance();
    $_CI->load->model('group/member_model');
    $_CI->load->library('sms');

    $ip = ip();
    $posttime = time();

    // 先检查同一个手机号码是否大于1分钟
    $data = array();
    $data['mobile'] = $mobile;
    $arr_log = $_CI->member_model->getPassportSmsLog($data);

    $last_post_time = (count($arr_log) > 0) ? ($arr_log[0]['posttime'] + 0) : 0;

    $pass_time = $posttime - $last_post_time;
    if($pass_time < 60)
    {
        return '请在60秒后再试';
    }

    $data = array();
    $data['mobile'] = $mobile + 0;
    $data['posttime'] = $posttime;
    $data['content'] = trim($content);
    $data['return_msg'] = '测试呀'; //$_CI->sms->msg_post($data['mobile'], $data['content']);
    $data['ip'] = $ip;
    $data['user_name'] = $_CI->sms->user_name;
    $data['money'] = $_CI->sms->find_money();  // 这个要大于30S调用1次


    $id = $_CI->member_model->setPassportSmsLog($data);
    if($id + 0 > 0) {
        return TRUE;
    } else {
        return '记录失败，请稍候再试';
    }
}
