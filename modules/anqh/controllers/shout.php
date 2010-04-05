<?php
/**
 * Shout controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Shout_Controller extends Website_Controller {

	/**
	 * Show shouts or shout
	 */
	public function index() {

		$shout = new Shout_Model();
		$form_values = $shout->as_array();
		$form_errors = array();

		// Check post
		if (csrf::valid() && $post = $this->input->post()) {
			$shout->author_id = $this->user->id;
			$shout->shout = $post['shout'];

			try {
				$shout->save();
				if (!request::is_ajax()) {
					url::redirect(url::current());
				}
			} catch (ORM_Validation_Exception $e) {
				$form_errors = $e->validation->errors();
				$form_values = arr::overwrite($form_values, $post);
			}
		}

		$shouts = ORM::factory('shout')->find_all(10);
		$view =  View_Mod::factory('generic/shout', array(
			'mod_title' => __('Shouts'),
			'shouts'    => $shouts,
			'can_shout' => ORM::factory('shout')->has_access(Shout_Model::ACCESS_WRITE, $this->user),
			'errors'     => $form_errors,
			'values'     => $form_values,
		));

		if (request::is_ajax()) {
			echo $view;
			return;
		}

		widget::add('main', $view);
	}

}