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
	protected $rules = array(
		'*'              => array('pre_filter' => 'trim'),
		'forum_group_id' => array('rules' => array('required', 'digit')),
		'name'           => array('rules' => array('required', 'length[1, 64]')),
		'description'    => array('rules' => array('length[0, 250]')),
		'sort'           => array('rules' => array('required', 'digit', 'range[0, 999]')),
		'access'         => array('rules' => array('required', 'digit', 'range[0, 255]')),
		'bind'           => array('rules' => array('length[0, 32]'), 'callbacks' => array('Forum_Area_Model::bind_config')),
	);

	protected $_rules = array(
		'forum_group_id' => array('required', 'valid::numeric'),
		'name'           => array('required', 'length[1, 32]'),
		'description'    => array('length[0, 250]'),
		'sort'           => array('required', 'valid::digit', 'valid::range[0, 999]'),
		'type'           => array('required', 'valid::digit'),
	);

	protected $url_base = 'forum';


	/**
	 * Handles setting of all model values, and tracks changes between values.
	 *
	 * @param   string  column name
	 * @param   mixed   column value
	 * @return  void
	 */
	public function __set($column, $value) {
		switch ($column) {

			// Set access type
			case 'area_type':
				$types = self::area_types();
				if (!isset($types[$value])) {
					throw new Kohana_Exception('The area type :type does not exist in the :class class', array(':type' => $value, ':class' => get_class($this)));
				}

				$old_access = $this->__get('access');
				parent::__set('access', $old_access | $value);
				break;

			default:
				parent::__set($column, $value);

		}
	}


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
			self::TYPE_HIDDEN   => __('Hidden area'),
		);
	}


	/**
	 * Check if bind config is OK
	 *
	 * @see    $callbacks
	 * @param  Validation  $array
	 * @param  string      $field
	 */
	public static function bind_config(Validation $array, $field) {
		if ($array['access'] & self::TYPE_BIND) {

			if (!isset($array[$field]) || !$array[$field]) {

				// Bind config is required is area type bind is set
				$array->add_error($field, 'required');

			} else if (!in_array($array[$field], array_keys(self::area_types()))) {

				// Invalid bind config, this should not happen(tm)
				$array->add_error($field, 'invalid');

			}

		} else {
			$array[$field] = null;
		}
	}


	/**
	 * Get list of possible model bindings
	 *
	 * @param   boolean|string  true = short list, false = full list, string = specific bind
	 * @return  array
	 */
	public static function binds($bind = true) {
		static $config;

		// Load bind config
		if (!is_array($config)) {
			$config = (array)Kohana_Config::instance()->get('forum.bind');
		}

		if ($bind === true) {

			// Short list for selects etc
			$list = array();
			foreach ($config as $type => $data) {
				$list[$type] = $data['name'];
			}
			return $list;

		} else if ($bind === false) {

			// Full bind config
			return $config;

		} else if (is_string($bind)) {

			// Specific config
			return $config[$bind];

		}
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
	 * @param  integer  $area_type
	 * @see    TYPE_NORMAL
	 * @see    TYPE_READONLY
	 * @see    TYPE_LOGGED
	 * @see    TYPE_PRIVATE
	 * @see    TYPE_BIND
	 */
	public function is_type($area_type) {
		return $area_type == self::TYPE_NORMAL ? ($this->access === 0) : (bool)($this->access & $area_type);
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
