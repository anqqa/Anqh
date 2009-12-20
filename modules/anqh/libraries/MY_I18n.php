<?php
/**
 * Anqh extended I18n library.
 *
 * Added profiler and key prefix support.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

/**
 * Plural translation function
 *
 * @param   string   $string
 * @param   string   $string_plural
 * @param   integer  $count
 * @param   array    $args
 * @return  string
 */
function __2($string, $string_plural, $count, array $args = NULL) {
	return (int)$count == 1 ? __($string, $args) : __($string_plural, $args);
}

class I18n extends I18n_Core { }
