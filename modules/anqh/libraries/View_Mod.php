<?php
/**
 * Module view
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class View_Mod_Core extends View {

	/**
	 * Creates a new View Mod using the given parameters.
	 *
	 * @param   string  view name
	 * @param   array   pre-load data
	 * @param   string  type of file: html, css, js, etc.
	 * @return  object
	 */
	public static function factory($name = null, $data = null, $type = null) {
		return new self($name, $data, $type);
	}


	/**
	 * Wrap view with module specific markup
	 *
	 * @param   string  $output
	 * @return  string
	 */
	public function wrap(&$output) {
		$data = array(

			// Class name defaults to view name
			'class'      => 'mod ' . arr::get($this->kohana_local_data, 'mod_class', strtr(basename($this->kohana_filename, '.php'), '_', '-')),

			'id'         => arr::get($this->kohana_local_data, 'mod_id'),
			'title'      => arr::get($this->kohana_local_data, 'mod_title'),
			'pagination' => arr::get($this->kohana_local_data, 'pagination'),
			'content'    => $output,
		);

		return (string)View::factory('generic/mod', $data);
	}


	/**
	 * Magically converts mod view object to string.
	 *
	 * @return  string
	 */
	public function __toString() {
		try {
			return $this->render(false, array($this, 'wrap'));
		} catch (Exception $e) {
			Kohana_Exception::handle($e);
			return '';
		}
	}

}
