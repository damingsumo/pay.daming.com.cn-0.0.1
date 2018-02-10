<?php
class File {
	//http请求超时时间
	const TIME_OUT = 10;
	//允许上传文件扩展名
	private static $allow_type = array ('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/bmp', 'image/x-icon', 'text/plain', 'application/vnd.ms-excel', 'application/msword' );
	//水印切图允许的图片类型
	private static $image_allow_type = array ('image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/bmp' );
	//允许上传文件大小单位M
	private static $allow_size = 3;
	//一次上传大小单位M
	private static $file_once_size = 3;
	//上传地址
	private static $file_host = FILE_SERVER;
	//上传接口方法
	private static $file_func = 'upload.php';
	
	private static $file_name = '';
	
	private static $params = '';
	
	//文件临时地址
	private static $file_tmp_dir = '/tmp_upload/';
	
	//本地临时文件绝对路径
	private static $file_tmp_address = '';
	
	private static $instance__;
	static public function &instance() {
		if (! isset ( self::$instance__ )) {
			$class = __CLASS__;
			self::$instance__ = new $class ( );
		}
		return self::$instance__;
	}
	
	/**
	 * 上传文件
	 */
	public function upload() {
		if (empty ( $_FILES ['file'] )) {
			return array ();
		}
		
		if (! in_array ( $_FILES ["file"] ['type'], self::$allow_type )) {
			return array ('code' => '400', 'msg' => '文件格式不允许上传' );
		}
		$filesize = $_FILES ["file"] ["size"] / (1024 * 1024);
		if ($filesize > self::$allow_size) {
			return array ('code' => '400', 'msg' => '文件过大不允许上传' );
		}
		
		self::$file_name = $_FILES ["file"] ["name"];
		$result = $this->tmpSave ();
		if (is_array ( $result )) {
			return array ('code' => '400', 'msg' => $result ['msg'] );
		}
		
		$params ['file_name'] = self::$file_name;
		$params ['contents'] = self::$file_tmp_address;
		$result = $this->send ( $params );
		//删除临时文件
		$this->deleteTmp ();
		return $result;
	}
	
	/**
	 * 上传至服务器
	 */
	final function send($fileData) {
		//文件服务器接口地址
		$url = trim ( self::$file_host, '/' ) . '/' . self::$file_func;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ("content-type: application/x-www-form-urlencoded; charset=utf-8" ) );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $fileData ) );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$strReturn = curl_exec ( $ch );
		if (curl_errno ( $ch )) {
			$msg = '  CURL请求' . $url . '超时,错误信息：' . curl_error ( $ch );
			curl_close ( $ch );
			return array ('status' => '400', 'data' => $msg );
		}
		
		curl_close ( $ch );
		return json_decode ( $strReturn, true );
	}
	
	/**
	 * 临时保存本地服务器
	 */
	final function tmpSave() {
		if (! is_dir ( WWW_ROOT . self::$file_tmp_dir )) {
			mkdir ( WWW_ROOT . self::$file_tmp_dir );
		}
		$file = explode ( '.', self::$file_name );
		$newFileName = time () . '.' . end ( $file );
		if (file_exists ( WWW_ROOT . self::$file_tmp_dir . $newFileName )) {
			return array ('code' => 400, 'msg' => '文件已存在' );
		}
		$result = move_uploaded_file ( $_FILES ["file"] ["tmp_name"], WWW_ROOT . self::$file_tmp_dir . $newFileName );
		if (! $result) {
			return array ('code' => 400, 'msg' => '文件临时保存失败' );
		}
		
		self::$file_tmp_address = WWW_ROOT . self::$file_tmp_dir . $newFileName;
		return true;
	}
	
	/**
	 * 删除临时文件
	 */
	final function deleteTmp() {
		@unlink ( self::$file_tmp_address );
		self::$file_tmp_address = '';
	}
}
