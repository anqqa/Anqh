<?php
/**
 * Shout model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Shout_Model_Core extends Modeler_ORM {

	/**
	 * Access to shout
	 */
	const ACCESS_WRITE = 'write';

	// ORM
	protected $belongs_to = array('author' => 'user');
	protected $sorting    = array('id' => 'DESC');

	// Validation
	protected $rules = array(
		'author_id' => array('rules' => array('required', 'valid::numeric')),
		'shout'     => array('rules' => array('required', 'length[1, 300]'), 'pre_filter' => 'trim'),
	);


	/**
	 * Check if user has access to the shouts
	 *
	 * @param  string          $type  'read', 'write' etc
	 * @param  int|User_Model  $user  current user on false
	 */
	public function has_access($type, $user = false) {
		$user = ORM::factory('user')->find_user($user);

		return (bool)$user;
	}

}
