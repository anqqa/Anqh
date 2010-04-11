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
	 * Transliterate UTF8 text to lowercase 7bit ASCII, 0-9a-z
	 *
	 * @param   string  $str
	 * @return  string
	 */
	public static function clean($str) {
		$str = mb_strtolower(text::strip_ascii_ctrl($str));
		if (!text::is_ascii($str)) {
			$str = strtolower(text::transliterate_to_ascii($str));
		}
		if (!text::is_ascii($str)) {
			$str = text::strip_non_ascii($str);
		}

		return $str;
		// return strtolower(iconv(Kohana::CHARSET, 'ASCII//TRANSLIT//IGNORE', $str));
	}


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
	 * Makes a UTF-8 string uppercase.
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function strtoupper($str) {
		return mb_strtoupper($str);
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
