<?php
/**
 * Forum area model
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Forum_Area_Model extends Modeler_ORM {

	/**
	 * Access to edit area
	 */
	const ACCESS_EDIT = 'edit';

	/**
	 * Area read access
	 */
	const ACCESS_READ = 'read';

	/**
	 * Area write access (= new topic)
	 */
	const ACCESS_WRITE = 'write';

	/**
	 * Normal area
	 */
	const TYPE_NORMAL = 0;

	/**
	 * Read-only area
	 */
	const TYPE_READONLY = 1;

	/**
	 * Log-in required area
	 */
	const TYPE_LOGGED = 2;

	/**
	 * Private area
	 */
	const TYPE_PRIVATE = 4;

	/**
	 * Bound area, topics bound to other model
	 */
	const TYPE_BIND = 8;

	/**
	 * Hidden area
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
	 * Get list of area types
	 *
	 * @return  array
	 */
	public static function area_types() {
		return array(
			self::TYPE_NORMAL   => __('Normal area, everybody can read and start new topics'),
			self::TYPE_READONLY => __('Read only, only moderators can start new topics'),
			self::TYPE_LOGGED   => __('Members only, only logged in members can read topics'),
			self::TYPE_PRIVATE  => __('Private area, uses different db (deprecated?)'),
			self::TYPE_BIND     => __('Bound area, topics are bound to other models'),
			self::TYPE_HIDDEN   => __('Hidden area, nobody can see'),
		);
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

				// Edit access to area
				case self::ACCESS_EDIT:
					$access = ($user && $user->has_role('admin'));
					break;

				// Read access to area
				case self::ACCESS_READ:
					if (!$this->is_type(self::TYPE_HIDDEN)) {
						$access = ($user || !$this->is_type(self::TYPE_LOGGED | self::TYPE_PRIVATE));
					}
					break;

				// Write access to area (= new topic)
				case self::ACCESS_WRITE:
					if (!$this->is_type(self::TYPE_HIDDEN && $user)) {
						$access = (!$this->is_type(self::TYPE_READONLY | self::TYPE_BIND) || $user->has_role('admin', 'forum moderator'));
					}
					break;

			}
			$cache[$cache_id] = $access;
		}

		return $cache[$cache_id];
	}


	/**
	 * Is the area of area type
	 *
	 * @param  integer  $access_type
	 * @see    TYPE_NORMAL
	 * @see    TYPE_READONLY
	 * @see    TYPE_LOGGED
	 * @see    TYPE_PRIVATE
	 * @see    TYPE_BIND
	 */
	public function is_type($access_type) {
		return $access_type == self::TYPE_NORMAL ? ($this->access === 0) : (bool)($this->access & $access_type);
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
