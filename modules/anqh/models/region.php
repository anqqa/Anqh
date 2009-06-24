<?php
/**
 * Region model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Region_Model extends ORM {

	protected $belongs_to = array('country');
	protected $has_many = array('cities');
}
