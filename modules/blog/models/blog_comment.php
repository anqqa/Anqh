<?php
/**
 * Blog comment model
 *
 * @package    Blog
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Blog_Comment_Model extends Modeler_ORM {

	// ORM
	protected $ignored_columns = array('id');
	protected $has_one    = array('blog_entry', 'author' => 'user');
	protected $belongs_to = array('user');
	//protected $load_with  = array('author');
	protected $sorting    = array('id' => 'DESC');
	protected $reload_on_wakeup = false;

	// Validation
	protected $rules = array(
		'comment'       => array('required', 'length[1, 300]'),
		'blog_entry_id' => array('required', 'valid::numeric'),
		'user_id'       => array('required', 'valid::numeric'),
		'author_id'     => array('required', 'valid::numeric'),
		'private'       => array('in_array[1]'),
	);


	public function __get($key) {
		if ($key == 'author') {
			return ORM::factory('user')->find_user($this->author_id);
		} else {
			return parent::__get($key);
		}
	}

}
