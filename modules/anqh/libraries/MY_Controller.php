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
	 * Current language
	 *
	 * @var  string
	 */
	public $language = 'en';

	/**
	 * Template view name
	 *
	 * @var  string
	 */
	protected $template = 'layout';

	/**
	 * User Model
	 *
	 * @var  User_Model
	 */
	protected $user;

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

		// Maybe controllers shouldn't know about cache? But still..
		$this->cache = Cache::instance();

		// Load current visitor for easy access
		$this->visitor = Visitor::instance();

		// Load current user for easy controller access, null if not logged
		$this->user = $this->visitor->get_user();

		// Build the page
		$this->template = View::factory($this->template);

		if ($this->auto_render === true) {

			// Display the template immediately after the controller method
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
