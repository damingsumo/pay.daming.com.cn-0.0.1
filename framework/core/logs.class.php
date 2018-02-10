<?php
class Logs {
	public static $log_id = '';
	public static $log_dir = LOGS_DIR;
	public static $log_name = '';
	//访问日志
	public static function access($content, $params = array()) {
		self::$log_name = 'access.log.' . date ( 'Y-m-d' );
		self::write ( 'access', $content, $params );
	}
	//API请求日志
	public static function api($content, $params = array()) {
		self::$log_name = 'api.log.' . date ( 'Y-m-d' );
		self::write ( 'api', $content, $params );
	}
	//业务服务日志
	public static function service($content, $params = array()) {
		self::$log_name = 'service.log.' . date ( 'Y-m-d' );
		self::write ( 'service', $content, $params );
	}
	//调试日志
	public static function debug($content, $params = array()) {
		self::$log_name = 'debug.log.' . date ( 'Y-m-d' );
		self::write ( 'debug', $content, $params );
	}
	//异常日志
	public static function exception($content, $params = array()) {
		self::$log_name = 'exception.log.' . date ( 'Y-m-d' );
		self::write ( 'exception', $content, $params );
	}
	//错误日志
	public static function error($content, $params = array()) {
		self::$log_name = 'error.log.' . date ( 'Y-m-d' );
		self::write ( 'error', $content, $params );
	}
	//性能日志
	public static function perf($content, $params = array()) {
		self::$log_name = 'perf.log.' . date ( 'Y-m-d' );
		self::write ( 'perf', $content, $params );
	}
	//mysql日志@todo
	public static function sql($sql, $binds, $type = 'sql') {
		self::$log_name = 'sql.log.' . date ( 'Y-m-d' );
		if (! empty ( $binds ) && is_array ( $binds )) {
			foreach ( $binds as $key => $value ) {
				$sql = str_replace ( $key, "'" . $value . "'", $sql );
			}
		}
		self::write ( $type, $sql );
	}
	public static function write($type, $content, $params = array()) {
		$data = '[' . self::$log_id . ']' . date ( 'Y-m-d H:i:s' ) . '[' . $type . ']';
		if (! is_dir ( self::$log_dir )) {
			mkdir ( self::$log_dir, 0775, true );
		}
		$log = self::$log_dir . self::$log_name;
		if (is_array ( $content )) {
			foreach ( $content as $key => $value ) {
				if (is_array ( $value )) {
					$data .= '[' . $key . ']{array->json}' . json_encode ( $value );
				} else {
					$data .= '[' . $key . ']' . $value;
				}
			
			}
		} else {
			$data .= $content;
		}
		if (! empty ( $params ) && is_array ( $params )) {
			foreach ( $params as $key => $value ) {
				if (is_array ( $value )) {
					$data .= '[' . $key . ']{array->json}' . json_encode ( $value );
				} else {
					$data .= '[' . $key . ']' . $value;
				}
			
			}
		} else if (! is_array ( $params )) {
			$data .= $params;
		}
		$data .= "\r\n";
		file_put_contents ( $log, $data, FILE_APPEND );
	}
}