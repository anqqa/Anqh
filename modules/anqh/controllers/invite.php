<?php
/**
 * Invite page controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class Invite_Controller extends Website_Controller {

	/**
	 * Redirect back to front page after login
	 *
	 * @var  string
	 */
	public $history = '/';
	
	/**
	 * Use different page template for invite only page
	 *
	 * @var  string
	 */
	protected $template = 'invite';

	/**
	 * Invite only page
	 */
	public function index() {
		
		// Redirect to front page if already logged in
		if (Auth::instance()->logged_in() || !Kohana::config('site.inviteonly')) {
			url::redirect('/');
		}
	}
	
}
