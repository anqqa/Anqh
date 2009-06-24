<?php
/**
 * User comment model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class User_Comment_Model extends Auto_Modeler_ORM {

	// ORM
	protected $has_one    = array('author' => 'user');
	protected $belongs_to = array('user');
	protected $load_with  = array('author');
	protected $sorting    = array('id' => 'DESC');
	protected $reload_on_wakeup = false;


	// Validation
	protected $rules = array(
		'comment'   => array('required', 'length[1, 300]'),
		'user_id'   => array('required', 'valid::numeric'),
		'author_id' => array('required', 'valid::numeric'),
		'private'   => array('in_array[1]'),
	);

}
