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
	public static function factory($name = NULL, $data = NULL, $type = NULL) {
		return new View_Mod($name, $data, $type);
	}


	/**
	 * Wrap view with module specific markup
	 *
	 * @param   string  $output
	 * @return  string
	 */
	public function wrap($output) {
		$data = array(
			'id'      => arr::get($this->kohana_local_data, 'mod_id'),
			'title'   => arr::get($this->kohana_local_data, 'mod_title'),

			// Class name defaults to view name
			'class'   => 'mod ' . arr::get($this->kohana_local_data, 'mod_class', strtr('_', '-', basename($this->kohana_filename, '.php'))),

			'content' => $output,
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
