<?php
/**
 * NewsFeed item helper for User events
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class newsfeeditem_user extends newsfeeditem {

	/**
	 * Add a user to friends
	 *
	 * Data: friend_id
	 */
	const TYPE_FRIEND = 'friend';

	/**
	 * Login event
	 */
	const TYPE_LOGIN = 'login';


	/**
	 * Get newsfeed item as HTML
	 *
	 * @param   Newsfeed_Model  $item
	 * @return  string
	 */
	public static function get(NewsFeedItem_Model $item) {
		$text = '';
		switch ($item->type) {

			case self::TYPE_FRIEND:
				$friend = ORM::factory('user')->find_user($item->data['friend_id']);
				$text = __('added :friend as a friend', array(':friend' => html::user($friend)));
				break;

			case self::TYPE_LOGIN:
				$text = __('logged in');
				break;

		}

		return $text;
	}


	/**
	 * Add a user to friends
	 *
	 * @param  User_Model  $user
	 * @param  User_Model  $friend
	 */
	public static function friend(User_Model $user = null, User_Model $friend = null) {
		if ($user && $friend) {
			newsfeeditem::add($user->id, 'user', self::TYPE_FRIEND, array('friend_id' => $friend->id));
		}
	}


	/**
	 * Add new login event
	 *
	 * @param  User_Model  $user
	 */
	public static function login(User_Model $user = null) {
		if ($user) {
			newsfeeditem::add($user->id, 'user', self::TYPE_LOGIN);
		}
	}

}
