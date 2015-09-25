<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class plugin_hfilterad_killer{

    /**
     * @var null
     */
    public $killer = NULL;

    public $msg;

    public function common(){
        global $_G;
        $this->killer = $_G['cache']['plugin']['hfilterad_killer'];
        if($this->killer['switch']){
            $this->killer['forum'] = (array)unserialize($this->killer['forum']);
            $this->killer['group'] = (array)unserialize($this->killer['group']);
            $this->killer['field'] = (array)unserialize($this->killer['field']);
            $this->killer['message'] = array_filter(explode("\n", $this->killer['message']));
            $this->msg['subject'] = $this->killer['message'][0];
            $this->msg['message'] = $this->killer['message'][1];
        }
    }
}

class plugin_hfilterad_killer_forum extends plugin_hfilterad_killer {

    /**
     * @return null
     */
    function post_hfilterad_dx002(){
        global $_G;
        $this->common();

        try
        {
            if(
                ! $this->killer['switch'] ||
                ! in_array($_G['groupid'], $this->killer['group']) ||
                ! in_array($_G['fid'], $this->killer['forum'])
            )
            {
                throw new Exception('Plugin is off now.');
            }

            $charset = $_G['charset'] ? strtoupper($_G['charset']) : strtoupper(CHARSET);
            foreach ($this->killer['field'] as $v)
            {
                $message = $this->msg[$v];
                if(!isset($_GET[$v]))
                {
                    continue;
                }
                $val = trim($_GET[$v]);
                if(empty($val))
                {
                    continue;
                }

                preg_match("/\[attach(img)?\](\d+)\[\/attach(img)?\]/i", $val, $m1);
                if(!empty($m1)){
                    continue;
                }
                $val = preg_replace('/\[[^\[\]]{1,}\]/','',$val);
                $val = strip_tags(str_replace(array('[',']'), array('<', '>'), $val));
                $val = iconv($charset, 'utf-8', $val);

                if(strlen($val) == mb_strlen($val, $charset))  // 单字节
                {
                    showmessage($message);
                }

                if($this->killer['max']){
                    preg_match_all("/[a-zA-Z0-9]/i", $val, $m);
                    if(count($m[0])>= intval($this->killer['max']))
                    {
                        showmessage($message);
                    }
                }

                preg_match_all("/[\x{4e00}-\x{9fa5}]{1}/u", $val, $m);
                $char_count = isset($m[0]) ? count($m[0]) : 0;
                if($char_count <=intval($this->killer['min'])-1)
                {
                    showmessage($message);
                }
            }

        }
        catch (Exception $e)
        {}
        return NULL;
    }
}