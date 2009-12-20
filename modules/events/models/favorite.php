<?php
/**
 * Event model
 *
 * @package    Events
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Favorite_Model extends Modeler_ORM {

	// ORM
	protected $has_one   = array('user', 'event');
	protected $load_with = array('users', 'events');
	protected $sorting   = array('event.start_time' => 'DESC');
	//protected $primary_key = 'event_id';

}
