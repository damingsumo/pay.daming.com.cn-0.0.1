<?php
/**
 * 公司员工管理
 *@author liu
 */
class Log {
	
	public static $instance__;
	
	private $_operates = array ();
	private $_uri = '';
	private $_data = array ();
	private $_resName = '';
	
	private $_sid = 0;
	private $_sname = '';
	
	static public function &instance() {
		if (! isset ( self::$instance__ )) {
			$class = __CLASS__;
			self::$instance__ = new $class ( );
		}
		return self::$instance__;
	}
	
	public function addLog($opration) {
		$this->_operates [] = $opration;
	}
	
	public function addData($data) {
		$this->_data = $data;
	}
	
	public function addUri($uri) {
		$this->_uri = $uri;
	}
	
	public function addSid($sid) {
		$this->_sid = $sid;
	}
	
	public function addStaffName($sname) {
		$this->_sname = $sname;
	}
	
	public function addResName($resName) {
		$this->_resName = $resName;
	}
	
	public function __construct() {
	
	}
	
	public function __destruct() {
		if ($this->_sid && ! empty ( $this->_operates ))
			WebApi_Staff_Log::instance ()->BatchAdd ( $this->_uri, $this->_resName, $this->_sid, $this->_sname, $this->_operates, $this->_data );
	}
	
	/**
	 * 错误输出
	 */
	public static function error($module, $errorInfo) {
		$dir = ERROR_LOG_ADDR . $module;
		if (! is_dir ( $dir )) {
			@mkdir ( $dir, 0777, true );
		}
		$file = $dir . '/' . date ( 'Ymd' ) . '.log';
		$errorInfo = date ( 'Y-m-d H:i:s' ) . ' ' . $errorInfo;
		file_put_contents ( $file, $errorInfo . "\r\n", FILE_APPEND );
	}
}