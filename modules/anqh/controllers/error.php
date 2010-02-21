<?php
/**
 * Error pages controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Error_Controller extends Website_Controller {

	/**
	 * 404 - Page not found
	 */
	public function _404() {
		$this->page_title = __('404 - le fu.');
		$this->_display();
	}


}
