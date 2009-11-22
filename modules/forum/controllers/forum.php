<?php
/**
 * Forum main controller, handles groups, areas, topics and posts.
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Forum_Controller extends Website_Controller {

	protected $config;


	/**
	 * New forum controller
	 */
	public function __construct() {
		parent::__construct();

		$this->config = Kohana::config('forum');

		$this->breadcrumb[] = html::anchor('forum', __('Forum'));
		$this->page_title = __('Forum');

		$this->tabs = array(
			'active' => array('link' => 'forum',        'text' => __('Active topics')),
			'latest' => array('link' => 'forum/latest', 'text' => __('New topics')),
			'areas'  => array('link' => 'forum/areas',  'text' => __('Forum areas')),
		);
	}


	/***** INTERNAL *****/

	private function _side_views($extra_views = array()) {
		widget::add('side', implode("\n", $extra_views));

		// Initialize tabs
		$tabs = array(
			'active' => array('href' => '#topics-active', 'title' => __('Active'), 'tab' => new View('forum/topics_list', array(
				'id'     => 'topics-active',
				'title'  => __('Active topics'),
				'topics' => ORM::factory('forum_topic')->find_active($this->config['topics_per_list'])
			))),
			'latest' => array('href' => '#topics-new', 'title' => __('New'), 'tab' => new View('forum/topics_list', array(
				'id'     => 'topics-new',
				'title'  => __('New topics'),
				'topics' => ORM::factory('forum_topic')->find_latest($this->config['topics_per_list'])
			))),
			'areas' => array('href' => '#forum-areas', 'title' => __('Areas'), 'selected' => in_array($this->tab_id, array('active', 'latest')), 'tab' => new View('forum/groups_list', array(
				'id'     => 'forum-areas',
				'title'  => __('Forum areas'),
				'groups' => ORM::factory('forum_group')->find_all()
			))),
		);

		widget::add('side', View::factory('generic/tabs', array('id' => 'topics-tab', 'tabs' => $tabs)));
	}

	/***** /INTERNAL *****/


	/***** MAIN VIEWS *****/

	/**
	 * Default view
	 */
	public function index() {
		$this->active();
	}


	/**
	 * Show forum areas
	 */
	public function areas() {
		$this->tab_id = 'areas';

		if ($this->visitor->logged_in('admin')) {
			$this->page_actions[] = array('link' => 'forum/group/add', 'text' => __('New group'), 'class' => 'group-add');
			$this->page_actions[] = array('link' => 'forum/area/add',  'text' => __('New area'),  'class' => 'area-add');
		}

		widget::add('main', View::factory('forum/groups', array('groups' => ORM::factory('forum_group')->find_all())));

		$this->_side_views();
	}


	/**
	 * Show topics by latest post
	 */
	public function active() {
		$this->tab_id = 'active';

		widget::add('main', View::factory('forum/topics', array(
			'topics' => ORM::factory('forum_topic')->find_active($this->config['topics_per_list']),
			'area'   => true,
		)));

		$this->_side_views();
	}


	/**
	 * Show topics by latest topic
	 */
	public function latest() {
		$this->tab_id = 'latest';

		widget::add('main', View::factory('forum/topics', array(
			'topics' => ORM::factory('forum_topic')->find_latest($this->config['topics_per_list']),
		)));

		$this->_side_views();
	}

	/***** /MAIN VIEWS *****/


	/***** AREA VIEWS *****/

	/**
	 * Show forum area
	 *
	 * @param  mixed   $area_id
	 * @param  string  $action
	 */
	public function area($area_id, $action = false) {

		// Hide tabs
		$this->tabs = null;

		// add new area
		if ($area_id == 'add') {
			$this->_area_add();
			return;

		} else if ($action) {
			switch ($action) {

				// add new topic
				case 'post':
					$this->_topic_add($area_id);
					return;

				// delete area
				case 'delete':
					$this->_area_delete($area_id);
					return;

				// edit area
				case 'edit':
					$this->_area_edit($area_id);
					return;

			}
		}

		$forum_area = new Forum_Area_Model((int)$area_id);
		$errors = $forum_area->id ? array() : __('Area not found :area', array(':area' => $area_id));

		if (empty($errors)) {

			$this->breadcrumb[] = html::anchor(url::model($forum_area), $forum_area->name);
			$this->page_title = text::title($forum_area->name);
			$this->page_subtitle = html::specialchars($forum_area->description) . '&nbsp;';

			// Admin actions
			if ($this->visitor->logged_in('admin')) {
				$this->page_actions[] = array('link' => url::model($forum_area) . '/edit', 'text' => __('Edit area'), 'class' => 'area-edit');
			}

			// Logged user actions
			if ($this->visitor->logged_in()) {
				$this->page_actions[] = array('link' => url::model($forum_area) . '/post', 'text' => __('New topic'), 'class' => 'topic-add');
			}

			// check access and proceed
			if ($forum_area->access_has($this->user, Forum_Area_Model::ACCESS_READ)) {

				// handle pagination
				$per_page = $this->config['topics_per_page'];
				$page_num = (int)$this->input->get('page', 1);
				$offset = max(0, ($page_num - 1) * $per_page);
				$pagination = new Pagination(array(
					'style'          => 'digg',
					'items_per_page' => $per_page,
					//'query_string'   => 'page',
					'uri_segment'    => 'page',
					'total_items'    => $forum_area->topics,
					'auto_hide'      => true,
				));
				$topics = $forum_area->limit($per_page, $pagination->sql_offset)->forum_topics;

				if (count($topics)) {
					widget::add('main', $pagination);
					widget::add('main', View::factory('forum/topics', array('topics' => $topics)));
					widget::add('main', $pagination);
				} else {
					$errors[] = __('No topics found');
				}

			} else {
				$errors[] = __('Members only');
			}
		}

		if (count($errors)) {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * Add new forum area
	 *
	 * @param  midex  $group_id
	 */
	public function _area_add($group_id = false) {
		$this->_area_edit(false, $group_id);
	}


	/**
	 * Edit forum area
	 *
	 * @param  mixed  $area_id
	 * @param  mixed  $group_id
	 */
	public function _area_edit($area_id = false, $group_id = false) {
		$this->history = false;

		// for authenticated users only
		if (!$this->visitor->logged_in('admin')) url::redirect('/forum');

		$errors = $form_errors = array();

		$forum_area = new Forum_Area_Model((int)$area_id);
		if ($forum_area->id) $forum_group = $forum_area->forum_group;
		$form_values = $forum_area->as_array();

		// check post
		if (request::method() == 'post') {
			$post = $this->input->post();
			$extra = array('author_id' => $this->user->id);
			if ($forum_area->validate($post, true, $extra)) {
				url::redirect('/forum/' . url::title($forum_area->id, $forum_area->name));
			} else {
				$form_errors = $post->errors();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		// show form
		if ($forum_area->id) {
			$this->page_actions[] = array('link' => url::model($forum_area) . '/delete', 'text' => __('Delete area'), 'class' => 'area-delete');
			$this->template->subtitle = __('Edit area');
		} else {
			$this->template->subtitle = __('New area');
		}

		$form = $forum_area->get_form();
		$forum_groups = ORM::factory('forum_group')->find_all()->select_list('id', 'name');
		$form['forum_group_id'] = $forum_groups;

		if (empty($errors)) {
			widget::add('main', View::factory('forum/area_edit', array('form' => $form, 'values' => $form_values, 'errors' => $form_errors)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}

	/***** /AREA VIEWS *****/


	/***** GROUP VIEWS *****/

	/**
	 * Forum group
	 *
	 * @param  mixed   $group_id
	 * @param  string  $action
	 */
	public function group($group_id, $action = false) {

		// Hide tabs
		$this->tabs = null;

		// new group
		if ($group_id == 'add') {
			$this->_group_add();
			return;

		} else if ($action) {
			switch ($action) {

				// add area
				case 'add':
					$this->_area_add($group_id);
					return;

				// edit group
				case 'edit':
					$this->_group_edit($group_id);
					return;

			}
		}

		$forum_group = new Forum_Group_Model((int)$group_id);
		if ($forum_group->id) {

			$this->breadcrumb[] = html::anchor(url::model($forum_group), $forum_group->name);
			$this->page_title = html::specialchars($forum_group->name);

			if ($this->visitor->logged_in('admin')) {
				$this->page_actions[] = array('link' => url::model($forum_group) . '/edit', 'text' => __('Edit group'),   'class' => 'group-edit');
				$this->page_actions[] = array('link' => url::model($forum_group) . '/add',  'text' => __('New area'),     'class' => 'area-add');
			}

			//$forum_groups = ORM::factory('forum_group')->find_all();
			widget::add('main', View::factory('forum/groups', array('groups' => array($forum_group))));
			$this->_side_views();
		}

		//url::redirect('/forum');
	}


	/**
	 * Add new forum group
	 */
	public function _group_add() {
		$this->_group_edit();
	}


	/**
	 * Edit forum group
	 *
	 * @param  mixed  $group_id
	 */
	public function _group_edit($group_id = false) {
		$this->history = false;

		// for authenticated users only
		if (!$this->user) url::redirect('/forum');

		$errors = $form_errors = array();

		$forum_group = new Forum_Group_Model((int)$group_id);
		$form = $forum_group->get_defaults();

		// check post
		if (request::method() == 'post') {
			$post = $this->input->post();
			$extra = array('author_id' => $this->user->id);
			if ($forum_group->validate($post, true, $extra)) {
				URL::redirect('/forum');
			} else {
				$form_errors = $post->errors();
			}
			$form = arr::overwrite($form, $post->as_array());
		}

		// show form
		if ($forum_group->id) {
			$this->page_subtitle = __('Edit group');
			$this->page_actions[] = array('link' => url::model($forum_group) . '/delete', 'text' => __('Delete group'), 'class' => 'group-delete');
		} else {
			$this->page_subtitle = __('New group');
		}

		if (empty($errors)) {
			widget::add('main', View::factory('forum/group_edit', array('form' => $forum_group->get_form(), 'values' => $form, 'errors' => $form_errors)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}

	/***** /GROUP VIEWS *****/


	/***** POST VIEWS *****/

	/**
	 * Single post functions
	 *
	 * @param  integer  $post_id
	 * @param  string   $action
	 */
	public function post($post_id, $action = false) {

		// Hide tabs
		$this->tabs = null;

		if ($action) {
			switch ($action) {

				// delete post
				case 'delete':
					$this->_post_delete($post_id);
					return;

				// edit post
				case 'edit':
					$this->_post_edit($post_id);
					return;

				// quote post or reply
				case 'quote':
				case 'reply':
					$this->_post_reply($post_id, $action === 'quote');
					return;

			}
		}

		url::redirect('/forum');
	}


	/**
	 * Reply to topic
	 *
	 * @param  mixed  $topic_id
	 */
	public function _post_add($topic_id) {
		$this->_post_edit(false, $topic_id);
	}


	/**
	 * Delete post
	 *
	 * @param  int  $post_id
	 */
	public function _post_delete($post_id) {
		$this->history = false;

		$forum_post = new Forum_Post_Model((int)$post_id);
		if ($this->user && csrf::valid() && $forum_post->id && $forum_post->author_id == $this->user->id) {
			$forum_topic = $forum_post->forum_topic;
			$is_first_post = $forum_topic->first_post_id == $forum_post->id;
			$is_last_post = $forum_topic->last_post_id == $forum_post->id;

			// deleting whole topic
			if ($is_first_post && $is_last_post) {
				$this->_topic_delete($forum_topic->id);
				return;
			}

			// delete post
			$forum_post->delete();

			// refresh topic
			$forum_topic->refresh();

			// update area
			$forum_area = $forum_topic->forum_area;
			$forum_area->posts -= 1;
			$forum_area->save();
		}

		url::back('/forum');
	}


	/**
	 * Edit forum post
	 *
	 * @param  mixed    $post_id
	 * @param  mixed    $topic_id
	 * @param  mixed    $parent_id  when reply or quote
	 * @param  boolean  $quote
	 */
	public function _post_edit($post_id, $topic_id = null, $parent_id = null, $quote = false) {
		$this->history = false;

		// For authenticated users only
		if (!$this->user) url::redirect('/forum');

		// Load parent post if requested
		$parent_post = ($parent_id) ? new Forum_Post_Model((int)$parent_id) : false;
		if ($parent_post && $parent_post->id && !$topic_id) {
			$topic_id = $parent_post->forum_topic_id;
		}

		// Load post or start new
		$forum_post = new Forum_Post_Model((int)$post_id);
		$forum_topic = $forum_post->id ? $forum_post->forum_topic : new Forum_Topic_Model((int)$topic_id);
		$errors = $forum_topic->id ? array() : __('Topic not found');

		if (empty($errors)) {

			// Check access and proceed
			$forum_area = $forum_topic->forum_area;
			if ($forum_area->access_has($this->user, Forum_Area_Model::ACCESS_WRITE)) {

				$this->page_title = text::title($forum_topic->name);
				$this->page_subtitle = __('Area :area.', array(
					':area' => html::anchor(url::model($forum_area), text::title($forum_area->name), array('title' => strip_tags($forum_area->description)))
				));
				$form_errors = array();

				$form_values_topic = $forum_topic->as_array();
				$form_values_post  = $forum_post->as_array();
				$editing = (bool)$forum_post->id;

				if (!$editing && $quote && $parent_post->id) {
					$form_values_post['post'] = '[quote author="' . $parent_post->author->username .'" post="' . $parent_post->id . '"]' . $parent_post->post . '[/quote]';
				}

				// check post
				if (request::method() == 'post') {
					$post = $this->input->post();
					$post['forum_area_id'] = $forum_area->id;
					$post['forum_topic_id'] = $forum_topic->id;
					if ($editing) {
						$extra = array(
							'parent_id' => $forum_post->parent_id,
							'modifies'  => (int)$forum_post->modifies + 1,
							'modified'  => date::unix2sql(time()),
						);
					} else {
						$extra = array(
							'author_id' => $this->user->id,
							'author_name' => $this->user->username,
						);
					}
					$extra['author_ip'] = $this->input->ip_address();
					$extra['author_host'] = $this->input->host_name();

					if (csrf::valid() && $forum_post->validate($post, true, $extra)) {

						// update topic and area only on new posts
						if (!$editing) {

							// topic
							$forum_topic->last_post_id = $forum_post->id;
							$forum_topic->last_poster = $this->user->username;
							$forum_topic->last_posted = date::unix2sql(time());
							$forum_topic->posts += 1;
							$forum_topic->save();

							// area
							$forum_area->posts += 1;
							$forum_area->last_topic_id = $forum_topic->id;
							$forum_area->save();

							// user
							$this->user->posts += 1;
							$this->user->save();

							// News feed
							newsfeeditem_forum::reply($this->user, $forum_post);
						}

						URL::redirect(url::model($forum_topic));
					} else {
						$form_errors = $post->errors();
					}
					$form_values_post = arr::overwrite($form_values_post, $post->as_array());
				}

			// no access
			} else {
				$this->page_title = text::title($forum_area->name);
				$this->page_subtitle = html::specialchars($forum_area->description) . '&nbsp;';
				$errors[] = __('Access denied');
			}
		}

		// show form
		if (empty($errors)) {
			widget::add('head', html::script(array('js/jquery.markitup.pack', 'js/markitup.bbcode')));
			widget::add('main', View::factory('forum/post_edit', array(
				'topic'     => $form_values_topic,
				'post'      => $form_values_post,
				'errors'    => $form_errors,
				'parent_id' => $parent_id,
			)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();

	}


	/**
	 * Reply or quote to post
	 *
	 * @param  integer  $parent_id  parent post id
	 * @param  boolean  $quote
	 */
	public function _post_reply($parent_id, $quote = false) {
		$this->_post_edit(null, null, $parent_id, (bool)$quote);
	}

	/***** /POST VIEWS *****/


	/***** TOPIC VIEWS *****/

	/**
	 * View topic
	 *
	 * @param  mixed   $topic_id
	 * @param  string  $action
	 * @param  mixed   $extra
	 */
	public function topic($topic_id, $action = false, $extra = false) {

		// Hide tabs
		$this->tabs = null;

		if ($action) {
			switch ($action) {

				// Delete topic
				case 'delete':
					$this->_topic_delete($topic_id);
					return;

				// Edit topic
				case 'edit':
					$this->_topic_edit($topic_id);
					return;

				// Post to topic
				case 'post':
					$this->_post_add($topic_id);
					return;

				// Go to post
				default:
					if (is_numeric($action)) {
						$post_id = (int)$action;
					}

			}
		}

		$forum_topic = new Forum_Topic_Model((int)$topic_id);
		$errors = $forum_topic->id ? array() : __('Topic not found');

		if (empty($errors)) {
			$forum_area = $forum_topic->forum_area;

			$this->breadcrumb[] = html::anchor(url::model($forum_area), $forum_area->name);

			// Admin actions
			if ($forum_topic->is_author() || $this->visitor->logged_in('admin', 'forum moderator')) {
				$this->page_actions[] = array('link' => url::model($forum_topic) . '/edit',   'text' => __('Edit topic'),   'class' => 'topic-edit');
			}

			// Logged user actions
			if ($this->user) {
				$this->page_actions[] = array('link' => url::model($forum_topic) . '/post',   'text' => __('Reply to topic'),  'class' => 'topic-post');
			}

			// Check access and proceed
			if ($forum_area->access_has($this->user, Forum_Area_Model::ACCESS_READ)) {
				$this->breadcrumb[] = html::anchor(url::model($forum_topic), $forum_topic->name);

				// Update read counter
				$forum_topic->reads++;
				$forum_topic->save();

				$this->page_title = ($forum_topic->read_only ? '<span class="locked">' . __('[Locked]') . '</span> ' : '') . text::title($forum_topic->name);
				$this->page_subtitle = __('Area :area. ', array(
					':area' => html::anchor(url::model($forum_area), text::title($forum_area->name), array('title' => strip_tags($forum_area->description)))
				));

				// Handle pagination
				$per_page = $this->config['posts_per_page'];
				$pagination = new Pagination(array(
					'items_per_page' => $per_page,
					'total_items'    => $forum_topic->posts,
				));

				// Go to last page?
				if ($action == 'page' && $extra == 'last') {
					$pagination->to_last_page();
				}

				$posts = $forum_topic->limit($per_page, $pagination->sql_offset)->forum_posts;
				$this->page_subtitle .= __(':posts posts, page :page of :pages', array(
					':posts' => '<var>' . num::format($forum_topic->posts) . '</var>',
					':page'  => '<var>' . $pagination->current_page . '</var>',
					':pages' => '<var>' . $pagination->total_pages . '</var>'
				));

				if (count($posts)) {
					widget::add('main', $pagination);
					widget::add('main', View::factory('forum/topic', array('topic' => $forum_topic, 'posts' => $posts)));
					widget::add('main', $pagination);
				} else {
					$errors[] = __('No posts found.');
				}

			// no access
			} else {
				$this->page_title = text::title($forum_area->name);
				$this->page_subtitle = html::specialchars($forum_area->description) . '&nbsp;';
				$errors[] = __('Access denied.');
			}
		}

		if (count($errors)) {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * Start new topic
	 *
	 * @param  mixed  $area_id
	 */
	public function _topic_add($area_id) {
		$this->_topic_edit(false, $area_id);
	}


	/**
	 * Delete topic
	 *
	 * @param  int  $topic_id
	 */
	public function _topic_delete($topic_id) {
		$this->history = false;

		$forum_topic = new Forum_Topic_Model((int)$topic_id);
		if ($this->user && $forum_topic->id && csrf::valid() && ($forum_topic->is_author() || $this->visitor->logged_in('admin', 'forum moderator'))) {
			$forum_area = $forum_topic->forum_area;

			$forum_topic->delete();

			$forum_area->refresh();

			url::redirect(url::model($forum_area));
		} else {
			url::back('/forum');
		}

}


	/**
	 * Edit topic
	 *
	 * @param  mixed  $topic_id
	 * @param  mixed  $area_id
	 */
	public function _topic_edit($topic_id, $area_id = false) {
		$this->history = false;

		$forum_topic = new Forum_Topic_Model((int)$topic_id);
		$forum_area = $forum_topic->id ? $forum_topic->forum_area : new Forum_Area_Model((int)$area_id);
		$errors = $forum_area->id ? array() : __('Area :area not found', array(':area' => $area_id));
		$forum_post = new Forum_Post_Model((int)$forum_topic->first_post_id);

		// for authenticated users only
		if (!$forum_topic->is_author() && !$this->visitor->logged_in('admin', 'forum moderator')) url::redirect('/forum');

		if (empty($errors)) {

			// check for write access
			if ($forum_area->access_has($this->user, Forum_Area_Model::ACCESS_WRITE)) {
				$form_errors = array();

				$this->page_title = $forum_topic->id ? text::title($forum_topic->name) : __('New topic');
				$this->page_subtitle = __('Area :area', array(
					':area' => html::anchor(url::model($forum_area), text::title($forum_area->name), array('title' => strip_tags($forum_area->description)))
				));

				// Admin actions
				if ($forum_topic->id) {
					$this->page_actions[] = array('link' => url::model($forum_topic) . '/delete/?token=' . csrf::token(), 'text' => __('Delete topic'), 'class' => 'topic-delete');
				}

				$form_values_topic = $forum_topic->as_array();
				$form_values_post = $forum_post->as_array();
				$editing = (bool)$forum_post->id;

				// check post
				if (request::method() == 'post') {
					$post = $this->input->post();
					$post['forum_area_id'] = $forum_area->id;
					$topic = $post;
					$post_extra = $topic_extra = array(
						'author_id'   => $this->user->id,
						'author_name' => $this->user->username
					);
					if ($editing) {
						$post_extra['modifies'] = (int)$forum_post->modifies + 1;
						$post_extra['modified'] = date::unix2sql(time());
					}
					$post_extra['author_ip'] = $this->input->ip_address();
					$post_extra['author_host'] = $this->input->host_name();

					// validate post first and save topic if ok
					if (csrf::valid() && $forum_post->validate($post, false, $post_extra) && $forum_topic->validate($topic, true, $topic_extra)) {

						// post
						$forum_post->forum_topic_id = $forum_topic->id;
						$forum_post->save();

						if (!$editing) {
							// topic
							$forum_topic->first_post_id = $forum_post->id;
							$forum_topic->last_post_id = $forum_post->id;
							$forum_topic->last_poster = $this->user->username;
							$forum_topic->last_posted = date::unix2sql(time());
							$forum_topic->posts = 1;
							$forum_topic->save();

							// area
							$forum_area->last_topic_id = $forum_topic->id;
							$forum_area->posts += 1;
							$forum_area->topics += 1;
							$forum_area->save();

							// user
							$this->user->posts += 1;
							$this->user->save();

							// News feed
							newsfeeditem_forum::topic($this->user, $forum_topic);
						}

						// redirect back to topic
						URL::redirect(url::model($forum_topic));
					} else {
						$form_errors = array_merge($post->errors(), is_object($topic) ? $topic->errors() : array());
					}

					$form_values_topic = arr::overwrite($form_values_topic, is_object($topic) ? $topic->as_array() : $topic);
					$form_values_post = arr::overwrite($form_values_post, $post->as_array());
				}

			// no access
			} else {
				$this->page_title = text::title($forum_area->name);
				$this->page_subtitle = html::specialchars($forum_area->description) . '&nbsp;';
				$errors[] = __('Access denied');
			}

		}

		// show form
		if (empty($errors)) {
			widget::add('head', html::script(array('js/jquery.markitup.pack', 'js/markitup.bbcode')));
			widget::add('main', View::factory('forum/topic_edit', array('topic' => $form_values_topic, 'post' => $form_values_post, 'errors' => $form_errors)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}

	/***** /TOPIC VIEWS *****/

}
