<?php
/**
 * NewsFeed item helper for calendar events
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class newsfeeditem_events extends newsfeeditem {

	/**
	 * Add a new event
	 *
	 * Data: event_id
	 */
	const TYPE_EVENT = 'event';

	/**
	 * Add an event to favorites
	 *
	 * Data: event_id
	 */
	const TYPE_FAVORITE = 'favorite';


	/**
	 * Get newsfeed item as HTML
	 *
	 * @param   Newsfeed_Model  $item
	 * @return  string
	 */
	public static function get(NewsFeedItem_Model $item) {
		$text = '';

		switch ($item->type) {

			case self::TYPE_EVENT:
				$event = new Event_Model($item->data['event_id']);
				if ($event->id) {
					$text = __('added new event :event', array(':event' => html::anchor(url::model($event), text::title($event->name), array('title' => $event->name))));
				}
				break;

			case self::TYPE_FAVORITE:
				$event = new Event_Model($item->data['event_id']);
				if ($event->id) {
					$text = __('added event :event to favorites', array(':event' => html::anchor(url::model($event), text::title($event->name), array('title' => $event->name))));
				}
				break;

		}

		return $text;
	}


	/**
	 * Add a new event
	 *
	 * @param  User_Model   $user
	 * @param  Event_Model  $event
	 */
	public static function event(User_Model $user = null, Event_Model $event = null) {
		if ($user && $event) {
			newsfeeditem::add($user->id, 'events', self::TYPE_EVENT, array('event_id' => (int)$event->id));
		}
	}


	/**
	 * Add an event to favorites
	 *
	 * @param  User_Model   $user
	 * @param  Event_Model  $event
	 */
	public static function favorite(User_Model $user = null, Event_Model $event = null) {
		if ($user && $event) {
			newsfeeditem::add($user->id, 'events', self::TYPE_FAVORITE, array('event_id' => (int)$event->id));
		}
	}

}
