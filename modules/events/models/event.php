<?php
/**
 * Event model
 *
 * @package    Events
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Event_Model extends Modeler_ORM {

	// ORM
	protected $has_and_belongs_to_many = array('tags', 'images');
	protected $has_many  = array('favorites');
	protected $has_one   = array('author' => 'user', 'city', 'country', 'venue', 'flyer_front_image' => 'image', 'flyer_back_image' => 'image');
	protected $load_with = array('cities');

	// Validation
	protected $_rules = array(
		'id'          => array('valid::digit'),
		'name'        => array('required', 'length[3,100]'),
		'homepage'    => array('length[1,100]', 'valid::url'),
		'venue_name'  => array('length[1,100]'),
		'venue_id'    => array('valid::digit'),
		'city_name'   => array('required', 'length[1,50]'),
		'city_id'     => array('required', 'valid::digit'),
		'dj'          => array('length[1,4000]'),
		'start_date'  => array('required', 'length[4,10]', 'valid::date'),
		'start_hour'  => array('required', 'length[1,5]', 'valid::time'),
		'end_hour'    => array('length[1,5]', 'valid::time'),
		'age'         => array('length[1,2]', 'valid::digit'),
		'price'       => array('length[1,5]', 'valid::numeric'),
		'price2'      => array('length[1,5]', 'valid::numeric'),
		'info'        => array('length[1,40000]'),

		'flyer_front' => array('upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[400K]'),
		'flyer_back'  => array('upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[400K]'),
	);
	protected $filters = array(
		'homepage' => array('format::url'),
	);


	/***** FAVORITES *****/

	/**
	 * Create favorite
	 *
	 * @param  User_Model  $user
	 */
	public function add_favorite(User_Model $user) {

		// don't add duplicate favorites
		if ($this->loaded() && !$this->is_favorite($user)) {
			$favorite = new Favorite_Model();
			$favorite->user_id = $user->id;
			$favorite->event_id = $this->id;
			$favorite->save();
			return true;
		}

		return false;
	}


	/**
	 * Delete favorite
	 *
	 * @param  User_Model  $friend
	 */
	public function delete_favorite(User_Model $user) {

		// don't add duplicate favorites
		if ($this->loaded() && $this->is_favorite($user)) {
			return (bool)count(Database::instance()->limit(1)->delete('favorites', array('user_id' => $user->id, 'event_id' => $this->id)));
		}

		return false;
	}


	/**
	 * Get past events
	 *
	 * @param   int    $limit
	 * @param   array  $filter  field => value, field => value
	 * @return  ORM_Iterator
	 */
	public function find_past($limit = 25, $filter = null) {

		// try to fetch events for the past 7 days
		$where = array(
			array('start_time', '>=', date::unix2sql(strtotime('-6 days'))),
			array('start_time', '<', date('Y-m-d', time())),
		);
		$events = ORM::factory('event')->where($where)->order_by(array('start_time' => 'DESC', 'city_name' => 'ASC'))->find_all();

		// if no events found, fetch next n
		if (!$events->count()) {
			unset($where[1]);
			$events = ORM::factory('event')->where($where)->order_by(array('start_time' => 'DESC', 'city_name' => 'ASC'))->find_all($limit);
		}

		return $events;
	}


	/**
	 * Get upcoming events
	 *
	 * @param   int    $limit
	 * @param   array  $filter  field => value, field => value
	 * @return  ORM_Iterator
	 */
	public function find_upcoming($limit = 25, $filter = null) {

		// try to fetch events for the next 7 days
		$where = array(
			array('start_time', '>=', date('Y-m-d', time())),
			array('start_time', '<=', date::unix2sql(strtotime('+6 days')))
		);
		$events = ORM::factory('event')->where($where)->order_by(array('start_time' => 'ASC', 'city_name' => 'ASC'))->find_all();

		// if no events found, fetch next n
		if (!$events->count()) {
			unset($where[1]);
			$events = ORM::factory('event')->where($where)->order_by(array('start_time' => 'ASC', 'city_name' => 'ASC'))->find_all($limit);
		}

		return $events;
	}


	/**
	 * Check for favorite
	 *
	 * @param  mixed  $user  id, username, User_Model
	 */
	public function is_favorite($user) {
		static $favorites;

		if (empty($user)) {
			return false;
		}

		// load favored people
		if (!is_array($favorites)) {
			$favorites = array();

			if ($this->loaded()) {
				foreach ($this->favorites as $favorite) {
					$favorites[$favorite->user->id] = utf8::strtoupper($favorite->user->username);
				}
			}
		}

		if ($user instanceof User_Model) {
			$user = $user->id;
		}

		return is_numeric($user) ? isset($favorites[$user]) : in_array(utf8::strtoupper($user), $favorites);
	}

	/***** /FAVORITES *****/

}
