<?php
/**
 * Venues main controller
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Venues_Controller extends Website_Controller {


	/***** MAGIC *****/

	public function __construct() {
		parent::__construct();

		$this->page_title = __('Venues');
		$this->breadcrumb[] = html::anchor('venues', __('Venues'));
	}


	/**
	 * Magic catch-all
	 *
	 * @param  string  $method
	 * @param  array   $data
	 */
	public function __call($method, $data = null) {

		// Venue category
		if ((int)$method > 0) {
			$this->category($method, isset($data[0]) ? $data[0] : null);
		}

	}

	/***** /MAGIC *****/


	/***** INTERNAL *****/

	private function _side_views() {
	}

	/***** /INTERNAL *****/


	/***** VIEWS *****/

	public function index() {
		if ($this->visitor->logged_in('admin')) {
			$this->page_actions[] = array('link' => 'venues/category/add', 'text' => __('Add category'), 'class' => 'category-add');
		}

		$cities = $this->country ? ORM::factory('country')->find($this->country)->cities->as_array() : false;
		$venue_categories = ORM::factory('venue_category')->find_all();

		widget::add('main', View::factory('venues/venue_categories', array('venue_categories' => $venue_categories, 'cities' => $cities)));

		$this->_side_views();
	}


	/**
	 * Venue category
	 *
	 * @param  int|string  $id
	 * @param  string      $action
	 */
	public function category($category_id, $action = null) {

		// new category
		if ($category_id == 'add') {
			$this->_category_add();
			return;

		} else if ($action) {
			switch ($action) {

				// add venue
				case 'add':
					$this->_venue_add($category_id);
					return;

				// delete category
				case 'delete':
					$this->_category_delete($category_id);
					return;

				// edit category
				case 'edit':
					$this->_category_edit($category_id);
					return;

			}
		}

		$venue_category = new Venue_Category_Model((int)$category_id);
		$errors = $venue_category->id ? array() : array('venues.error_venue_category_not_found');

		if (empty($errors)) {
			$this->breadcrumb[] = html::anchor(url::model($venue_category), $venue_category->name);
			$this->page_title = text::title($venue_category->name);
			$this->page_subtitle = html::specialchars($venue_category->description);

			if ($this->visitor->logged_in(array('admin', 'venue moderator'))) {
				$this->page_actions[] = array('link' => url::model($venue_category) . '/edit', 'text' => __('Edit category'), 'class' => 'category-edit edit');
				$this->page_actions[] = array('link' => url::model($venue_category) . '/add',  'text' => __('Add venue'),     'class' => 'venue-add add');
			}

			// organize by city
			$cities = $this->country ? ORM::factory('country')->find($this->country)->cities->as_array() : false;
			$venues = $venue_category->venues->find_all();
			$venues_by_city = array();
			if (count($venues)) {
				foreach ($venues as $venue) {
					if ($cities && !in_array($venue->city_id, $cities)) continue;
					if (!isset($venues_by_city[$venue->city->city])) {
						$venues_by_city[$venue->city->city] = array($venue);
					} else {
						$venues_by_city[$venue->city->city][] = $venue;
					}
				}
			}
			$this->page_subtitle .= ' - '
				. __2(':cities city',  ':cities cities', count($venues_by_city), array(':cities' => '<var>' . count($venues_by_city) . '</var>')) . ', '
				. __2(':venues venue', ':venues venues', count($venues),         array(':venues' => '<var>' . count($venues) . '</var>'));

			widget::add('main', View::factory('venues/venues', array('venues' => $venues_by_city, 'country' => $this->country)));
		}

		if (count($errors)) {
			//$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * Add new category
	 */
	public function _category_add() {
		$this->_category_edit();
	}


	/**
	 * Delete category
	 *
	 * @param  int|string  $category_id
	 */
	public function _category_delete($category_id) {
		// for authenticated users only
		if (!csrf::valid() || !$this->visitor->logged_in('admin')) {
			url::redirect('/venues');
		}

		$venue_category = new Venue_Category_Model((int)$category_id);
		if ($venue_category->id) {
			$venue_category->delete();
			url::redirect('/venues/');
		}
	}


	/**
	 * Edit category
	 *
	 * @param  int|string  $category_id
	 */
	public function _category_edit($category_id = false) {
		$this->history = false;

		// for authenticated users only
		if (!$this->visitor->logged_in('admin')) {
			url::redirect('/venues');
		}

		$errors = $form_errors = array();

		$venue_category = new Venue_Category_Model((int)$category_id);
		$form_values = $venue_category->as_array();

		// check post
		if (request::method() == 'post') {
			$post = $this->input->post();
			if (csrf::valid() && $venue_category->validate($post, true, array('author_id' => $this->user->id))) {
				url::redirect('/venues/' . url::title($venue_category->id, $venue_category->name));
			} else {
				$form_errors = $post->errors();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		// show form
		if ($venue_category->id) {
			$this->page_subtitle = __('Edit category');

			if ($this->visitor->logged_in('admin')) {
				$this->page_actions[] = array('link' => url::model($venue_category) . '/delete/?token=' . csrf::token(), 'text' => __('Delete category'), 'class' => 'category-delete delete');
			}

		} else {
			$this->page_subtitle = __('Add category');
		}

		if (empty($errors)) {
			$form = $venue_category->get_form();
			$tag_groups = ORM::factory('tag_group')->select_list('id', 'name');
			$form['tag_group_id'] = (empty($tag_groups)) ? $tag_groups : array('') + $tag_groups;
			widget::add('main', View::factory('venues/category_edit', array('form' => $form, 'values' => $form_values, 'errors' => $form_errors)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * venue
	 *
	 * @param  integer|string  $venue_id
	 * @param  string          $action
	 */
	public function venue($venue_id, $action = null) {

		if ($action) {
			switch ($action) {

				// delete venue
				case 'delete':
					$this->_venue_delete($venue_id);
					return;

				// add venue
				case 'edit':
					$this->_venue_edit($venue_id);
					return;

			}
		}

		$venue = new Venue_Model((int)$venue_id);
		$errors = $venue->id ? array() : array('venues.error_venue_not_found');

		if (empty($errors)) {
			$venue_category = $venue->venue_category;
			$this->breadcrumb[] = html::anchor('/venues/' . url::title($venue_category->id, $venue_category->name), $venue_category->name);
			$this->breadcrumb[] = html::anchor('/venue/' . url::title($venue->id, $venue->name), $venue->name);
			$this->page_class = 'venue-' . (int)$venue->id;
			$this->page_title = text::title($venue->name);

			if ($this->user) {
				$this->page_actions[] = array('link' => 'venue/' . url::title($venue->id, $venue->name) . '/edit',   'text' => __('Edit venue'),   'class' => 'venue-edit edit');
				$this->page_actions[] = array('link' => 'venue/' . url::title($venue->id, $venue->name) . '/delete/?token=' . csrf::token(), 'text' => __('Delete venue'), 'class' => 'venue-delete delete');
			}

			widget::add('main', View::factory('venues/venue', array('venue' => $venue)));
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * Add venue
	 *
	 * @param  integer|string  $category_id
	 */
	public function _venue_add($category_id) {
		$this->_venue_edit(false, $category_id);
	}


	/**
	 * Delete venue
	 *
	 * @param  integer|string  $venue_id
	 */
	public function _venue_delete($venue_id) {

		// For authenticated users only
		if (!csrf::valid() || !$this->visitor->logged_in(array('admin', 'venue moderator'))) {
			url::back('/venues');
		}

		$venue = new Venue_Model((int)$venue_id);
		if ($venue->id) {
			$return_id = $venue->venue_category_id;
			$venue->delete();
		}
		url::redirect('/venues/' . $return_id);
	}


	/**
	 * Edit venue
	 *
	 * @param  integer|string  $venue_id
	 * @param  integer|string  $category_id
	 */
	public function _venue_edit($venue_id = false, $category_id = false) {
		$this->history = false;

		// for authenticated users only
		if (!$this->visitor->logged_in(array('admin', 'venue moderator'))) url::redirect('/venues');

		$errors = $form_errors = array();

		$venue = new Venue_Model((int)$venue_id);
		$form_values = $venue->as_array();

		// check post
		if (request::method() == 'post') {
			$post = array_merge($this->input->post(), $_FILES);
			$extra = array('author_id' => $this->user->id);

			// got address, get geocode
			if (!empty($post['address']) && !empty($post['city_name'])) {
				list($extra['latitude'], $extra['longitude']) = Gmap::address_to_ll(implode(', ', array($post['address'], $post['zip'], $post['city_name'])));
			}

			if (csrf::valid() && $venue->validate($post, true, $extra)) {

				// handle logo upload
				if (isset($post->logo) && empty($post->logo['error'])) {
					$logo = Image_Model::factory('venues.logo', $post->logo, $this->user->id);
					if ($logo->id) {
						$venue->add($logo);
						$venue->default_image_id = $logo->id;
						$venue->save();
					}
				}

				// handle picture uploads
				foreach (array($post->picture1, $post->picture2) as $picture) {
					if (isset($picture) && empty($picture['error'])) {
						$image = Image_Model::factory('venues.image', $picture, $this->user->id);
						if ($image->id) {
							$venue->add($image);
							$venue->save();
						}
					}
				}

				// update tags
				$venue->remove(ORM::factory('tag'));
				if (!empty($post->tags)) {
					foreach ($post->tags as $tag_id => $tag) {
						$venue->add(ORM::factory('tag', $tag_id));
					}
				}

				url::redirect('/venue/' . url::title($venue->id, $venue->name));
			} else {
				$form_errors = $post->errors();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		// editing old?
		if ($venue_id) {
			$this->page_subtitle = __('Edit venue');
			if (!$venue->id) {
				$errors = array('venues.error_venue_not_found');
			} else {
				$venue_category = $venue->venue_category;
			}
		} else {
			$this->page_subtitle = __('Add venue');
			if ($category_id) {
				$venue_category = new Venue_Category_Model((int)$category_id);
				if ($venue_category->id) {
					$form_values['venue_category_id'] = $venue_category->id;
				} else {
					$errors = array('venues.error_venue_category_not_found');
				}
			}
		}

		$this->breadcrumb[] = html::anchor('/venues/' . url::title($venue_category->id, $venue_category->name), $venue_category->name);
		if ($venue->id) {
			$this->breadcrumb[] = html::anchor('/venue/' . url::title($venue->id, $venue->name), $venue->name);
		}

		// show form
		if (empty($errors)) {
			$form = array();

			// tags
			if ($venue_category->tag_group_id) {
				$form['tags'] = $form_values['tags'] = array();
				foreach ($venue_category->tag_group->tags as $tag) {
					$form['tags'][$tag->id] = $tag->name;
					if ($venue->has($tag)) $form_values['tags'][$tag->id] = $tag->name;
				}
			}

			$venue_categories = ORM::factory('venue_category')->find_all()->select_list('id', 'name');
			$form['venue_category_id'] = $venue_categories;

			widget::add('main', View::factory('venues/venue_edit', array('form' => $form, 'values' => $form_values, 'errors' => $form_errors)));

			// city autocomplete
			$this->_autocomplete_city();
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}

	/***** /VIEWS *****/

}
