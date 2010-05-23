<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Geo City Name Model
 *
 * @package    Geo
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Geo_City_Name_Model_Core extends ORM {

	protected $belongs = array('geo_country');

	protected $rules = array(
		'*'           => array('pre_filter' => 'trim'),
		'geo_city_id' => array('rules' => array('required', 'valid::digit')),
		'lang'        => array('rules' => array('required', 'length[2]')),
		'name'        => array('rules' => array('required', 'length[1,200]'))
	);

}
