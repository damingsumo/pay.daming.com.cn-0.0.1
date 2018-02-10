<?php
class Model {
	
	/**
	 * object data by array
	 *
	 * @var array(k => v...)
	 */
	protected $_properties;
	
	/**
	 * construct function
	 *
	 * @param array $_properties
	 * @return void
	 */
	public function __construct($_properties = array()) {
		if (! is_null ( $_properties ) && is_array ( $_properties )) {
			$this->_properties = $_properties;
		}
	}
	
	/**
	 * magic get, access _properties
	 *
	 * @param string $name
	 * @return string
	 */
	public function __get($name) {
		if (! is_null ( $this->_properties ) && is_array ( $this->_properties ) && array_key_exists ( $name, $this->_properties )) {
			return isset ( $this->_properties [$name] ) ? $this->_properties [$name] : '';
		}
		return '';
	}
	
	/**
	 * magic set
	 *
	 * @param string $name
	 * @param mix $value
	 * @return bool
	 */
	public function __set($name, $value) {
		if (! is_array ( $this->_properties )) {
			$this->_properties = array ();
		} else {
			if (array_key_exists ( $name, $this->_properties )) {
				$this->_properties [$name . "_ORI"] = $this->_properties [$name];
			}
		}
		$this->_properties [$name] = $value;
		return true;
	}
	
	/**
	 * magic unset
	 *
	 * @param string $name
	 * @return void
	 */
	public function __unset($name) {
		if (is_array ( $this->_properties ) && array_key_exists ( $name, $this->_properties )) {
			unset ( $this->_properties [$name] );
		}
	}
	
	/**
	 * magic isset
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name) {
		if (is_array ( $this->_properties ) && array_key_exists ( $name, $this->_properties )) {
			return true;
		}
		return false;
	}
	
	/**
	 * 赋值一行
	 * @param array $row
	 */
	public function setRow(Array $row = null) {
		if (is_array ( $row )) {
			$this->_properties = $row;
		}
	}
	
	public function set($var, $val = null) {
		$this->_properties [$var] = $val;
	}
	
	/**
	 * 成员以数组形式返回
	 * @return array
	 */
	public function toArray() {
		return $this->_properties;
	}
	
	/**
	 * 重载方法
	 * @param string $_method
	 * @param $args
	 */
	public function __call($_method, $args) {
		if (method_exists ( $this, $_method )) {
			return $this->$_method ( $args );
		} else {
			$_method = substr ( $_method, 3, strlen ( $_method ) ); // filter "get"
			$key = implode ( '_', preg_split ( '#(?=[A-Z])#', lcfirst ( $_method ) ) );
			$key = strtolower ( $key );
			return isset ( $this->_properties [$key] ) ? $this->_properties [$key] : null;
		}
	}
	
	/**
	 * 判断这是不是一个空对象
	 * 
	 */
	public function isEmpty() {
		if (is_array ( $this->_properties ) && ! empty ( $this->_properties )) {
			return false;
		}
		return true;
	}

}

?>