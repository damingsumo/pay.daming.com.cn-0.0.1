<?php
class WebApi {
    static public function &instance (){
    	
    	$class = get_called_class();
        if (!isset($class::$instance__) || empty($class::$instance__)) {
            $class::$instance__ = new $class;
        }
        return $class::$instance__;
    }
    
    /**
	 * 多种方法查询数据库
	 * @param string $fields 筛选字段
	 * @param array $params 条件数组
	 * @param integer $page 页码
	 * @param integer $pagesize 每页条数
	 * @param string $order 排序
	 * @param string $orderSequence 升序asc / 降序desc
	 * @return array 
	 */
    public function search($fields = '*', $params = array(), $page = '', $pagesize = '', $order = '', $orderSequence = 'DESC', $returnType='array') {
        $class = get_called_class();
        $class = str_replace('WebApi', 'CoreApi', $class);
        return $class::instance()->search($fields, $params, $page, $pagesize, $order, $orderSequence, $returnType);
    } 
    
    /**
	 * 返回多个ID的记录
	 * @param array $fields 筛选字段
	 * @param 数组 $primaryKeys 主键ID
	 * @return array
	 */
	public function rows($fields = '*', $primaryKeys = array(), $returnType='Array') {
        if(empty($primaryKeys) && !is_array($primaryKeys)) {
            return array();
        }
        $class = get_called_class();
        $class = str_replace('WebApi', 'CoreApi', $class);
        return $class::instance()->rows($fields, $primaryKeys, $returnType);
    }
    
    /**
     * 返回多个ID的记录
     * @param array $fields 筛选字段
     * @param 数组 $primaryKeys 主键ID
     * @return array
     */
    public function row($fields = '*', $primaryKey, $returnType='Array') {
        if(!$primaryKey) {
			return false;
		}
        $class = get_called_class();
        $class = str_replace('WebApi', 'CoreApi', $class);
        return $class::instance()->row($fields, $primaryKey, $returnType);
    }
    
    /**
     * @note 返回查询的数量
     * @param $data 查询条件数组
     * @return int 
     */
	public  function count($data) {
		if(!is_array($data)) {
			return 0;
		}
		 
		$class = get_called_class();
        $class = str_replace('WebApi', 'CoreApi', $class);
        return $class::instance()->count($data); 
	}
}
