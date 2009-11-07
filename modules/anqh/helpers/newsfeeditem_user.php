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

		// Get event author
		$user = ORM::factory('user')->find_user($item->user_id);
		$user_html = html::avatar($user->avatar, $user->username) . html::user($user);
		$ago = html::time(date::timespan_short($item->stamp), $item->stamp);

		switch ($item->type) {

			// Login event
			case self::TYPE_LOGIN:
				return __(':user logged in :ago ago', array(
					':user' => $user_html,
					':ago' => $ago,
				));
				break;

		}

		return null;
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
