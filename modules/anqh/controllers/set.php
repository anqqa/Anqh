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
	 * Session
	 *
	 * @var  Session
	 */
	protected $session;


	/**
	 * Init setter
	 */
	public function __construct() {
		parent::__construct();

		$this->history = false;
		$this->auto_render = false;
		$this->session = Session::instance();
	}


	/**
	 * Change country
	 *
	 * @param  string  $country
	 */
	public function country($country) {
		if (in_array($country, Kohana::config('site.countries')))	{
			if ($this->session->get('country') == $country) {

				// Clear country if same as given
				$this->session->delete('country');

			} else {

				// Set country
				$this->session->set('country', $country);

			}
		}

		url::back();
	}


	/**
	 * Change language
	 *
	 * @param  string  $language
	 */
	public function lang($language) {
		$locale = Kohana::config('locale');
		if (isset($locale['locales'][$language])) {
			$this->session->set('language', $locale['locales'][$language]['language'][0]);
		}

		url::back();
	}


	/**
	 * Set page width
	 *
	 * @param  string  $width
	 */
	public function width($width) {
		$this->session->set('page_width', $width == 'wide' ? 'liquid' : 'fixed');

		if (request::is_ajax()) {
			return;
		}

		url::back();
	}

}
