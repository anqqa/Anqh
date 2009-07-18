<?php
/**
 * Anqh extended date helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class date extends date_Core {

	/**
	 * SQL date
	 */
	const DATE_SQL = 'date_sql';

	/**
	 * SQL time
	 */
	const TIME_SQL = 'time_sql';


	/**
	 * Returns date range Y-m-d - Y-m-d
	 *
	 * @param	 DateTime	$date
	 * @param	 string	  $type (day, week, month)
	 * @return array
	 */
	public static function datetime2range(DateTime $date, $type) {
		switch ($type) {
			case 'day':
				$from = $to = $date->format('Y-m-d');
				break;
			case 'week':
				$fdow = Kohana::config('locale.start_monday') ? 1 : 7;
				$dow = $date->format('N');
				if ($dow > $fdow) $date->modify('-' . ($dow - $fdow) . ' days');
			case '7days':
				$from = $date->format('Y-m-d');
				$date->modify('+6 days');
				$to = $date->format('Y-m-d');
				break;
			case 'month':
				$from = $date->format('Y-m-01');
				$to = $date->format('Y-m-t');
				break;
			default:
				return false;
		}
		return date::range($from, $to);
	}


	/**
	 * Locale formatted date
	 *
	 * @param   string  $format
	 * @param   mixed   $date    default now
	 * @return  strign
	 */
	public static function format($format, $date = false) {
		if (!$date) $date = time();
		if (!is_numeric($date)) $date = strtotime($date);
		switch ($format) {

			// SQL date
			case self::DATE_SQL:
				$format = 'Y-m-d';
				break;

			// SQL time
			case self::TIME_SQL:
				$format = 'Y-m-d H:i:s';
				break;

			default:
				if (strpos($format, 'generic') === false) $format = Kohana::lang('generic.date_' . $format);
				break;

		}

		return date($format, $date);
	}


	/**
	 * Returns date range
	 *
	 * @param		string|int	$from
	 * @param		string|int	$to
	 * @return	array
	 */
	public static function range($from, $to) {
		is_numeric($from) or $from = strtotime($from);
		is_numeric($to) or $to = strtotime($to);
		if ($from > $to) {
			$swap = $from;
			$from = $to;
			$to = $swap;
		}
		$from = date('Y-m-d', $from);
		$to = date('Y-m-d', $to);
		$range = array($from);
		while ($from != $to) {
			$from = date('Y-m-d', strtotime($from . ' +1 day'));
			$range[] = $from;
		}
    return $range;
	}


	/**
	 * Return valid time in format hh:mm
	 *
	 * @param int|string $time
	 */
	public static function time_24h($time) {
		$hour = false;
		$minute = false;
		$len = strlen($time = trim($time));

		// time is always 2-5 characters
		if ($len < 1 || $len > 5)
			return false;

		if (ctype_digit($time)) {
			// 0-23
			if ($len < 3) {
				$hour = $time;
			// 0000-2359
			} else if ($len == 4) {
				list($hour, $minute) = str_split($time, 2);
			}
		} else {
			// 0:00-23:59, 0.00-23.59
			if ($len > 3)
				list($hour, $minute) = sscanf($time, '%d[:\.]%d');
		}

		if ($hour === false || (int)$hour < 0 || (int)$hour > 23 || (int)$minute < 0 && (int)$minute > 59)
			return false;

		return sprintf('%02d:%02d', (int)$hour, (int)$minute);
	}

	/**
	 * Returns time difference in human readable format with only the largest span
	 *
	 * @param		int|string	$time1
	 * @param		int|string	$time2
	 * @param		string			$output
	 * @return	string
	 */
	public static function timespan_short($time1, $time2 = null) {
		if (!is_numeric($time1)) $time1 = strtotime($time1);
		if (!is_null($time2) && !is_int($time2)) $time2 = strtotime($time2);
		if ($difference = date::timespan($time1, $time2) AND is_array($difference)) {
			foreach ($difference as $span => $amount)
				if ($amount > 0)
					return $amount . ' ' . Kohana::lang('generic.' . inflector::singular($span, $amount));
		}

		if (empty($difference)) {
			return '0 ' . Kohana::lang('generic.seconds');
		}

		return Kohana::lang('generic.unknown_span');
	}


	/**
	 * Returns month of given week
	 *
	 * @param		int	$week
	 * @param		int	$year
	 * @return	int
	 */
	public static function week2month($week, $year) {
		return date('n', strtotime('+' . $week . ' weeks', mktime(0, 0, 0, 1, 1, $year)));
	}


	/**
	 * Returns number of weeks
	 *
	 * @param		int	year
	 * @return	int
	 */
	public static function weeks($year) {
		return (int)date('W', mktime(0, 0, 0, 12, 28, $year));
	}


	/**
	 * Converts unix timestamp to SQL timestamp YYYY-MM-DD HH:II:SS
	 *
	 * @param	  int  $timestamp default now
	 * @return  string
	 */
	public static function unix2sql($timestamp = false) {
		if ($timestamp === false) $timestamp = time();
		return is_numeric($timestamp) ? date('Y-m-d H:i:s', $timestamp) : $timestamp;
	}

}
