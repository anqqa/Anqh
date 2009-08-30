<?php
/**
 * Tags controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Tags_Controller extends Website_Controller {


	/***** MAGIC *****/

	public function __construct() {
		parent::__construct();

		$this->breadcrumb[] = html::anchor('tags', __('Tags'));
		$this->page_title = __('Tags');
	}

	/***** /MAGIC *****/


	/***** INTERNAL *****/

	private function _side_views() {
	}

	/***** /INTERNAL *****/


	/***** VIEWS *****/

	/**
	 * Main tags page
	 */
	public function index() {
		if ($this->visitor->logged_in('admin')) {
			$this->page_actions[] = array('link' => 'tags/group/add', 'text' => __('Add group'), 'class' => 'group-add');
		}

		$tag_groups = ORM::factory('tag_group')->find_all();

		widget::add('main', new View('tags/tag_groups', array('tag_groups' => $tag_groups)));

		$this->_side_views();
	}


	/**
	 * Tag group view
	 *
	 * @param  integer|string  $group_id
	 * @param  string          $action
	 */
	public function group($group_id, $action = null) {

		// Add new group
		if ($group_id == 'add') {
			$this->_group_add();
			return;

		} else if ($action) {
			switch ($action) {

				// Add new venue
				case 'add':
					$this->_tag_add($group_id);
					return;

				// Delete group
				case 'delete':
					$this->_group_delete($group_id);
					return;

				// Edit group
				case 'edit':
					$this->_group_edit($group_id);
					return;

			}
		}

		$tag_group = new Tag_Group_Model((int)$group_id);
		$errors = $tag_group->id ? array() : array('tags.error_tag_group_not_found');

		if (empty($errors)) {
			$this->breadcrumb[]  = html::anchor(url::model($tag_group), $tag_group->name);
			$this->page_title    = text::title($tag_group->name);
			$this->page_subtitle = html::specialchars($tag_group->description) . '&nbsp;';

			if ($this->visitor->logged_in('admin')) {
				$this->page_actions[] = array('link' => url::model($tag_group) . '/edit', 'text' => __('Edit group'), 'class' => 'group-edit');
				$this->page_actions[] = array('link' => url::model($tag_group) . '/add',  'text' => __('New tag'),   'class' => 'tag-add');
			}

			widget::add('main', View::factory('tags/tags', array('tags' => $tag_group->tags)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * Add new tag group
	 */
	public function _group_add() {
		$this->_group_edit();
	}


	/**
	 * Delete tag group
	 *
	 * @param  integer|string  $group_id
	 */
	public function _group_delete($group_id) {
		$this->history = false;

		// for authenticated users only
		if (!$this->user) url::redirect('tags');

		$tag_group = new Tag_Group_Model((int)$group_id);
		if ($tag_group->id) {
			$tag_group->delete();
			url::redirect('tags');
		}
	}


	/**
	 * Edit tag group
	 *
	 * @param  integer|string  $group_id
	 */
	public function _group_edit($group_id = false) {
		$this->history = false;

		// For authenticated users only
		if (!$this->user) url::redirect('tags');

		$errors = $form_errors = array();

		$tag_group = new Tag_Group_Model((int)$group_id);
		$form = $tag_group->get_defaults();

		// check post
		if (request::method() == 'post') {
			$post = $this->input->post();
			if ($tag_group->validate($post, true, array('author_id' => $this->user->id))) {
				url::redirect(url::model($tag_group));
			} else {
				$form_errors = $post->errors();
			}
			$form = arr::overwrite($form, $post->as_array());
		}

		// show form
		if ($tag_group->id) {
			$this->breadcrumb[] = html::anchor(url::model($tag_group), $tag_group->name);
			$this->page_subtitle = __('Edit group');

			if ($this->visitor->logged_in('admin')) {
				$this->page_actions[] = array('link' => url::model($tag_group) . '/delete', 'text' => __('Delete group'), 'class' => 'group-delete');
			}

		} else {
			$this->breadcrumb[] = html::anchor('/tags/group/add', __('Add group'));
			$this->page_subtitle = __('Add group');
		}

		if (empty($errors)) {
			widget::add('main', new View('tags/group_edit', array('values' => $form, 'errors' => $form_errors)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * Show tag
	 *
	 * @param  intege|string  $tag_id
	 * @param  string  $action
	 */
	public function tag($tag_id, $action = null) {

		if ($action) {
			switch ($action) {

				// Delete tag
				case 'delete':
					$this->_tag_delete($tag_id);
					return;

				// Edit tag
				case 'edit':
					$this->_tag_edit($tag_id);
					return;

			}
		}

		$tag = new Tag_Model((int)$tag_id);
		$errors = $tag->id ? array() : array('tags.error_tag_not_found');

		if (empty($errors)) {
			$tag_group = $tag->tag_group;

			$this->breadcrumb[]  = html::anchor(url::model($tag_group), $tag_group->name);
			$this->breadcrumb[]  = html::anchor(url::model($tag), $tag->name);
			$this->page_title    = text::title($tag->name);
			$this->page_subtitle = html::specialchars($tag->description) . '&nbsp;';

			if ($this->visitor->logged_in('admin')) {
				$this->page_actions[] = array('link' => url::model($tag) . '/edit', 'text' => __('Edit tag'), 'class' => 'tag-edit');
			}

			foreach ($tag->get_defaults() as $key => $field)
				if (!empty($field)) {
					widget::add('main', $key . ' = ' . html::specialchars($field) . "<br />\n");
				}
			} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * Add new tag
	 *
	 * @param  integer|string  $group_id
	 */
	public function _tag_add($group_id) {
		$this->_tag_edit(false, $group_id);
	}


	/**
	 * Delete tag
	 *
	 * @param  integer|string  $tag_id
	 */
	public function _tag_delete($tag_id) {
		$this->history = false;

		// for authenticated users only
		if (!$this->user) url::redirect('/tags');

		$tag = new Tag_Model((int)$tag_id);
		if ($tag->id) {
			$return_id = $tag->tag_group_id;
			$tag->delete();
			url::redirect('/tags/' . $return_id);
		}
	}


	/**
	 * Edit tag
	 *
	 * @param  integer|string  $tag_id
	 * @param  integer|string  $group_id
	 */
	public function _tag_edit($tag_id = false, $group_id = false) {
		$this->history = false;

		// for authenticated users only
		if (!$this->user) url::redirect('/tags');

		$errors = $form_errors = array();

		$tag = new Tag_Model((int)$tag_id);
		$form_values = $tag->get_defaults();

		// check post
		if (request::method() == 'post') {
			$post = $this->input->post();
			$extra = array('author_id' => $this->user->id);

			if ($tag->validate($post, true, $extra)) {
				url::redirect(url::model($tag));
			} else {
				$form_errors = $post->errors();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		// editing old?
		if ($tag_id) {
			$this->page_subtitle = __('Edit tag');
			$this->breadcrumb[] = html::anchor(url::model($tag->tag_group), $tag->tag_group->name);
			$this->breadcrumb[] = html::anchor(url::model($tag), $tag->name);

			if ($this->visitor->logged_in('admin')) {
				$this->page_actions[] = array('link' => url::model($tag) . '/delete', 'text' => __('Delete tag'), 'class' => 'tag-delete');
			}

			if (!$tag->id) {
				$errors = array('tags.error_tag_not_found');
			}
		} else {
			$this->page_subtitle = __('Add tag');
			if ($group_id) {
				$tag_group = new Tag_Group_Model((int)$group_id);
				$this->breadcrumb[] = html::anchor(url::model($tag_group), $tag_group->name);
				$this->breadcrumb[] = html::anchor(url::model($tag_group) . '/add', __('Add tag'));
				if ($tag_group->id) {
					$form_values['tag_group_id'] = $tag_group->id;
				} else {
					$errors = array('tags.error_tag_group_not_found');
				}
			}
		}

		// show form
		if (empty($errors)) {
			widget::add('main', new View('tags/tag_edit', array('values' => $form_values, 'errors' => $form_errors)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}

	/***** /VIEWS *****/

}
