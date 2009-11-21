<?php
/**
 * CSRF helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class csrf_Core {

	/**
	 * Token time to live in seconds, 30 minutes
	 *
	 * @var  integer
	 */
	private static $ttl = 1800;


	/**
	 * Get CSRF token
	 *
	 * @param   mixed    $id      Custom token id, e.g. uid
	 * @param   string   $action  Optional action
	 * @param   integer  $time
	 * @return  string
	 */
	public static function token($id = '', $action = '', $time = 0) {

		// Get id string for token, could be uid or ip etc
		if (!$id) $id = Input::instance()->ip_address();

		// Get time to live
		if (!$time) $time = ceil(time() / self::$ttl);

		// Get session specific salt
		if (!isset($_SESSION['csrf_secret'])) {
			$_SESSION['csrf_secret'] = text::random('alnum', 16);
		}

		return md5($time . $_SESSION['csrf_secret'] . $id . $action);
	}


	/**
	 * Validate CSRF token
	 *
	 * @param   string   $token
	 * @param   mixed    $id      Custom token id, e.g. uid
	 * @param   string   $action  Optional action
	 * @return  boolean
	 */
	public static function valid($token = false, $id = '', $action = '') {

		// Default to token
		if (!$token) $token = $_REQUEST['token'];

		// Get time to live
		$time = ceil(time() / self::$ttl);

		// Check token validity
		return ($token === self::token($id, $action, $time) || $token === self::token($id, $action, $time - 1));

	}

}
