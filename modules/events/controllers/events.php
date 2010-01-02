<?php
/**
 * Events calendar controller
 *
 * @package    Events
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Events_Controller extends Website_Controller {

	public $date, $type;


	/***** MAGIC *****/

	function __construct() {
		parent::__construct();

		$this->date = new DateTime;
		$this->breadcrumb[] = html::anchor('events', __('Events'));

		$this->tabs = array(
			'upcoming' => array('link' => 'events/upcoming', 'text' => __('Upcoming events')),
			'past'     => array('link' => 'events/past',     'text' => __('Past events')),
			'browse'   => array('link' => 'events/calendar', 'text' => __('Browse calendar')),
		);
	}


	public function __call($method, $data) {
		// do we have a year?
		if (is_numeric($method) && valid::year($method)) {

			// get rest of the data
			if (is_array($data)) {
				if (count($data)) {

					// are we browsing as calendar?
					switch ($data[0]) {

						// week view
						case 'week':
							$this->week($method, $data[1]);
							break;

						// month or day view
						case 1:
						case 2:
						case 3:
						case 4:
						case 5:
						case 6:
						case 7:
						case 8:
						case 9:
						case 10:
						case 11:
						case 12:
							// is day given?
							if (count($data) > 1 && valid::date($method, $data[0], $data[1])) {

								// yes, show only 1 day
								$this->day($method, $data[0], $data[1]);

							} else {

								// no, show whole month
								$this->month($method, $data[0]);

							}
							break;

						// month view
						case 'month':
							$this->month($method, $data[1]);
							break;

						// no, show upcoming
						default:
							$this->index();
							break;

					}

				}
			}
		}
	}


	/***** /MAGIC *****/


	/***** INTERNAL *****/

	/**
	 * Build calendar view
	 */
	private function _build_calendar() {

		// Actions
		if ($this->visitor->logged_in(array('event', 'event moderator', 'admin'))) {
			$this->page_actions[] = array('link' => 'event/add', 'text' => __('Add event'), 'class' => 'event-add');
		}

		// Build time range
		$period = date::datetime2range($this->date, $this->type);
		$start_time = strtotime(reset($period));
		$end_time = strtotime('+1 day', strtotime(end($period)));

		// Fetch events
		if ($this->country) {
			$country = ORM::factory('country', $this->country);
			$events = ORM::factory('event')
				->where(array(
					array('start_time', '>=', date::unix2sql($start_time)),
					array('start_time', '<=', date::unix2sql($end_time)),
					array('country_id', '=', $country->id)
				))
				->order_by(array('event' => 'ASC', 'city_name' => 'ASC'))
				->find_all();
		} else {
			$events = ORM::factory('event')
				->where(array(
					array('start_time', '>=', date::unix2sql($start_time)),
					array('start_time', '<=', date::unix2sql($end_time))
				))
				->order_by(array('event' => 'ASC', 'city_name' => 'ASC'))
				->find_all();
		}

		if ($events->count()) {
			$this->page_subtitle = __2(':events event', ':events events', $events->count(), array(':events' => '<var>' . $events->count() . '</var>'));
			widget::add('main', new View('generic/filters', array('filters' => $this->_build_filters($events))));
			widget::add('main', new View('events/events', array('events' => $this->_build_events_list($events))));
		} else {
//			$this->_error(false, Kohana::lang('events.error_events_not_found'));
		}

		$this->_side_views();
	}


		/**
	 * Build date/city arrays from events
	 *
	 * @param   ORM_Iterator  $events
	 * @return  array
	 */
	public function _build_events_list(ORM_Iterator $events) {
		$ordered = array();
		if ($events->count()) {

			// Build initial array
			foreach ($events as $event) {

				// Build date
				$date = date('Y-m-d', strtotime($event->start_time));
				if (!isset($ordered[$date])) {
					$ordered[$date] = array();
				}

				// Build city
				$city = utf8::ucfirst(utf8::strtolower($event->city_id ? $event->city->city : $event->city_name));
				if (!isset($ordered[$date][$city])) {
					$ordered[$date][$city] = array();
				}

				$ordered[$date][$city][] = $event;
			}

			// Sort by city
			$dates = array_keys($ordered);
			foreach ($dates as $day) {
				ksort($ordered[$day]);

				// Drop empty cities to last
				if (isset($ordered[$day][''])) {
					$ordered[$day][__('Elsewhere')] = $ordered[$day][''];
					unset($ordered[$day]['']);
				}
			}

		}

		return $ordered;
	}


	/**
	 * Build filter items
	 *
	 * @param   ORM_Iterator  $events
	 * @return  array
	 */
	public function _build_filters(ORM_Iterator $events) {
		$filters = array();
		if ($events->count())	{

			$cities = array();

			// Build filter list
			foreach ($events as $event) {

				// Build city
				$city = ($event->city_id) ? $event->city->city : $event->city_name;
				$filter = url::title($city);
				if (!isset($cities[$filter])) {
					$cities[$filter] = utf8::ucfirst(utf8::strtolower($city));
				}

			}

			// Drop empty to last
			ksort($cities);
			if (isset($cities[''])) {
				$cities[url::title(__('Elsewhere'))] = utf8::ucfirst(utf8::strtolower(__('Elsewhere')));
				unset($cities['']);
			}

			// Build city filter
			$filters['city'] = array(
				'name'    => __('City'),
				'filters' => $cities,
			);

		}

		return $filters;
	}


	/**
	 * Side views
	 */
	public function _side_views() {

		// calendar
		$calendar = Calendar::factory($this->date->format('n'), $this->date->format('Y'));
		widget::add('side', $calendar->render());

		// events
		$new_events = ORM::factory('event')->order_by('id', 'DESC')->find_all(15);
		$updated_events = ORM::factory('event')->where('modifies', '>', 0)->order_by('modified', 'DESC')->find_all(15);

		$tabs = array();
		$tabs[] = array('href' => '#events-new',     'title' => __('New events'),     'tab' => new View('events/events_list', array('id' => 'events-new',     'title' => __('New events'),     'events' => $new_events)));
		$tabs[] = array('href' => '#events-updated', 'title' => __('Updated events'), 'tab' => new View('events/events_list', array('id' => 'events-updated', 'title' => __('Updated events'), 'events' => $updated_events)));
		widget::add('side', new View('generic/tabs', array('id' => 'events-tab', 'tabs' => $tabs)));
		//widget::add('foot', html::script_source('$(function() { $("#events-tab > ul").tabs({ fx: { height: "toggle", opacity: "toggle", duration: "fast" } }); });'));
	}

	/***** /INTERNAL *****/


	/***** VIEWS *****/

	/**
	 * Default page, upcoming events
	 */
	public function index() {
		$this->upcoming();
	}


	/**
	 * Browse events as calendar
	 */
	public function calendar() {
		$year = date('Y');
		$week = date('W');

		// check for new years fix
		if ($week == 1) {
			$date = new DateTime;
			$date->setISODate($year, $week, 6);
			if ($date->format('Y') < $year) $year++;
		}

		$this->week($year, $week);
	}


	/**
	 * Event view
	 *
	 * @param  mixed   $event_id or add
	 * @param  string  $action
	 */
	public function event($event_id, $action = false) {

		// add new event
		if ($event_id == 'add') {
			$this->_event_edit();
			return;

		} else if ($action){
			switch ($action) {

				// delete event
				case 'delete':
					$this->_event_delete($event_id);
					return;

				// edit event
				case 'edit':
					$this->_event_edit($event_id);
					return;

				// add to favorites
				case 'favorite':
					$this->_favorite_add($event_id);
					return;

				// remove from favorites
				case 'unfavorite':
					$this->_favorite_delete($event_id);
					return;
			}
		}

		$event = new Event_Model((int)$event_id);
		$errors = !$event->id ? array('events.error_event_not_found') : array();

		if (empty($errors)) {
			$this->breadcrumb[] = html::anchor(url::model($event), $event->name);

			// Actions
			if ($this->visitor->logged_in()) {
				if ($event->is_favorite($this->user)) {
					$this->page_actions[] = array('link' => url::model($event) . '/unfavorite/?token=' . csrf::token(), 'text' => __('Remove favorite'), 'class' => 'favorite-delete');
				} else {
					$this->page_actions[] = array('link' => url::model($event) . '/favorite/?token=' . csrf::token(),   'text' => __('Add favorite'),    'class' => 'favorite-add');
				}
				if ($event->is_author() || $this->visitor->logged_in(array('admin', 'event moderator'))) {
					$this->page_actions[] = array('link' => url::model($event) . '/edit', 'text' => __('Edit event'), 'class' => 'event-edit');
				}
			}

			list($year, $month, $day) = explode('-', date('Y-m-d', strtotime($event->start_time)));
			$this->date->setDate($year, $month, $day);
			$this->page_title = text::title($event->name);
			widget::add('main', View::factory('events/event', array('event' => $event)));
		} else {
//			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		$this->_side_views();
	}


	/**
	 * Delete event
	 *
	 * @param  int|string  $event_id
	 */
	public function _event_delete($event_id) {
		$this->history = false;

		$event = new Event_Model((int)$event_id);

		// For authenticated users only
		if (!$this->user || !csrf::valid() || (!$event->is_author() && !$this->visitor->logged_in(array('admin', 'event moderator')))) {
			url::back('/events');
		}

		if ($event->id) {
			$event->delete();
			url::redirect('/events');
		}

		url::back('/events');
	}


	/**
	 * Edit event
	 *
	 * @param  int|string  $event_id
	 */
	public function _event_edit($event_id = false) {
		$this->history = false;

		$event = new Event_Model((int)$event_id);

		// For authenticated users only
		if (!$this->user || (!$event->is_author() && !$this->visitor->logged_in(array('admin', 'event moderator')))) {
			url::back('/events');
		}

		$errors = $form_errors = array();
		$form_messages = '';
		$form_values = $event->as_array();
		$form_values['start_date'] = '';
		$form_values['start_hour'] = '';
		$form_values['end_hour'] = '';


		/***** CHECK POST *****/

		if (request::method() == 'post') {
			$post = array_merge($this->input->post(), $_FILES);
			$extra = array(
				'start_time' => date::unix2sql(strtotime($post['start_date'] . ' ' . date::time_24h($post['start_hour']))),
			);
			if (!empty($post['end_hour'])) {
				$end_time = strtotime($post['start_date']);
				// end hour is earlier than start hour = event ends the next day
				if ($post['end_hour'] < $post['start_hour']) $end_time = strtotime('+1 day', $end_time);
				$extra['end_time'] = date('Y-m-d', $end_time) . ' ' . date::time_24h($post['end_hour']) . ':00';
			}

			// update
			$editing = (bool)$event->id;
			if ($editing) {
				$extra['modified'] = date::unix2sql(time());
				$extra['modifies'] = (int)$event->modifies + 1;
			} else {
				$extra['author_id'] = $this->user->id;
			}
			$city = ORM::factory('city', $post['city_id']);
			if ($city) {
				$extra['country_id'] = $city->country_id;
			}

			if (csrf::valid() && $event->validate($post, true, $extra)) {

				// Update tags
				$event->remove(ORM::factory('tag'));
				if (!empty($post['tags'])) {
					foreach ($post['tags'] as $tag_id => $tag) {
						$event->add(ORM::factory('tag', $tag_id));
					}
					$event->save();
				}

				// Handle flyer uploads
				foreach (array('flyer_front_image_id' => $post->flyer_front, 'flyer_back_image_id' => $post->flyer_back) as $image_id => $flyer) {
					if (isset($flyer) && empty($flyer['error'])) {
						$image = Image_Model::factory('events.flyer', $flyer, $this->user->id);
						if ($image->id) {
							$event->add($image);
							$event->{$image_id} = $image->id;
							$event->save();
						}
					}
				}

				if (!$editing) {

					// News feed event
					newsfeeditem_events::event($this->user, $event);

				}
				/*// handle flyer uploads
				if (isset($post->flyer_front) && empty($post->flyer_front['error'])) {
					$flyer = Image_Model::factory($post->flyer_front, false, Kohana::config('events.flyer_normal'), Kohana::config('events.flyer_thumb'), $this->user->id);
					if ($flyer->id) {
						$event->add($flyer);
						$event->flyer_front_image_id = $flyer->id;
						$event->save();
					}
				}
				if (isset($post->flyer_back) && empty($post->flyer_back['error'])) {
					$flyer = Image_Model::factory($post->flyer_back, false, Kohana::config('events.flyer_normal'), Kohana::config('events.flyer_thumb'), $this->user->id);
					if ($flyer->id) {
						$event->add($flyer);
						$event->flyer_back_image_id = $flyer->id;
						$event->save();
					}
				}*/

				url::redirect(url::model($event));
			} else {
				$form_errors = $post->errors();
				$form_messages = $post->message();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		/***** /CHECK POST *****/


		/***** SHOW FORM *****/

		if ($event->id) {
			$this->page_actions[] = array('link' => url::model($event) . '/delete/?token=' . csrf::token(), 'text' => __('Delete event'), 'class' => 'event-delete');
			$this->page_title = text::title($event->name);
			$this->page_subtitle = __('Edit event');
			list($form_values['start_date'], $form_values['start_hour']) = explode(' ', date('Y-m-d H', strtotime($event->start_time)));
			if (!empty($event->end_time)) {
				list($temp, $form_values['end_hour']) = explode(' ', date('Y-m-d H', strtotime($event->end_time)));
			}
		} else {
			$this->page_title = __('New event');
		}

		$form = $event->get_form();

		// Tags
		if ($tag_group = Kohana::config('events.tag_group')) {
			$form['tags'] = $form_values['tags'] = array();
			$tags = ORM::factory('tag_group', $tag_group);
			foreach ($tags->tags as $tag) {
				$form['tags'][$tag->id] = $tag->name;
				if ($event->has($tag)) $form_values['tags'][$tag->id] = $tag->name;
			}
		}

		// City autocomplete
		$this->_autocomplete_city('city_name');

		// Venue autocomplete
		$venues = ORM::factory('venue')->where('event_host', '=', 1)->find_all();
		$hosts = array();
		foreach ($venues as $venue) {
			$hosts[] = "{ id: '" . $venue->id . "', text: '" . html::chars($venue->name) . "' }";
		}
		widget::add('foot', html::script_source('var venues = [' . implode(', ', $hosts) . "];
$('input#venue_name').autocomplete(venues, {
	formatItem: function(item) {
		return item.text;
	}
}).result(function(event, item) {
	$('input#venue_id').val(item.id);
});"));

		// Date pickers
		widget::add('foot', html::script_source("$('input#start_date').datepicker({ dateFormat: 'd.m.yy', firstDay: 1, changeFirstDay: false, showOtherMonths: true, showWeeks: true, showStatus: true, showOn: 'both' });"));

		if (empty($errors)) {
			widget::add('main', View::factory('events/event_edit', array('form' => $form, 'values' => $form_values, 'errors' => $form_errors, 'messages' => $form_messages)));
		} else {
//			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		/***** /SHOW FORM *****/


		$this->_side_views();
	}


	/**
	 * Day view
	 *
	 * @param  int  $year
	 * @param  int  $month
	 * @param  int  $day
	 */
	public function day($year, $month, $day) {
		$this->tab_id = 'browse';

		if (valid::date($year . '-' . $month . '-' . $day)) {
			$this->type = 'day';
			$this->date->setDate($year, $month, $day);

			$this->page_title = text::title($this->date->format('j.n.Y'));
			$this->_build_calendar();
		}
	}


	/**
	 * Month view
	 *
	 * @param  int  $year
	 * @param  int  $month
	 */
	public function month($year, $month) {
		$this->tab_id = 'browse';

		if (valid::year($year) && valid::month($month)) {
			$this->type = 'month';
			$this->date->setDate($year, $month, 1);

			$this->page_title = text::title($this->date->format('F Y'));
			$this->_build_calendar();
		}
	}


	/**
	 * Default page, upcoming events
	 */
	public function past() {
		$this->breadcrumb[] = html::anchor('events/past', __('Past events'));
		$this->page_title = text::title(__('Past events'));
		$this->tab_id = 'past';

		// actions
		if ($this->visitor->logged_in(array('event', 'event moderator',  'admin'))) {
			$this->page_actions[] = array('link' => 'event/add', 'text' => __('Add event'), 'class' => 'event-add');
		}

		// fetch events
		if ($this->country) {
			$country = ORM::factory('country', $this->country);
			$filter = array('events.country_id' => $country->id);
		} else {
			$filter = false;
		}
		$events = ORM::factory('event')->find_past(25, $filter);

		if ($events->count()) {
			$this->page_subtitle = __2(':events event', ':events events', $events->count(), array(':events' => '<var>' . $events->count() . '</var>'));
			widget::add('main', new View('generic/filters', array('filters' => $this->_build_filters($events))));
			widget::add('main', new View('events/events', array('events' => $this->_build_events_list($events))));
		}

		$this->_side_views();
	}


	/**
	 * Upcoming events
	 */
	public function upcoming() {
		$this->breadcrumb[] = html::anchor('events/upcoming', __('Upcoming events'));
		$this->page_title = text::title(__('Upcoming events'));
		$this->tab_id = 'upcoming';

		// actions
		if ($this->visitor->logged_in(array('event', 'event admin', 'admin'))) {
			$this->page_actions[] = array('link' => 'event/add', 'text' => __('Add event'), 'class' => 'event-add');
		}

		// fetch events
		if ($this->country) {
			$country = ORM::factory('country', $this->country);
			$filter = array('events.country_id' => $country->id);
		} else {
			$filter = false;
		}
		$events = ORM::factory('event')->find_upcoming(25, $filter);

		if ($events->count()) {
			$this->page_subtitle = __2(':events event', ':events events', $events->count(), array(':events' => '<var>' . $events->count() . '</var>'));
			widget::add('main', new View('generic/filters', array('filters' => $this->_build_filters($events))));
			widget::add('main', new View('events/events', array('events' => $this->_build_events_list($events))));
		}

		$this->_side_views();
	}


	/**
	 * Week view
	 *
	 * @param  int  $year
	 * @param  int  $week
	 */
	public function week($year, $week) {
		$this->tab_id = 'browse';

		if (valid::year($year) && valid::week($week)) {
			$this->type = 'week';
			$this->date->setISODate($year, $week);

			// check for new years fix
			if ($week == 1) {
				$saturday = new DateTime;
				$saturday->setISODate($year, $week, 6);
				$this->date = $saturday;
			}

			$this->page_title = text::title(__('Week') . ' ' . $this->date->format('W/Y'));
			$this->breadcrumb[] = html::anchor(Router::$routed_uri, html::specialchars($this->page_title));
			$this->_build_calendar();
		}
	}

	/***** /VIEWS *****/


	/***** FAVORITES *****/

	/**
	 * Add to favorites
	 *
	 * @param  int|string  $event_id
	 */
	public function _favorite_add($event_id) {
		$this->history = false;

		// For authenticated only
		if ($this->user && csrf::valid()) {

			// Require valid event
			$this->event = new Event_Model((int)$event_id);
			if ($this->event->id) {
				$this->event->add_favorite($this->user);

				// News feed event
				newsfeeditem_events::favorite($this->user, $this->event);

			}
		}

		url::back('/members');
	}


	/**
	 * Remove from favorites
	 *
	 * @param  int|string  $event_id
	 */
	public function _favorite_delete($event_id) {
		$this->history = false;

		// for authenticated only
		if ($this->user && csrf::valid()) {

			// require valid user
			$this->event = new Event_Model((int)$event_id);
			if ($this->event->id) {
				$this->event->delete_favorite($this->user);
			}
		}

		url::back('/members');
	}

	/***** /FAVORITES *****/

}
