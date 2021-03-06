<?php
/**
 * Base controller, page and ajax controllers should inherit from this.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
abstract class Controller extends Controller_Core {

	/**
	 * Use auto-rendering, defaults to false
	 *
	 * @var  bool
	 */
	protected $auto_render = true;

	/**
	 * Cache library
	 *
	 * @var  Cache
	 */
	protected $cache;

	/**
	 * Current selected country
	 *
	 * @var  string
	 */
	public $country;

	/**
	 * Add current page to history
	 *
	 * @var  bool
	 */
	public $history = true;

	/**
	 * Input library
	 *
	 * @var  Input
	 */
	public $input;

	/**
	 * Current language
	 *
	 * @var  string
	 */
	public $language = 'en';

	/**
	 * Loaded modules
	 *
	 * @var  array
	 */
	protected $modules;

	/**
	 * Template view name
	 *
	 * @var  string
	 */
	protected $template = 'layout';

	/**
	 * URI library
	 *
	 * @var  URI
	 */
	protected $uri;

	/**
	 * User Model
	 *
	 * @var  User_Model
	 */
	protected $user;

	/**
	 * Page got a valid CSRF token
	 *
	 * @var  boolean
	 */
	protected $valid_csrf = false;

	/**
	 * Visitor Model, current site visiting user
	 *
	 * @var  Visitor
	 */
	protected $visitor;


	/**
	 * Template loading and setup routine.
	 */
	public function __construct()	{
		parent::__construct();

		// Get loaded modules
		$this->modules = Kohana_Config::instance()->get('core.modules');

		// Initialize libraries
		$this->cache = Cache::instance();
		$this->input = Input::instance();
		$this->uri = URI::instance();
		$this->visitor = Visitor::instance();

		// Validate CSRF token
		if (isset($_REQUEST['csrf'])) {
			$this->valid_csrf = csrf::valid($_REQUEST['csrf']);
		}

		// Load current user for easy controller access, null if not logged
		$this->user = &$this->visitor->get_user();

		// Build the page
		$this->template = View::factory($this->template);

		// Display the template immediately after the controller method?
		if ($this->auto_render === true) {
			Event::add('system.post_controller', array($this, '_display'));
		}

	}


	/**
	 * Display the loaded template.
	 */
	public function _display() {

		// Render the template
		if ($this->auto_render === true) {
			$this->template->render(true);
		}

	}

}
