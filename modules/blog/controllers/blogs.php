<?php
/**
 * Blog controller
 *
 * @package    Blog
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Blogs_Controller extends Website_Controller {

	/**
	 * New blog controller
	 */
	public function __construct() {
		parent::__construct();

		$this->breadcrumb[] = html::anchor('blogs', __('Blogs'));
		$this->page_title = __('Blogs');
	}


	/**
	 * Default view
	 */
	public function index() {
		$this->latest();
	}


	/**
	 * Latest blog entries
	 */
	public function latest() {
		widget::add('main', View::factory('blog/entries', array(
			'entries' => ORM::factory('blog_entry')->find_latest(20),
		)));
	}


	/**
	 * Single blog entry
	 *
	 * @param  string  $entry_id
	 */
	public function view($entry_id) {
		$entry = new Blog_Entry_Model((int)$entry_id);
		$errors = $entry->id ? array() : __('Blog entry found :entry', array(':entry' => $entry_id));

		if (empty($errors)) {
			$this->breadcrumb[] = html::anchor(url::model($entry), $entry->name);
			$this->page_title = text::title($entry->name);
			$this->page_subtitle = __('By :user :ago ago', array(
				':user' => html::user($entry->user),
				':ago'  => html::time(date::timespan_short($entry->created), $entry->created)
			));

			$entry->views++;
			$entry->save();

			widget::add('main', View::factory('blog/entry', array('entry' => $entry)));
		}

		if (count($errors)) {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}
	}

}
