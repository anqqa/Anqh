<?php
/**
 * Country model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Country_Model extends ORM {

	protected $has_many = array('cities', 'regions');

}
