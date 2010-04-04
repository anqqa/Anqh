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

	// ORM
	protected $belongs_to = array('author' => 'user');
	protected $sorting    = array('id' => 'DESC');

	// Validation
	protected $rules = array(
		'author_id'   => array('rules' => array('required', 'valid::numeric')),
		'author_name' => array('rules' => array('length[1, 30]')),
		'message'     => array('rules' => array('required', 'length[1, 300]'), 'pre_filter' => 'trim'),
	);
}
