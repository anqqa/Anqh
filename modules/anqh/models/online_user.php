<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Online user Model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Online_User_Model extends ORM {

	protected $primary_key = 'session_id';

	protected $rules = array(
		'session_id'    => array('rules' => array('required', 'length[1,40]')),
		'last_activity' => array('rules' => array('required')),
	);


	/**
	 * Get online users
	 *
	 * @static
	 * @return  array
	 */
	public static function find_online_users() {
		self::gc();

		$online = array();
		$users = db::build()->select('user_id')->from('online_users')->where('user_id', '>', 0)->execute()->as_array();
		foreach ($users as $user) {
			$online[(int)$user['user_id']] = (int)$user['user_id'];
		}

		return $online;
	}


	/**
	 * Garbage collect
	 *
	 * @static
	 */
	public static function gc() {
		static $collected = false;

		// Remove users idle for over 15 minutes
		if (!$collected) {
			$collected = true;
			db::build()->delete('online_users', array(array('last_activity', '<', time() - 60 * 15)))->execute();
		}

	}


	/**
	 * Get number of guests online
	 *
	 * @static
	 * @return  integer
	 */
	public static function get_guest_count() {
		self::gc();

		return (int)ORM::factory('online_user')->where('user_id', 'IS', null)->count_all();
	}

}
