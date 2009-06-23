<?php
/**
 * CSRF helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class csrf_Core {

	/**
	 * Get CSRF token
	 *
	 * @return  string
	 */
	public static function token() {

		// create new token if old exists
		if (!$_SESSION['csrf']) {
			$_SESSION['csrf'] = text::random('alnum', 16);
		}

		return $_SESSION['csrf'];
	}


	/**
	 * Validate and clear CSRF token
	 *
	 * @param   string  $token
	 * @return  bool
	 */
	public static function valid($token) {

		// compare the given token to session token
		$valid = ($_SESSION['csrf'] && $token === $_SESSION['csrf']);

		// clear session token - can use only once
		unset($_SESSION['csrf']);

		return $valid;
	}

}
