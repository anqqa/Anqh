<?php
/**
 * Anqh extended format helper
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Format extends Format_Core {

	/**
	 * Formats a money value according to the current locale.
	 *
	 * @param   float   $money
	 * @param   string  $currency  code, e.g. EUR
	 * @return  string
	 */
	public static function money($money, $currency = null) {
		$currency = text::currency($currency ? $currency : arr::get(Kohana::config('locale.country'), 2), 'symbol');
		$localeconv = localeconv();

		return sprintf('%s %s', number_format($money, round($money) == $money ? 0 : 2, $localeconv['mon_decimal_point'], $localeconv['mon_thousands_sep']), $currency);
	}


	/**
	 * Extends URL formatter with urlencode
	 *
	 * @param   string  $url
	 * @return  string
	 */
	public static function url($str = '') {

		// Clear protocol-only strings like "http://"
		if ($str === '' OR substr($str, -3) === '://') {
			return '';
		}

		// If no protocol given, prepend "http://" by default
		if (strpos($str, '://') === false) {
			return 'http://' . urlencode($str);
		}

		// Return the original URL
		return $str;
	}

}
