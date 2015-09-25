<?php
// 取积分榜 eg. http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=get_data&method=leaguescore&league_id=1
// 取射手榜 eg. http://zhangjh.usport.com.cn/plugin.php?id=fansclub:api&ac=get_data&method=shooter&league_id=1

// band_id 是win007上联赛的ID
$arr_config = array('1'  => array('band_id' => '36', 'league_name' => '英超', 'season' => '2015-2016'),
                    '15' => array('band_id' => '31', 'league_name' => '西甲', 'season' => '2015-2016'),
                    '2'  => array('band_id' => '8',  'league_name' => '德甲', 'season' => '2015-2016'),
                    '3'  => array('band_id' => '11', 'league_name' => '法甲', 'season' => '2015-2016'),
                    '21' => array('band_id' => '34', 'league_name' => '意甲', 'season' => '2015-2016'),
                    '13' => array('band_id' => '60', 'league_name' => '中超', 'season' => '2015'));

$league_id = intval($_G['gp_league_id']);
$method = trim($_G['gp_method']);
$arr_return = array('success' => FALSE, 'message' => 'init', 'data' => array());

if($league_id > 0)
{
    $arr_shooter = $arr_leaguescore = $version = FALSE;
    
    $mem_check = memory('check'); // 先检查缓存是否生效
    if($mem_check != '') // 缓存可以用
    {
        $arr_shooter = memory('get', 'fansclub_arr_get_shooter_data_'.$league_id);
        $arr_leaguescore = memory('get', 'fansclub_arr_get_leaguescore_data_'.$league_id);
        $version = memory('get', 'fansclub_get_data_version');
    }

    if($version === FALSE || $arr_shooter === FALSE || $arr_leaguescore === FALSE)
    {
        $arr_param = array();
        $arr_param['leagueid'] = $arr_config[$league_id]['band_id'];
        $arr_param['type'] = 'web';
        $arr_param['time'] = time();
        $arr_param['version'] = $arr_param['time'];
        $arr_param['season'] = $arr_config[$league_id]['season'];
        if($method == 'shooter' || $method == 'leaguescore')
        {
            $srt_method = $method;
            $url = fansclub_get_api_url2($arr_param, $srt_method);
            $result = curl_access($url);
            $arr_result = json_decode($result, TRUE);
            
            memory('set', 'fansclub_arr_get_'.$method.'_data_'.$league_id, $arr_result['data'], 60*60); // 缓存一小时
            memory('set', 'fansclub_get_data_version', $arr_result['dataversion'], 60*60);
            
            $arr_return['success'] = TRUE;
            $arr_return['message'] = '成功';
            $arr_return['data'] = $arr_result['data'];
        }
        else
        {
            $arr_return['message'] = '参数错误';
        }
    }
    else
    {
        if($method == 'shooter' || $method == 'leaguescore')
        {
            $arr_result = memory('get', 'fansclub_arr_get_'.$method.'_data_'.$league_id);
            $arr_return['success'] = TRUE;
            $arr_return['message'] = '成功2';
            $arr_return['data'] = $arr_result;
        }
        else
        {
            $arr_return['message'] = '参数错误2';
        }
    }
}
else
{
    $arr_return['message'] = '参数不足';
}
die(json_encode($arr_return));
// echo "<pre>";
// print_r($arr_return);

/*
echo "\n\n";
echo "地区列表 ";
$srt_method = 'area';
$arr_param = array();
$arr_param['version'] = '12234';
$arr_param['time'] = time();
fansclub_get_api_url2($arr_param, $srt_method);


echo "\n";
echo "国家列表 ";
$srt_method = 'country';
$arr_param = array();
$arr_param['area_id'] = '1';
$arr_param['version'] = '12234';
$arr_param['time'] = time();
fansclub_get_api_url2($arr_param, $srt_method);
*/
