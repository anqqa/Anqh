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
	 * Return text with smileys
	 *
	 * @param  string  $text
	 */
	public static function smileys($text) {
		static $smileys;

		// Load smileys
		if (!is_array($smileys)) {
			$smileys = array();

			$config = Kohana::config('site.smiley');
			if (!empty($config)) {
				$url = url::base() . $config['dir'] . '/';
				foreach ($config['smileys'] as $name => $smiley) {
					$smileys[$name] = html::image(array('src' => $url . $smiley['src'], 'class' => 'smiley'), $name);
				}
			}

		}

		// Smile!
		return empty($smileys) ? $text : str_replace(array_keys($smileys), $smileys, $text);
	}


	/**
	 * Formatted title text
	 *
	 * @param	  string  $title
	 * @param   bool    $format text
	 * @return  string
	 */
	public static function title($title, $format = true) {
		return html::specialchars($title);
		return html::specialchars($format ? utf8::ucwords(utf8::strtolower($title)) : $title);
	}

}
