<?php
/**
 * City model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class City_Model extends ORM {

	protected $belongs_to = array('country', 'region');
	protected $sorting = array('city' => 'ASC');

}
