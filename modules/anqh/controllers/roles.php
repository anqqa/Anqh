<?php
/**
 * Roles controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Roles_Controller extends Website_Controller {

	/**
	 * Page constructor to enable role check
	 */
	public function __construct() {

		// allow only admin access
		if (!Auth::instance()->logged_in('admin')) {
			url::redirect();
		}

		parent::__construct();

		$this->breadcrumb[] = html::anchor('roles', __('Roles'));
	}


	/**
	 * Roles index page
	 */
	public function index() {
		$this->page_title = __('Roles');
		$this->page_actions[] = array('link' => 'role/add', 'text' => __('Add new role'), 'class' => 'role-add');

		$roles = new Role_Model();
		widget::add('main', View::Factory('roles/roles', array('roles' => $roles->orderby('name', 'ASC')->find_all())));
	}


	/**
	 * Single role view
	 *
	 * @param  string  $role_id
	 * @param  string  $action
	 */
	public function role($role_id, $action = null) {
		if ($action) {
			switch ($action) {

				// delete role
				case 'delete':
					$this->_role_delete($role_id);
					return;

			}
		}

		$this->history = false;

		$role = new Role_Model((int)$role_id);
		$form_values = $role->as_array();
		$form_errors = $errors = array();

		// check post
		if (request::method() == 'post') {
			$post = $this->input->post();
			if ($role->validate($post, true)) {
				URL::redirect('/roles');
			} else {
				$form_errors = $post->errors();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		// show form
		if ($role->id) {
			$this->breadcrumb[] = html::anchor('role/' . URL::title($role->id, $role->name), html::specialchars($role->name));
			$this->page_title = text::title($role->name);
			$this->page_actions[] = array('link' => 'role/' . URL::title($role->id, $role->name) . '/delete', 'text' => __('Delete role'), 'class' => 'role-delete');
			html::confirm('.role-delete', 'role-delete', __('Delete role'), __('Area you sure you want to delete :target', array(':target' => html::specialchars($role->name))), __('Delete'), __('Cancel'));
		} else {
			$this->page_title = __('Role');
		}

		if (empty($errors)) {
			//widget::add('main', $formo->get());
			widget::add('main', View::factory('roles/role_edit', array('values' => $form_values, 'errors' => $form_errors)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

	}


	/**
	 * Delete role
	 *
	 * @param  id|string  $role_id
	 */
	public function _role_delete($role_id) {
		// for authenticated users only
		if (!$this->user) url::redirect('roles');

		$role = new Role_Model((int)$role_id);
		if ($role->id) {
			$role->delete();
			url::redirect('roles');
		}
	}

}
