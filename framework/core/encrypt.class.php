<?php
/**
 * base64（3des）加密
 * @author zq
 * @copyright bestdo.com
 */
class encrypt {
	private static $key = "1234567";
	
	/**
	 *加密
	 * @param $value
	 */
	public static function encryptValue($value) {
		$td = mcrypt_module_open ( MCRYPT_3DES, '', MCRYPT_MODE_ECB, '' );
		mcrypt_enc_get_block_size ( $td );
		$iv = mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		mcrypt_generic_init ( $td, self::$key, $iv );
		$ret = base64_encode ( mcrypt_generic ( $td, $value ) );
		mcrypt_generic_deinit ( $td );
		mcrypt_module_close ( $td );
		return $ret;
	}
	
	/**
	 *解密
	 * @param $value
	 */
	public static function decrypt($value) {
		$td = mcrypt_module_open ( MCRYPT_3DES, '', MCRYPT_MODE_ECB, '' );
		$iv = mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		mcrypt_generic_init ( $td, self::$key, $iv );
		$ret = trim ( mdecrypt_generic ( $td, base64_decode ( $value ) ) );
		mcrypt_generic_deinit ( $td );
		mcrypt_module_close ( $td );
		return $ret;
	}
}
