<?php
/**
 * AJAX call controller, all ajax pages should inherit from this.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
abstract class Ajax_Controller extends Controller {

	/**
	 * Build the controller
	 */
	public function __construct() {
		parent::__construct();

		if (!request::is_ajax()) {
			header('HTTP/1.1 403 Forbidden');
			return;
		}
	}

}
