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
	protected $belongs_to = array('user');
	protected $load_with  = array('user');

	protected $url_base = 'blog';


	/**
	 * Find latest blog entries
	 *
	 * @param   integer  $limit
	 * @param   integer  $page
	 * @return  ORM_Iterator
	 */
	public function find_latest($limit = 20, $page = 1) {
		$entries = $this->orderby('id', 'DESC')->find_all($limit, ($page - 1) * $limit);

		return $entries;
	}

}
