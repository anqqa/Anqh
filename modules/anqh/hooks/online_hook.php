<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Hook to keep track of online users
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class online_hook {

	/**
	 * Hook the recorder
	 */
	public function __construct() {
		Event::add('system.pre_controller', array($this, 'online'));
	}


	/**
	 * Save current user to online cache
	 */
	public function online() {
		$user = Visitor::instance()->get_user();

		$online = new Online_User_Model($_SESSION['session_id']);
		if (!$online->loaded()) {
			$online->session_id = $_SESSION['session_id'];
		}
		$online->last_activity = $_SESSION['last_activity'];
		$online->user_id = $user ? $user->id : null;
		try {
			$online->save();
		} catch (ORM_Validation_Exception $e) {}
	}

}

new online_hook();
