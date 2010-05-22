<?php
/**
 * Index page controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Index_Controller extends Website_Controller {

	/**
	 * Index constructor
	 */
	function __construct() {
		parent::__construct();

		$this->page_id = 'home';
	}


	/**
	 * Home page
	 */
	public function index() {
		$this->page_title = __('Welcome to :site', array(':site' => Kohana::config('site.site_name')));

		// Display news feed
		$newsfeed = new NewsFeed($this->user);
		$newsfeed->max_items = 25;
		widget::add('main', View_Mod::factory('generic/newsfeed', array('newsfeed' => $newsfeed->as_array())));

		// Shout
		$shouts = ORM::factory('shout')->find_all(10);
		widget::add('side', View_Mod::factory('generic/shout', array(
			'mod_title' => __('Shouts'),
			'shouts'    => $shouts,
			'can_shout' => ORM::factory('shout')->has_access(Shout_Model::ACCESS_WRITE, $this->user),
			'errors'    => array(),
			'values'    => array(),
		)));

		// Online
		$guests = Online_User_Model::get_guest_count();
		$online = Online_User_Model::find_online_users();
		$counts = array();
		if ($guests) {
			$counts[] = __2(':guests guest', ':guests guests', $guests, array(':guests' => $guests));
		}
		if (count($online)) {
			$counts[] = __2(':members member', ':members members', count($online), array(':members' => count($online)));
		}
		widget::add('side', View_Mod::factory('generic/users', array(
			'mod_title' => __('Online') . ': ' . implode(', ', $counts),
			'viewer'    => $this->user,
			'users'     => $online,
		)));
	}

}
