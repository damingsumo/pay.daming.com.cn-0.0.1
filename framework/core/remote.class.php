<?php
/**
 * SDK通信HTTP类
 * 2014-12-17 新增加CURL通信
 * 远程通信必须引入此类
 * @author liu 2014-03-05
 * socket 通信采用了workerman的功能.http://www.workerman.net/workerman-jsonrpc
 * @version 1.0.1 2014-09-18 更新的时候务必更改Remote类的version !
 *
 */
class Remote {
	
	private  $version = '1.0.1';
	
	//发送数据和接收数据的超时时间  单位S
	const TIME_OUT = 10;
	
	//基础服务方式
	const REMOTE_MODE = 'CURL';//CURL或者SOCKET只限于两种
	
	//curl模式下域名地址
	const REMOTE_CURL_DOMAIN = 'http://base.service.bestdo.com/';
	
	//socket模式下的地址
	const REMOTE_SOCKET_ADDRESS = 'tcp://192.168.18.189:2014';
	/**
	 * 服务端地址数据, 支持多个服务端
	 * @var array
	 */
	protected static $addressArray = array(
		0	=>	self::REMOTE_SOCKET_ADDRESS
	);

	/**
	 * 异步调用实例
	 * @var string
	 */
	protected static $asyncInstances = array();

	/**
	 * 同步调用实例
	 * @var string
	 */
	protected static $instances = NULL;

	/**
	 * 到服务端的socket连接
	 * @var resource
	 */
	protected  $connection = NULL;

	/**
	 * 实例的服务名
	 * @var string
	 */
	protected $serviceName = 'BASE-SERVICE';

	/**
	 * 获取一个实例
	 *
	 * @param string $service_name
	 * @return object
	 */
	public static function instance() {
		if(self::$instances == NULL) {
			self::$instances = new self();
		}
		return self::$instances;
	}

	/**
	 * 构造函数
	 *
	 * @param string $service_name
	 */
	protected function __construct() {
		
	}

	/**
	 * 调用,目前仅支付同步操作
	 *
	 * @param string $domian
	 * @param string $uri
	 * @param array $arguments
	 * @param string $type GET|POST
	 * @return mixed
	 * @throws Exception
	 */
	public function call($domian, $uri, $arguments, $type = 'GET') {
		if(self::REMOTE_MODE == 'SOCKET') {
			$this->sendSocketData($uri, $arguments, $type, $this->version);
			return $this->recvSocketData();
		} elseif (self::REMOTE_MODE == 'CURL') {//curl
			return $this->sendCurlRequest($domian, $uri, $arguments, $type, $this->version);
		}
		return array('status' => 500, 'data' => null);
	}

	/**
	 * GET方式调用
	 *
	 * @param string $domian
	 * @param string $uri
	 * @param array $arguments
	 * @return array
	 */
	public function get($domian, $uri, $arguments) {
		return $this->call($domian, $uri, $arguments, 'GET');
	}

	/**
	 * POST方式调用
	 *
	 * @param string $domian
	 * @param string $uri
	 * @param array $arguments
	 * @return array
	 */
	public function post($domian, $uri, $arguments) {
		return $this->call($domian, $uri, $arguments, 'POST');
	}

	/**
	 * 发送数据给服务端
	 *
	 * @param string $uri
	 * @param array $arguments
	 * @param string $type GET|POST
	 * @return bool
	 */
	public function sendSocketData($uri, $arguments, $type, $version) {
		$this->openConnection();
		$bin_data = JsonProtocol::encode(array(
				'type' => $type,
				'uri' => $uri,
				'version' => $version,
				'param_array' => $arguments
		));
		return fwrite($this->connection, $bin_data) == strlen($bin_data);
	}

	/**
	 * 从服务端接收数据
	 *
	 * @throws Exception
	 */
	public function recvSocketData() {
		$ret = fgets($this->connection);
		$this->closeConnection();
		if(!$ret) {
			throw new Exception("recvData empty");
		}

		return JsonProtocol::decode($ret);
	}

	//通过CURL访问的处理请求
	public function sendCurlRequest($domain, $uri, $arguments, $type, $version) {
		$enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
		$url = $enviroment == 'online' ? 'http://'.$domain : 'http://test.'.$domain;
		$post = array();
		$url .= '/'.$uri.'?version='.$version.'&action=curl&';
		if($type == 'GET') {
			$url .= http_build_query($arguments);
		} else {
			$post = $arguments;
		} 
		
		$result = self::curl($url, $post);
		 
		return JsonProtocol::decode($result);
	}
	
	/**
	 * 打开到服务端的连接
	 *
	 * @throws Exception
	 */
	protected function openConnection() {
		$address = self::$addressArray[array_rand(self::$addressArray)];
		$this->connection = stream_socket_client($address, $err_no, $err_msg);
		if(!$this->connection)
		{
			throw new Exception("can not connect to $address , $err_no:$err_msg");
		}
		stream_set_timeout($this->connection, self::TIME_OUT);
	}

	/**
	 * 关闭到服务端的连接
	 *
	 * @return void
	 */
	protected function closeConnection() {
		fclose($this->connection);
		$this->connection = null;
	}

	public function setDebug() {
		$this->debug = true;
	}

	public function cancelDebug() {
		$this->debug = false;
	}
	
	//CURL	通信 2014-12-17
	public static function curl($url, $post = array()) {
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		//    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type: application/x-www-form-urlencoded; charset=utf-8" ));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::TIME_OUT);
		$strReturn = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Errno'.curl_error($ch);
		}
		curl_close ($ch);
		return trim($strReturn);
	}
	
}

/**
 * RPC 协议解析 相关
 * 协议格式为 [json字符串\n]
 * */
class JsonProtocol {
	/**
	 * 从socket缓冲区中预读长度
	 * @var integer
	 */
	const PRREAD_LENGTH = 87380;

	/**
	 * 判断数据包是否接收完整
	 *
	 * @param string $bin_data
	 * @return integer 0代表接收完毕，大于0代表还要接收数据
	 */
	public static function dealInput($bin_data) {
		$bin_data_length = strlen($bin_data);
		// 判断最后一个字符是否为\n，\n代表一个数据包的结束
		if($bin_data[$bin_data_length-1] !="\n")
		{
			// 再读
			return self::PRREAD_LENGTH;
		}
		return 0;
	}

	/**
	 * 将数据打包成Rpc协议数据
	 *
	 * @param mixed $data
	 * @return string
	 */
	public static function encode($data) {
		return json_encode($data)."\n";
	}

	/**
	 * 解析Rpc协议数据
	 *
	 * @param string $bin_data
	 * @return mixed
	 */
	public static function decode($bin_data) {
		$result = json_decode(trim($bin_data), true);
		if(json_last_error() == 0) {
			return $result;
		}
		return array('status'=>500, 'data'=>null);
	}
}


?>
