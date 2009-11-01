<?php
/**
 * User's 3rd party account model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class User_External_Model extends Modeler_ORM {

	/**
	 * Facebook account
	 */
	const PROVIDER_FACEBOOK = 'Facebook';

	// ORM
	protected $belongs_to = array('user');
	protected $ignored_columns = null;

}
