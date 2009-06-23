<?php
/**
 * Anqh extended text helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class text extends text_Core {

	/**
	 * Kohana::lang output depending on plural
	 *
	 * @param		string		$one
	 * @param		string		$many
	 * @param		int|array	$n
	 * @return	string
	 */
	public static function nlang($one, $many, $n) {
		$n = (int)$n;
		return Kohana::lang($n == 1 ? $one : $many, $n);
	}


	/**
	 * Formatted title text
	 *
	 * @param	  string  $title
	 * @param   bool    $format text
	 * @return  string
	 */
	public static function title($title, $format = true) {
		return html::specialchars($format ? utf8::ucwords(utf8::strtolower($title)) : $title);
	}

}
