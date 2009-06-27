<?php
/**
 * Forum post model
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Forum_Post_Model extends Modeler_ORM {

	// ORM
	protected $belongs_to = array('forum_area', 'forum_topic', 'author' => 'user', 'parent' => 'forum_post');
	protected $load_with  = array('author');

	// Validation
	protected $rules = array(
		'forum_topic_id' => array('required', 'valid::numeric'),
		'forum_area_id'  => array('required', 'valid::numeric'),
		'post'           => array('required', 'length[1, 4096]'),
		'parent_id'      => array('valid::numeric'),
	);

}
