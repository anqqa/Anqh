<?php
/**
 * Forum topic model
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Forum_Topic_Model extends Modeler_ORM {

	/**
	 * Topic edit access
	 *
	 * @var  string
	 */
	const ACCESS_EDIT = 'edit';

	/**
	 * Topic read access
	 *
	 * @var  string
	 */
	const ACCESS_READ = 'read';

	/**
	 * Topic write access (= reply)
	 *
	 * @var  string
	 */
	const ACCESS_WRITE = 'write';

	// ORM
	protected $belongs_to = array('forum_area', 'author' => 'user');
	protected $has_many   = array('forum_posts');
	protected $has_one    = array('last_post' => 'forum_post', 'first_post' => 'forum_post', 'event');
	protected $sorting    = array('last_post_id' => 'DESC');
	protected $reload_on_wakeup = false;

	// Validation
	protected $_rules = array(
		'forum_area_id' => array('required', 'valid::numeric'),
		'name'          => array('required', 'length[1, 200]'),
		'event_id'      => array('valid::numeric'),
		'read_only'     => array('is_numeric'),
	);

	protected $url_base = 'topic';


	/**
	 * Get topics by latest post
	 *
	 * @param   integer  $limit
	 * @param   integer  $page
	 * @return  ORM_Iterator
	 */
	public function find_active($limit = 20, $page = 1) {

		// Add area filter if necessary
		if (!empty($this->forum_area_id)) {
			$topics = ORM::factory('forum_topic')->order_by('last_post_id', 'DESC')->where('forum_area_id', $this->forum_area_id)->find_all($limit, ($page - 1) * $limit);
		} else {
			$topics = ORM::factory('forum_topic')->order_by('last_post_id', 'DESC')->find_all($limit, ($page - 1) * $limit);
		}

		return $topics;
	}


	/**
	 * Get topics by latest topic
	 *
	 * @param   integer  $limit
	 * @param   integer  $page
	 * @return  ORM_Iterator
	 */
	public function find_latest($limit = 20, $page = 1) {

		// Add area filter if necessary
		if (!empty($this->forum_area_id)) {
			$topics = ORM::factory('forum_topic')->order_by('id', 'DESC')->where('forum_area_id', $this->forum_area_id)->find_all($limit, ($page - 1) * $limit);
		} else {
			$topics = ORM::factory('forum_topic')->order_by('id', 'DESC')->find_all($limit, ($page - 1) * $limit);
		}

		return $topics;
	}


	/**
	 * Check if user has access to the forum area
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

				// Edit access to topic
				case self::ACCESS_EDIT:
					$access = ($user && ($this->is_author($user) || $user->has_role('admin', 'forum moderator')));
					break;

				// Read access to topic
				case self::ACCESS_READ:
					$access = $this->forum_area->has_access(Forum_Area_Model::ACCESS_READ, $user);
					break;

				// Write access to topic
				case self::ACCESS_WRITE:
					$access = ($user && !$this->read_only);
					break;

			}
			$cache[$cache_id] = $access;
		}

		return $cache[$cache_id];
	}


	/**
	 * Refresh topic values, fixing ids, counts etc
	 *
	 * @param   boolean  $save  save new values
	 * @return  boolean
	 */
	public function refresh($save = true) {
		if ($this->loaded()) {

			// First post data
			$first_post = ORM::factory('forum_post')->where('forum_topic_id', '=', $this->id)->order_by('id', 'ASC')->find();
			$this->first_post_id = $first_post->id;
			$this->author_id     = $first_post->author_id;
			$this->author_name   = $first_post->author_name;

			// Last post data
			$last_post = ORM::factory('forum_post')->where('forum_topic_id', '=', $this->id)->order_by('id', 'DESC')->find();
			$this->last_post_id = $last_post->id;
			$this->last_posted  = $last_post->created;
			$this->last_poster  = $last_post->author_name;

			// Counts
			$this->posts = count($this->forum_posts);

			if ($save) $this->save();

			return true;
		}

		return false;
	}

}
