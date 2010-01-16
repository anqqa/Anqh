<?php
/**
 * Forum group model
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Forum_Group_Model extends Modeler_ORM {

	// ORM
	protected $has_many   = array('forum_areas');
	protected $belongs_to = array('author' => 'user');
	protected $sorting    = array('sort' => 'ASC');
	protected $load_with  = array('forum_areas');

	// Validation
	protected $_rules = array(
		'name'         => array('required', 'length[1, 32]'),
		'description'  => array('length[0, 250]'),
		'sort'         => array('required', 'valid::digit')
	);

	protected $form = array(
		'name' => array(
			'label' => 'name',
		),
		'description' => array(
			'label' => 'description',
		),
		'sort' => array(
			'label' => 'sort order',
		),
	);

	protected $url_base = 'forum/group';


}
