<?php
/**
 * User profile controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Member_Controller extends Website_Controller {

	/**
	 * Selected user
	 *
	 * @var  User_Model
	 */
	protected $member;

	/**
	 * Base url used for tabs
	 *
	 * @var  string
	 */
	protected $tab_url;


	/***** MAGIC *****/

	public function __construct() {
		parent::__construct();

		$this->breadcrumb[] = html::anchor('members', __('Members'));

		$this->tabs = array();
		$this->tabs['profile'] = array('link' => '', 'text' => __('Profile'));
		if ($this->user) {
			$this->tabs['friends'] = array('link' => '/friends', 'text' => __('Friends'));
		}
		$this->tabs['favorites'] = array('link' => '/favorites', 'text' => __('Favorites'));
		if ($this->user) {
			$this->tabs['facebook'] = array('link' => '/facebook', 'text' => __('Facebook'));
		}
	}


	public function __call($username, $actions = false) {
		$username = urldecode($username);
		$action = is_array($actions) && count($actions) ? $actions[0] : false;
		switch ($action) {

			// Edit basic info
			case 'edit':
				$this->edit($username);
				break;

			// Show Facebook content
			case 'facebook':
				if (FB::enabled()) {
					$this->facebook($username);
				} else {
					$this->view($username);
				}
				break;

			// Show favorite
			case 'favorites':
				$this->favorites($username);
				break;

			// Add to friends
			case 'friend':
				$this->friendadd($username);
				break;

			// View friends
			case 'friends':
				$this->friends($username);
				break;

			// Remove from friends
			case 'unfriend':
				$this->frienddelete($username);
				break;

			// View profile
			default:
				$this->view($username);
				break;

		}
	}

	/***** /MAGIC *****/


	/***** INTERNAL *****/

	public function _side_views($extra_views = array()) {

		if ($this->member->id) {

			$this->breadcrumb[] = html::anchor(url::user($this->member), html::specialchars($this->member->username));

			// Update tabs
			foreach ($this->tabs as &$tab) {
				$tab['link'] = url::user($this->member) . $tab['link'];
			}

			if ($this->user) {

				// owner functions
				if ($this->user->id == $this->member->id) {
					// $this->functions[] = array('link' => url::user($this->member) . '/edit',     'text' => Kohana::lang('member.info_edit'));
					// $this->functions[] = array('link' => 'member/' . urlencode($this->member->username) . '/settings', 'text' => Kohana::lang('member.settings_edit'));
				} else {
					$this->page_actions[] = ($this->user->is_friend($this->member))
						? array('link' => url::user($this->member) . '/unfriend', 'text' => __('Remove from friends'), 'class' => 'friend-delete')
						: array('link' => url::user($this->member) . '/friend',   'text' => __('Add to friends'),      'class' => 'friend-add');
				}
			}

		}

		widget::add('side', implode("\n", $extra_views));

	}

	/***** /INTERNAL *****/


	/***** COMMENTS *****/

	/**
	 * Comment action
	 *
	 * @param  int     $comment_id
	 * @param  string  $action
	 */
	public function comment($comment_id, $action = false) {
		$this->history = false;

		if ($action) {
			switch ($action) {

				// delete comment
				case 'delete':
					$this->commentdelete($comment_id);
					return;

			}
		}

		url::redirect(empty($_SESSION['history']) ? '/members' : $_SESSION['history']);
	}


	/**
	 * Delete comment
	 *
	 * @param  int  $comment_id
	 */
	public function commentdelete($comment_id) {
		$this->history = false;

		// for authenticated users only
		if (!$this->user) url::redirect(empty($_SESSION['history']) ? '/members' : $_SESSION['history']);

		$comment = new User_Comment_Model((int)$comment_id);
		if ($comment->id) {

			// allow only to delete one's own comments
			if (in_array($this->user->id, array($comment->author_id, $comment->user_id))) {
				$user = $comment->user;
				$comment->delete();
				$user->clear_comment_cache();

				url::redirect('/member/' . urlencode($user->username));
				return;
			}
		}

		url::redirect(empty($_SESSION['history']) ? '/members' : $_SESSION['history']);
	}

	/***** /COMMENTS *****/


	/***** SETTINGS *****/

	/**
	 * Edit basic info
	 *
	 * @param  string  $username
	 */
	public function edit($username) {
		$this->tab_id = 'profile';
		$this->history = false;

		$this->member = new User_Model($username);
		$errors = $this->member->id ? array() : array('member.error_member_not_found');

		// only owner or admin
		if ($this->member->id !== $this->user->id && !$this->visitor->logged_in('admin')) {
			url::redirect(empty($_SESSION['history']) ? '/members' : $_SESSION['history']);
		}

		$form_errors = array();
		$form_values = $this->member->as_array();

		// check post
		if (request::method() == 'post') {
			$post = array_merge($this->input->post(), $_FILES);

			$extra = array();

			// location
			if (empty($post['address_street']) && empty($post['address_city'])) {

				// empty address, clear location
				$extra['latitude'] = 0;
				$extra['longitude'] = 0;

			} else if ($post['address_street'] != $this->member->address_street || $post['address_city'] != $this->member->address_city) {

				// update location
				list($extra['latitude'], $extra['longitude']) = Gmap::address_to_ll(implode(', ', array($post['address_street'], $post['address_zip'], $post['address_city'])));

			}

			if ($this->member->validate($post, true, $extra)) {

				// handle picture upload
				if (isset($post->image) && empty($post->image['error'])) {
					$image = Image_Model::factory('members.image', $post->image, $this->member->id);
					if ($image->id) {
						$this->member->add($image);
						$this->member->default_image_id = $image->id;
						$this->member->save();
					}
				}

				url::redirect('/member/' . urlencode($this->member->username));
			} else {
				$form_errors = $post->errors();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		// show form
		$this->page_title = text::title($this->member->username, false);
		$this->template->subtitle = __('Edit info');

		// city autocomplete
		$this->_autocomplete_city('address_city');

		// date pickers
		widget::add('footer', html::script_source("$('input#dob').datepicker({ dateFormat: 'd.m.yy', firstDay: 1, changeFirstDay: false, showOtherMonths: true, showStatus: true, showOn: 'both', minDate: '-60Y', maxDate: 0 });"));

		if (empty($errors)) {
			widget::add('main', View::factory('member/info_edit', array('values' => $form_values, 'errors' => $form_errors)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}

	/***** /SETTINGS *****/


	/***** FRIENDS *****/

	/**
	 * Add to friends
	 *
	 * @param  string  $username
	 */
	public function friendadd($username) {
		$this->history = false;

		// for authenticated only
		if ($this->user) {

			// require valid user
			$this->member = new User_Model($username);
			if ($this->member->id) {
				$this->user->add_friend($this->member);

				// News feed event
				newsfeeditem_user::friend($this->user, $this->member);

			}
		}

		url::redirect(empty($_SESSION['history']) ? '/members' : $_SESSION['history']);
	}


	/**
	 * User friends
	 *
	 * @param  string  $username
	 */
	public function friends($username) {
		$this->tab_id = 'friends';
		$member = new User_Model($username);

		$this->member = $member;

		$errors = $this->member->id ? array() : array('member.error_member_not_found');
		$side_views = array();

		if (empty($errors)) {
			$owner = ($this->user && $this->member->id == $this->user->id);

			// basic information
			$this->page_title = text::title($this->member->username, false);
			$this->template->subtitle = __('Friends');

			// handle pagination
			$pagination = new Pagination(array(
				'items_per_page' => 25,
				'total_items'    => $this->member->get_friend_count(),
			));
			$this->page_subtitle .= __(':friends friends, page :page of :pages', array(
				':friends' => '<var>' . num::format($pagination->total_items) . '</var>',
				':page'    => '<var>' . $pagination->current_page . '</var>',
				':pages'   => '<var>' . $pagination->total_pages . '</var>'
			));

			$friends = $this->member->find_friends($pagination->current_page, $pagination->items_per_page);

			widget::add('main', $pagination);
			widget::add('main', View::factory('member/friends', array('friends' => $friends)));
			widget::add('main', $pagination);
		}

		if (count($errors)) {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views($side_views);
	}


	/**
	 * Remove from friendlist
	 *
	 * @param  string  $username
	 */
	public function frienddelete($username) {
		$this->history = false;

		// for authenticated only
		if ($this->user) {

			// require valid user
			$this->member = new User_Model($username);
			if ($this->member->id) {
				$this->user->delete_friend($this->member);
			}
		}

		url::redirect(empty($_SESSION['history']) ? '/members' : $_SESSION['history']);
	}

	/***** /FRIENDS *****/


	/**
	 * Facebook connect
	 *
	 * @param  string  $username
	 */
	public function facebook($username) {
		$this->tab_id = 'facebook';
		$member = new User_Model($username);

		$this->member = $member;

		$errors = $this->member->id ? array() : array('member.error_member_not_found');
		$side_views = array();

		if (empty($errors)) {

			// Only owner may view this for now
			$owner = $this->user && $member->id == $this->user->id;
			if (!$owner) {
				url::redirect(url::user($member));
			}

			// Basic information
			$this->page_title = text::title($this->member->username, false);

			// Are we logged in Facebook?
			$fb_uid = FB::instance()->get_loggedin_user();
			$external_user = ($fb_uid) ?
				$member->find_external_by_id($fb_uid) :
				$member->find_external_by_provider(User_External_Model::PROVIDER_FACEBOOK);

			// Did we do an action?
			if (request::method() == 'post') {

				// Connect accounts
				if ($_POST['connect'] == User_External_Model::PROVIDER_FACEBOOK && $fb_uid) {
					if (!$external_user->loaded && $member->map_external($fb_uid, User_External_Model::PROVIDER_FACEBOOK)) {

						// Map succesful

					} else {

						// Map failed

					}
				} else {

					// Not connected or invalid post

				}

				url::redirect(url::user($member) . '/facebook');
			}

			$parameters = array();
			if ($fb_uid) {
				$parameters['fb_uid'] = $fb_uid;
			}
			if ($external_user->loaded) {
				$parameters['external_user'] = $external_user;
			}

			widget::add('main', View::factory('member/facebook', $parameters));
		}

		if (count($errors)) {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views($side_views);
	}


	/**
	 * User favorites
	 *
	 * @param  string  $username
	 */
	public function favorites($username) {
		$this->tab_id = 'favorites';
		$member = new User_Model($username);

		$this->member = $member;

		$errors = $this->member->id ? array() : array('member.error_member_not_found');
		$side_views = array();

		if (empty($errors)) {
			$owner = $this->user && $this->member->id == $this->user->id;

			// Basic information
			$this->page_title = text::title($this->member->username, false);

			$favorites = $this->member->favorites;
			widget::add('main', View::factory('member/favorites', array('favorites' => $favorites)));
		}

		if (count($errors)) {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views($side_views);
	}


	/**
	 * User profile
	 *
	 * @param  string  $username
	 */
	public function view($username) {
		$this->tab_id = 'profile';

		// Be careful, $member is the viewed user here
		$member = ORM::factory('user')->find_user($username);

		$this->member = $member;

		$errors = $member->id ? array() : array('member.error_member_not_found');
		$side_views = array();

		if (empty($errors)) {
			$owner = ($this->user && $member->id == $this->user->id);

			// Actions
			if ($owner) {
				$this->page_actions[] = array('link' => url::user($this->member) . '/edit', 'text' => __('Settings'), 'class' => 'settings');
			}

			// basic information
			$this->page_title = text::title($member->username, false);
			if (!empty($member->title)) $this->template->subtitle = html::specialchars($member->title);

			// picture
			widget::add('main', View::factory('member/member', array('user' => $member)));

			// comments
			if ($this->visitor->logged_in()) {

				$comment = new User_Comment_Model();
				$form_values = $comment->as_array();
				$form_errors = array();

				// check post
				if ($post = $this->input->post()) {
					$post['author_id'] = $this->user->id;
					$post['user_id'] = $member->id;
					if ($comment->validate($post, true, array())) {
						$member->clear_comment_cache();
						$this->user->commentsleft += 1;
						$this->user->save();
						url::redirect(url::current());
					} else {
						$form_errors = $post->errors();
						$form_values = arr::overwrite($form_values, $post->as_array());
					}
				}

				// handle pagination
				$per_page    = 25;
				$page_num    = $this->uri->segment('page') ? $this->uri->segment('page') : 1;
				$page_offset = ($page_num - 1) * $per_page;

				$total_comments = $member->get_comment_count();
				$comments = $member->find_comments($page_num, $per_page);

				$pagination = new Pagination(array(
					'items_per_page' => $per_page,
					'total_items'    => $total_comments,
				));

				widget::add('main',
					View::factory('member/comments', array(
						'comments'   => $comments,
						'errors'     => $form_errors,
						'values'     => $form_values,
						'pagination' => $pagination)
					)
				);


				// Side
				$basic_info = array();
				if (!empty($member->name)) {
					$basic_info[__('Name')] = html::specialchars($member->name);
				}
				if (!empty($member->city_name)) {
					$basic_info[__('City')] = html::specialchars($member->city_name);
				}
				if (!empty($member->dob) && $member->dob != '0000-00-00') {
					$basic_info[__('Date of Birth')] = __(':dob (:years years)', array(
						':dob'   => date::format('DMYYYY', $member->dob),
						':years' => date::timespan(strtotime($member->dob), null, 'years'),
					));
				}
				if (!empty($member->gender)) {
					$basic_info[__('Gender')] = ($member->gender == 'm') ? __('Male') : __('Female');
				}
				if (!empty($member->latitude) && !empty($member->longitude)) {
					$basic_info[__('Location')] = $member->latitude . ', ' . $member->longitude;
				}

				$site_info = array(
					__('Registered') => date::format('DMYYYY_HM', $member->created),
					__('Logins')     => __(':logins (:ago ago)', array(
						':logins' => number_format($member->logins, 0),
						':ago'    => '<abbr title="' . date::format('DMYYYY_HM', $member->last_login) . '">' . date::timespan_short($member->last_login) . '</abbr>',
					)),
					__('Posts')      => number_format($member->posts, 0),
					__('Comments')   => number_format($member->commentsleft, 0),
				);

				// Initialize tabs
				$tabs = array(
					'basic-info' => array('href' => '#basic-info', 'title' => __('Basic info'), 'tab' => new View('generic/list_info', array(
						'id'      => 'basic-info',
						'title'   => __('Basic info'),
						'list'    => $basic_info,
					))),
					'site-info' => array('href' => '#site-info', 'title' => __('Site info'), 'tab' => new View('generic/list_info', array(
						'id'      => 'site-info',
						'title'   => __('Site info'),
						'list'    => $site_info,
					))),
				);

				widget::add('side', View::factory('generic/tabs', array('id' => 'info-tab', 'tabs' => $tabs)));
			}
		}

		if (count($errors)) {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views($side_views);
	}

}
