<?php
/**
 * Anqh extended utf8 helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class utf8 extends utf8_Core {

	/**
	 * Makes a UTF-8 string lowercase.
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function strtolower($str) {
		return mb_strtolower($str);
	}


	/**
	 * UTF-8 substring
	 *
	 * @param   string   $str
	 * @param   integer  $offset
	 * @param   integer  $length
	 * @return  string
	 */
	public static function substr($str, $offset, $length = NULL) {
		return ($length === NULL) ? mb_substr($str, $offset) : mb_substr($str, $offset, $length);
	}

}
