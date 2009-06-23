<?php
/**
 * Anqh extended number helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class num extends num_Core {

	public static function format($number, $decimals = 0) {
		return number_format($number, $decimals, '.', ',');
	}
}
