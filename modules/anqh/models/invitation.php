<?php
/**
 * Invitation model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Invitation_Model extends Modeler_ORM {

	// ORM
	protected $primary_key = 'code';

	// Validation
	protected $callbacks = array(
		'email' => array('unique_email'),
	);
	protected $rules = array(
		'email' => array('required', 'valid::email'),
	);


	/**
	 * Create new invitation code based on email
	 */
	public function code() {
		return text::random('alnum', 16);
	}


	/**
	 * Check if current field is unique
	 *
	 * @see    $callbacks
	 * @param  Validation  $array
	 * @param  string      $field
	 */
	public function unique_email(Validation $array, $field) {
		if (ORM::factory('user')->where('email', strtolower($array[$field]))->count_all()) {
			$array->add_error($field, 'unique');
		}
	}


	/**
	 * Allows a model to be loaded by code or email
	 *
	 * @param   mixed  $id  code, email
	 * @return  string
	 */
	public function unique_key($code)	{
		return valid::email($code) ? 'email' : 'code';
	}

}
