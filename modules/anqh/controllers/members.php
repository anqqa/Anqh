<?php
/**
 * User listing controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Members_Controller extends Website_Controller {

	/***** MAGIC *****/

	function __construct() {
		parent::__construct();

		$this->page_title = __('Members');
	}

	/***** /MAGIC *****/


	/***** VIEWS *****/

	public function index() {
		$error = array();
		$side_views = array();

		// New users
		$new_users = ORM::factory('user')->order_by('id', 'DESC')->limit(50)->find_all();
		$users = array();
		foreach ($new_users as $user) {
			$users[date('Y-m-d', strtotime($user->created))][] = $user;
		}

		try {
			$birthdays = Users::get_birthdays();
		} catch (Kohana_Exception $e) {
			$error[] = $e;
		}

		if (empty($error)) {
			$this->page_subtitle = __('Latest :users members', array(':users' => '<var>' . count($new_users) . '</var>'));

			widget::add('main', View_Mod::factory('member/members', array('users' => $users, 'type' => 'new')));
			widget::add('side', View_Mod::factory('member/birthdays_list', array('mod_class' => 'birthdays', 'birthdays' => $birthdays)));
		} else {
			$this->page_title .= ' ' . __('Uh oh.');
			widget::add('main', implode('<br />', $error));
		}

	}

	/***** /VIEWS *****/

}
