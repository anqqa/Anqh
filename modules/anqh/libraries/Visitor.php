<?php
/**
 * Site visitor library with roles for Anqh, handles authorization.
 * Based heavily on Auth Library by Kohana Team.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Visitor_Core {

	/**
	 * Session instance
	 *
	 * @var  Session
	 */
	protected $session;

	/**
	 * Configuration values
	 *
	 * @var  array
	 */
	protected $config;


	/**
	 * Loads Session and configuration options.
	 *
	 * @param  array  $config
	 */
	public function __construct($config = array()) {

		// Load Session
		$this->session = Session::instance();

		// Append default visitor configuration
		$config += Kohana::config('visitor');

		// Save the config in the object
		$this->config = $config;

		Kohana::log('debug', 'Visitor Library loaded');
	}


	/**
	 * Attempt to automatically log a user in.
	 *
	 * @return  boolean
	 */
	public function auto_login() {
		if ($token = cookie::get($this->config['cookie_name'])) {

			// Load the token and user
			$token = ORM::factory('user_token', $token);

			if ($token->loaded AND $token->user->loaded) {
				if ($token->user_agent === sha1(Kohana::$user_agent)) {

					// Save the token to create a new unique token
					$token->save();

					// Set the new token
					cookie::set($this->config['cookie_name'], $token->token, $token->expires - time());

					// Complete the login with the found data
					$this->complete_login($token->user);

					// Automatic login was successful
					return true;
				}

				// Token is invalid
				$token->delete();
			}
		}

		return false;
	}


	/**
	 * Complete the login for a user by incrementing the logins and setting
	 * session data: user_id, username, roles
	 *
	 * @param   User_Model  $user
	 * @return  boolean
	 */
	protected function complete_login(User_Model $user) {
		$user->logins += 1;
		$user->old_login = $user->last_login;
		$user->last_login = time();
		$user->ip = Input::instance()->ip_address();
		$user->hostname = Input::instance()->host_name();
		$user->save();

		// Regenerate session_id
		$this->session->regenerate();

		// Store user in session
		$_SESSION[$this->config['session_key']] = $user;

		return true;
	}


	/**
	 * Attempt to login with 3rd party account
	 *
	 * @return  bool
	 */
	public function external_login($provider) {
		if ($provider == User_External_Model::PROVIDER_FACEBOOK && $fb_uid = FB::instance()->get_loggedin_user()) {

			// Load the external user
			$user = User_Model::find_user_by_external($fb_uid, $provider);

			if ($user->loaded && $this->complete_login($user)) {
				$this->session->set($this->config['session_key'] . '_provider', $provider);

				return true;
			}
		}

		return false;
	}


	/**
	 * Create an instance of Visitor.
	 *
	 * @param   array  $config
	 * @return  Visitor
	 */
	public static function factory($config = array()) {
		return new Visitor($config);
	}


	/**
	 * Finds the salt from a password, based on the configured salt pattern.
	 *
	 * @param   string  $password  hashed
	 * @return  string
	 */
	public function find_salt($password) {
		$salt = '';

		foreach ($this->config['salt_pattern'] as $i => $offset) {

			// Find salt characters, take a good long look...
			$salt .= substr($password, $offset + $i, 1);

		}

		return $salt;
	}


	/**
	 * Force a login for a specific username.
	 *
	 * @param   User_Model|string  $user
	 * @return  boolean
	 */
	public function force_login($user) {

		// Load the user
		if (!is_object($user)) {
			$user = ORM::factory('user', $user);
		}

		// Mark the session as forced, to prevent users from changing account information
		$_SESSION['visitor_forced'] = true;

		// Run the standard completion
		$this->complete_login($user);
	}


	/**
	 * Get 3rd party provider used to sign in
	 *
	 * @return  string
	 */
	public function get_provider() {
		return $this->session->get($this->config['session_key'] . '_provider', null);
	}


	/**
	 * Gets the currently logged in user from the session or null
	 *
	 * @return  User_Model
	 */
	public function get_user() {
		return ($this->logged_in()) ? $this->session->get($this->config['session_key'], null) : null;
	}


	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 *
	 * @param   string  $password  plaintext
	 * @return  string  hashed password
	 */
	public function hash_password($password, $salt = false) {

		// Create a salt seed, same length as the number of offsets in the pattern
		if ($salt === false) {
			$salt = substr(hash($this->config['hash_method'], uniqid(null, true)), 0, count($this->config['salt_pattern']));
		}

		// Password hash that the salt will be inserted into
		$hash = hash($this->config['hash_method'], $salt . $password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
		$password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($this->config['salt_pattern'] as $offset) {

			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part . array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;

		}

		// Return the password, with the remaining hash appended
		return $password . $hash;
	}


	/**
	 * Return a static instance of Visitor.
	 *
	 * @param   array  $config
	 * @return  Visitor
	 */
	public static function instance($config = array()) {
		static $instance;

		// Load the Visitor instance
		if (empty($instance)) {
			$instance = new Visitor($config);
		}

		return $instance;
	}


	/**
	 * Checks if a session is active.
	 *
	 * @param   string|array  $roles  OR matched
	 * @return  boolean
	 */
	public function logged_in($roles = null) {
		$status = false;

		// Get the user from the session
		$user = $this->session->get($this->config['session_key']);

		// Not logged in, maybe autologin?
		if (!is_object($user) && $this->config['lifetime'] && $this->auto_login()) {
			$user = $this->session->get($this->config['session_key']);
		}

		if (is_object($user)) {
			$status = (empty($roles)) ? true : $user->has_role($roles);
		}

		return $status;
	}


	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param   string|User_Model  $user
	 * @param   string             $password  plain text
	 * @param   boolean            $remember  auto-login
	 * @return  boolean
	 */
	public function login($user, $password, $remember = false) {
		if (empty($password)) {
			return false;
		}

		// Load the user
		if (!is_object($user)) {
			$user = ORM::factory('user', $user);
		}

		if (is_string($password)) {

			// Get the salt from the stored password
			$salt = $this->find_salt($user->password);

			// Create a hashed password using the salt from the stored password
			$password = $this->hash_password($password, $salt);
		}

		// If the passwords match, perform a login
		if ($user->has_role('login') and $user->password === $password) {
			if ($remember === true) {
				// Create a new autologin token
				$token = ORM::factory('user_token');

				// Set token data
				$token->user_id = $user->id;
				$token->expires = time() + $this->config['lifetime'];
				$token->save();

				// Set the autologin cookie
				cookie::set($this->config['cookie_name'], $token->token, $this->config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			return true;
		}

		// Login failed
		return false;
	}


	/**
	 * Log out a user by removing the related session variables.
	 *
	 * @param   boolean  $destroy  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy = false) {

		// Delete the autologin cookie to prevent re-login
		if (cookie::get($this->config['cookie_name'])) {
			cookie::delete($this->config['cookie_name']);
		}

		// Logout 3rd party?
		if (FB::enabled() && Visitor::instance()->get_provider()) {
			$this->session->delete($this->config['session_key'] . '_provider');
			try {
				FB::instance()->expire_session();
			} catch (Exception $e) { }
		}

		// Destroy the session completely?
		if ($destroy === true) {
			$this->session->destroy();
		} else {

			// Remove the user from the session
			$this->session->delete($this->config['session_key']);

			// Regenerate session_id
			$this->session->regenerate();

		}

		// Double check
		return !$this->logged_in();
	}

}
