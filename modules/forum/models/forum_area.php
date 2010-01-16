<?php
/**
 * Forum area model
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Forum_Area_Model extends Modeler_ORM {

	/**
	 * Area read access
	 *
	 * @var  string
	 */
	const ACCESS_READ = 'read';

	/**
	 * Area write access (= new topic)
	 *
	 * @var  string
	 */
	const ACCESS_WRITE = 'write';

	/**
	 * Normal area
	 *
	 * @var  int
	 */
	const TYPE_NORMAL = 0;

	/**
	 * Read-only area
	 *
	 * @var  int
	 */
	const TYPE_READONLY = 1;

	/**
	 * Log-in required area
	 *
	 * @var  int
	 */
	const TYPE_LOGGED = 2;

	/**
	 * Private area
	 *
	 * @var  int
	 */
	const TYPE_PRIVATE = 4;

	/**
	 * Special area
	 *
	 * @var  int
	 */
	const TYPE_SPECIAL = 8;

	/**
	 * Hidden area
	 *
	 * @var int
	 */
	const TYPE_HIDDEN = 128;

	// ORM
	protected $has_many    = array('forum_topics');
	protected $belongs_to  = array('last_topic' => 'forum_topic', 'forum_group', 'author' => 'user');
	protected $sorting     = array('sort' => 'ASC');
	protected $load_with   = array('forum_groups');

	// Validation
	protected $_rules = array(
		'forum_group_id' => array('required', 'valid::numeric'),
		'name'           => array('required', 'length[1, 32]'),
		'description'    => array('length[0, 250]'),
		'sort'           => array('required', 'valid::digit', 'valid::range[0, 999]'),
		'type'           => array('required', 'valid::digit'),
	);

	protected $url_base = 'forum';

	/**
	 * Check access rights
	 *
	 * @param   mixed  $user
	 * @param   int    $type
	 * @return  bool
	 *
	 * @deprecated  in favor of has_access
	 */
	public function access_has($user, $type = self::ACCESS_READ) {
		return $this->has_access($type, $user);
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

				// Read access to area
				case self::ACCESS_READ:
					$mask = self::TYPE_HIDDEN;

					// Non-logged has more restrictions
					if (!$user) $mask |= self::TYPE_LOGGED | self::TYPE_PRIVATE;

					// Mask fits, no access
					if (!($this->access & $mask)) $access = true;
					break;

				// Write access to area (= new topic)
				case self::ACCESS_WRITE:

					// Non-logged can't never write
					if ($user) {
						$mask = self::TYPE_READONLY;

						// Mask fits, no access
						if (!($this->access & $mask)) $access = true;

					}
					break;

			}
			$cache[$cache_id] = $access;
		}

		return $cache[$cache_id];
	}


	/**
	 * Refresh area values, fixing ids, counts etc
	 *
	 * @return  bool
	 */
	public function refresh($save = true) {
		if ($this->loaded) {

			// first topic
			$last_topic = ORM::factory('forum_topic')->where('forum_area_id', $this->id)->order_by('last_post_id', 'DESC')->find();
			$this->last_topic_id = $last_topic->id;

			// counts
			$num_topics = count($this->forum_topics);
			$this->topics = $num_topics;
			$num_posts = $this->db->where('forum_area_id', $this->id)->count_records('forum_posts');
			$this->posts = $num_posts;

			if ($save) $this->save();
			return true;
		}
		return false;
	}

}
