<?php
class Response {
	private static $instance__;
	
	static public function &instance() {
		if (! isset ( self::$instance__ )) {
			$class = __CLASS__;
			self::$instance__ = new $class ( );
		}
		return self::$instance__;
	}
	
	/**
	 * 输出特定格式格式, 根据传入的格式
	 *
	 * @param string $str
	 */
	public static function output($data, $status = 200, $msg = 'success', $isEncrypt = false, $outputFormat = 'json') {
	    global $_start;
	    $result['execute_time'] = microtime(true) - $_start;
	    $result['code'] = $status;
	    $result['msg'] = $msg;
	    $result['timestamp'] = microtime(true);
	    $result['data'] = $data;
	    if(strtolower($outputFormat) == 'json') {
	        echo json_encode($result);exit;
	    }
	
	    exit;
	}
	
	/**
	 * display according to template
	 *
	 * @param string $template
	 * @param array $parameters
	 * @return string
	 */
	public function display($template, $parameters = array()) {
		return template::instance ()->render ( $template, $parameters );
	}
	
	//统一报错方式
	public static function displayError($code = 0, $msg = '', $returnUrl = '') {
		//获得输出错误方式
		$error = '';
		if ($code) {
			$error = config ( 'errorCodes', $code );
		}
		if ($msg) {
			$error = $msg;
		}
		
		$adapterRequest = config ( "domainArray", $_SERVER ['HTTP_HOST'] );
		$outputFormat = $adapterRequest ['outputFormat'];
		$data ['status'] = $code;
		$data ['error'] = $error;
		$data ['redirect_url'] = $returnUrl;
		header ( "Content-type: text/html; charset=utf-8" );
		@header ( 'HTTP/1.0 500 Framework Error' );
		$outputFormat = isset ( $_GET ['outputFormat'] ) ? $_GET ['outputFormat'] : $adapterRequest ['outputFormat'];
		
		if ($outputFormat == 'json') {
			echo json_encode ( $data );
			exit ();
		} elseif ($outputFormat == 'xml') {
			//@todo
			echo 'not finished';
			exit ();
		} elseif ($outputFormat == 'txt') {
			echo $error;
		} else if ($outputFormat == 'html') {
			$str = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
			$str .= '<HTML><HEAD><TITLE>错误提示 </TITLE>';
			if ($returnUrl != '') {
				$str .= '<meta http-equiv="refresh" content="3;url=' . $returnUrl . '"  >';
			}
			$str .= '</HEAD><body><div style="width:430px;color: #4f6b72; background-color:#FFF; margin:140px auto auto 300px; height:170px;';
			$str .= ' border:7px solid #EFEFEF; font-size:12px; overflow:hidden;"><div style="height:28px; border-bottom:1px solid #C1DAD7; line-height:28px; text-indent:20px; font-weight:bold">错误提示</div><div style="background:';
			// 			$str .='url('.HOME_URL.'/static/images/error.gif) no-repeat 40px 35px; height:150px;"><span></span><div style="margin-left:120px; margin-top:40px; float:left; font-size:14px;  height:80px;">错误代码:'.$code;
			$str .= ' <p>错误信息:' . $error . '</p></div></div></div></BODY></HTML>';
			echo $str;
		}
		exit ();
	}
}

?>