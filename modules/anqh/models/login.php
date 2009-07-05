<?php
/**
 * Login attempt model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Login_Model extends ORM {

	/**
	 * Create new login attempt
	 */
	public function __construct() {
		parent::__construct();

		$this->ip = Input::instance()->ip_address();
		$this->hostname = Input::instance()->host_name();
	}

}
