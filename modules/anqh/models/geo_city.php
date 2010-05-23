<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Geo_City Model
 *
 * @package    Geo
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Geo_City_Model_Core extends ORM {

	protected $belongs_to = array('geo_country');
	protected $has_many   = array('names' => 'geo_city_names');

	protected $rules = array(
		'*'              => array('pre_filter' => 'trim'),
		'geo_country_id' => array('rules' => array('required', 'valid::digit')),
		'name'           => array('rules' => array('required', 'length[1,200]')),
	);


	/**
	 * Get localized city name
	 *
	 * @param   string  $lang
	 * @return  string
	 */
	public function get_name($lang = 'en') {
		if ($this->loaded()) {
			foreach ($this->names->find_all() as $name) {
				if ($name->lang == strtolower($lang)) {
					return $name->name;
				}
			}
		}

		return false;
	}

}
