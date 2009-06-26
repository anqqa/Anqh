<?php
/**
 * Sign in/up/out controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Sign_Controller extends Website_Controller {

	public $controller = 'sign';


	/**
	 * Register
	 */
	public function index() {
		$this->up();
	}


	/**
	 * Sign in
	 */
	public function in() {
		$this->history = false;

		$return = empty($_SESSION['history']) ? '/' : $_SESSION['history'];
		if (request::method() == 'post') {
			ORM::factory('user')->login($this->input->post(), false);
		}

		url::redirect($return);
	}


	/**
	 * Send new invitation
	 */
	public function _invite() {
		$this->history = false;

		$invitation = new Invitation_Model();

		$form_values = $invitation->as_array();
		$form_errors = array();
		$form_message = '';

		// handle post
		if (request::method() == 'post') {
			$post = $this->input->post();

			// validate email
			if ($invitation->validate($post, false)) {
				$code = $invitation->code();
				$mail = Kohana::lang('member.invitation_email', $code, url::site('/sign/up/' . $code));
				$subject = Kohana::lang('member.invitation_email_subject', Kohana::config('site.site_name'));

				// send invitation
				if (email::send($post->email, Kohana::config('site.email_invitation'), $subject, $mail)) {
					$invitation->code = $code;
					$invitation->save();

					$form_message = Kohana::lang('member.invitation_sent');
				} else {
					$form_message = Kohana::lang('generic.error_sending_email', $post->email);
				}

			} else {
				$form_errors = $post->errors();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		$this->template->main = new View('member/invite', array('values' => $form_values, 'errors' => $form_errors, 'message' => $form_message));
	}


	/**
	 * Register with code
	 *
	 * @param  Invitation_Model  $invitation
	 */
	public function _join(Invitation_Model $invitation) {
		$this->history = false;

		$user = new User_Model();
		$form_values = $user->as_array();
		$form_errors = array();

		// handle post
		if (request::method() == 'post') {
			$post = $this->input->post();
			$post['email'] = $invitation->email;

			if ($user->validate($post, false, null, null, array('rules' => 'register', 'callbacks' => 'register'))) {
				$invitation->delete();
				$user->save();
				$user->add(ORM::factory('role', 'login'));
				Auth::instance()->login($user, $post->password);

				$return = empty($_SESSION['history']) ? '/' : $_SESSION['history'];
				url::redirect($return);
			} else {
				$form_errors = $post->errors();
				$form_values = arr::overwrite($form_values, $post->as_array());
			}

		}

		$this->template->main = new View('member/signup', array('values' => $form_values, 'errors' => $form_errors, 'invitation' => $invitation));
	}


	/**
	 * Sign out
	 */
	public function out() {
		$this->history = false;

		// Load auth and log out
		Auth::instance()->logout();

		// Redirect back to the login page
		$return = empty($_SESSION['history']) ? '/' : $_SESSION['history'];
		url::redirect($return);
	}


	/**
	 * Sign up
	 *
	 * @param  string  $code
	 */
	public function up($code = false) {
		$this->history = false;

		$this->template->title = Kohana::lang('member.signup');

		// check if we got the code from the form
		if (!$code && request::method() == 'post') {
			$code = $this->input->post('code');
			if ($code) {
				url::redirect('/sign/up/' . $code);
			}
		}

		// check invitation code
		if ($code) {
			$invitation = new Invitation_Model($code);
			if ($invitation->email) {
				$this->_join($invitation);
				return;
			}
		}

		$this->_invite();
	}

}
