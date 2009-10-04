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

	/**
	 * Register
	 */
	public function index() {
		$this->history = false;

		url::redirect('sign/up');
	}


	/**
	 * Sign in
	 */
	public function in() {
		$this->history = false;

		if (request::method() == 'post') {
			ORM::factory('user')->login($this->input->post(), false);
		}

		url::back();
	}


	/**
	 * Send new invitation
	 *
	 * @param  string  $code  Invalid code given?
	 */
	public function _invite($code = null) {
		$this->history = false;

		$invitation = new Invitation_Model();

		$form_values = $invitation->as_array();
		$form_errors = array();
		$form_message = '';

		if ($code) {

			// Invalid code given
			$form_errors = array('code' => 'default');
			$form_values['code'] = $code;

		} else if(request::method() == 'post') {

	 		// Handle post
			$post = $this->input->post();

			// Validate email
			if ($invitation->validate($post, false)) {

				// Send invitation
				$code = $invitation->code();
				$subject = __(':site invite', array(':site' => Kohana::config('site.site_name')));
				$mail = __("Your invitation code is: :code\n\nOr click directly to sign up: :url", array(':code' => $code, ':url' => url::site('/sign/up/' . $code)));

				// Send invitation
				if (email::send($post->email, Kohana::config('site.email_invitation'), $subject, $mail)) {
					$invitation->code = $code;
					$invitation->save();

					$form_message = __('Invitation sent, you can proceed to Step 2 when you receive your mail.');
				} else {
					$form_message =__('Could not send email to :email', array(':email' => $post->email));
				}

			} else {
				$form_errors = $post->errors();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		widget::add('main', View::factory('member/invite', array('values' => $form_values, 'errors' => $form_errors, 'message' => $form_message)));
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

				$user->add(ORM::factory('role', 'login'));
				$user->save();

				$this->visitor->login($user, $post->password);

				url::back();
			} else {
				$form_errors = $post->errors();
				$form_values = arr::overwrite($form_values, $post->as_array());
			}

		}

		widget::add('main', View::factory('member/signup', array('values' => $form_values, 'errors' => $form_errors, 'invitation' => $invitation)));
	}


	/**
	 * Sign out
	 */
	public function out() {
		$this->history = false;

		// Load auth and log out
		$this->visitor->logout();

		// Redirect back to the login page
		url::back();
	}


	/**
	 * Sign up
	 *
	 * @param  string  $code
	 */
	public function up($code = false) {
		$this->history = false;

		$this->page_title = __('Sign up');

		// Check invitation code
		if ($code) {
			$invitation = new Invitation_Model($code);
			if ($invitation->email) {

				// Valid invitation code found, sign up form
				$this->_join($invitation);

			} else {

				// Invite only hook
				if (Kohana::config('site.inviteonly')) {
					url::redirect('/');
					return;
				}

				$this->_invite($code);
			}
			return;
		}

		// Invite only hook
		if (Kohana::config('site.inviteonly')) {
			url::redirect('/');
			return;
		}

		// Check if we got the code from the form
		if (!$code && request::method() == 'post') {
			$code = $this->input->post('code');
			if ($code) {
				url::redirect('/sign/up/' . $code);
				return;
			}
		}

		$this->_invite();
	}

}
