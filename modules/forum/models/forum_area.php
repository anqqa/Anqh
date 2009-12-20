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
	const ACCESS_READ = 'r';

	/**
	 * Area write access
	 *
	 * @var  string
	 */
	const ACCESS_WRITE = 'w';

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
	protected $has_many   = array('forum_topics');
	protected $has_one    = array('last_topic' => 'forum_topic');
	protected $belongs_to = array('forum_group', 'author' => 'user');
	protected $sorting    = array('sort' => 'ASC');
	protected $load_with  = array('forum_groups', 'last_topics');

	// Validation
	protected $rules = array(
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
	 */
	public function access_has($user, $type = self::ACCESS_READ) {
		$access = false;

		switch ($type) {

			// read access to area
			case self::ACCESS_READ:
				$mask = self::TYPE_HIDDEN;

				// non-logged has more restrictions
				if (!$user) $mask |= self::TYPE_LOGGED | self::TYPE_PRIVATE;

				// mask fits, no access
				if (!($this->access & $mask)) $access = true;
				break;

			// write access to area (= new topic)
			case self::ACCESS_WRITE:
				$mask = self::TYPE_READONLY;

				// mask fits, no access
				if (!($this->access & $mask)) $access = true;

				// non-logged can't never write
				if (!$user) $access = false;
				break;
		}
		return $access;
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
