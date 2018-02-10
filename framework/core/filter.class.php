<?php
/**
 * @参数验证函数
 * @method:
 * @author liu
 * */
abstract class Filter {
	/**
	 * 类型
	 * @var array
	 */
	public static $varType = array ('GET' => INPUT_GET, 'POST' => INPUT_POST, 'COOKIE' => INPUT_COOKIE, 'SERVER' => INPUT_SERVER, 'ENV' => INPUT_ENV );
	
	public static $filterType = array ('STRING' => FILTER_SANITIZE_STRING, 'INT' => FILTER_VALIDATE_INT, 'BOOLEAN' => FILTER_VALIDATE_BOOLEAN, 'FLOAT' => FILTER_VALIDATE_FLOAT, 'REGEXP' => FILTER_VALIDATE_REGEXP, 'URL' => FILTER_VALIDATE_URL, 'EMAIL' => FILTER_VALIDATE_EMAIL, 'IP' => FILTER_VALIDATE_IP );
	
	/**
	 * 支持过滤列表
	 */
	private static function lists() {
		return filter_list ();
	}
	
	/**
	 * 验证类型
	 * @param string $type
	 */
	public static function filterType($type) {
		$filter_list = self::lists ();
		return array_search ( $type, $filter_list ) !== false ? true : false;
	}
	
	/**
	 *
	 * @param $setVarType
	 */
	private static function getVarType($setVarType) {
		$setVarType = strtoupper ( $setVarType );
		return isset ( self::$varType [$setVarType] ) ? self::$varType [$setVarType] : null;
	}
	
	/**
	 *
	 * @param string $setFilterType
	 */
	private static function getFilterType($setFilterType) {
		$setFilterType = strtoupper ( $setFilterType );
		return isset ( self::$filterType [$setFilterType] ) ? self::$filterType [$setFilterType] : null;
	}
	
	/**
	 * 验证变量
	 * @param string $var
	 * @param string $filterType
	 */
	public static function FilterVar($var, $filterType) {
		$filterType = self::getFilterType ( $filterType );
		return filter_var ( $var, $filterType );
	}
	
	/**
	 * 字符串
	 * @param string $var
	 */
	public static function string($var) {
		return self::FilterVar ( $var, 'STRING' );
	}
	
	public static function int($var) {
		return self::FilterVar ( $var, 'INT' );
	}
	
	public static function boolean($var) {
		return self::FilterVar ( $var, 'INT' );
	}
	
	public static function float($var) {
		return self::FilterVar ( $var, 'FLOAT' );
	}
	
	/**
	 *
	 * @param string $var
	 * @param array $option array("options"=>array("regexp"=>"/^M(.*)/"))
	 */
	public static function Regexp($var, $option) {
		$filterType = self::getFilterType ( $filterType );
		return filter_var ( $var, $filterType, $option );
	}
	
	// 验证邮件格式  
	public static function email($str) {
		return self::FilterVar ( $str, 'EMAIL' );
	}
	
	// 验证身份证  
	public static function idcode($str) {
		if (preg_match ( "/^\d{14}(\d{1}|\d{4}|(\d{3}[xX]))$/", $str ))
			return true;
		else
			return false;
	}
	
	// 验证http地址  
	public static function http($str) {
		if (preg_match ( "/[a-zA-Z]+:\/\/[^\s]*/", $str ))
			return true;
		else
			return false;
	}
	
	//匹配QQ号(QQ号从10000开始)  
	public static function qq($str) {
		if (preg_match ( "/^[1-9][0-9]{4,}$/", $str ))
			return true;
		else
			return false;
	}
	
	//匹配中国邮政编码  
	public static function postcode($str) {
		if (preg_match ( "/^[1-9]\d{5}$/", $str ))
			return true;
		else
			return false;
	}
	
	//匹配ip地址  
	public static function ip($str) {
		if (preg_match ( "/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $str ))
			return true;
		else
			return false;
	}
	
	// 匹配电话格式  
	public static function telephone($str) {
		if (preg_match ( "/^\d{3}-\d{8}$|^\d{4}-\d{7}$|^\d{4}-\d{8}$/", $str ))
			return true;
		else
			return false;
	}
	
	// 匹配手机格式  
	public static function mobile($str) {
		if (preg_match ( "/^1[0-9]{10}$/", $str ))
			return true;
		else
			return false;
	}
	
	// 匹配26个英文字母  
	public static function en_word($str) {
		if (preg_match ( "/^[A-Za-z]+$/", $str ))
			return true;
		else
			return false;
	}
	
	// 匹配只有中文  
	public static function cn_word($str) {
		if (preg_match ( "/^[\x80-\xff]+$/", $str ))
			return true;
		else
			return false;
	}
	
	// 验证账户(字母开头，由字母数字下划线组成，4-16字节)  
	public static function username($str) {
		if (preg_match ( "/^[a-z|A-Z]\w{3,15}$/", $str ))
			return true;
		else
			return false;
	}
	
	// 验证数字  
	public static function number($str) {
		if (preg_match ( "/^[0-9]+$/", $str ))
			return true;
		else
			return false;
	}
	
	// 验证密码
	public static function password($str) {
		if (preg_match ( "/^(([a-z|A-Z|0-9])|(.)){6,16}/", $str ))
			return true;
		else
			return false;
	}
	
	// 验证邮箱
	public static function zip($str) {
		if (preg_match ( "/^[0-9]{6}$/", $str ))
			return true;
		else
			return false;
	}
	
	// 验证区号 (验证固话部分)
	public static function area($str) {
		if (preg_match ( "/^[0-9]{3,6}$/", $str ))
			return true;
		else
			return false;
	}
	
	// 验证电话 (验证固话部分)
	public static function phone($str) {
		if (preg_match ( "/^[0-9]{7,8}$/", $str ))
			return true;
		else
			return false;
	}
	
	// 验证码 格式验证
	public static function code($str) {
		if (preg_match ( "/^[0-9|A-Z|a-z]{4}$/", $str ))
			return true;
		else
			return false;
	}
	
	// 纯数字验证码 格式验证
	public static function number_code($str) {
		if (preg_match ( "/^[0-9]{6}$/", $str ))
			return true;
		else
			return false;
	}
}
