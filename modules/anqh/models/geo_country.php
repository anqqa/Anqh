<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Geo Country Model
 *
 * @package    Geo
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Geo_Country_Model_Core extends ORM {

	protected $has_many = array('cities' => 'geo_cities', 'names' => 'geo_country_names');

	protected $rules = array(
		'*'        => array('pre_filter' => 'trim'),
		'name'     => array('rules' => array('required', 'length[1,200]')),
		'code'     => array('rules' => array('required', 'length[2]')),
		'currency' => array('rules' => array('length[3]')),
	);


	/**
	 * Get localized country name
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


	/**
	 * Allows finding countries by country code
	 */
	public function unique_key($id) {
		if (!empty($id) && is_string($id) && !ctype_digit($id)) {
			return 'code';
		}

		return parent::unique_key($id);
	}

}
