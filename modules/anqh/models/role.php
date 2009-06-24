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
 */
class Role_Model extends ORM {

	protected $has_and_belongs_to_many = array('users');


	/**
	 * Validates and optionally saves a role record from an array.
	 *
	 * @param   array    $array  values to check
	 * @param   boolean  $save   save the record when validation succeeds
	 * @return  boolean
	 */
	public function validate(array &$array, $save = false) {
		$array = Validation::factory($array)
			->pre_filter('trim')
			->add_rules('name', 'required', 'length[2,32]')
			->add_rules('description', 'length[0,255]');

		return parent::validate($array, $save);
	}


	/**
	 * Allows finding roles by name.
	 */
	public function unique_key($id) {
		if (!empty($id) and is_string($id) and !ctype_digit($id)) {
			return 'name';
		}

		return parent::unique_key($id);
	}

}
