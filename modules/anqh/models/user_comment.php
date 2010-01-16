<?php
/**
 * User comment model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class User_Comment_Model extends Modeler_ORM {

	/**
	 * Access to delete comment
	 */
	const ACCESS_DELETE = 'delete';

	/**
	 * Access to set comment as private
	 */
	const ACCESS_PRIVATE = 'private';

	/**
	 * Number of pages to cache
	 *
	 * @var  int
	 */
	public static $cache_max_pages = 3;

	/**
	 * Cache age in seconds
	 *
	 * @var  int
	 */
	public static $cache_max_age = 3600;

	// ORM
	protected $table_name = 'user_comments';
	protected $ignored_columns = array('id');
	protected $has_one    = array('author' => 'user');
	protected $belongs_to = array('user');
	//protected $load_with  = array('authors');
	protected $sorting    = array('id' => 'DESC');
	protected $reload_on_wakeup = false;

	// Validation
	protected $rules = array(
		'comment'   => array('rules' => array('required', 'length[1, 300]'), 'pre_filter' => 'trim'),
		'user_id'   => array('rules' => array('required', 'valid::numeric')),
		'author_id' => array('rules' => array('required', 'valid::numeric')),
		'private'   => array('rules' => array('in_array[0,1]')),
	);


	public function __get($key) {
		if ($key == 'author') {
			return ORM::factory('user')->find_user($this->author_id);
		} else {
			return parent::__get($key);
		}
	}


	/**
	 * Check if user has access to the comment
	 *
	 * @param  string          $type  'read', 'write' etc
	 * @param  int|User_Model  $user  current user on false
	 */
	public function has_access($type, $user = false) {
		static $cache = array();

		$user = ORM::factory('user')->find_user($user);
		$cache_id = sprintf('%d_%s_%d', $this->id, $type, $user ? $user->id : 0);

		if (!isset($cache[$cache_id])) {
			$access = false;
			switch ($type) {

				// Access to delete comment
				case self::ACCESS_DELETE:

				// Access to set comment as private
				case self::ACCESS_PRIVATE:
					$access = ($user && in_array($user->id, array($this->user_id, $this->author_id)));
					break;

			}
			$cache[$cache_id] = $access;
		}

		return $cache[$cache_id];
	}

}
