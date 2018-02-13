<?php
/**
 * @package     Framework
 * @copyright   Framework
 * @author      Liu
 * @version     $ create by Liu 2013-05-18 $
 */

/**
 * template Class
 *
 */
class template {
	
	/**
	 * Instance of this singleton
	 *
	 * @var template
	 */
	private static $instance__;
	
	private $engine;
	/**
	 * Instance of this singleton class
	 *
	 * @return template
	 */
	static public function &instance() {
		if (! isset ( self::$instance__ )) {
			$class = __CLASS__;
			self::$instance__ = new $class ( );
		}
		return self::$instance__;
	}
	
	public function __construct() {
		$this->engine = $this->getEngine ();
	}
	/**
	 * Prepare a Smarty object
	 *
	 * @return Smarty
	 */
	public function &getEngine(&$module = null) {
		require (FW_PATH . '/plugins/smarty/Smarty.class.php');
		
		$smarty = new Smarty ( );
		//上线后,因为模板不会变,会更改为false
		$smarty->setCompile_check ( true );
		//模板目录
		$smarty->setTemplateDir ( TEMPLATE_DIR );
		//编译目录
		$smarty->setCompileDir ( TPLCACHE_PATH ); #设置新的编译目录
		//缓存目录
		$smarty->setCacheDir ( FW_PATH . '/cache/' );
		
		//		$smarty->force_compile = true;
		$smarty->debugging = false;
		$smarty->caching = false;
		$smarty->cache_lifetime = 120;
		return $smarty;
	}
	
	/**
	 * Check if template exists.
	 *
	 * @param $module
	 * @param string $template
	 * @return bool
	 */
	public function templateExists($template) {
		return file_exists ( $template );
	}
	
	/**
	 * Render according to a template
	 *
	 * @param $module
	 * @param string $template
	 * @param array $parameters
	 * @return string
	 */
	public function render($template, $parameters = array()) {
		$template = TEMPLATE_DIR . $template . '.tpl';
		
		//检查模板是否存在
		if (! $this->templateExists ( $template )) {
			return Response::displayError ( 1029, '找不到模板 ' . $template );
		}
		
		foreach ( $parameters as $varname => $value ) {
			$this->engine->assign ( $varname, $value );
		}
		print_r(1);exit;
		$this->engine->display ( $template );
	}
	
	/**
	 * fetch according to a template
	 *
	 * @param string $template
	 * @param array $parameters
	 * @return string
	 */
	public function fetch($template, $parameters = array()) {
		$template = TEMPLATE_DIR . $template . '.tpl';
		
		//检查模板是否存在
		if (! $this->templateExists ( $template )) {
			return Response::displayError ( 1029, '找不到模板 ' . $template );
		}
		
		foreach ( $parameters as $varname => $value ) {
			$this->engine->assign ( $varname, $value );
		}
		return $this->engine->fetch ( $template );
	}
}
?>