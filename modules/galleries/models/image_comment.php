<?php
/**
 * Image comment model
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Image_Comment_Model extends Comment_Model {

	// ORM
	protected $belongs_to = array('image', 'user', 'author' => 'user');

	// Validation
	protected $rules = array(
		'comment'   => array('rules' => array('required', 'length[1, 300]'), 'pre_filter' => 'trim'),
		'image_id'  => array('rules' => array('required', 'valid::numeric')),
		'user_id'   => array('rules' => array('required', 'valid::numeric')),
		'author_id' => array('rules' => array('required', 'valid::numeric')),
		'private'   => array('rules' => array('in_array[0,1]')),
	);

}
