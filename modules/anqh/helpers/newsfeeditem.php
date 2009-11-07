<?php
/**
 * NewsFeed item helper
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
abstract class newsfeeditem_Core {

	/**
	 * Add news feed item
	 *
	 * @param   integer  $user_id
	 * @param   string   $class    e.g. 'user'
	 * @param   string   $type     e.g. 'login'
	 * @param   array    $data     Data to be user with item
	 * @return  boolean
	 */
	protected static function add($user_id, $class, $type, array $data = null) {
		$item = new NewsFeedItem_Model();
		$item->user_id = $user_id;
		$item->stamp = time();
		$item->class = $class;
		$item->type = $type;
		$item->data = $data;
		$item->save();

		return $item->saved;
	}


	/**
	 * Get newsfeed item as HTML
	 *
	 * @param   Newsfeed_Model  $item
	 * @return  string
	 */
	public abstract static function get(NewsFeedItem_Model $item);

}
