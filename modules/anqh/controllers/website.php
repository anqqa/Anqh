<?php
/**
 * Base website controller, all pages should inherit from this.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
abstract class Website_Controller extends Controller {

	/**
	 * Breadcrumb
	 *
	 * @var  array
	 */
	protected $breadcrumb;

	/**
	 * Actions for current page
	 *
	 * @var  array
	 */
	protected $page_actions = array();

	/**
	 * Current page class
	 *
	 * @var  string
	 */
	protected $page_class = '';

	/**
	 * Current page id
	 *
	 * @var  string
	 */
	protected $page_id = 'page';

	/**
	 * Page main content position
	 *
	 * @var  string
	 */
	protected $page_main = 'left';

	/**
	 * Current page subtitle
	 *
	 * @var  string
	 */
	protected $page_subtitle = '&nbsp;';

	/**
	 * Current page title
	 *
	 * @var  string
	 */
	protected $page_title = '&nbsp;';

	/**
	 * Page width setting, 'fixed' or 'liquid'
	 *
	 * @var  string
	 */
	protected $page_width = 'fixed';

	/**
	 * Skin for the site
	 *
	 * @var  string
	 */
	protected $skin;

	/**
	 * Skin files imported in skin, check against file modification time for LESS
	 *
	 * @var  array
	 */
	protected $skin_imports;

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
	 * Construct new page controller
	 */
	function __construct() {
		parent::__construct();

		// Init page values
		$this->country = Session::instance()->get('country', false);

		// AJAX requests output without template
		if (request::is_ajax()) {
			$this->auto_render = false;
			$this->history = false;
			return;
		}

		// Use profiler only when an admin is logged in
		if ($this->visitor->logged_in('admin')) {
			Profiler::enable();
		}

		// Bind the generic page variables
		$this->template
			->bind('skin',          $this->skin)
			->bind('skin_imports',  $this->skin_imports)
			->bind('stylesheets',   $this->stylesheets)
			->bind('language',      $this->language)
			->bind('page_width',    $this->page_width)
			->bind('page_main',     $this->page_main)
			->bind('page_id',       $this->page_id)
			->bind('page_class',    $this->page_class)
			->bind('page_title',    $this->page_title)
			->bind('page_subtitle', $this->page_subtitle);

		// Add controller name as default page id
		$this->page_id = Router::$controller;

		// Init page values
		$this->menu = Kohana::config('site.menu');

		$skin_path = 'ui/' . Kohana::config('site.skin') . '/';
		$this->skin = $skin_path . 'skin.less';
		$this->skin_imports = array(
			'ui/layout.less',
			'ui/widget.less',
			'ui/jquery-ui.css',
			'ui/site.css',
			$skin_path . 'jquery-ui.css',
		);
		$this->page_width = Session::instance()->get('page_width', 'fixed');
		$this->page_main = Session::instance()->get('page_main', 'left');
		//$this->stylesheets = array('ui/' . Kohana::config('site.skin') . '/skin', 'ui/' . Kohana::config('site.skin') . '/jquery-ui');
		$this->breadcrumb = array(html::anchor('/', __('Home')));
		$this->tabs = array();

		// If a country is seleced, add custom stylesheet
		if ($this->country && Kohana::config('site.country_css')) {
			widget::add('head', html::stylesheet('ui/' . utf8::strtolower($this->country) . '/skin'));
		}

		// Generic views
		widget::add('actions',    View::factory('generic/actions')->bind('actions', $this->page_actions));
		widget::add('breadcrumb', View::factory('generic/breadcrumb')->bind('breadcrumb', $this->breadcrumb));
		widget::add('navigation', View::factory('generic/navigation')->bind('items', $this->menu)->bind('selected', $this->page_id));
		widget::add('tabs',       View::factory('generic/tabs_top')->bind('tabs', $this->tabs)->bind('selected', $this->tab_id));
		widget::add('search',     View::factory('generic/search', array('providers' => array('members' => __('Members'), 'forum' => __('Forum'))/*Search::instance()->get_provider_list()*/)));

		// Header
		widget::add('header', View::factory('generic/header'));

		// Footer
		widget::add('footer', View::factory('events/events_list', array('id' => 'footer-events-new',    'class' => 'unit size1of4', 'title' => __('New events'), 'events' => ORM::factory('event')->order_by('id', 'DESC')->find_all(10))));
		widget::add('footer', View::factory('forum/topics_list',  array('id' => 'footer-topics-active', 'class' => 'unit size1of4', 'title' => __('New posts'),  'topics' => ORM::factory('forum_topic')->order_by('last_post_id', 'DESC')->find_all(10))));

		// Dock
		$classes = array(
			html::anchor('set/width/narrow', __('Narrow'), array('onclick' => '$("body").addClass("fixed").removeClass("liquid"); $.get(this.href); return false;')),
			html::anchor('set/width/wide',   __('Wide'),   array('onclick' => '$("body").addClass("liquid").removeClass("narrow"); $.get(this.href); return false;')),
			html::anchor('set/main/left',    __('Left'),   array('onclick' => '$("body").addClass("left").removeClass("right"); $.get(this.href); return false;')),
			html::anchor('set/main/right',   __('Right'),  array('onclick' => '$("body").addClass("right").removeClass("left"); $.get(this.href); return false;')),
		);
		widget::add('dock2', __('Layout: ') . implode(', ', $classes));

		$locales = Kohana::config('locale');
		if (count($locales['locales'])) {
			$languages = array();
			foreach ($locales['locales'] as $lang => $locale) {
				$languages[] = html::anchor('set/lang/' . $lang, html::chars($locale['language'][2]));
			}
			widget::add('dock2', ' | ' . __('Language: ') . implode(', ', $languages));
		}
		if ($this->user) {

			// Authenticated view
			widget::add('dock', __('Welcome, :user!', array(':user' => html::nick($this->user->id, $this->user->username))));

			// Logout also from Facebook
			if (FB::enabled() && Visitor::instance()->get_provider()) {
				widget::add('dock', ' ' . html::anchor('sign/out', FB::icon() . __('Sign out'), array('onclick' => "FB.Connect.logoutAndRedirect('/sign/out'); return false;")));
			} else {
				widget::add('dock', ' ' . html::anchor('sign/out', __('Sign out')));
			}

			if (Kohana::config('site.inviteonly')) {
//				widget::add('dock', ' | ' . html::anchor('sign/up', __('Send invite')));
			}

			// Admin functions
			if ($this->visitor->logged_in('admin')) {
				widget::add('dock2', ' | ' . __('Admin: ')
					. html::anchor('roles', __('Roles')) . ', '
					. html::anchor('tags', __('Tags')) . ', '
					. html::anchor('#kohana-profiler', __('Profiler'), array('onclick' => '$("#kohana-profiler").toggle();'))
				);
			}

		} else {

			// Non-authenticated view
			$form =  form::open('sign/in');
			$form .= form::input('username', null, 'title="' . __('Username') . '"');
			$form .= form::password('password', '', 'title="' . __('Password') . '"');
			$form .= form::submit('submit', __('Sign in'));
			$form .= form::close();
			$form .= html::anchor('/sign/up', __('Sign up'));
			if (FB::enabled()) {
				$form .= ' | ' . FB::fbml_login();
			}
			widget::add('dock', $form);

		}

		// End
		widget::add('end', View::factory('generic/end'));

		// Analytics
		$google_analytics = Kohana::config('site.google_analytics');
		if ($google_analytics) {
			widget::add('head', html::script_source("
var _gaq = _gaq || []; _gaq.push(['_setAccount', '" . $google_analytics . "']); _gaq.push(['_trackPageview']);
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
"));
		}

		// Ads
		$ads = Kohana::config('site.ads');
		if ($ads && $ads['enabled']) {
			foreach ($ads['slots'] as $ad => $slot) {
				widget::add($slot, View::factory('ads/' . $ad));
			}
		}

		// Facebook connect
		if (FB::enabled()) {
			FB::init();
		}

	}


	/**
	 * Add autocomplete for city
	 *
	 * @param string $field
	 * @param string $hidden
	 */
	public function _autocomplete_city($field = 'city_name', $hidden = 'city_id') {
		$countries = ORM::factory('country')->where('country', 'IN', Kohana::config('site.countries'))->find_all();

		$cities = array();
		foreach ($countries as $country) {
			foreach ($country->cities->find_all() as $city) {
				$cities[] = array(
					'id'   => $city->id,
					'text' => html::chars($city->city)
				);
			}
		}

		widget::add('foot', html::script_source('var cities = ' . json_encode($cities) /*implode(', ', $cities)*/ . ';
$("input#' . $field . '").autocomplete(cities, {
	formatItem: function(item) {
		return item.text;
	}
}).result(function(event, item) {
	$("input[name=' . $hidden . ']").val(item.id);
});'));
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
