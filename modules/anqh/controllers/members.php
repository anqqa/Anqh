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


	/***** INTERNAL *****/

	private function _side_views($extra_views = array()) {
		widget::add('side', implode("\n", $extra_views));
	}

	/***** /INTERNAL *****/


	/***** VIEWS *****/

	public function index() {
		$error = array();
		$side_views = array();

		$new_users = ORM::factory('user')->orderby('id', 'DESC')->limit(50)->find_all();

		try {
			$birthdays = Users::get_birthdays();
		} catch (Kohana_Exception $e) {
			$error[] = $e;
		}

		if (empty($error)) {
			$this->page_subtitle = __('Latest :users members', array(':users' => '<var>' . count($new_users) . '</var>'));

			widget::add('main', View::factory('member/members', array('users' => $new_users, 'type' => 'new')));
			$side_views[] = new View('member/birthdays_list', array('birthdays' => $birthdays));
		} else {
			$this->page_title .= ' ' . Kohana::lang('generic.error');
			widget::add('main', implode('<br />', $error));
		}

		$this->_side_views($side_views);
	}

	/***** /VIEWS *****/

}
