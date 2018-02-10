<?php
/**
 * Controller控制层
 * @author liu
 * @copyright bestdo.com 2013-05-18
 */
class Controller {
	
	//用户请求访问的方法
	public $_function = '';
	
	public $_outputFormat = 'html';
	
	protected $_data = array ();
	
	//默认网站头
	public $_header = 'layouts/header'; //尚未启用，启用会降低smarty效率
	

	//默认网站底部
	public $_footer = 'layouts/footer'; //
	

	//完成controller的时候才执行
	//	public function __destruct() {
	//	 	return Response::instance()->output($this->_data);
	//	}
	

	/**
	 * Entry function of the module
	 * @see http
	 *
	 * @return bool
	 */
	public function entry__() {
		$funName = $this->_function;
		//$this->before();	//预处理
		$this->$funName ();
		//$this->after();	//主要方法执行后工作
		return true;
	}
	
	/**
	 * @todo smarty性能原因，尚未启用
	 * 显示网站头
	 * 从权限地方得到title,再传递到前端,一般根据不同的模块与平台的时候,需要在所属的controller类时行重写这个方法
	 * 
	 *
	 */
	public function before() {
		if ($this->_outputFormat == 'html') {
			$this->display ( $this->_header );
		}
	}
	
	/**
	 * @todo smarty性能原因，尚未启用
	 * 显示网站尾
	 *
	 */
	public function after() {
		if ($this->_outputFormat == 'html') {
			$this->display ( $this->_footer );
		}
	}
	
	//HTML的形式输出方式
	public function display($template, $parameters = array()) {
		//暂时不启用自动更新网站头
		//    	$parameters['template_header'] = $this->_header;
		//    	$parameters['template_footer'] = $this->_footer;
		//期望模板与模块自动对应
		if (! strpos ( $template, '/' )) {
			$template = str_replace ( '_', '/', ltrim ( strtolower ( get_class ( $this ) ), 'controller_' ) ) . '/' . $template;
		}
		
		Response::instance ()->display ( $template, $parameters );
	}
	
	//输出方式只有非HTML的方式的时候才生效
	public function output($data, $status = 200, $msg = 'success', $isEncrypt = false) {
		Response::instance ()->output ( $data, $status, $msg, $isEncrypt );
	}
	
	/**
	 * ajax返回数据
	 *
	 * @param 返回数据 $data
	 * @param 状态码 $status 只有200是正确的
	 */
	public function ajaxSuccess($data, $status = 200) {
		echo json_encode ( array ('data' => $data, 'status' => $status ) );
		exit ();
	}
	
	/**
	 * ajax返回数据
	 *
	 * @param 返回数据 $data
	 * @param 状态码 $status 只有200是正确的
	 */
	public function ajaxError($data, $status = 400) {
		echo json_encode ( array ('data' => $data, 'status' => $status ) );
		exit ();
	}
	
	//smarty fetch方法 HTML的形式返回方式模板 
	public function fetch($template, $parameters = array()) {
		//暂时不启用自动更新网站头
		//    	$parameters['template_header'] = $this->_header;
		//    	$parameters['template_footer'] = $this->_footer;
		//期望模板与模块自动对应
		if (! strpos ( $template, '/' )) {
			$template = str_replace ( '_', '/', ltrim ( strtolower ( get_class ( $this ) ), 'controller_' ) ) . '/' . $template;
		}
		return Response::instance ()->fetch ( $template, $parameters );
	}
}
?>
