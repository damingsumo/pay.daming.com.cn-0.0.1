<?php
/**
 * @package     Tiger
 * @copyright   Tiger
 * @author      Tiger
 * @version     $ $
 */
class db {
	
	public static $conn = null;
	public static $connArray = array ();
	private $STMT = null;
	private $connectArray = array ();
	
	public static $lastSQL = '';
	public static $line;
	public static $transTimes;
	
	public static $mod = 'Default';
	
	/**
	 * 初始化DB,并且要找到相应的静态数据库连接
	 *
	 * @param string $mod 配置模块
	 * @param string $connChar 编码集
	 */
	public function __construct($mod) {
		self::$mod = $mod;
	}
	
	//初始化数据库的连接
	public function initConn() {
		$dsn = config ( "dbs", self::$mod );
		if (empty ( $dsn )) {
			echo '数据库配置错误';
			exit ();
		}
		
		$dsn = explode ( '|', $dsn, 3 );
		$dsn0 = isset ( $dsn [0] ) ? $dsn [0] : "";
		$dsn1 = isset ( $dsn [1] ) ? $dsn [1] : "";
		$dsn2 = isset ( $dsn [2] ) ? $dsn [2] : "";
		$this->connectArray = array ($dsn0, $dsn1, $dsn2 ); //为什么不写成$this->writeArray = $dsn;呢？
		

		if (! isset ( self::$connArray [self::$mod] ) || self::$connArray [self::$mod] === null) {
			self::$connArray [self::$mod] = $this->get_connection ( $this->connectArray [0], $this->connectArray [1], $this->connectArray [2] );
		}
		self::$conn = self::$connArray [self::$mod];
		
		if (isset ( self::$transTimes [self::$mod] ) && self::$transTimes [self::$mod] == 1) {
			self::$connArray [self::$mod]->beginTransaction ();
			self::$transTimes [self::$mod] = 0;
		}
		// 		var_dump(self::$mod);
	}
	
	/**
	 *    create connection
	 *    $url connect to db url
	 *    $user connect to db user
	 *    $passwd connect to db password
	 *  OK
	 */
	private function get_connection($url, $user, $passwd) {
		$conn = null;
		try {
			$attr = array ();
			$attr [PDO::ATTR_PERSISTENT] = FALSE;
			$attr [PDO::ATTR_TIMEOUT] = 5;
			$attr [PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'utf8'";
			$conn = new PDO ( $url, $user, $passwd, $attr );
		} catch ( PDOException $e ) {
			self::log ( '[WARN!!!] CLIENT=' . http::getClientIP () . ' URI= ' . $_SERVER ['REQUEST_URI'] . ' DB=' . $url . ' ERRCODE= ' . $e->getCode () . "\n ERRMSG=" . $e->getMessage () . " Referer: " . (isset ( $_SERVER ['HTTP_REFERER'] ) ? $_SERVER ['HTTP_REFERER'] : "None") );
		}
		if ($conn) {
			$db_info = $conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			if ($db_info == "MySQL server has gone away") {
				$conn = new PDO ( $url, $user, $passwd, $attr );
			}
		}
		return $conn;
	}
	
	/**
	 * prepare write sql
	 * OK
	 */
	private function prepare($sql) {
		if ($sql == '') {
			return false;
		}
		$this->initConn ();
		if (null == self::$conn) {
			return false;
		}
		
		self::$lastSQL = $sql;
		$this->STMT = self::$conn->prepare ( $sql );
	}
	
	/**
	 * bind write params
	 * OK
	 */
	private function bind($param, $var) {
		if (! isset ( $param )) {
			return;
		}
		if (null == $param) {
			return;
		}
		if (! isset ( $this->STMT )) {
			return;
		}
		$this->STMT->bindParam ( $param, $var );
	}
	
	public function page($sql, $binds, $page, $pageSize, $returnFormat = 'Array') {
		$limitSQL = '';
		if ($pageSize > 0) {
			$limitSQL = " limit " . (($page - 1) * $pageSize) . ", " . $pageSize;
		}
		return $this->select ( $sql . $limitSQL, $binds, $returnFormat );
	
	}
	
	public function select($sql, $binds = array(), $returnFormat = 'Array', $close = true) {
		$bool = $this->prepare ( $sql );
		$results = array ();
		
		if (is_array ( $binds ) && ! empty ( $binds )) {
			$i = 0;
			foreach ( $binds as $param => $var ) {
				$i ++;
				if (! $param || is_numeric ( $param )) {
					$param = $i;
				} else if ($param [0] != ':') {
					$param = ':' . $param;
				}
				
				$this->bind ( $param, $var );
			}
		}
		try {
			Logs::sql ( $sql, $binds );
			
			$this->STMT->execute ();
			$results = $this->STMT->fetchAll ( PDO::FETCH_ASSOC );
			
			if ($results == false) {
				$error = $this->STMT->errorInfo ();
				Logs::sql ( $error, array () );
				return strtolower ( $returnFormat ) != 'array' ? new ArrayObject ( ) : array ();
			}
			
		//     		if ($close !== false) {
		//     			var_dump('colose');
		//     			$this->STMT_close();
		//     			$this->write_close();
		//     		}
		} catch ( Exception $e ) {
			Logs::sql ( $e->getMessage (), array () );
			echo 'SQL:' . self::$lastSQL;
			ECHO '<HR>错误原因:' . $e->getMessage ();
			return false;
		}
		
		if (strtolower ( $returnFormat ) == 'array') {
			return $results;
		}
		
		//对象化数据
		$object = new ArrayObject ( );
		if (! empty ( $results )) {
			$model = new $returnFormat ( );
			foreach ( $results as $row ) {
				$model = clone $model;
				$model->setRow ( $row );
				$object->append ( $model );
			}
		}
		return $object;
	}
	
	public function select_one($sql, $binds = array(), $returnFormat = 'Array', $close = false) {
		$results = $this->select ( $sql, $binds, 'Array', $close );
		if (strtolower ( $returnFormat ) == 'array') {
			return isset ( $results [0] ) && is_array ( $results [0] ) ? $results [0] : array ();
		} else {
			$model = new $returnFormat ( );
			if (isset ( $results [0] ) && is_array ( $results [0] ) && ! empty ( $results [0] )) {
				$model->setRow ( $results [0] );
			}
			return $model;
		}
	}
	
	public function update($sql, $binds = array(), $close = false) {
		return $this->execute ( $sql, $binds, $close, "update" );
	}
	
	public function delete($sql, $binds = array(), $close = false) {
		return $this->execute ( $sql, $binds, $close, "delete" );
	}
	
	public function insert($sql, $binds = array(), $close = false) {
		return $this->execute ( $sql, $binds, $close, "insert" );
	}
	
	public function emptyPage() {
		$r = new stdClass ( );
		$r->total = 0;
		$r->totalPage = 0;
		$r->page = 1;
		$r->pageSize = 20;
		$r->offset = 0;
		$r->records = array ();
		$r->recordFormat = "array";
		$r->querytime = 0;
		return $r;
	}
	
	public function execute($sql, $binds = array(), $close = false, $type = "other") {
		$this->prepare ( $sql );
		if (! $this->STMT)
			return false;
		try {
			Logs::sql ( $sql, $binds );
			if (is_array ( $binds ) && ! empty ( $binds )) {
				$i = 0;
				foreach ( $binds as $param => $var ) {
					$i ++;
					if (! $param || is_numeric ( $param )) {
						$param = $i;
					} else if ($param [0] != ':') {
						$param = ':' . $param;
					}
					$this->bind ( $param, $var );
				}
			}
			
			$result = $this->STMT->execute ();
			if ($result == false) {
				$error = $this->STMT->errorInfo ();
				Logs::sql ( $error, array () );
				return false;
			}
			
		// 			if ($close !== false) {
		// 				var_dump(222);
		// 				$this->STMT_close();
		// 				$this->write_close();
		// 			}
		

		} catch ( Exception $e ) {
			Logs::sql ( $e->getMessage (), array () );
			echo 'SQL:' . self::$lastSQL;
			ECHO '<HR>错误原因:' . $e->getMessage ();
			return false;
		}
		switch ($type) {
			case "insert" :
				return ( integer ) $this->lastInsertID () ? ( integer ) $this->lastInsertID () : true;
			case "delete" :
			case "update" :
				return ( integer ) $this->getRowCount ();
		}
		
		return $result;
	}
	
	/**
	 * close  stmt
	 * OK
	 */
	public function STMT_close() {
		$this->STMT = null;
	}
	
	/**
	 * close all
	 * OK
	 */
	public function write_close() {
		$this->STMT = null;
		self::$conn = null;
		self::$connArray = null;
	}
	
	/**
	 * close all
	 * OK
	 */
	public function read_close() {
		$this->STMT = null;
		self::$conn = null;
		self::$connArray = null;
	}
	
	/**
	 * get last insert primary key
	 * OK
	 */
	public function lastInsertID() {
		if (! isset ( self::$conn ))
			return - 1;
		if (null == self::$conn)
			return - 1;
		return self::$conn->lastInsertId ();
	}
	
	/**
	 * get last update/delete affect row num
	 * OK
	 */
	public function getRowCount() {
		if (! isset ( $this->STMT ))
			return - 1;
		if (null == $this->STMT)
			return - 1;
		return $this->STMT->rowCount ();
	}
	
	/**
	 * log
	 * OK
	 */
	static private function log($info) {
		error_log ( '[db]' . $info );
	}
	
	/**
	 * destruct
	 * OK
	 */
	function __destruct() {
		$this->STMT_close ();
		$this->read_close ();
		//$this->write_close();
	}
	
	/**
	 * begin transaction
	 * OK
	 */
	public static function begintrans($mod) {
		self::$mod = $mod;
		self::$transTimes [$mod] = 1;
		return true;
	
	}
	
	/**
	 * commit
	 * OK
	 */
	public static function commit($mod) {
		if (isset ( self::$connArray [$mod] )) {
			$result = self::$connArray [$mod]->commit ();
		} else {
			echo '没有找到相关事务,请在使用事务时将rollback方法中配置数据库连接';
			exit ();
		}
		
		if (! $result) {
			return false;
		}
		
		return true;
	
	}
	
	/**
	 * rollback
	 * OK
	 */
	public static function rollback($mod) {
		if (isset ( self::$connArray [$mod] )) {
			$result = self::$connArray [$mod]->rollback ();
		} else {
			echo '没有找到相关事务,请在使用事务时将rollback方法中配置数据库连接';
			exit ();
		}
		if (! $result) {
			return false;
		}
		return true;
	}

}
?>
