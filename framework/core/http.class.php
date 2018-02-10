<?php
/**
 * HTTP公用类
 * @author liu
 * 
 */
class http {
	//是否为ajax
	public static function isAjax() {
		if (isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest') {
			return true;
		}
		return false;
	}
	
	//访问者IP
	public static function clientIP() {
		if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" )) {
			$ip = getenv ( "HTTP_CLIENT_IP" );
		} else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" )) {
			$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
		} else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" )) {
			$ip = getenv ( "REMOTE_ADDR" );
		} else if (isset ( $_SERVER ['HTTP_CLIENT_IP'] ) && strcasecmp ( $_SERVER ['HTTP_CLIENT_IP'], "unknown" )) {
			$ip = $_SERVER ['HTTP_CLIENT_IP'];
		} else if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) && strcasecmp ( $_SERVER ['HTTP_X_FORWARDED_FOR'], "unknown" )) {
			$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
		} else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" )) {
			$ip = $_SERVER ['REMOTE_ADDR'];
		} else {
			$ip = "unknown";
		}
		return $ip;
	}
	
	public static function getClientIP() {
		self::clientIP ();
	}
	/**
	 * Do a 302                           
	 *
	 * @param string $url
	 * @param integer $seconds
	 */
	public static function go($url, $seconds = 0, $target = '') {
		if ($target) {
			$target = 'target="' . $target . '"';
		}
		$str = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
		$str .= '<HTML><HEAD><TITLE> 正在加载...... </TITLE>';
		$str .= '<meta http-equiv="refresh" content="' . $seconds . ';url=' . $url . '" ' . $target . ' >';
		$str .= '</HEAD><BODY></BODY></HTML>';
		echo $str;
		die ();
	}
	
	///////////////////////////////////////////////////////COOKIE SESSION///////////////////////////
	//可能根据不同的客户端写cookie,
	public static function setCookie($key, $value, $expire = 0, $path = "/", $domain = "") {
		if (empty ( $domain )) {
			$domain = DOMAIN;
		}
		return @setcookie ( $key, $value, $expire, $path, $domain );
	}
	
	public static function delCookie($key, $path = "/", $domain = "") {
		if (empty ( $domain )) {
			$domain = DOMAIN;
		}
		@setcookie ( $key, null, time () - 3600, $path, $domain );
		if (array_key_exists ( $key, $_COOKIE )) {
			$_COOKIE [$key] = null;
			unset ( $_COOKIE [$key] );
		}
	}
	
	public static function COOKIE($key, $defaultValue = "") {
		return is_array ( $_COOKIE ) && array_key_exists ( $key, $_COOKIE ) ? $_COOKIE [$key] : $defaultValue;
	}
	
	public static function SESSION($key, $defaultValue = "", $sessionId = 0) {
		if (! isset ( $_SESSION )) {
			session_start ();
			session_write_close ();
		}
		if (ini_get ( 'session.save_handler' ) == 'redis' && $sessionId != '') {
			$redis = new redis ( );
			global $redisConf;
			$redis->connect ( $redisConf ['host'], $redisConf ['port'] );
			$session = $redis->get ( "PHPREDIS_SESSION:" . $sessionId );
			$session = unserialize ( $session );
		} else {
			$session = $_SESSION;
		}
		return is_array ( $session ) && array_key_exists ( $key, $session ) ? $session [$key] : $defaultValue;
	}
	
	public static function setSession($key, $value) {
		session_start ();
		$_SESSION [$key] = $value;
		session_write_close ();
	}
	
	public static function delSession($key) {
		session_start ();
		if (array_key_exists ( $key, $_SESSION )) {
			$_SESSION [$key] = null;
			unset ( $_SESSION [$key] );
		}
		session_write_close ();
		return true;
	}
	
	public static function sessionDestroy() {
		if (isset ( $_SESSION )) {
			return @session_destroy ();
		}
		return false;
	}
	
	public static function curl($url, $post = array()) {
		if (! Filter::http ( $url )) {
			return false;
		}
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		//		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		//    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ("content-type: application/x-www-form-urlencoded; charset=utf-8" ) );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $post ) );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		$strReturn = curl_exec ( $ch );
		
		if (curl_errno ( $ch )) {
			echo 'Errno' . curl_error ( $ch );
		}
		curl_close ( $ch );
		return trim ( $strReturn );
	}
	
	/**
	 * post 发送curl 并且获取返回状态和信息
	 * @param string $url
	 * @param string or array $post
	 * @param string $code
	 * @param boolean $isXml
	 * @return array 
	 */
	public static function curlPost($url, $post = array(), $code = 'utf-8', $isXml = false) {
		$msg = array ('status' => 0, 'msg' => '' );
		if (! Filter::http ( $url )) {
			$msg ['msg'] = 'url格式错误';
			return $msg;
		}
		if (is_array ( $post )) {
			$post = http_build_query ( $post );
		}
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		if ($isXml) {
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ("Content-type: text/xml; charset=" . $code ) );
		} else {
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ("Content-type: text/html; charset=" . $code ) );
		}
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		$strReturn = curl_exec ( $ch );
		$status = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		
		$msg ['status'] = $status;
		if (curl_errno ( $ch )) {
			$msg ['msg'] = 'Errno' . curl_error ( $ch );
		} else {
			$msg ['msg'] = trim ( $strReturn );
		}
		
		curl_close ( $ch );
		return $msg;
	}
	
	// php curl ssl 双向对接
	public static function curlPostSsl($url, $post = array(), $code = 'utf-8', $isXml = false) {
		$msg = array ('status' => 0, 'msg' => '' );
		if (! Filter::http ( $url )) {
			$msg ['msg'] = 'url格式错误';
			return $msg;
		}
		if (is_array ( $post )) {
			$post = http_build_query ( $post );
		}
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		if ($isXml) {
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ("Content-type: text/xml; charset=" . $code ) );
		} else {
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ("Content-type: text/html; charset=" . $code ) );
		}
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		//新的ssl 本地判别文件
		//curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true); ;
		// curl_setopt($ch,CURLOPT_CAINFO,'server.pem');
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, true );
		curl_setopt ( $ch, CURLOPT_CAINFO, 'D:/AppServ/www/BPMS/cert/test.pfx' );
		curl_setopt ( $ch, CURLOPT_SSLCERTPASSWD, '123456' );
		curl_setopt ( $ch, CURLOPT_SSLKEYTYPE, 'pkcs12' );
		
		// ssl end
		

		$strReturn = curl_exec ( $ch );
		$status = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		
		$msg ['status'] = $status;
		if (curl_errno ( $ch )) {
			$msg ['msg'] = 'Errno' . curl_error ( $ch );
		} else {
			$msg ['msg'] = trim ( $strReturn );
		}
		
		curl_close ( $ch );
		return $msg;
	}

}

?>