<?php
/**
 * Venue category model
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Venue_Category_Model extends Modeler_ORM {

	// ORM
	protected $has_many = array('venues');
	protected $has_one  = array('author' => 'user', 'tag_group');
	protected $sorting  = array('name' => 'ASC');

	// Validation
	protected $callbacks = array(
		'name' => array('unique'),
	);
	protected $rules = array(
		'name'         => array('required', 'length[1, 32]'),
		'description'  => array('length[1, 250]'),
		'tag_group_id' => array('valid::digit'),
	);

	protected $url_base = 'venues';
}
