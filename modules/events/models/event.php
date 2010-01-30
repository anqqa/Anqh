<?php
/**
 * Event model
 *
 * @package    Events
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Event_Model_Core extends Modeler_ORM {

	// ORM
	protected $has_and_belongs_to_many = array('tags', 'images');
	protected $has_many   = array('users');
	protected $has_many_through = array('users' => 'favorites');
	protected $belongs_to = array('author' => 'user', 'city', 'country', 'venue', 'flyer_front_image' => 'image', 'flyer_back_image' => 'image');
	protected $load_with  = array('cities');

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
		return $this->loaded()
			&& $this->is_favorite($user)
			&& (bool)count(db::build()
				->delete('favorites')
				->where('user_id', '=', $user->id)
				->where('event_id', '=', $this->id)
				->execute());
	}


	/**
	 * Get bind forum topics
	 *
	 * @param   string  $bind
	 * @return  array
	 */
	public function find_bind_topics($bind) {
		$topics = array();
		switch ($bind) {

			// Upcoming events
			case 'events_upcoming':
				$events = $this->find_upcoming(100);
				break;

			// Past events
			case 'events_past':
				$events = $this->find_past(100);
				break;

		}

		// Build human readable list
		if (!empty($events)) {
			foreach ($events as $event) {
				$topics[$event->id] = $event->name . ' ' . date::format('DDMMYYYY', $event->start_time);
			}
		}

		return $topics;
	}


	/**
	 * Get past events
	 *
	 * @param   int    $limit
	 * @param   array  $filter  field => value, field => value
	 * @return  ORM_Iterator
	 */
	public function find_past($limit = 25, $filter = null) {
		$where = array(
			array('start_time', '<', date('Y-m-d', time()))
		);
		if ($filter) {
			$where[] = $filter;
		}

		return ORM::factory('event')->where($where)->limit($limit)->order_by(array('start_time' => 'DESC', 'city_name' => 'ASC'))->find_all();
	}


	/**
	 * Get users who have added event as their favorite
	 *
	 * @return  array
	 */
	public function find_favorites() {
		static $favorites;

		if (!is_array($favorites)) {
			$favorites = array();
			if ($this->loaded()) {
				$users = db::build()->select('user_id')->from('favorites')->where('event_id', '=', $this->id)->execute()->as_array();
				foreach ($users as $user) {
					$favorites[(int)$user['user_id']] = (int)$user['user_id'];
				}
			}
		}

		return $favorites;
	}


	/**
	 * Get upcoming events
	 *
	 * @param   int    $limit
	 * @param   array  $filter  where
	 * @return  ORM_Iterator
	 */
	public function find_upcoming($limit = 25, $filter = null) {
		$where = array(
			array('start_time', '>=', date('Y-m-d', time())),
		);
		if ($filter) {
			$where[] = $filter;
		}

		return ORM::factory('event')->where($where)->limit($limit)->order_by(array('start_time' => 'ASC', 'city_name' => 'ASC'))->find_all();
	}


	/**
	 * Check for favorite
	 *
	 * @param  mixed  $user  id, User_Model
	 */
	public function is_favorite($user) {
		if (empty($user)) {
			return false;
		}

		if ($user instanceof User_Model) {
			$user = $user->id;
		}

		$favorites = $this->find_favorites();

		return isset($favorites[(int)$user]);
	}

}
