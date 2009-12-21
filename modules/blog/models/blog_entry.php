<?php
/**
 * Blog entry model
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Blog_Entry_Model extends Modeler_ORM {

	// ORM
	protected $has_many   = array('blog_comments');
	protected $belongs_to = array('author' => 'user');
	protected $load_with  = array('authors');

	// Validation
	protected $_rules = array(
		'name'  => array('required', 'length[1, 200]'),
		'entry' => array('required', 'length[1, 8192]'),
	);

	protected $url_base = 'blog';


	/**
	 * Get blog comments
	 *
	 * @param  int  $page_num
	 * @param  int  $page_size
	 */
	public function find_comments($page_num = 1, $page_size = 25) {

		// Not found from cache, load from DB
		$page_offset = ($page_num - 1) * $page_size;
		$comments = $this->limit($page_size, $page_offset)->blog_comments;

		return $comments;
	}


	/**
	 * Find latest blog entries
	 *
	 * @param   integer  $limit
	 * @param   integer  $page
	 * @return  ORM_Iterator
	 */
	public function find_latest($limit = 20, $page = 1) {
		$entries = $this->order_by('id', 'DESC')->find_all($limit, ($page - 1) * $limit);

		return $entries;
	}

}
