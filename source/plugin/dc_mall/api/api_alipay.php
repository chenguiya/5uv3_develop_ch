<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
list($ec_contract, $ec_securitycode, $ec_partner, $ec_creditdirectpay) = explode("\t", authcode($_G['setting']['ec_contract'], 'DECODE', $_G['config']['security']['authkey']));
define('DISCUZ_PARTNER', $ec_partner);
define('DISCUZ_SECURITYCODE', $ec_securitycode);
define('DISCUZ_DIRECTPAY', $ec_creditdirectpay);
define('STATUS_SELLER_SEND', 4);
define('STATUS_WAIT_BUYER', 5);
define('STATUS_TRADE_SUCCESS', 7);
define('STATUS_REFUND_CLOSE', 17);

class api_alipay{
	public function __construct() {
		
	}
	public function create_payurl($orderid, $price, $subject,$showurl) {
		global $_G;
		$args = array(
			'subject' 		=> $subject,
			'body' 			=> $_G['setting']['bbname'].' - '.$_G['member']['username'].' - '.$subject,
			'service' 		=> 'trade_create_by_buyer',
			'partner' 		=> DISCUZ_PARTNER,
			'notify_url' 		=> $_G['siteurl'].'source/plugin/dc_mall/api/notify_mall.php',
			'return_url' 		=> $_G['siteurl'].'source/plugin/dc_mall/api/notify_mall.php',
			'show_url'		=> $showurl,
			'_input_charset' 	=> CHARSET,
			'out_trade_no' 		=> $orderid,
			'price' 		=> $price,
			'quantity' 		=> 1,
			'seller_email' 		=> $_G['setting']['ec_account'],
			'extend_param'	=> 'isv^dz11'
		);
		if(DISCUZ_DIRECTPAY) {
			$args['service'] = 'create_direct_pay_by_user';
			$args['payment_type'] = '1';
		} else {
			$args['logistics_type'] = 'EXPRESS';
			$args['logistics_fee'] = 0;
			$args['logistics_payment'] = 'SELLER_PAY';
			$args['payment_type'] = 1;
		}
		return $this->trade_returnurl($args);

	}


	public function trade_returnurl($args) {
		global $_G;
		ksort($args);
		$urlstr = $sign = '';
		foreach($args as $key => $val) {
			$sign .= '&'.$key.'='.$val;
			$urlstr .= $key.'='.rawurlencode($val).'&';
		}
		$sign = substr($sign, 1);
		$sign = md5($sign.DISCUZ_SECURITYCODE);
		return 'https://www.alipay.com/cooperate/gateway.do?'.$urlstr.'sign='.$sign.'&sign_type=MD5';
	}

	public function trade_notifycheck() {
		global $_G;
		if(!empty($_POST)) {
			$notify = $_POST;
			$location = FALSE;
		} elseif(!empty($_GET)) {
			$notify = $_GET;
			$location = TRUE;
		} else {
			exit('Access Denied');
		}

		if(dfsockopen("http://notify.alipay.com/trade/notify_query.do?partner=".DISCUZ_PARTNER."&notify_id=".$notify['notify_id'], 60) !== 'true') {
			exit('Access Denied');
		}
		if(!DISCUZ_SECURITYCODE) {
			exit('Access Denied');
		}
		ksort($notify);
		$sign = '';
		foreach($notify as $key => $val) {
			if($key != 'sign' && $key != 'sign_type') $sign .= "&$key=$val";
		}
		if($notify['sign'] != md5(substr($sign,1).DISCUZ_SECURITYCODE)) {
			exit('Access Denied');
		}
		if(!DISCUZ_DIRECTPAY && $notify['notify_type'] == 'trade_status_sync' && ($notify['trade_status'] == 'WAIT_SELLER_SEND_GOODS' || $notify['trade_status'] == 'TRADE_FINISHED') || DISCUZ_DIRECTPAY && ($notify['trade_status'] == 'TRADE_FINISHED' || $notify['trade_status'] == 'TRADE_SUCCESS')) {
			return array(
				'validator'	=> TRUE,
				'status' 	=> $this->trade_getstatus(!empty($notify['refund_status']) ? $notify['refund_status'] : $notify['trade_status'], 1),
				'order_no' 	=> $notify['out_trade_no'],
				'price' 	=> !DISCUZ_DIRECTPAY && $notify['price'] ? $notify['price'] : $notify['total_fee'],
				'trade_no'	=> $notify['trade_no'],
				'notify'	=> 'success',
				'location'	=> $location
				);
		} else {
			return array(
				'validator'	=> FALSE,
				'notify'	=> 'fail',
				'location'	=> $location
				);
		}
	}
	function trade_getstatus($key, $method = 2) {
		$language = lang('forum/misc');
		$status[1] = array(
			'WAIT_BUYER_PAY' => 1,
			'WAIT_SELLER_CONFIRM_TRADE' => 2,
			'WAIT_SYS_CONFIRM_PAY' => 3,
			'WAIT_SELLER_SEND_GOODS' => 4,
			'WAIT_BUYER_CONFIRM_GOODS' => 5,
			'WAIT_SYS_PAY_SELLER' => 6,
			'TRADE_FINISHED' => 7,
			'TRADE_CLOSED' => 8,
			'WAIT_SELLER_AGREE' => 10,
			'SELLER_REFUSE_BUYER' => 11,
			'WAIT_BUYER_RETURN_GOODS' => 12,
			'WAIT_SELLER_CONFIRM_GOODS' => 13,
			'WAIT_ALIPAY_REFUND' => 14,
			'ALIPAY_CHECK' => 15,
			'OVERED_REFUND' => 16,
			'REFUND_SUCCESS' => 17,
			'REFUND_CLOSED' => 18
		);
		$status[2] = array(

			0  => $language['trade_unstart'],
			1  => $language['trade_waitbuyerpay'],
			2  => $language['trade_waitsellerconfirm'],
			3  => $language['trade_sysconfirmpay'],
			4  => $language['trade_waitsellersend'],
			5  => $language['trade_waitbuyerconfirm'],
			6  => $language['trade_syspayseller'],
			7  => $language['trade_finished'],
			8  => $language['trade_closed'],
			10 => $language['trade_waitselleragree'],
			11 => $language['trade_sellerrefusebuyer'],
			12 => $language['trade_waitbuyerreturn'],
			13 => $language['trade_waitsellerconfirmgoods'],
			14 => $language['trade_waitalipayrefund'],
			15 => $language['trade_alipaycheck'],
			16 => $language['trade_overedrefund'],
			17 => $language['trade_refundsuccess'],
			18 => $language['trade_refundclosed']
		);
		return $method == -1 ? $status[2] : $status[$method][$key];
	}
	
	
}
?>