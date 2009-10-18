<?php
/**
 * Anqh extended array helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class arr extends arr_Core {

	/**
	 * Get a value from array or default if not found
	 *
	 * @param   array   $array
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public static function get(array $array, $key, $default = null) {
		return $value = array_key_exists($key, $array) ? $array[$key] : $default;
	}


	/**
	 * Get a value from array and delete key or default if not found
	 *
	 * @param   array   $array
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public static function get_once(array &$array, $key, $default = null) {
		$value = self::get($array, $key, $default);
		unset($array[$key]);

		return $value;
	}

}
