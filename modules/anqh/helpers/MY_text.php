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
	 * Return currency
	 *
	 * @param   string  $currency
	 * @param   string  $type  'symbol', 'short', 'long', 'code'
	 * @return  string
	 */
	public static function currency($currency, $type) {
		$currencies = Kohana::config('locale.currencies');
		if (isset($currencies[strtoupper($currency)])) {
			switch ($type) {
				case 'symbol': $currency = $currencies[strtoupper($currency)][0]; break;
				case 'short':  $currency = $currencies[strtoupper($currency)][1]; break;
				case 'long':   $currency = $currencies[strtoupper($currency)][2]; break;
				case 'code':   strtoupper($currency);
			}
		}

		return $currency;
	}


	/**
	 * Kohana::lang output depending on plural
	 *
	 * @param   string         $one
	 * @param   string         $many
	 * @param   integer|array  $n
	 * @return  string
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


	/**
	 * Replaces special/accented UTF-8 characters by ASCII-7 'equivalents'.
	 *
	 * @param   string   string to transliterate
	 * @param   integer  -1 lowercase only, +1 uppercase only, 0 both cases
	 * @return  string
	 */
	public static function transliterate_to_ascii($str, $case = 0) {
		static $UTF8_SPECIAL_CHARS = NULL;

		if ($UTF8_SPECIAL_CHARS === null) {
			$UTF8_SPECIAL_CHARS = array(
				'⁰' => '0', '₀' => '0', '¹' => '1', 'ˡ' => 'l', '₁' => '1', '²' => '2', '₂' => '2',
				'³' => '3', '₃' => '3', '⁴' => '4', '₄' => '4', '⁵' => '5', '₅' => '5', '⁶' => '6',
				'₆' => '6', '⁷' => '7', '₇' => '7', '⁸' => '8', '₈' => '8', '⁹' => '9', '₉' => '9',
				'¼' => '1/4', '½' => '1/2', '¾' => '3/4', '⅓' => '1/3', '⅔' => '2/3', '⅕' => '1/5',
				'⅖' => '2/5', '⅗' => '3/5', '⅘' => '4/5', '⅙' => '1/6', '⅚' => '5/6', '⅛' => '1/8',
				'⅜' => '3/8', '⅝' => '5/8', '⅞' => '7/8', '⅟' => '1/', '⁺' => '+', '₊' => '+',
				'⁻' => '-', '₋' => '-', '⁼' => '=', '₌' => '=', '⁽' => '(', '₍' => '(', '⁾' => ')', '₎' => ')',
				'ª' => 'a', '@' => 'a', '€' => 'e', 'ⁿ' => 'n', '°' => 'o', 'º' => 'o', '¤' => 'o', 'ˣ' => 'x',
				'ʸ' => 'y', '$' => 'S', '©' => '(c)', '℠' => 'SM', '℡' => 'TEL', '™' => 'TM',
				'ä' => 'ae', 'Ä' => 'Ae', 'ö' => 'oe', 'Ö' => 'Oe', 'ü' => 'ue', 'Ü' => 'eE', 'å' => 'aa', 'Å' => 'Aa',
			);
		}

		$str = str_replace(
			array_keys($UTF8_SPECIAL_CHARS),
			array_values($UTF8_SPECIAL_CHARS),
			$str
		);

		return parent::transliterate_to_ascii($str, $case);
	}

}
