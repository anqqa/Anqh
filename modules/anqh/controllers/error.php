<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Error pages controller
 * 
 * @package  Anqh
 */
class Error_Controller extends Website_Controller {

	/**
	 * 404 - Page not found
	 */
	public function _404() {
		$this->page_title = Kohana::lang('generic.404_title');
		$this->_display();
	}
	
	
}
