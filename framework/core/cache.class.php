<?php
/**
 * redis操作类包括缓存和队列
 * @author wqy
 */
class Cache {
	protected static $host;
	protected static $port;
	protected static $db;
	protected static $password;
	protected static $redis;
	public function __construct($config) {
		self::$host = $config ['host'];
		self::$port = $config ['port'];
		self::$db = $config ['db'];
		self::$password = $config ['password'];
		self::$redis = $this->connect ();
	}
	//连接redis
	public function connect() {
		if (! isset ( self::$host ) || ! isset ( self::$port ) || ! isset ( self::$password )) {
			die ( 'redis配置参数缺失' );
		}
		$server = new Redis ( );
		$bool = $server->connect ( self::$host, self::$port );
		if ($bool) {
			$result = $server->auth ( self::$password );
			if ($result == 'OK') {
				return $server;
			}
			$result = $server->select ( self::$db );
			if ($result == 'OK') {
				return $server;
			}
			die ( 'redis授权密码错误' );
		}
		die ( 'redis连接失败' );
	}
	
	/**
	 * 存储到redis
	 * @param string $key 存储key名
	 * @param string $value 存储值
	 * @return bool
	 */
	public function set($key, $value) {
		if (empty ( $key )) {
			return false;
		}
		
		return self::$redis->set ( $key, json_encode ( $value ) );
	}
	
	/**
	 * 一次存储多个key、value
	 * @param array $params 一次存储多个key、value的数组参数
	 * @return bool
	 */
	public function mset($params) {
		if (empty ( $params ) || ! is_array ( $params )) {
			return false;
		}
		
		foreach ( $params as $key => &$value ) {
			if (is_array ( $value )) {
				$value = json_encode ( $value );
			}
		}
		return self::$redis->mset ( $params );
	}
	
	/**
	 * 获取redis中的一个键的数据
	 * @param string $key 请求参数
	 * @return string
	 */
	public function get($key) {
		if (empty ( $key )) {
			return false;
		}
		
		$result = self::$redis->get ( $key );
		return json_decode ( $result, true );
	}
	
	/**
	 * 删除一个键值
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key) {
		return self::$redis->del ( $key );
	}
	
	/**
	 * 判断一个键值是否存在
	 * @param string $key
	 * @return boolean
	 */
	public function exist($key) {
		return self::$redis->exists ( $key );
	}
	
	//查找所有符合给定模式 pattern 的 key
	public function keys($key) {
		return self::$redis->keys ( $key );
	}
	
	/**
	 * 在list头部添加
	 * @param array $params 请求参数
	 * @return bool
	 */
	public function lpush($key, $value) {
		if (empty ( $key )) {
			return false;
		}
		
		return self::$redis->lPush ( $key, json_encode ( $value ) );
	}
	
	public function rpush($key, $value) {
		if (empty ( $key )) {
			return false;
		}
		
		return self::$redis->rPush ( $key, json_encode ( $value ) );
	}
	/**
	 * @note 获取hash数据
	 * @params string $queue
	 * @param string $key
	 */
	public function hGet($queue, $key) {
		if ($queue == '' || $key == '') {
			return array ();
		}
		$data = self::$redis->hGet ( $queue, $key );
		$data = json_decode ( $data, true );
		return $data;
	}
	
	/**
	 * @note 设置hash数据
	 * @params string $queue
	 * @param string $key
	 * @param string $value
	 */
	public function hSet($queue, $key, $value) {
		if ($queue == '' || $key == '' || $value == '') {
			return false;
		}
		return self::$redis->hSet ( $queue, $key, json_encode ( $value ) );
	}
}
