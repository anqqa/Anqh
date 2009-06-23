<?php
/**
 * Anqh extended Input library.
 *
 * Added profiler and key prefix support.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Input extends Input_Core {

	/**
	 * Host name of current user.
	 *
	 * @var  string
	 */
	public $host_name;


	/**
	 * Fetch the host name of current user.
	 *
	 * @return  string
	 */
	public function host_name() {
		if (!is_string($this->host_name)) {
			$ip = $this->ip_address();
			$this->host_name = ($ip == '0.0.0.0') ? $ip : gethostbyaddr($this->ip_address());
		}

		return $this->host_name;
	}

}
