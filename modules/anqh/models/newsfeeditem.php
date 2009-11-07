<?php
/**
 * News feed item model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class NewsFeedItem_Model extends Modeler_ORM {

	// ORM
	protected $belongs_to = array('user');
	protected $sorting    = array('id' => 'DESC');


	/**
	 * Magic setter
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 */
	public function __set($key, $value) {
		if ($key == 'data') {
			$value = is_null($value) ? null : json_encode($value);
		}

		parent::__set($key, $value);
	}


	/**
	 * Magic getter
	 *
	 * @param  string  $key
	 */
	public function __get($key) {
		$value = parent::__get($key);

		if ($key == 'data') {
			$value = is_null($value) ? null : json_decode($value, true);
		}

		return $value;
	}

}
