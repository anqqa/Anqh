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

}
