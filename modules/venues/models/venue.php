<?php
/**
 * Venue model
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Venue_Model extends Modeler_ORM {

	// ORM
	protected $belongs_to = array('author' => 'user', 'venue_category');
	protected $has_one    = array('city', 'default_image' => 'image');
	protected $has_and_belongs_to_many = array('images', 'tags');
	protected $sorting    = array('city_name' => 'ASC', 'name' => 'ASC');
	//protected $load_with = array('city');

	// Validation
	protected $rules = array(
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
		'city_name'         => array('length[0, 50]'),
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
		return $this->orderby('', 'RANDOM()')->find_all($limit);
	}
}
