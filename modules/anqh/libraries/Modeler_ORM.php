<?php
/**
 * ORM auto modeler library.
 * Based heavily on Auto Modeler ORM by Jeremy Bush, see official site for more
 * info: http://projects.kohanaphp.com/projects/show/automodeler
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Modeler_ORM extends ORM {

	protected $cache = 'default';
	protected $cache_age = 86400; // 60 * 60 * 24
	protected $cache_key;

	// database fields and default values
	protected $data = array();

	protected $form = array();

	// By default the model can't change id
	protected $ignored_columns = array('id');

	// Validation
	protected $validation = array();
	protected $filters = array();
	protected $_rules = array();
	protected $callbacks = array();

	/**
	 * Model's URL base, e.g. event
	 *
	 * @see  url::model()
	 * @var  string
	 */
	protected $url_base;


	/**
	 * Construct
	 *
	 * @param   mixed  $id
	 * @return  void
	 */
	public function __construct($id = null) {
		if (!is_object($this->cache)) {
			$this->cache = Cache::instance($this->cache);
		}

		if (empty($id)) $id = null;
		parent::__construct($id);
	}


	/**
	 * Handles retrieval of all model values, relationships, and metadata.
	 *
	 * @param   string  column name
	 * @return  mixed
	 */
	public function __get($column) {
		return ($column == 'url_base') ? $this->url_base : parent::__get($column);
	}


	/**
	 * Get validation errors
	 *
	 * @param  array  $lang
	 */
	public function errors($lang = null) {
		return ($this->validation instanceof Validation) ? $this->validation->errors($lang) : array();
	}


	public function get_defaults($form_data = 'form') {
		$form = array();
		foreach ($this->$form_data as $key => $data) {
			$form[$key] = $this->loaded() && isset($this->$key) ? $this->$key : '';
		}

		return $form;
	}


	public function get_form($form = 'form') {
		return $this->$form;
	}


	/**
	 * Check if the user is owner of current model, e.g. topic author
	 *
	 * @param  int|User_Model  $user  current user on null
	 */
	public function is_author($user = null) {

		// Check if we even have the author id
		if ($this->loaded() && isset($this->table_columns['author_id'])) {
			$author_id = $this->author_id;
		} else {
			return false;
		}

		if (empty($user)) {

			// No user given, use logged in user
			$user = Visitor::instance()->get_user();
			return (isset($user->id) && $author_id === $user->id);

		} else if (is_int($user)) {

			// User id given
			return ($author_id === $user);

		} else if ($user instanceof User_Model) {

			// User Model given
			return ($author_id === $user->id);

		}

		return false;
	}


	/**
	 * Replace empty value with null
	 *
	 * @param   mixed  $value
	 * @return  mixed
	 */
	public function null($value) {
		return $value == '' || $value === false ? null : $value;
	}


	/**
	 * Set array of values to object
	 *
	 * @param  array  $data
	 */
	public function set_fields($data) {

		// Strip properties not in model
		$data = array_intersect_key($data, $this->table_columns);

		foreach ($data as $key => $value) {
			if ($key != 'id') {
				$this->__set($key, $value);
			}
		}
	}


	/**
	 * Check if current field is unique
	 *
	 * @see    $callbacks
	 * @param  Validation  $array
	 * @param  string      $field
	 */
	public function unique(Validation $array, $field) {

		// Skip non changed
		if ($array[$field] == $this->$field) return;

		$exists = (bool)$this->db->where($field, $array[$field])->count_records($this->table_name);
		if ($exists) {
			$array->add_error($field, 'unique');
		}
	}


	/**
	 * Return unique field of table based on id type (int = id, string = name etc)
	 *
	 * @param   mixed  $id
	 * @return  string
	 */
	public function unique_key($id) {
		if (!empty($id) && is_string($id) && !ctype_digit($id) && $this->primary_val) {
			return $this->primary_val;
		}

		return parent::unique_key($id);
	}


	/**
	 * Check if current object contains valid data
	 *
	 * @param  array   $extra_data       to be set to the object
	 * @param  array   $extra_functions  to be executed with data
	 * @param  string  $set              name of validation rules etc if not default
	 */
	public function valid($extra_data = array(), $extra_functions = array(), $set = null) {
		//$validation = Validation::factory(array_merge($extra_data, $this->));
	}


	/**
	 * Validate form input
	 *
	 * @param   array  $values e.g. $_POST
	 * @param   bool   $save
	 * @param	  array  $extra_values not to be validated
	 * @param   array  $set instead of default $rules etc, array('rules' => 'login') -> $this->rules_login
	 * @return  int|bool
	 */
	public function validate(array &$values = null, $save = false, $extra_values = null, $extra_functions = null, $set = null) {

		// Fugly hax to handle Kohana 2.4 way of validating
		if (is_null($values)) {
			return parent::validate();
		}

		// Create new Validation object and remove empty values
		$values = Validation::factory($values)->pre_filter('trim')->post_filter(array($this, 'null'));

		// What data to use with validation
		$validation_filters   = isset($set['filters'])   ? 'filters_'   . $set['filters']   : 'filters';
		$validation_rules     = isset($set['rules'])     ? 'rules_'     . $set['rules']     : '_rules';
		$validation_callbacks = isset($set['callbacks']) ? 'callbacks_' . $set['callbacks'] : 'callbacks';

		// Add filters
		foreach ($this->$validation_filters as $field => $filters)
			foreach ($filters as $filter)
				$values->pre_filter($filter, $field);

		// Add validation rules
		foreach ($this->$validation_rules as $field => $rules)
			foreach ($rules as $rule)
				$values->add_rules($field, $rule);

		// Add validation callbacks
		foreach ($this->$validation_callbacks as $field => $callbacks)
			foreach ($callbacks as $callback)
				$values->add_callbacks($field, array($this, $callback));

		// Non-model validation functions
		if (!empty($extra_functions))
			foreach ($extra_functions as $function)
				$this->$function($values);

		// Validate
		if ($values->validate()) {
			$this->_valid = true;

			// Set validated values to current object, skipping non-db
			$this->set_fields($values->safe_array());

			// Add extra values
			if (!empty($extra_values)) {
				$this->set_fields($extra_values);
			}

			try {
				return $save ? $this->save() : true;
			} catch (Kohana_Database_Exception $e) {
				//$values->message('error', 'saving_to_database');
				return false;
			}
		}

		return false;
	}

}
