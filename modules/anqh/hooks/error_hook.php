<?php
/**
 * Error pages hook
 * 
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class error_hook {

	/**
	 * Replace default error pages
	 */
	public function __construct() {
		Event::replace('system.404', array('Kohana', 'show_404'), array($this, 'show_404'));
	}
	
	
	/**
	 * 404 handler
	 */
	public function show_404() {
		header('HTTP/1.1 404 File Not Found');

		$page = new Error_Controller();
		$page->_404();
		exit;
	}

}

new error_hook();
