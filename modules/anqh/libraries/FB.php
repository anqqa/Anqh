<?php
require_once(Kohana::find_file('lib', 'facebook/facebook', true, 'php'));

/**
 * Facebook Connect class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class FB extends Facebook {

	/**
	 * Facebook config
	 *
	 * @var  array
	 */
	protected static $config;

	/**
	 * Facebook client instance
	 *
	 * @var unknown_type
	 */
	protected static $instance;


	/**
	 * Create new Facebook client
	 *
	 * @param  bool  $generate_session_secret
	 */
	public function __construct() {

		// Allow only one instance
		if (FB::$instance === null) {

				// Load config
			if (!is_array(FB::$config)) {
				FB::$config = Kohana::config('facebook');
			}

			// Init Facebook client
			parent::__construct(FB::$config['api_key'], FB::$config['secret']);

			FB::$instance = $this;
		}
	}


	/**
	 * Check if Facebook Connect is enabled
	 *
	 * @return  bool
	 */
	public static function enabled() {
		if (!is_array(FB::$config)) {
			FB::$config = Kohana::config('facebook');
		}

		return FB::$config['enabled'];
	}


	/**
	 * Get Facebook Connect login button
	 *
	 * @return  string
	 */
	public static function fbml_login() {
		return '<fb:login-button v="2" size="small" onlogin="FBConnect.login()">Sign in</fb:login-button>';
	}


	/**
	 * Get Facebook icon
	 *
	 * @return  string
	 */
	public static function icon() {
		return html::image('http://static.ak.fbcdn.net/images/icons/favicon.gif', 'Facebook');
	}


	/**
	 * Initializes Facebook Connect
	 */
	public static function init() {
		widget::add('foot', html::script(array('js/fbconnect.js', 'http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_US')));
		widget::add('foot', html::script_source("FB.init('" . FB::$config['api_key'] . "');"));

		// Add logged in Facebook user id to session for easier access
		if ($logged_in = FB::instance()->get_loggedin_user()) {
			$_SESSION['fb_uid'] = $logged_in;
		}

	}


	/**
	 * Get Facebook client instance
	 *
	 * @return  FB
	 */
	public static function instance() {
		if (FB::$instance === null) {
			return new FB();
		}

		return FB::$instance;
	}


}
