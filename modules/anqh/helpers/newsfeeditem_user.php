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
		$text = '';
		switch ($item->type) {

			// Login event
			case self::TYPE_LOGIN:
				$text = __('logged in');
				break;

		}

		return $text;
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
