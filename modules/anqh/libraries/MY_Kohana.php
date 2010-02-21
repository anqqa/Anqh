<?php
/**
 * Anqh extended Kohana core
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
final class Kohana extends Kohana_Core {

	/**
	 * Migration helper for K2.3.4 -> K2.4
	 *
	 * @param   string  $string
	 * @param   array   $args
	 * @return  string
	 */
	public static function lang($string, $args = null) {
		return __($string, $args);
	}

	/**
	 * Inserts global Anqh variables into the generated output and prints it.
	 *
	 * @param   string  final output that will displayed
	 * @return  void
	 */
	public static function render($output) {
		if (Kohana::config('core.render_stats') === true) {
			$queries = Database::$benchmarks;
			$output = str_replace(array('{database_queries}'), array(count($queries)), $output);
		}

		parent::render($output);
	}

}
