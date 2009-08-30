<?php
/**
 * Set the site to invite only mode, valid login credentials required
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class invite_hook {

	/**
	 * Adds invite only check to the routing event
	 */
	public function __construct() {

		// Hook only if enabled in config
		if (Kohana::config('site.inviteonly')) {
			Event::add('system.routing', array($this, 'login'));
		}
	}


	/**
	 * Show invite only page if enabled
	 */
	public function login() {
		$uri = new URI();

		// Redirect to invite page if not logged or signing in
		if (!in_array($uri->string(), array('invite', 'sign/in')) && !Visitor::instance()->logged_in()) {
			url::redirect('invite');
		}
	}
}

new invite_hook();
