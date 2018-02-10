<?php
class Tools {
	/**
	 * 输出LOG
	 * @param sting	   $str
	 * @param boolean $start 
	 * @param boolean $end
	 * @author zy
	 */
	public static function echoLog($str, $start = false, $end = false) {
		$time = explode('.', microtime(true));
		$echoDateString = date("Y-m-d H:i:s").'.'. str_pad($time[1], 4, 0, STR_PAD_LEFT);
		if($start) {
			echo '========================================================'."\r\n";
		}
		
		echo $echoDateString.' : '.$str.' '."\r\n";
		
		if($end) {
			echo '========================================================'."\r\n";
		}
	}
}
?>