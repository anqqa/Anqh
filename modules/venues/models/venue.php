<?php
/**
 * Venue model
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Venue_Model extends Modeler_ORM {

	/**
	 * Access to delete a venue
	 */
	const ACCESS_DELETE = 'delete';

	/**
	 * Access to edit a venue
	 */
	const ACCESS_EDIT = 'edit';

	// ORM
	protected $has_and_belongs_to_many = array('images', 'tags');
	protected $belongs_to = array('author' => 'user', 'venue_category', 'city', 'default_image' => 'image');
	protected $sorting    = array('city_name' => 'ASC', 'name' => 'ASC');
	//protected $load_with = array('cities');

	// Validation
	protected $_rules = array(
		'name'              => array('required', 'length[1, 32]'),
		'homepage'          => array('length[0, 100]', 'valid::url'),
		'description'       => array('length[0, 250]'),
		'hours'             => array('length[0, 250]'),
		'info'              => array('length[0, 512]'),
		'address'           => array('length[0, 50]'),
		'zip'               => array('length[4, 5]', 'valid::digit'),
		'latitude'          => array('length[0, 10]', 'valid::numeric'),
		'longitude'         => array('length[0, 10]', 'valid::numeric'),
		'venue_category_id' => array('required', 'valid::digit'),
		'city_id'           => array('required', 'valid::digit'),
		'city_name'         => array('required', 'length[0, 50]'),
		'event_host'        => array('in_array[1]'),

		'logo'              => array('upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[400K]'),
		'picture1'          => array('upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[400K]'),
		'picture2'          => array('upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[400K]'),
	);

	protected $filters = array(
		'homepage' => array('format::url'),
	);

	protected $url_base = 'venue';


	/**
	 * Get random venues
	 *
	 * @param   integer  $limit
	 * @return  Venue_Model
	 */
	public function get_random_venues($limit = 2) {
		return $this->order_by('', 'RANDOM()')->find_all($limit);
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

				// Access to delete venue
				case self::ACCESS_DELETE:

				// Access to edit venue
				case self::ACCESS_EDIT:
					$access = ($user && ($this->is_author($user) || $user->has_role('admin', 'venue moderator')));
					break;

			}
			$cache[$cache_id] = $access;
		}

		return $cache[$cache_id];
	}

}
