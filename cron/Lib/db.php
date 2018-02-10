<?php
class db {
        static private $connect = null;
		static private $recordConnect = array();
		static private $selfObj = null;
		static private $currentDB = null;
        private function __construct() {
                
        }
		
        public static function instance($dbKey = 'default') {
			global $recordConnect;
            if(self::$selfObj === null || self::$currentDB != $dbKey) {
				if($dbKey == '') {
					$dbKey = 'default';
				}
				
				if(!isset(Conf::$arr[$dbKey])) {
					echo '连接数据库信息不存在';exit;
				}
				
				if(!isset(Conf::$arr[$dbKey]['host']) ||
				!isset(Conf::$arr[$dbKey]['port']) ||
				!isset(Conf::$arr[$dbKey]['username']) ||
				!isset(Conf::$arr[$dbKey]['password']) ||
				!isset(Conf::$arr[$dbKey]['dbname']) ||
				!isset(Conf::$arr[$dbKey]['charset'])) {
					echo '连接数据库信息不存在';exit;
				}
				
				$host = Conf::$arr[$dbKey]['host'];
				$port = Conf::$arr[$dbKey]['port'];
				$username = Conf::$arr[$dbKey]['username'];
				$password = Conf::$arr[$dbKey]['password'];
				$dbname = Conf::$arr[$dbKey]['dbname'];
				$charset = Conf::$arr[$dbKey]['charset'];
				
				self::$currentDB = $dbKey;
				self::$selfObj = new self;
				self::$connect = mysqli_connect($host.':'.$port, $username, $password);
				if(!self::$connect) {
					echo '连接数据库失败';exit;
				}

				$status = mysqli_select_db(self::$connect , $dbname);
				if(!$status) {
					echo '选择数据库失败';exit;
				}

				$status = mysqli_query(self::$connect, 'set names '.$charset);
				if(!$status) {
					echo '设置编码集失败';exit;
				}

				$recordConnect[self::$currentDB] = self::$connect;
            }

            return self::$selfObj;
        }
		
        public function query($sql) {
                if(!self::$connect) {
                        return false;
                }
                $boo = mysqli_query(self::$connect, $sql);
                return $boo;
        }
		
        public function select_one($sql) {
                $result = $this->query($sql);
                if(!$result) {
                        return $result;
                }
				
                $row = mysqli_fetch_assoc($result);
				if(empty($row)) {
					return array();
				}
				
				return $row;
        }
		
        public function select($sql, $primaryKey = '') {
                $result = $this->query($sql);
                if(!$result) {
					return $result;
                }
				
                $rows = array();
				if(empty($primaryKey)) {
					while($row = mysqli_fetch_assoc($result)) {
							$rows[] = $row;
					}
				}else {
					while($row = mysqli_fetch_assoc($result)) {
							$rows[$row[$primaryKey]] = $row;
					}
				}
                
                return $rows;
        }
        public function getError() {
            return  mysqli_errno(self::$connect) . " : " . mysqli_error(self::$connect). "\n";
        }
		
		public function insert($sql) {
			$result = $this->query($sql);
			if(!$result) {
				return false;
			}
			return mysqli_insert_id(self::$connect);
		}
		
		public function batchInsert($sql) {
			$result = $this->query($sql);
			if(!$result) {
				return false;
			}
			
			return true;
		}
		
		public function update($sql) {
			$result = $this->query($sql);
			if(!$result) {
				return false;
			}
			return mysqli_affected_rows(self::$connect);
		}
		
		public function delete($sql) {
			return $this->query($sql);
		}
		
		public function startTransAction() {
			$sql = 'start transaction';
			return $this->query($sql);
		}
		
		public function rollback() {
			$sql = 'rollback';
			return $this->query($sql);
		}
		
		public function commit() {
			$sql = 'commit';
			return $this->query($sql);
		}
		
		public function showDatabaseInfo() {
			$database = $this->select_one('select database()');
			if(empty($database)) {
				return '';
			}
			
			return $database['database()'];
		}
}
?>