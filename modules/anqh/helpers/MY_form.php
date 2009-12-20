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
	 * @param   string|array  $tip
	 * @return  string
	 */
	public static function button_wrap($data = '', $value = '', $extra = '', $label = '', $error = '', $tip = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::button($data, $value, $extra);

		return form::wrap($input, $name, $label, $error, $tip);
	}


	/**
	 * Creates an HTML form checkbox input tag.
	 *
	 * @param   string|array   input name or an array of HTML attributes
	 * @param   string         input value, when using a name
	 * @param   boolean|array  make the checkbox checked by default
	 * @param   string         a string to be attached to the end of the attributes
	 * @param   string         $label
	 * @param   string|array   $error
	 * @param   string|array   $tip
	 * @return  string
	 */
	public static function checkbox_wrap($data, $value = '', $checked = FALSE, $extra = '', $label = '', $error = '', $tip = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;
		$checked = is_array($checked) ? arr::get($checked, $name) == $value : $checked;

		$input = form::checkbox($data, $value, $checked, $extra);

		return form::wrap($input, $name, $label, $error, $tip, true);
	}


	/**
	 * Creates checkboxes list
	 *
	 * @param   string        $name    input name
	 * @param   array         $data    array of checkboxes
	 * @param   array         $values  checked values
	 * @param   string        $label
	 * @param   string|array  $error
	 * @param   string|array  $tip
	 * @return  string
	 */
	public static function checkboxes_wrap($name, $data = array(), $values = array(), $label = '', $error = '', $class = '', $tip = '') {

		// Get checkboxes
		$checkboxes = isset($data[$name]) ? $data[$name] : $data;

		if (!empty($checkboxes)) {

			// Create internal id
			$singular = inflector::singular($name) . '_';

			// Get values
			$values = array_key_exists($name, $values) ? $values[$name] : $values;
			$input = (empty($class)) ? "<ul>\n" : '<ul class="' . $class . "\">\n";
			foreach ($checkboxes as $checkbox_id => $checkbox_name) {
				$internal_id = $singular . $checkbox_id;
				$input .= '<li>';
				$input .= form::checkbox(array('id' => $internal_id, 'name' => $name . '[' . $checkbox_id . ']'), $checkbox_name, isset($values[$checkbox_id]));
				$input .= form::label($internal_id, $checkbox_name);
				$input .= "</li>\n";
			}
			$input .= "</ul>\n";

			return form::wrap($input, $name, $label, $error, $tip);
		}
	}


	/**
	 * Closes an open form tag.
	 *
	 * @return  string
	 */
	public static function close() {
		return '</form>';
	}


	/**
	 * Creates CSRF token input
	 *
	 * @param  mixed   $id      e.g. uid
	 * @param  string  $action  optional action
	 */
	public static function csrf($id = '', $action = '') {
		return form::hidden('token', csrf::token($id, $action));
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
	 * @param   string|array  $tip
	 * @return  string
	 */
	public static function dropdown_wrap($data, $options = NULL, $selected = NULL, $extra = '', $label = '', $error = '', $tip = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$selected = (is_array($selected) && array_key_exists($name, $selected)) ? $selected[$name] : $selected;
		$options = (is_array($options) && isset($options[$name])) ? $options[$name] : $options;

		$input = form::dropdown($data, $options, $selected, $extra);

		return form::wrap($input, $name, $label, $error, $tip);
	}


	/**
	 * Creates an HTML form input tag. Defaults to a text type.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string|array  input value, when using a name or values
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   string        $label
	 * @param   string|array  $error
	 * @param   string|array  $tip
	 * @return  string
	 */
	public static function input_wrap($data, $value = '', $extra = '', $label = '', $error = '', $tip = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::input($data, $value, $extra);

		return form::wrap($input, $name, $label, $error, $tip);
	}


	/**
	 * Creates a HTML form password input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string|array  input value, when using a name or values
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   string        $label
	 * @param   string|array  $error
	 * @param   string|array  $tip
	 * @param   string        $show_password
	 * @return  string
	 */
	public static function password_wrap($data, $value = '', $extra = '', $label = '', $error = '', $tip = '', $show_password = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		// Inject show password element id
		if ($show_password) {
			if (is_array($data)) {
				$data['show'] = $name . '_show';
			} else {
				$data = array('name' => $name, 'show' => $name . '_show');
			}
		}

		$input = form::password($data, $value, $extra);

		// Add 'Show password' ?
		if ($show_password) {
			$input .= form::checkbox($name . '_show', 'yes') . form::label($name . '_show', $show_password);
		}

		return form::wrap($input, $name, $label, $error, $tip);
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
	 * @param   string|array  $tip
	 * @return  string
	 */
	public static function textarea_wrap($data, $value = '', $extra = '', $double_encode = TRUE, $label = '', $error = '', $tip = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::textarea($data, $value, $extra, $double_encode);

		return form::wrap($input, $name, $label, $error, $tip);
	}


	/**
	 * Creates an HTML form upload input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string|array  input value, when using a name or values
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   string        $label
	 * @param   string|array  $error
	 * @param   string|array  $tip
	 * @return  string
	 */
	public static function upload_wrap($data, $value = '', $extra = '', $label = '', $error = '', $tip = '') {
		$name = is_array($data) ? arr::get($data, 'name') : $data;
		$value = is_array($value) ? arr::get($value, $name) : $value;

		$input = form::upload($data, $value, $extra);

		return form::wrap($input, $name, $label, $error, $tip);
	}


	/**
	 * Create Anqh styles form input wrapped in list
	 *
	 * @param   string        $input
	 * @param   string|array  $name
	 * @param   string        $label
	 * @param   string|array  $error
	 * @param   string|array  $tip
	 * @param   bool          $label_after
	 * @return  string
	 */
	public static function wrap($input, $name = '', $label = '', $error = '', $tip = '', $label_after = false) {

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

		// Input tip if any
		if (!empty($tip)) {
			$tip = '<p class="tip">' . (is_array($tip) ? arr::get($tip, $name) : $tip) . '</p>';
		}

		return ($label_after ? $input . $wrap : $wrap . $input) . $tip . "</li>\n";
	}

}
