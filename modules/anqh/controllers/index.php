<?php
/**
 * Index page controller
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Index_Controller extends Website_Controller {

	public function index() {
		widget::add('main', __('Welcome to :site', array(':site' => Kohana::config('site.site_name'))));
	}

}