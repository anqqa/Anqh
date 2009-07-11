<?php
/**
 * Tag group model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Tag_Group_Model extends Modeler_ORM {

	// ORM
	protected $has_many = array('tags');
	protected $belongs_to = array('author' => 'user');

	// Validation
	protected $callbacks = array(
		'name' => array('unique'),
	);
	protected $rules = array(
		'name'         => array('required', 'length[1, 32]'),
		'description'  => array('length[0, 250]'),
	);

	protected $url_base = 'tags';


	public function __construct($id = null) {
		parent::__construct($id);

		$this->form = array(
			'id' => array(
				'input' => array('type' => 'hidden'),
			),
			'name' => array(
				'input' => array('maxlength' => 32),
				'label' => Kohana::lang('tags.group_name'),
			),
			'description' => array(
				'input' => array('maxlength' => 250),
				'label' => Kohana::lang('tags.group_description'),
			),
		);
	}

}
