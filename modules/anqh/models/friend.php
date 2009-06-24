<?php
/**
 * Friend model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Friend_Model extends Auto_Modeler_ORM {

	// ORM
	protected $belongs_to  = array('user', 'friend' => 'user');
	protected $load_with   = array('friend');
	protected $sorting     = array('friend.username' => 'ASC');
	//protected $primary_key = 'friend_id';

}
