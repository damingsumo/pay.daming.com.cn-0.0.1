<?php
class WebApi {
		public $_tableName = '';
		protected $_db = null;
		protected $_splitTableNumber = 100;
		static protected $_currentDB = null;
		public function __construct($dbKey = 'default', $id = 0) {
			$this->_getTableName($id);
			$this->_db = db::instance($dbKey);
		}

		
        public function _query($sql) {
			return $this->_db->query($sql);
        }
		
        public function _select_one($sql) {
			return $this->_db->select_one($sql);
        }
		
        public function _select($sql, $primaryKey = '') {
			return $this->_db->select($sql, $primaryKey);
        }
		
        public function _getError($sql) {
            return $this->_db->getError($sql);
        }

        public function _insertLog($sql){
            return $this->_db->insert($sql);
        }
		
		public function _insert($data, $returnSql = false) {
			if(!is_array($data) || empty($data)) {
				return false;
			}
			
			if(empty($this->_tableName)) {
				return false; 
			}
			
			$field = '';
			$values = '';
			foreach($data as $key => $val) {
				$field .= $key.',';
				$values .= '\''.$val.'\',';
			}
			
			$field = substr($field,0,-1);
			$values = substr($values,0,-1);
			$sql = 'insert into '.$this->_tableName.'('.$field.') values('.$values.')';
			
			if($returnSql) {
				return $sql;
			}else {
				return $this->_db->insert($sql);
			}
		}
		
		public function _batchInsert($data, $returnSql = false) {
			if(!is_array($data) || empty($data)) {
				return false;
			}
			
			if(empty($this->_tableName)) {
				return false; 
			}
			
			$firstLine = true;
			$field = '';
			$values = '';
			foreach($data as $index=> $item) {
				if(!empty($item)) {
					if($firstLine) {
						foreach($item as $key => $val) {

							$field .= $key.',';
						}
						$firstLine = false;
					}
					
					$values .= "('".implode("','",$item)."'),";
				}
			}
			
			$field = substr($field,0,-1);
			$values = substr($values,0,-1);
			$sql = 'insert into '.$this->_tableName.'('.$field.') values'.$values;
			if($returnSql) {
				return $sql;
			}else {
				return $this->_db->batchInsert($sql);
			}
		}
		
		public function _update($data, $primaryKeyId, $returnSql = false) {
			if(!is_array($data) || empty($data) || $primaryKeyId <= 0) {
				return false;
			}
			
			if(empty($this->_tableName) || empty($this->_primaryKey)) {
				return false;
			}
			
			$setString = '';
			foreach($data as $key => $val) {
				$setString .= $key.'=\''.$val.'\',';
			}
			
			$setString = substr($setString,0,-1);
			$sql = 'update '.$this->_tableName.' set '.$setString.' where '.$this->_primaryKey.'=\''.$primaryKeyId.'\'';
			
			if($returnSql) {
				return $sql;
			}else {
				return $this->_db->update($sql);
			}
		}
		
		public function _delete($primaryKeyId, $returnSql = false) {
			if(empty($this->_tableName) || empty($this->_primaryKey) || $primaryKeyId <= 0) {
				return false;
			}
			
			$sql = 'delete from '.$this->_tableName.' where '.$this->_primaryKey.'= \''.$primaryKeyId.'\'';
			if($returnSql) {
				return $sql;
			}else {
				return $this->_db->delete($sql);
			}
		}
		
		public function _startTransAction() {
			return $this->_db->startTransAction();
		}
		
		public function _rollback() {
			return $this->_db->rollback();
		}
		
		public function _commit() {
			return $this->_db->commit();
		}
		
		public function _showDatabaseInfo() {
			return $this->_db->showDatabaseInfo();
		}
		
		public function _getTableName($id = 0) {
			$className = get_class($this);
			if(strpos($className,'_') !== false) {
				$className = explode('_',$className,2);
				if (is_numeric($id) && $id > 0) {
					$this->_tableName = strtolower($className[1]).'_'.($id%$this->_splitTableNumber);
				}else {
					$this->_tableName = strtolower($className[1]);
				}
				
			}
			
		}

}
?>