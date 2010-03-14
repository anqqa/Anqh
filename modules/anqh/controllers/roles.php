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
		parent::__construct();

		// Allow only admin access
		if (!$this->visitor->logged_in('admin')) {
			url::back();
		}

		$this->breadcrumb[] = html::anchor('roles', __('Roles'));
	}


	/**
	 * Roles index page
	 */
	public function index() {
		$this->page_title = __('Roles');
		$this->page_actions[] = array('link' => 'role/add', 'text' => __('Add new role'), 'class' => 'role-add');

		$roles = new Role_Model();
		widget::add('main', View_Mod::Factory('roles/roles', array('roles' => $roles->order_by('name', 'ASC')->find_all())));
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

				// Delete role
				case 'delete':
					$this->_role_delete($role_id);
					return;

			}
		}

		$this->history = false;

		$role = new Role_Model((int)$role_id);
		$form_values = $role->as_array();
		$form_errors = $errors = array();

		// Check post
		if ($post = $this->input->post()) {
			$role->name = $post['name'];
			$role->description = $post['description'];
			try {
				$role->save();
				url::redirect('/roles');
			} catch (ORM_Validation_Exception $e) {
				$form_errors = $e->validation->errors();
			}
			$form_values = arr::overwrite($form_values, $post);
		}

		// show form
		if ($role->id) {
			$this->breadcrumb[] = html::anchor('role/' . url::title($role->id, $role->name), html::specialchars($role->name));
			$this->page_title = text::title($role->name);
			$this->page_actions[] = array('link' => 'role/' . url::title($role->id, $role->name) . '/delete', 'text' => __('Delete role'), 'class' => 'role-delete');
		} else {
			$this->page_title = __('Role');
		}

		if (empty($errors)) {
			widget::add('main', View_Mod::factory('roles/role_edit', array('values' => $form_values, 'errors' => $form_errors)));
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
		$role = new Role_Model((int)$role_id);
		if ($role->id) {
			$role->delete();
		}

		url::redirect('roles');
	}

}
