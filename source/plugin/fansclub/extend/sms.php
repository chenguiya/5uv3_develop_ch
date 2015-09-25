<?php
/******************************************************************************
文件名： sms.php
作  者： zhangjh
邮  件： 
日  期： 2014-10-30
说  明： 发短信类
任务ID： NULL
修  改： m1 zhangjh 2014-10-30 新建
复  查： r1 zhangjh 2014-10-30 复查
******************************************************************************/

/**
// eg.
$sms = new Sms();

// 1、查剩余条数（间隔大于30秒）
echo '剩余条数：'.$sms->find_money()."<br>";

// 2、发短信
$to = '13560838447'; // 发送手机号码
$content = '请注意：您的登录验证码是[123456]。本信息发送时间['.date("Y-m-d H:i:s").']'; // 内容
echo $sms->msg_post($to, $content);
*/


class sms {

    public $user_name = '';    // 账号
    private $password = '';     // 密码
    private $sign = '';         // 短信落款
    private $send_url = '';     // 发送地址
    private $check_url = '';    // 查询余额地址

    public function __construct()
    {
        // $this->user_name = 'u24';    // 测试用
        // $this->password = 'u24999';
        
        $this->user_name = 'usport';    // 正式用
        $this->password = 'usport33';
        
        //$this->sign = '【U24】';
        $this->sign = '';
        $this->send_url = 'http://120.132.132.102/WS/Send.aspx?CorpID=%s&Pwd=%s&Mobile=%s&Content=%s&Cell=&SendTime=';
        $this->check_url = 'http://120.132.132.102/WS/SelSum.aspx?CorpID=%s&Pwd=%s';
    }
    
    // 发送短信 （发送手机号码，内容）
    function msg_post($to, $content)
    {
        // 内容去掉两边多余的空格，转GBK，空格转下划线
        $content = str_replace(' ', '_', $this->to_gbk(trim($content).$this->sign));   
        
        $rurl = sprintf($this->send_url, $this->user_name, $this->password, $to, $content);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $rurl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        $result = curl_exec($ch);
        
        switch($result)
        {
            case  0 : $str_return = '发送成功'; break;
            case -1 : $str_return = '账号未注册'; break;
            case -2 : $str_return = '其他错误'; break;
            case -3 : $str_return = '密码错误'; break;
            case -4 : $str_return = '手机号格式不对'; break;
            case -5 : $str_return = '余额不足'; break;
            case -6 : $str_return = '定时发送时间不是有效的时间格式'; break;
            case -7 : $str_return = '禁止10小时以内向同一手机号发送相同短信'; break;
            default : $str_return = '意外的返回';
        }
        
        return $str_return;
    }
    
    // 查询剩余条数
    function find_money()
    {
        $rurl = sprintf($this->check_url, $this->user_name, $this->password);
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_POST, 0);
        curl_setopt($ch,CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_URL, $rurl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);

        // -1 账号未注册 -2 其他错误 –3 密码错误 0 剩余条数 -101 调用接口频率过快（大于30s调用一次)
        return $result;
    }
    
    // UTF-8转GBK
    public function to_gbk($arr) 
    {
        if(is_array($arr))
        {
            foreach($arr as $k => $v)
            {
                $_k = $this->to_gbk($k);
                $arr[$_k] = $this->to_gbk($v);
                
                if($k != $_k)
                    unset($arr[$k]);
            }
        }
        else
        {
            $arr = iconv('UTF-8', 'GBK', $arr);
        }
        return $arr;
    }
    
    // GBK转UTF-8
    public function to_utf8($arr) 
    {
        if(is_array($arr))
        {
            foreach($arr as $k => $v)
            {
                $_k = $this->to_utf8($k);
                $arr[$_k] = $this->to_utf8($v);
                
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

