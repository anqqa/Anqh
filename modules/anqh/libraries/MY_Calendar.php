<?php
/**
 * Anqh extended Calendar library.
 *
 * Added profiler and key prefix support.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Calendar extends Calendar_Core {

	/**
	 * Start week on monday?
	 *
	 * @var  boolean
	 */
	public static $start_monday = true;


	/**
	 * Convert the calendar to HTML using the kohana_calendar view.
	 *
	 * @return  string
	 */
	public function render() {
		$view =  new View('generic/calendar', array('month' => $this->month, 'year' => $this->year, 'weeks' => $this->weeks()));

		return $view->render();
	}

}
