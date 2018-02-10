<?php
/**
 * Request 网站整体入口,之后递交Controller
 * 整过过程就是在定位到底最终要执行哪个controller, 找到了相应的controller后, 转交给controller进行业务处理
 * @author liu
 * 2013-05-17
 */
class Request {
	
	//访问的模块名
	protected $_module = '';
	
	//调用的controller
	protected $_controller = 'help';
	
	//执行的action
	protected $_controller_function = 'index';
	//默认首页
	protected $_default_module = 'index';
	
	//默认执行的方法
	protected $_default_function = 'index';
	
	//默认的输出方式,如果是ajax或者别的请求使用别的输出方式
	protected $_outputFormat = 'html';
	
	//通用操作(一般是不带前缀的操作方法)
	public function publicService() {
		return true;
	}
	
	//action 操作服务(带action前缀的操作)
	public function actionService() {
		$result = Account::checkLogin ();
		return true;
	}
	
	//system 系统操作服务(系统整体需要进行的操作)
	public function systemService() {
		return true;
	}
	
	/**
	 * 前台程序的入口
	 * 1.判断是什么页面, 普通网页 或 用户中心
	 * 2.如果是用户中心进行登录检测,日志操作
	 * 3.真实的业务操作
	 *
	 */
	public function execute() {
		Logs::$log_id = time () . rand ( 10, 100 );
		$controller = $this->loadModuleClass ( $this->_controller );
		$action = false;
		$function = $this->_controller_function;
		//判断$this->_controller_function 是否带前缀,前缀系统识别action,未来可能增加更多
		$bool = method_exists ( $controller, $this->_controller_function );
		if (! $bool) {
			$function = PREFIX_ACTION . ucfirst ( $this->_controller_function );
			$bool = method_exists ( $controller, $function );
			$action = true;
		}
		if (! $bool) {
			return Response::output ( '', '1026', '不存在该方法' );
		}
		
		//系统全局服务
		$this->systemService ();
		if ($action) { //如果有action前缀的,执行什么特别的操作
			$this->actionService ();
		} else {
			$this->publicService ();
		}
		
		//初始化数据传递
		$controller->_function = $function;
		$controller->_outputFormat = $this->_outputFormat;
		//@todo 第三方服务不走拦截器
		$this->interceptor ();
		$controller->entry__ ();
	}
	
	/**
	 * Constructing method, parse url
	 *
	 */
	public function __construct() {
		if ($_SERVER ['HTTP_HOST'] != substr ( substr ( HOME_URL, 7 ), 0, strlen ( substr ( HOME_URL, 7 ) ) - 1 )) {
			echo '当前域名信息 : ' . $_SERVER ['HTTP_HOST'] . ' : 该域名与系统设置的域名不一致,请到framework/conf/' . $_SERVER ['RUNTIME_ENVIROMENT'] . '/domain.config.php核实';
			exit ();
		}
		
		$this->parse ();
		$this->_controller = CONTROLLER . $this->_controller;
		//@todo增加参数处理与系统服务,或者以后ICE
		ignore_user_abort ( true ); //断开是否会终止脚本的执行
	}
	
	/**
	 * 初始化寻找执行的module, app与action
	 * @todo 增加更多的支持,特别是www.domain.com/docs_cate等支持
	 * @todo 更改为正则.
	 */
	protected function parse() {
		$uri = preg_replace ( '/^\/api\/[\w-]+\/[\w\.]+\//', '', $_SERVER ['REQUEST_URI'], 1 );
		$p = explode ( PARAMS_SEPARATOR, ltrim ( $uri, URL_SEPARATOR ) );
		$p = $p [0];
		if ($p == '') {
			$this->_controller = ucfirst ( $this->_default_module );
			$this->_module = $this->_default_module;
			$this->_controller_function = $this->_default_function;
			return true;
		}
		
		$p = explode ( URL_SEPARATOR, $p );
		$count = count ( $p );
		$this->_module = $p [0];
		$this->_controller = ucfirst ( $this->_module );
		if ($count == 1) {
			$this->_controller_function = $this->_default_function;
		} else {
			for($i = 1; $i < $count - 1; $i ++) {
				$this->_controller .= '_' . ucfirst ( $p [$i] );
			}
			$this->_controller_function = empty ( $p [$count - 1] ) ? $this->_default_function : $p [$count - 1];
		}
	}
	
	/**
	 * Load apps object
	 *
	 * @param string $module
	 * @return module
	 */
	protected function &loadModuleClass($class) {
		if (__loadclass ( $class )) {
			$object = new $class ( );
			return $object;
		}
		//增加跳转 404
		return Response::output ( '', '1028', $class . ' 类不存在.' );
	}
	
	/**
	 * 拦截器
	 * 权限访问 + 参数处理
	 */
	private function interceptor() {
		//调试模式不拦截
		if (DEBUG) {
			return true;
		}
		$msg = empty ( $_REQUEST ) ? '请求参数为空' : $_REQUEST;
		Logs::access ( '调用接口类' . $this->_controller . '方法' . $this->_controller_function . '参数：', $msg );
		//step1：固定参数处理
		//业务线ID 来源基础服务 必须
		$serviceId = isset ( $_REQUEST ['serviceId'] ) ? $_REQUEST ['serviceId'] : '';
		//业务线版本 统一版本管理 必须
		$serviceVersion = isset ( $_REQUEST ['serviceVersion'] ) ? $_REQUEST ['serviceVersion'] : '';
		//业务线类型 0 Web端,1 移动端， 2 Wap端 必须
		$serviceType = isset ( $_REQUEST ['serviceType'] ) ? $_REQUEST ['serviceType'] : '';
		//回溯信息
		$state = isset ( $_REQUEST ['state'] ) ? $_REQUEST ['state'] : '';
		//默认当前版本号（对方服务版本）
		$version = isset ( $_REQUEST ['version'] ) ? $_REQUEST ['version'] : '';
		//代理发送
		$proxy = isset ( $_REQUEST ['proxy'] ) ? $_REQUEST ['proxy'] : '';
		//设备信息
		$device = isset ( $_REQUEST ['device'] ) ? $_REQUEST ['device'] : '';
		//请求参数
		$data = isset ( $_REQUEST ['data'] ) ? $_REQUEST ['data'] : '';
		if ($serviceId != '') {
			$_REQUEST = $_POST = $_GET = $data;
			$_SERVER ['serviceId'] = $serviceId;
			$_SERVER ['serviceVersion'] = $serviceVersion;
			$_SERVER ['serviceType'] = $serviceType;
			$_SERVER ['state'] = $state;
			$_SERVER ['version'] = $version;
			$_SERVER ['proxy'] = $proxy;
			$_SERVER ['device'] = $device;
		}
		$adapterRequest = config ( "domainArray", $_SERVER ['HTTP_HOST'] );
		//外部请求
		if ($adapterRequest ['controller'] == 'api') {
			if ($serviceId == '') {
				return Response::output ( '', 1053, '请求API接口时serviceId不能为空' );
			}
			if ($data != '') {
				//解密
				$remote = new Remote ( $serviceId, 'from' );
				$key = $remote->getFromKey ();
				$encrypt = new Encrypt ( $key );
				if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
					$_REQUEST = $_GET = $_POST = JsonProtocol::onedecode ( $encrypt->decrypt ( $data ) );
					Logs::access ( '请求参数解密结果', $_REQUEST );
				}
			}
		}
		
	//echo json_encode($data);exit;
	

	//参数校验
	

	//step2：权限管理
	//        echo '您没有权限访问此接口';exit;
	}
}