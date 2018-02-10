<?php
/**
 * @package     Framework
 * @copyright   Framework
 * @author      liu
 * @version     $ create by liu 2009-09-05$
 */

/**
 * plugins Class
 *
 */
class plugins {
	/**
	 * Instance of this singleton
	 *
	 * @var http
	 */
	private static $instance__;
	
	/**
	 * Instance of this singleton class
	 *
	 * @return plugins
	 */
	static public function &instance() {
		if (! isset ( self::$instance__ )) {
			$class = __CLASS__;
			self::$instance__ = new $class ( );
		}
		return self::$instance__;
	}
	
	static public function load($pluginsName) {
		$pluginsPath = PFW_ROOT . "/plugins/" . $pluginsName;
		if (file_exists ( $pluginsPath )) {
			include_once ($pluginsPath . "/" . $pluginsName . ".plugins.php");
			return new $pluginsName ( );
		}
	}
	
	static public function getMicrotime() {
		$mic_arr = explode ( ' ', microtime () );
		$temp_time = $mic_arr [1];
		$temp_microtime = substr ( strchr ( $mic_arr [0], '.' ), 1 );
		$microtime = $temp_time . '.' . $temp_microtime;
		return $microtime;
	}
	
	static public function calculateDataBatch($data, $everyOneBatchMaxNumber = 100) {
		if (! is_array ( $data ) || empty ( $data ) || $everyOneBatchMaxNumber <= 0) {
			return false;
		}
		
		$recordNumber = 0;
		$aSingleBatch = array ();
		$totalBatch = array ();
		if (count ( $data ) <= $everyOneBatchMaxNumber) {
			$aSingleBatch = $data;
		} else {
			foreach ( $data as $key => $dataItem ) {
				$aSingleBatch [$key] = $dataItem;
				++ $recordNumber;
				
				if ($recordNumber >= $everyOneBatchMaxNumber) {
					$totalBatch [] = $aSingleBatch;
					$aSingleBatch = array ();
					$recordNumber = 0;
				}
			}
		}
		
		if (! empty ( $aSingleBatch )) {
			$totalBatch [] = $aSingleBatch;
			$aSingleBatch = array ();
			$recordNumber = 0;
		}
		
		return $totalBatch;
	}
	
	/**
	 * 转换字符串编码集
	 * @param type $content
	 * @param type $toCode
	 * @return type 
	 */
	public static function convertEncode($content, $toCode = 'UTF-8') {
		if (empty ( $content ) || ! is_string ( $content ) || empty ( $toCode ) || ! is_string ( $toCode )) {
			return $content;
		}
		
		$formCode = mb_detect_encoding ( $content, array ('UTF-8', 'ASCII', 'GB2312', 'GBK', 'BIG5' ) );
		if (strtolower ( $formCode ) != strtolower ( $toCode )) {
			$content = mb_convert_encoding ( $content, $toCode, $formCode );
		}
		
		return $content;
	}
}
?>