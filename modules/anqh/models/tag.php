<?php
/**
 * Tag model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Tag_Model extends Auto_Modeler_ORM {

	// ORM
	protected $belongs_to = array('tag_group', 'author' => 'user');
	protected $has_and_belongs_to_many = array('places');

	// Validation
	protected $rules = array(
		'name'         => array('required', 'length[1, 32]'),
		'description'  => array('length[0, 250]'),
		'tag_group_id' => array('valid::digit'),
	);


	public function __construct($id = null) {
		parent::__construct($id);

		$this->form = array(
			'id' => array(
				'input' => array('type' => 'hidden'),
			),
			'tag_group_id' => array(
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
