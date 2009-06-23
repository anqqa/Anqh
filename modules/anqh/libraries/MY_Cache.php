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
	 * Delete a cache item by id.
	 *
	 * @param   string   $id
	 * @return  boolean
	 */
	public function delete($id) {
		Cache::$queries['deletes'][] = $id;
		return parent::delete($id);
	}


	/**
	 * Fetches a cache by id. NULL is returned when a cache item is not found.
	 *
	 * @param   string  $id
	 * @return  mixed   cached data or NULL
	 */
	public function get($id) {
		Cache::$queries['gets'][] = $id;
		return parent::get($id);
	}


	/**
	 * Return prefixed cache id.
	 *
	 * @param   string  $id
	 * @return  string
	 */
	public function key() {
		static $prefix;

		empty($prefix) and $prefix = $this->config['prefix'] . '_';

		$parts = func_get_args();

		return $prefix . implode('_', $parts);
	}


	/**
	 * Set a cache item by id. Tags may also be added and a custom lifetime
	 * can be set. Non-string data is automatically serialized.
	 *
	 * @param   string   $id
	 * @param   mixed    $data
	 * @param   array    $tags      use NULL
	 * @param   integer  $lifetime  number of seconds until the cache expires
	 * @return  boolean
	 */
	public function set($id, $data, $tags = NULL, $lifetime = NULL) {
		Cache::$queries['sets'][] = $id;
		return parent::set($id, $data, $tags, $lifetime);
	}

}
