<?php
/**
 * Anqh extended Form helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class form extends form_Core {

	/**
	 * Creates an HTML form button input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string|array  input value, when using a name or values
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   string        $label
	 * @param   string|array  $error
	 * @return  string
	 */
	public static function button_wrap($data = '', $value = '', $extra = '', $label = '', $error = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::button($data, $value, $extra);

		return form::wrap($input, $name, $label, $error);
	}


	/**
	 * Creates checkboxes list
	 *
	 * @param   string        $name    input name
	 * @param   array         $data    array of checkboxes
	 * @param   array         $values  checked values
	 * @param   string        $label
	 * @param   string|array  $error
	 * @return  string
	 */
	public static function checkboxes_wrap($name, $data = array(), $values = array(), $label = '', $error = '', $class = '') {

		// Get checkboxes
		$checkboxes = isset($data[$name]) ? $data[$name] : $data;

		if (!empty($checkboxes)) {

			// Create internal id
			$singular = inflector::singular($name) . '_';

			// Get values
			$values = isset($values[$name]) ? $values[$name] : $values;
			$input = (empty($class)) ? "<ul>\n" : '<ul class="' . $class . "\">\n";
			foreach ($checkboxes as $checkbox_id => $checkbox_name) {
				$internal_id = $singular . $checkbox_id;
				$input .= '<li>';
				$input .= form::checkbox(array('id' => $internal_id, 'name' => $name . '[' . $checkbox_id . ']'), $checkbox_name, isset($values[$checkbox_id]));
				$input .= form::label($internal_id, $checkbox_name);
				$input .= "</li>\n";
			}
			$input .= "</ul>\n";

			return form::wrap($input, $name, $label, $error);
		}
	}


	/**
	 * Creates an HTML form select tag, or "dropdown menu".
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   array         select options, when using a name
	 * @param   string|array  option key(s) that should be selected by default
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   string        $label
	 * @param   string|array  $error
	 * @return  string
	 */
	public static function dropdown_wrap($data, $options = NULL, $selected = NULL, $extra = '', $label = '', $error = '') {
		$input = form::dropdown($data, $options, $selected, $extra);

		return form::wrap($input, $name, $label, $error);
	}


	/**
	 * Creates an HTML form input tag. Defaults to a text type.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string|array  input value, when using a name or values
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   string        $label
	 * @param   string|array  $error
	 * @return  string
	 */
	public static function input_wrap($data, $value = '', $extra = '', $label = '', $error = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::input($data, $value, $extra);

		return form::wrap($input, $name, $label, $error);
	}


	/**
	 * Creates a HTML form password input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string|array  input value, when using a name or values
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   string        $label
	 * @param   string|array  $error
	 * @return  string
	 */
	public static function password_wrap($data, $value = '', $extra = '', $label = '', $error = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::password($data, $value, $extra);

		return form::wrap($input, $name, $label, $error);
	}


	/**
	 * Creates an HTML form textarea tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string|array  input value, when using a name or values
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   boolean       encode existing entities
	 * @param   string        $label
	 * @param   string|array  $error
	 * @return  string
	 */
	public static function textarea_wrap($data, $value = '', $extra = '', $double_encode = TRUE, $label = '', $error = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::textarea($data, $value, $extra, $double_encode);

		return form::wrap($input, $name, $label, $error);
	}


	/**
	 * Creates an HTML form upload input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string|array  input value, when using a name or values
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   string        $label
	 * @param   string|array  $error
	 * @return  string
	 */
	public static function upload_wrap($data, $value = '', $extra = '', $label = '', $error = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::upload($data, $value, $extra);

		return form::wrap($input, $name, $label, $error);
	}


	/**
	 * Create Anqh styles form input wrapped in list
	 *
	 * @param   string        $input
	 * @param   string|array  $name
	 * @param   string        $label
	 * @param   string|array  $error
	 * @return  string
	 */
	public static function wrap($input, $name = '', $label = '', $error = '') {

		$wrap = '';

		// Find the input name
		$name = is_array($name) ? arr::get($name, 'name') : $name;

		// Find the input error if any
		$error = html::error($error, $name);
		if (!empty($error)) {
			$wrap = '<li class="error">';
			$wrap .= $error;
		} else {
			$wrap = '<li>';
		}

		// Input label if any
		if (!empty($label)) {
			$wrap .= form::label($name, $label);
		}

		return $wrap . $input . "</li>\n";
	}

}
