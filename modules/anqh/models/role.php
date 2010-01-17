<?php
/**
 * Role model. Roles are used to limit users' access to certain areas or
 * functions.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Role_Model extends ORM {

	protected $has_and_belongs_to_many = array('users');

	protected $rules = array(
		'*'           => array('pre_filter' => 'trim'),
		'name'        => array('rules' => array('required', 'length[2,32]')),
		'description' => array('rules' => array('length[0,255]'))
	);


	/**
	 * Allows finding roles by name.
	 */
	public function unique_key($id) {
		if (!empty($id) && is_string($id) && !ctype_digit($id)) {
			return 'name';
		}

		return parent::unique_key($id);
	}

}
