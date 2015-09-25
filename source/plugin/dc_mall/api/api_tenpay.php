<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
define('DISCUZ_PARTNER', $_G['setting']['ec_tenpay_bargainor']);
define('DISCUZ_SECURITYCODE', $_G['setting']['ec_tenpay_key']);
define('DISCUZ_AGENTID', '1204737401');
define('DISCUZ_TENPAY_OPENTRANS_CHNID', $_G['setting']['ec_tenpay_opentrans_chnid']);
define('DISCUZ_TENPAY_OPENTRANS_KEY', $_G['setting']['ec_tenpay_opentrans_key']);
define('STATUS_SELLER_SEND', 3);
define('STATUS_WAIT_BUYER', 4);
define('STATUS_TRADE_SUCCESS', 5);
define('STATUS_REFUND_CLOSE', 9);
class RequestHandler {
	var $gateUrl;
	var $key;
	var $parameters;
	var $debugInfo;
	function __construct() {
		$this->RequestHandler();
	}
	function RequestHandler() {		$this->gateUrl = "https://www.tenpay.com/cgi-bin/med/show_opentrans.cgi";
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
	}
	function init() {
	}
	function getGateURL() {
		return $this->gateUrl;
	}
	function setGateURL($gateUrl) {
		$this->gateUrl = $gateUrl;
	}
	function getKey() {
		return $this->key;
	}
	function setKey($key) {
		$this->key = $key;
	}
	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	function getAllParameters() {
		$this->createSign();
		return $this->parameters;
	}
	function getRequestURL() {
		$this->createSign();
		$reqPar = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			$reqPar .= $k . "=" . urlencode($v) . "&";
		}
		$reqPar = substr($reqPar, 0, strlen($reqPar)-1);
		$requestURL = $this->getGateURL() . "?" . $reqPar;
		return $requestURL;
	}
	function getDebugInfo() {
		return $this->debugInfo;
	}
	function doSend() {
		header("Location:" . $this->getRequestURL());
		exit;
	}
	function createSign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("" !== $v && "sign" !== $k) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$this->setParameter("sign", $sign);
		$this->_setDebugInfo($signPars . " => sign:" . $sign);
	}
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}
}
class ResponseHandler  {
	var $key;
	var $parameters;
	var $debugInfo;
	function __construct() {
		$this->ResponseHandler();
	}
	function ResponseHandler() {
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
		foreach($_GET as $k => $v) {
			$this->setParameter($k, $v);
		}
		foreach($_POST as $k => $v) {
			$this->setParameter($k, $v);
		}
	}
	function getKey() {
		return $this->key;
	}
	function setKey($key) {
		$this->key = $key;
	}
	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	function getAllParameters() {
		return $this->parameters;
	}
	function isTenpaySign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" !== $k && "" !== $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$tenpaySign = strtolower($this->getParameter("sign"));
		$this->_setDebugInfo($signPars." => sign:".$sign." tenpaySign:".$this->getParameter("sign"));
		return $sign == $tenpaySign;
	}
	function getDebugInfo() {
		return $this->debugInfo;
	}
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}

}
class MediPayRequestHandler extends RequestHandler {
	function __construct() {
		$this->MediPayRequestHandler();
	}
	function MediPayRequestHandler() {
		$this->setGateURL("https://www.tenpay.com/cgi-bin/med/show_opentrans.cgi");
	}
	function init() {
		$this->setParameter("attach", "1");
		$this->setParameter("chnid",  "");
		$this->setParameter("cmdno", "12");
		$this->setParameter("encode_type", "1");
		$this->setParameter("mch_desc", "");
		$this->setParameter("mch_name", "");
		$this->setParameter("mch_price",  "");
		$this->setParameter("mch_returl",  "");
		$this->setParameter("mch_type",  "");
		$this->setParameter("mch_vno",  "");
		$this->setParameter("need_buyerinfo",  "");
		$this->setParameter("seller",  "");
		$this->setParameter("show_url",  "");
		$this->setParameter("transport_desc",  "");
		$this->setParameter("transport_fee",  "");
		$this->setParameter("version",  "2");
		$this->setParameter("sign",  "");
	}
}
class MediPayResponseHandler extends ResponseHandler {
	function doShow() {
		$strHtml = "<html><head>\r\n" .
			"<meta name=\"TENCENT_ONLINE_PAYMENT\" content=\"China TENCENT\">" .
			"</head><body></body></html>";
		echo $strHtml;
		exit;
	}

	function isTenpaySign() {
		$signParameterArray = array(
			'attach',
			'buyer_id',
			'cft_tid',
			'chnid',
			'cmdno',
			'mch_vno',
			'retcode',
			'seller',
			'status',
			'total_fee',
			'trade_price',
			'transport_fee',
			'version'

		);
		ksort($signParameterArray);
		foreach($signParameterArray as $k ) {
			$v = $this->getParameter($k);
			if(isset($v)) {
				$signPars .= $k . "=" . urldecode($v) . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$tenpaySign = strtolower($this->getParameter("sign"));
		$this->_setDebugInfo($signPars." => sign:".$sign ." tenpaySign:".$this->getParameter("sign"));
		return $sign == $tenpaySign;
	}
}
class api_tenpay{
	function create_payurl($orderid, $price, $subject,$showurl) {
		include_once DISCUZ_ROOT . './source/class/class_chinese.php';
		global $_G;
		$date = dgmdate(TIMESTAMP, 'YmdHis');
		$suffix = dgmdate(TIMESTAMP, 'His').rand(1000, 9999);
		$transaction_id = DISCUZ_PARTNER.$date.$suffix;
		$chinese = new Chinese(strtoupper(CHARSET), 'GBK');
		$subject = $chinese->Convert($subject);
		$reqHandler = new RequestHandler();
		$reqHandler->setGateURL("https://gw.tenpay.com/gateway/pay.htm");
		$reqHandler->init();
		$reqHandler->setKey(DISCUZ_SECURITYCODE);
		$reqHandler->setParameter("partner", DISCUZ_PARTNER);
		$reqHandler->setParameter("out_trade_no", $orderid);
		$reqHandler->setParameter("total_fee", $price * 100);
		$reqHandler->setParameter("return_url", $_G['siteurl'].'source/plugin/dc_mall/api/notify_mall.php');
		$reqHandler->setParameter("notify_url", $_G['siteurl'].'source/plugin/dc_mall/api/notify_mall.php');
		$reqHandler->setParameter("body", $subject);
		$reqHandler->setParameter("bank_type", 'DEFAULT');
		$reqHandler->setParameter("spbill_create_ip", $_G['clientip']);
		$reqHandler->setParameter("fee_type", "1");
		$reqHandler->setParameter("subject", $subject);
		$reqHandler->setParameter("sign_type", "MD5");
		$reqHandler->setParameter("service_version", "1.0");
		$reqHandler->setParameter("input_charset", "GBK");
		$reqHandler->setParameter("sign_key_index", "1");
		$reqHandler->setParameter("attach", "tenpay");
		$reqHandler->setParameter("time_start", $date);
		$reqHandler->setParameter("trade_mode","1");
		$reqHandler->setParameter("trans_type","1");
		$reqHandler->setParameter("agentid", DISCUZ_AGENTID);
		$reqHandler->setParameter("agent_type","2");
		$reqUrl = $reqHandler->getRequestURL();
		return $reqUrl;
	}
	function trade_notifycheck() {
		$resHandler = new ResponseHandler();
		$resHandler->setKey(DISCUZ_SECURITYCODE);
		if($resHandler->isTenpaySign() && DISCUZ_PARTNER == $_GET['partner']) {
			return array(
				'validator'	=> !$_GET['trade_state'],
				'order_no' 	=> $_GET['out_trade_no'],
				'trade_no'	=> $_GET['transaction_id'],
				'price' 	=> $_GET['total_fee'] / 100,
				'bargainor_id' => $_GET['partner'],
				'location'	=> true,
				);
		}else {
			return array(
				'validator'	=> FALSE,
				'location'	=> FALSE,
			);

		}

	}
}
?>