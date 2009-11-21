<?php
/**
 * Hook to record short page history
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class history_hook {

	/**
	 * Hook the recorder
	 */
	public function __construct() {
		Event::add('system.post_controller', array($this, 'history'));
	}


	/**
	 * Save current url to session
	 */
	public function history() {
		if (Kohana::$instance->history) $_SESSION['history'] = url::current();
	}

}

new history_hook();
