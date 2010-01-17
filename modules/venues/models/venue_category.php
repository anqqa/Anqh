<?php
/**
 * Venue category model
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Venue_Category_Model extends Modeler_ORM {

	/**
	 * Access to create new categories
	 */
	const ACCESS_CREATE = 'create';

	/**
	 * Access to delete categories
	 */
	const ACCESS_DELETE = 'delete';

	/**
	 * Access to edit categories
	 */
	const ACCESS_EDIT = 'edit';

	/**
	 * Access to add new venue
	 */
	const ACCESS_VENUE = 'venue';

	// ORM
	protected $has_many = array('venues');
	protected $has_one  = array('author' => 'user', 'tag_group');
	protected $sorting  = array('name' => 'ASC');

	// Validation
	protected $callbacks = array(
		'name' => array('unique'),
	);
	protected $_rules = array(
		'name'         => array('required', 'length[1, 32]'),
		'description'  => array('length[1, 250]'),
		'tag_group_id' => array('valid::digit'),
	);

	protected $url_base = 'venues';


	/**
	 * Get venues by category, ordered by city
	 *
	 * @param  string  $country
	 */
	public function find_venues($country = false) {
		$cities = $country ? ORM::factory('country')->find($country)->cities->as_array() : false;
		$venues = $this->venues->find_all();

		$venues_by_city = array();
		if (count($venues)) {
			foreach ($venues as $venue) {
				if ($cities && !in_array($venue->city_id, $cities)) continue;
				if (!isset($venues_by_city[$venue->city->city])) {
					$venues_by_city[$venue->city->city] = array($venue);
				} else {
					$venues_by_city[$venue->city->city][] = $venue;
				}
			}
		}

		return $venues_by_city;
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

				// Create, delete or edit categories
				case self::ACCESS_CREATE:
				case self::ACCESS_DELETE:
				case self::ACCESS_EDIT:
					$access = ($user && $user->has_role('admin', 'venue moderator'));
					break;

				// Add venues to categiry
				case self::ACCESS_VENUE:
					$access = ($user && $user->has_role('admin', 'venue moderator', 'venue'));
					break;

			}
			$cache[$cache_id] = $access;
		}

		return $cache[$cache_id];
	}

}
