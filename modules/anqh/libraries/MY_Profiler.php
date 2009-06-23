<?php
/**
 * Anqh extended Profiler library.
 *
 * Added cache support.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Profiler extends Profiler_Core {

	public function __construct() {
		parent::__construct();
		Event::add('profiler.run', array($this, 'cache'));
	}


	/**
	 * Cache data.
	 *
	 * @return  void
	 */
	public function cache() {
		if (!$table = $this->table('cache'))
			return;

		$table->add_column();
		$table->add_column('kp-column kp-data');
		$table->add_column('kp-column kp-data');
		$table->add_column('kp-column kp-data');
		$table->add_row(array('Cache', 'Gets', 'Sets', 'Deletes'), 'kp-title', 'background-color: #E0FFE0');

		$queries = Cache::$queries;

		text::alternate();
		$total_gets = $total_sets = $total_deletes = 0;
		$total_requests = array();
		foreach ($queries as $type => $requests) {
			foreach ($requests as $query) {
				if (!isset($total_requests[$query])) $total_requests[$query] = array('gets' => 0, 'sets' => 0, 'deletes' => 0);
				$total_requests[$query][$type]++;
			}
		}
		foreach ($total_requests as $query => $types) {
			$data = array($query, $types['gets'], $types['sets'], $types['deletes']);
			$class = text::alternate('', 'kp-altrow');
			$table->add_row($data, $class);
			$total_gets += $types['gets'];
			$total_sets += $types['sets'];
			$total_deletes += $types['deletes'];
		}

		$data = array('Total: ' . count($total_requests), $total_gets, $total_sets, $total_deletes);
		$table->add_row($data, 'kp-totalrow');
	}

}
