<?php
/**
 * User model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class User_Model extends Modeler_ORM {

	// ORM
	protected $has_many = array('favorites', 'friends', 'tokens', 'user_comments');
	protected $has_one = array('city', 'default_image' => 'image');
	protected $has_and_belongs_to_many = array('images', 'roles');

	// Columns to ignore
	protected $ignored_columns = array('password_confirm');

	// Validation
	protected $rules = array(
		'name'           => array('length[1,50]'),
		'address_street' => array('length[0, 50]'),
		'address_zip'    => array('length[4, 5]', 'valid::digit'),
		'address_city'   => array('length[0,50]'),
		'city_id'        => array('valid::digit'),
		'dob'            => array('valid::date'),
		'gender'         => array('in_array[m,f]'),

		'image'          => array('upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[400K]'),
	);
	protected $rules_login = array(
		'username' => array('required'),
		'password' => array('required'),
	);
	protected $callbacks_register = array(
		'email'            => array('unique'),
		'username'         => array('unique'),
	);
	protected $rules_register = array(
		'email'            => array('required', 'length[4,127]', 'valid::email'),
		'username'         => array('required', 'length[4,32]', 'chars[a-zA-Z0-9_.]'),
		'password'         => array('required', 'length[5,128]'),
		//'password_confirm' => array('matches[password'),
	);
	protected $rules_password = array(
		'password'         => array('required', 'length[5,42]'),
		'password_confirm' => array('matches[password'),
	);

	// Cached data
	protected $data_roles;


	/***** MAGIC *****/

	/**
	 * Create new User Model
	 *
	 * @param  mixed  $id
	 */
	public function __construct($id = null) {
		parent::__construct($id);

		// override defaults with configurable values, username
		$min = max(1,  (int)Kohana::config('auth.username.length_min'));
		$max = min(30, (int)Kohana::config('auth.username.length_max'));
		$this->rules_register['username'] = array('required',  'length[' . $min . ',' . $max . ']', 'chars[' . Kohana::config('auth.username.chars') . ']');

		// password
		$min = max(1,  (int)Kohana::config('auth.password.length_min'));
		$this->rules_register['password'] = $this->rules_password['password'] = array('required',  'length[' . $min . ',128]');
	}


	/**
	 * Magic setter
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 */
	public function __set($key, $value)	{
		switch ($key) {

			// day of birth
			case 'dob':
				$value = date::format('date_SQL', $value);
				break;

			// use Auth to hash the password
			case 'password':
				$value = Auth::instance()->hash_password($value);
				break;

		}

		parent::__set($key, $value);
	}

	/***** /MAGIC *****/


	/***** AUTH *****/

	/**
	 * Validates an array for a matching password and password_confirm field.
	 *
	 * @param  array    values to check
	 * @param  string   save the user if
	 * @return boolean
	 */
	public function change_password(array &$array, $save = false) {

		if ($status = $this->validate($array, false, array(), array(), array('rules' => 'password'))) {
			// Change the password
			$this->password = $array['password'];

			if ($save !== false && $status = $this->save()) {
				if (is_string($save)) {
					// Redirect to the success page
					url::redirect($save);
				}
			}
		}

		return $status;
	}


	/**
	 * Validates login information from an array, and optionally redirects
	 * after a successful login.
	 *
	 * @param  array    values to check
	 * @param  string   URI or URL to redirect to
	 * @return boolean
	 */
	public function login(array &$array, $redirect = false) {
		// Login starts out invalid
		$status = false;

		if ($this->validate($array, false, array(), array(), array('rules' => 'login'))) {
			// Attempt to load the user
			$this->find($array['username']);

			if ($this->loaded && Auth::instance()->login($this, $array['password'])) 	{
				if (is_string($redirect))	{
					// Redirect after a successful login
					url::redirect($redirect);
				}

				// Login is successful
				$status = true;
			} else {
				$array->add_error('username', 'invalid');
			}
		}

		return $status;
	}

	/***** /AUTH *****/


	/***** COMMENTS *****/

	/**
	 * Call after adding/deleting comment
	 */
	public function clear_comment_cache() {

		// clear caches
		$this->cache->delete($this->cache->key('comments', $this->id, 1));
		$this->cache->delete($this->cache->key('comments', $this->id, 2));
		$this->cache->delete($this->cache->key('comments', $this->id, 3));
	}


	/**
	 * Get user's total comment count
	 *
	 * @param   User_Model  $user
	 * @return  int
	 */
	public function get_comment_count() {
		return (int)ORM::factory('user_comment')->where('user_id', $this->id)->count_all();
	}


	/**
	 * Get user's comments
	 *
	 * @param  int  $page_num
	 * @param  int  $page_size
	 */
	public function get_comments($page_num, $page_size = 25) {
		$cache_key = $this->cache->key('comments', $this->id, $page_num);

		// cache only 3 first pages
		if ($page_num < 4) {
			$comments = $this->cache->get($cache_key);
		}

		if (!empty($comments)) {
			return  unserialize($comments);
		} else {
			$page_offset = ($page_num - 1) * $page_size;
			$comments = $this->limit($page_size, $page_offset)->user_comments;

			// cache only 3 first pages
			if ($page_num < 4) {
				$this->cache->set($cache_key, serialize($comments->as_array()), null, 3600);
			}
			return $comments;
		}
	}

	/***** /COMMENTS *****/


	/***** FRIENDS *****/

	/**
	 * Create friendship
	 *
	 * @param  User_Model  $friend
	 */
	public function add_friend(User_Model $friend) {

		// don't add duplicate friends or oneself
		if ($this->loaded && $this->id != $friend->id && !$this->is_friend($friend)) {
			$friendship = new Friend_Model();
			$friendship->user_id = $this->id;
			$friendship->friend_id = $friend->id;
			$friendship->save();
			return true;
		}

		return false;
	}


	/**
	 * Delete friendship
	 *
	 * @param  User_Model  $friend
	 */
	public function delete_friend(User_Model $friend) {

		// don't add duplicate friends or oneself
		if ($this->loaded && $this->is_friend($friend)) {
			return (bool)count(Database::instance()->limit(1)->delete('friends', array('user_id' => $this->id, 'friend_id' => $friend->id)));
		}

		return false;
	}


	/**
	 * Does the user have any of these roles
	 *
	 * @param  array|string  $roles
	 */
	public function has_role($roles) {

		// Model must contain data
		if (!$this->loaded) {
			return false;
		}

		// Load roles
		if (!is_array($this->data_roles)) {
			$data_roles = array();
			foreach ($this->roles as $role) {
				$data_roles[$role->id] = $role->name;
			}
			$this->data_roles = $data_roles;
		}

		if (is_array($roles)) {

			// Multiple roles given
			$matching_roles = array_intersect($roles, $this->data_roles);
			$has_role = !empty($matching_roles);

		} else {

			// Sigle role given
			$has_role = in_array($roles, $this->data_roles);

		}
		return $has_role;
	}


	/**
	 * Check for friendship
	 *
	 * @param  mixed  $friend  id, username, User_Model
	 */
	public function is_friend($friend) {
		static $friends;

		if (empty($friend)) {
			return false;
		}

		// load friends
		if (!is_array($friends)) {
			$friends = array();

			if ($this->loaded) {
				foreach ($this->friends as $friendship) {
					$friends[$friendship->friend->id] = utf8::strtoupper($friendship->friend->username);
				}
			}
		}

		if ($friend instanceof User_Model) {
			$friend = $friend->id;
		}

		return is_numeric($friend) ? isset($friends[$friend]) : in_array(utf8::strtoupper($friend), $friends);
	}

	/***** /FRIENDS *****/


	/**
	 * Allows a model to be loaded by username or email address.
	 *
	 * @param   mixed  $id  id, username, email
	 * @return  string
	 */
	public function unique_key($id)	{
		if (!empty($id) && is_string($id) && ! ctype_digit($id)) {
			return valid::email($id) ? 'email' : 'username';
		}

		return parent::unique_key($id);
	}

}
