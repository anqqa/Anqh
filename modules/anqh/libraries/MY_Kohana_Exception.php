<?php
/**
 * Anqh exception handler
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Kohana_Exception extends Kohana_Exception_Core {

	/**
	 * Handle exception
	 *
	 * @param  Exception  $e
	 */
	public static function handle(Exception $e) {
		if ($e instanceof Kohana_404_Exception) {

			if (Kohana::config('site.inviteonly') && !Visitor::instance()->get_user()) {

				// Redirect to invite login if invite only and not logged in
				url::redirect('invite');

			} else {

				if (!headers_sent()) {
					$e->sendHeaders();
				}

				$page = new Error_Controller();
				$page->_404();
			}

		} else {
			return parent::handle($e);
		}
	}

}
