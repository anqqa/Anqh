<?php
/**
 * Generic settings setter
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Set_Controller extends Controller {


	/**
	 * Init setter
	 */
	public function __construct() {
		parent::__construct();

		$this->history = false;
	}


	/**
	 * Change country
	 *
	 * @param  string  $country
	 */
	public function country($country) {

		$countries = Kohana::config('site.countries');

		// set country or clear
		if (in_array($country, $countries))	$_SESSION['country'] = !empty($_SESSION['country']) && $_SESSION['country'] == $country ? null : $country;

		$return = empty($_SESSION['history']) ? '/' : $_SESSION['history'];
		url::redirect($return);
	}


	/**
	 * Change language
	 *
	 * @param  string  $language
	 */
	public function lang($language) {
		$this->history = false;

		$locale = Kohana::config('locale');
		if (isset($locale['locales'][$language])) {
			$_SESSION['language'] = $locale['locales'][$language]['language'][0];
		}

		$return = empty($_SESSION['history']) ? '/' : $_SESSION['history'];
		url::redirect($return);
	}

}
