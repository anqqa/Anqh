<?php
/**
 * Anqh extended Cache library.
 *
 * Added profiler and key prefix support.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Cache extends Cache_Core {

	public static $queries = array('gets' => array(), 'sets' => array(), 'deletes' => array());


	/**
	 * Delete a cache item by key.
	 *
	 * @param   string   $key
	 * @return  boolean
	 */
	public function delete($key) {
		Cache::$queries['deletes'][] = $key;
		return parent::delete($key);
	}


	/**
	 * Fetches a cache by key. NULL is returned when a cache item is not found.
	 *
	 * @param   string  $key
	 * @return  mixed   cached data or NULL
	 */
	public function get($key) {
		Cache::$queries['gets'][] = $key;
		return parent::get($key);
	}


	/**
	 * Return cache key.
	 *
	 * @return  string
	 */
	public function key() {
		$parts = func_get_args();

		return is_array($parts) ? implode('_', $parts) : $parts;
	}


	/**
	 * Set a cache item by key. Tags may also be added and a custom lifetime
	 * can be set. Non-string data is automatically serialized.
	 *
	 * @param   string   $key
	 * @param   mixed    $data
	 * @param   array    $tags      use NULL
	 * @param   integer  $lifetime  number of seconds until the cache expires
	 * @return  boolean
	 */
	public function set($key, $data, $tags = NULL, $lifetime = NULL) {
		Cache::$queries['sets'][] = $key;
		return parent::set($key, $data, $tags, $lifetime);
	}

}
