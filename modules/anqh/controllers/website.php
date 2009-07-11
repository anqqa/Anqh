<?php
/**
 * Base website controller, all pages should inherit from this.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
abstract class Website_Controller extends Controller {

	/**
	 * Breadcrumb
	 *
	 * @var  array
	 */
	public $breadcrumb;

	/**
	 * Actions for current page
	 *
	 * @var  array
	 */
	public $page_actions = array();

	/**
	 * Current page id
	 *
	 * @var  string
	 */
	public $page_id = 'page';

	/**
	 * Current page class
	 *
	 * @var  string
	 */
	public $page_class = '';

	/**
	 * Current page subtitle
	 *
	 * @var  string
	 */
	public $page_subtitle = '&nbsp;';

	/**
	 * Current page title
	 *
	 * @var  string
	 */
	public $page_title = '&nbsp;';

	/**
	 * Profiler library
	 *
	 * @var  Profiler
	 */
	protected $profiler;

	/**
	 * Selected tab
	 *
	 * @var  string
	 */
	protected $tab_id;

	/**
	 * Tabs navigation
	 *
	 * @var  array
	 */
	protected $tabs;

	/**
	 * Main content template
	 *
	 * @var  string
	 */
	protected $template_content = 'content64';


	/**
	 * Construct new page controller
	 */
	function __construct() {
		parent::__construct();

		// AJAX requests use different controller
		if (request::is_ajax()) {
			header('HTTP/1.1 403 Forbidden');
			return;
		}

		// Use profiler only when an admin is logged in
		if (Auth::instance()->logged_in('admin')) {
			$this->profiler = new Profiler;
		}

		// Build the main view
		$this->template
			->bind('content',       View::factory($this->template_content))
			->bind('stylesheets',   $this->stylesheets)
			->bind('language',      $this->language)
			->bind('page_id',       $this->page_id)
			->bind('page_class',    $this->page_class)
			->bind('page_title',    $this->page_title)
			->bind('page_subtitle', $this->page_subtitle);

		// Add controller name as default page id
		$this->page_id = Router::$controller;

		// Init page values
		$this->country = empty($_SESSION['country']) ? false : $_SESSION['country'];
		$this->menu = Kohana::config('site.menu');
		$this->stylesheets = array('ui/' . Kohana::config('site.skin') . '/skin', 'ui/' . Kohana::config('site.skin') . '/jquery-ui');
		$this->breadcrumb = array(html::anchor('/', __('Home')));
		$this->tabs = array();

		// If a country is seleced, add custom stylesheet
		if ($this->country && Kohana::config('site.country_css')) {
			$this->stylesheets[] = 'ui/' . utf8::strtolower($this->country) . '/skin';
		}

		// Generic views
		widget::add('actions',    View::factory('generic/actions')->bind('actions', $this->page_actions));
		widget::add('breadcrumb', View::factory('generic/breadcrumb')->bind('breadcrumb', $this->breadcrumb));
		widget::add('navigation', View::factory('generic/menu')->bind('items', $this->menu)->bind('selected', $this->page_id));
		widget::add('tabs',       View::factory('generic/tabs_side')->bind('tabs', $this->tabs)->bind('selected', $this->tab_id));

		// Header
		widget::add('header', View::factory('generic/header'));

		// Footer
		widget::add('footer', View::factory('events/events_list', array('id' => 'footer-events-new',    'class' => 'grid-3', 'title' => __('New events'),   'events' => ORM::factory('event')->orderby('id', 'DESC')->find_all(10))));
		widget::add('footer', View::factory('forum/topics_list',  array('id' => 'footer-topics-active', 'class' => 'grid-3', 'title' => __('Active topics'), 'topics' => ORM::factory('forum_topic')->orderby('last_post_id', 'DESC')->find_all(10))));

		// Dock
		$locales = Kohana::config('locale');
		if (count($locales['locales'])) {
			$languages = array();
			foreach ($locales['locales'] as $lang => $locale) {
				$languages[] = html::anchor('set/lang/' . $lang, html::specialchars($locale['language'][2]));
			}
			widget::add('dock2', __('Language: ') . implode(', ', $languages));
		}
		if ($this->user) {

			// Authenticated view
			widget::add('dock', __('Welcome, :user!', array(':user' => html::nick($this->user->id, $this->user->username))));
			widget::add('dock', html::anchor('sign/out', __('Sign out')));

			// Admin functions
			if (Auth::instance()->logged_in('admin')) {
				widget::add('dock2', ' | ' . __('Admin: ') . html::anchor('roles', __('Roles')) . ', ' . html::anchor('tags', __('Tags')));
			}

		} else {

			// Non-authenticated view
			$form =  form::open('sign/in');
			$form .= form::input('username', null, 'title="' . __('Username') . '"');
			$form .= form::input('password-hint', __('Password'), 'autocomplete="off" class="hint"');
			$form .= form::password('password');
			$form .= form::submit('submit', __('Sign in'));
			$form .= form::close();
			$form .= html::anchor('/sign/up', __('Sign up'));
			widget::add('dock', $form);

			$password = <<<JS
$(function() {
	$('#password-hint').show();
	$('#password').hide();

	$('#password-hint').focus(function() {
		$('#password-hint').hide();
		$('#password').show();
		$('#password').focus();
	});
	$('#password').blur(function() {
		if($('#password').val() == '') {
			$('#password-hint').show();
			$('#password').hide();
		}
	});
});
JS;
			widget::add('dock', html::script_source($password));
		}

		// End
		widget::add('end', View::factory('generic/end'));

		// Foot
		$google_analytics = Kohana::config('site.google_analytics');
		if ($google_analytics) {
			widget::add('foot', html::script_source('var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www."); document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));'));
			widget::add('foot', html::script_source("try { var pageTracker = _gat._getTracker('" . $google_analytics . "'); pageTracker._trackPageview(); } catch(err) {}"));
		}

		// Ads
		$ads = Kohana::config('site.ads');
		if ($ads && $ads['enabled']) {
			foreach ($ads['slots'] as $ad => $slot) {
				widget::add($slot, View::factory('ads/' . $ad));
			}
		}

	}


	/**
	 * Add autocomplete for city
	 *
	 * @param string $field
	 * @param string $hidden
	 */
	public function _autocomplete_city($field = 'city_name', $hidden = 'city_id') {
		$countries = ORM::factory('country')->in('country', Kohana::config('site.countries'));

		$cities = array();
		foreach ($countries->find_all() as $country) {
			foreach ($country->cities as $city) {
				$cities[] = "{ id: '" . $city->id . "', text: '" . html::specialchars($city->city) . "' }";
			}
		}

		widget::add('foot', html::script_source('var cities = [' . implode(', ', $cities) . '];'));
		widget::add('foot', html::script_source("$('input#" . $field . "').autocomplete(cities, { formatItem: function(item) { return item.text; }}).result(function(event, item) { $(\"input[name='" . $hidden . "']\").val(item.id); });"));
	}


	/**
	 * Display current page
	 */
	public function _display() {

		// Do some CSS magic to page class
		$page_class = explode(' ', $this->page_class);

		// Add controller method name
		$page_class[] = Router::$method;

		// Add language identifier
		$page_class[] = $this->language;

		// Build the class and remove dupes
		$this->page_class = implode(' ', array_unique($page_class));

		parent::_display();
	}


	/**
	 * Show error box
	 *
	 * @param string       $title
	 * @param string|array $errors
	 */
	public function _error($title = '', $errors = false) {
		$this->page_title = $title;
		widget::add('main', '<div class="error">' . (is_array($errors) ? implode('<br />', $errors) : $errors) . '</div>');
	}

}
