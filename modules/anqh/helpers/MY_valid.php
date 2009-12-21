<?php
/**
 * Anqh extended validation helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class valid extends valid_Core {

	/**
	 * Validate date
	 *
	 * @param  int|string $year
	 * @param  int $month
	 * @param  int $day
	 * @return bool
	 */
	public static function date_ymd($year, $month = false, $day = false) {
		if (!$month && !$day) {
			$date = is_int($year) ? $year : strtotime($year);
			list($year, $month, $day) = explode('-', date('Y-m-d', $date));
		}
		return checkdate($month, $day, $year);
	}


	/**
	 * Validate month
	 *
	 * @param  int $month
	 * @return bool
	 */
	public static function month($month) {
		return (is_numeric($month) && (int)$month >= 1 && (int)$month <= 12);
	}


	/**
	 * Validate time
	 *
	 * @param  int|string $time
	 * @return bool
	 */
	public static function time($time) {
		return (bool)date::time_24h($time);
	}


	/**
	 * Validate CSRF token
	 *
	 * @param   string  $token
	 * @return  bool
	 */
	public static function token($token) {
		// require token to be in session and remove after use
		return (!empty($token) && !empty($_SESSION['token']) && $token == arr::remove('token', $_SESSION['token']));
	}


	/**
 	 * Validate week
	 *
	 * @param  int $week
	 * @return bool
	 */
	public static function week($week) {
		return (is_numeric($week) && (int)$week >= 1 && (int)$week <= 53);
	}


	/**
	 * Validate year
	 *
	 * @param  int $year
	 * @return bool
	 */
	public static function year($year) {
		return is_numeric($year) && $year >= 1900 && $year <= 2100;
		// return (is_numeric($year) && $year >= Kohana::config('site.year_min') && (int)$year <= Kohana::config('site.year_max'));
	}

}
