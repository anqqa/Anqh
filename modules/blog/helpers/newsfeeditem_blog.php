<?php
/**
 * NewsFeed item helper for Blog events
 *
 * @package    Blog
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class newsfeeditem_blog extends newsfeeditem {

	/**
	 * Comment an entry
	 *
	 * Data: entry_id
	 */
	const TYPE_COMMENT = 'comment';

	/**
	 * Write a new entry
	 *
	 * Data: entry_id
	 */
	const TYPE_ENTRY = 'entry';


	/**
	 * Get newsfeed item as HTML
	 *
	 * @param   Newsfeed_Model  $item
	 * @return  string
	 */
	public static function get(NewsFeedItem_Model $item) {
		$text = '';

		switch ($item->type) {

			case self::TYPE_COMMENT:
				$entry = new Blog_Entry_Model($item->date['entry_id']);
				if ($entry->id) {
					$text = __('commented to blog :blog', array(':blog' => html::anchor(url::model($entry), text::title($entry->name), array('title' => $blog->name))));
				}
				break;

			case self::TYPE_ENTRY:
				$entry = new Blog_Entry_Model($item->date['entry_id']);
				if ($entry->id) {
					$text = __('wrote a new blog entry :blog', array(':blog' => html::anchor(url::model($entry), text::title($entry->name), array('title' => $blog->name))));
				}
				break;

		}

		return $text;
	}


	/**
	 * Write a new entry
	 *
	 * @param  User_Model        $user
	 * @param  Blog_Entry_Model  $entry
	 */
	public static function entry(User_Model $user = null, Blog_Entry_Model $entry = null) {
		if ($user && $entry) {
			newsfeeditem::add($user->id, 'blog', self::TYPE_ENTRY, array('entry_id' => (int)$entry->id));
		}
	}


	/**
	 * Comment an entry
	 *
	 * @param  User_Model        $user
	 * @param  Blog_Entry_Model  $entry
	 */
	public static function comment(User_Model $user = null, Blog_Entry_Model $entry = null) {
		if ($user && $entry) {
			newsfeeditem::add($user->id, 'blog', self::TYPE_COMMENT, array('entry_id' => (int)$entry->id));
		}
	}

}
