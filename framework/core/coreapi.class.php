<?php
/**
 * mysql数据库业务操作层,根据参数自动生成sql,执行并返回结果
 * 进行系统内置方法的封装
 * implements idata接口.
 * @version 1.0
 * @author liu
 * @copyright 2011-12-05 16:31
 */
class CoreApi {//implements idata {
	
	protected $db;
	protected $_tableName ;
	protected $_primaryKey;
	protected $_module;
	protected $_fields;
	
	static public function &instance (){
		$class = get_called_class();
		if (!isset($class::$instance__) || empty($class::$instance__)) {
			$class::$instance__ = new $class;
		}
 
		db::$mod = $class::$instance__->_module;//当使用了这个连接后, 当前操作的db切换为这个连接
		return $class::$instance__;
	}
	
	
	
	public function __construct() {
		$this->db = new db($this->_module);
	}
	
	/**
	 * 插入字段检测
	 * @param array $data 待插入数组
	 * @return bool 是否正确 
	 */
	protected function checkInsertFields($data) {
		if(!is_array($data) || empty($data)) {
			return false;
		}
		/*if(isset($data[$this->_primaryKey])) {
			return false;
		}*/
		foreach ($data as $k => $v) {
			if(!isset($this->_fields[$k])) {
				echo  '<p>数据库不存在字段 : '.$k.'  </p>';
				return false;
			}
		}
		//如果存在多的字段,返回错误,多一个主键
		if($this->_primaryKey == '') {
			if(count($data) != count($this->_fields)) {
				echo  '<p>提交数据有缺失字段</p>';
				return $this->compare($data, $this->_fields);
			}
		} else {
			if(isset($data[$this->_primaryKey])) {
				if(count($data) != count($this->_fields)) {
					echo  '<p>提交数据有缺失字段,请检查</p>';
					return $this->compare($data, $this->_fields);
				}
			} else {
				if(count($data)+1 != count($this->_fields)) {
					echo  '<p>错误信息:提交数据有缺失字段,请检查</p>';
					return $this->compare($data, $this->_fields);
				}
			}
		}
		return true;
	}
	
	
	private function compare($user, $fields) {
		$need = array();
		$error = array();
		$right = array();
		foreach ($fields as $k => $v) {
			if(isset($user[$k])) {
				$right[] = $k;
				unset($user[$k]);
			} else {
				if($k != $this->_primaryKey)
				$need[] = $k;
			}
		}
		$error = $user;
		echo ('<h3 style="color:green">正确的字段信息:</h3><hr>');
		var_dump($right);
		echo ('<h3>缺失的字段信息:</h3><hr>');
		var_dump($need);
		echo ('<h3 style="color:red">错误的字段信息:</h3><hr>');
		var_dump($error);
		return false;
	}
	/**
	 * 更新字段检测
	 *
	 * @param array $data 待更新数组
	 */
	protected function checkUpdateFields($data) {
		if(!is_array($data) || empty($data)) {
			return false;
		}
		//更新的字段中不允许有主键
		if(isset($data[$this->_primaryKey])) {
			echo  '<p>更新数组中不允许出现主键!  </p>';
			return false;
		}
		foreach ($data as $k => $v) {
			if(!isset($this->_fields[$k])) {
				echo  '<p>数据库不存在字段 : '.$k.'  </p>';
				var_dump($this->_tableName);
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 查询字段检测
	 *
	 * @param array $fields 查询字段数组
	 */
	protected function checkSearchFields($data) {
		if(!is_array($data) || empty($data)) {
			return false;
		}
		
		foreach ($data as $k => $v) {
			if(!isset($this->_fields[$v])) {
				echo  '<p>数据库不存在字段 : '.$k.'  </p>';
				return false;
			}
		}
		return true;
	}
	
	
	protected function checkFieldExist($field) {
		//var_dump($this->_fields);
		return array_key_exists($field, $this->_fields) ? true : false;
	}
	
	/**
	 * 基本的操作方法,如何是本地化的时候要重写此方法.重写只要确定$data不是来自于$_POST即可.
	 */
	public function insert($data = '', $force = false){
		if(!is_array($data) || empty($data)) {
			echo ('数据不匹配,不允许数据库操作');exit;
			return false;
		}
		/*if(isset($data[$this->_primaryKey])) {
			unset($data[$this->_primaryKey]);
		}*/
		if(!$this->checkInsertFields($data)) {
			echo ('字段不匹配,不允许数据库操作');exit;
			return false;
		}
		
		$binds=array();
		$fields = array();
		$i = 0;
		foreach ($data as $f => $v){
				$binds[":".$f] = $v;
				$fields[$i] = $f;
				$i++;
		}
		$sql = 'insert ';
		if($force) {
			$sql = 'replace ';
		} 
 		$sql .= " into ".$this->_tableName."(".implode(",",$fields).") values(:".implode(",:",$fields).")";
 		return $this->db->insert($sql, $binds);
	}
	
	/**
	 * 批量插入
	 * 应用层调用该方法，必须使用事务
	 * @param array $data						数据，二维数组
	 * @param int	 $everyOneBatchMaxNumber	分批插入时，每批中的最大个数
	 * @param boolean $force					是否使用 replace into
	 * @return boolean 
	 * @author zy
	 */
	public function insertBatch($data = '', $everyOneBatchMaxNumber = 100, $force = false){
		if(!is_array($data) || empty($data)) {
			echo ('数据不匹配,不允许数据库操作');exit;
			return false;
		}
		
		$firstData = array_shift($data);
		if(!is_array($firstData) || empty($firstData)) {
			echo ('数据不匹配,不允许数据库操作');exit;
			return false;
		}
		
		if(!$this->checkInsertFields($firstData)) {
			echo ('字段不匹配,不允许数据库操作');exit;
			return false;
		}
		
		$fields = array_keys($firstData);
		array_unshift($data, $firstData);
		// 将数据分批
		$dataBatch = plugins::calculateDataBatch($data, $everyOneBatchMaxNumber);
		foreach($dataBatch as $aSingleData) {
			// 插入每一个 批次的数据
			$values = array();
			$binds=array();
			$i = 0;
			foreach($aSingleData as $datak => $dataItem) {
				$valueFields = array();
				foreach($dataItem as $key => $val) {
					$i++;
					$fieldKey = ":".$i."_".$key;
					$binds[$fieldKey] = $val;
					$valueFields[] = $fieldKey;
				}
				
				$values[] = '('.implode(',', $valueFields).')';
			}

			$sql = 'insert ';
			if($force) {
				$sql = 'replace ';
			}

			$sql .= ' into '.$this->_tableName.'('.implode(',',$fields).') values'.implode(',',$values);
			$insertResult = $this->db->insert($sql, $binds);
			if($insertResult === false) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * 更新数据
	 *
	 * @param array $params
	 */
	public function update($data = '', $primaryKey = 0){
		if ($primaryKey == 0) {
			return false;
		}
		if(!is_array($data) || empty($data)) {
			return false;
		}
		//基本校验
		if(isset($data[$this->_primaryKey])) {
			unset($data[$this->_primaryKey]);
		}
		if(!$this->checkUpdateFields($data)) {
			return false;
		}
		$fields = array();
		$i = 0;
		foreach ($data as $f => $v){
				$binds[":".$f] = $v;
				$fields[$i] = $f."=:".$f;
				$i++;
		}
		
		$binds[':'.$this->_primaryKey] = $primaryKey;
		$sql = "update ".$this->_tableName." set  ".implode(",", $fields)." where ".$this->_primaryKey.'=:'.$this->_primaryKey;
		return $this->db->update($sql, $binds);
	}
	
	/**
	 * 删除一行记录
	 *
	 * @param integer $primaryKey 主键
	 * @return integer $result 影响条数
	 */
	public function delete($primaryKey){
		if(!$primaryKey) {//一定是数字类型?
			return false;
		}
		$sql = 'delete from '.$this->_tableName.' where '.$this->_primaryKey.'=:'.$this->_primaryKey;
		return $this->db->delete($sql, array(':'.$this->_primaryKey => $primaryKey));
	}
	
	/**
	 * 多种方法查询数据库
	 *
	 * @param array $fields 筛选字段
	 * @param array $params 条件数组
	 * @param integer $limit 返回条数
	 * @param integer $offset 开始条目
	 * @param string $order 排序
	 * @param string $orderSequence 升序asc / 降序desc
	 * @return array $list 多行记录
	 */
	public function search($fields = '*', $params, $page = 1, $pagesize = 20, $order = '', $orderSequence = 'DESC', $returnType='array') {
		if($fields != '*') {
			if(!$this->checkSearchFields($fields)) {
				return false;
			}
			$fields = implode(',', $fields); //暂不用过滤,视为安全
		}

		if(!is_array($params) ||$page < 0 || $pagesize < 0) {
			return false;
		} 
		//check $params
		foreach ($params as $k=>$v) {
			if(!$this->checkFieldExist($k)) {
				return false;
			}
		}
		$orderSequence = strtoupper($orderSequence);
		if($orderSequence != 'DESC' && $orderSequence != 'ASC') {
			return false;
		}
		$sql = 'select '.$fields.' from '.$this->_tableName.' where 1 ';
		;
		$binds = array();
		foreach ($params as $k=>$v) {
			$binds[':'.$k] = $v;
			$sql .= ' and '.$k.'=:'.$k;
		}
		
		if($order != '') {//检查字段存在情况
			$sql .= ' order by '.$order.' '.$orderSequence;
		}
		 
		$data = $this->db->page($sql, $binds, $page, $pagesize, $returnType);
		return $data;
	}
	
	/**
	 * 获取一行记录
	 *
	 * @param array $fields 筛选字段
	 * @param integer $primaryKey 主键ID
	 * @return array $result 一行记录
	 */
	public function row($fields = '*', $primaryKey, $returnType = 'Array') {
		if(!$primaryKey) {
			return false;
		}
		if(is_array($fields) && !empty($fields)) {
			$fields = implode(',', $fields);
		} else {
			$fields = '*';
		}
		$sql = 'select '.$fields.' from '.$this->_tableName.' where '.$this->_primaryKey.'=:'.$this->_primaryKey;
		return $this->db->select_one($sql, array(':'.$this->_primaryKey=>$primaryKey), $returnType);
	}
	
	/**
	 * 返回多个ID的记录
	 *
	 * @param array $fields 筛选字段
	 * @param 数组 $primaryKeys 主键ID
	 * @return array $list 多行记录
	 */
	public function rows($fields = '*', $primaryKeys = array(), $returnType='Array') {
		if(!is_array($primaryKeys) || empty($primaryKeys)) {
			return false;
		}
		if(is_array($fields) && !empty($fields)) {
			$fields = implode(',', $fields);
		} else {
			$fields = '*';
		}
		//@todo in 中可能存在安全性问题
		$sql = 'select '.$fields.' from '.$this->_tableName.' where '.$this->_primaryKey.' in('.implode(',', $primaryKeys).')';
		$results = $this->db->select($sql, array(), $returnType);
		$tmp = array();
		if(!empty($results)) {
			foreach ($results as $v) {
				if(strtolower($returnType) == 'array') {
					$tmp[$v[$this->_primaryKey]] = $v;
				} else {
					$primary = $this->_primaryKey;
					$tmp[$v->$primary] = $v;
				}
			}
		}
		return $tmp;
		
	}
	
	//比较操作
	public function max($params) {
		;
	}
	public function min($params) {
		;
	}
	/**
	 * 根据条件返回总数
	 *
	 * @param $array $params 筛选条件
	 * @return integer $result 总数
	 */
	public function count($params) {
		if(!is_array($params)) {
			return 0;
		}
		//check $params
		foreach ($params as $k=>$v) {
			if(!$this->checkFieldExist($k)) {
				return 0;
			}
		}
		
		$sql = 'select count(*) as recordCount from '.$this->_tableName.' where 1 ';
		$binds = array();
		foreach ($params as $k=>$v) {
			$binds[':'.$k] = $v;
			$sql .= ' and '.$k.'=:'.$k;
		}
		$result = $this->db->select_one($sql, $binds);
		return $result['recordCount'];
	}
}

?>