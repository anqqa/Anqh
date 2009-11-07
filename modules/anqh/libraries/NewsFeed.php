<?php
/**
 * NewsFeed library
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class NewsFeed_Core {

	/**
	 * Total items loaded
	 *
	 * @var  integer
	 */
	public $item_count;

	/**
	 * Feed items
	 *
	 * @var  ORM_Iterator
	 */
	public $items;

	/**
	 * Maximum number of items to fetch
	 *
	 * @var  integer
	 */
	public $max_items;

	/**
	 * NewsFeed viewing user
	 *
	 * @var  User_Model
	 */
	public $user;


	/**
	 * Create new NewsFeed
	 *
	 * @param  User_Model  $user
	 */
	public function __construct(User_Model $user = null) {
		$this->user = $user;

		// Set defaults
		$this->max_items = 20;
	}


	/**
	 * Get news feed as array
	 *
	 * @return  array
	 */
	public function as_array() {
		$this->find_items();
		$feed = array();

		// Print items
		foreach ($this->items as $item) {
			$helper = 'newsfeeditem_' . $item->class;
			if ($text = call_user_func(array($helper, 'get'), $item)) {
				$feed[] = array(
					'user'  => ORM::factory('user')->find_user($item->user_id),
					'stamp' => $item->stamp,
					'text'  => $text
				);
			}
		}

		return $feed;
	}


	/**
	 * Load newsfeed items
	 *
	 * @return  boolean
	 */
	public function find_items() {
		if (empty($this->items)) {
			$this->items = ORM::factory('newsfeeditem')->find_all($this->max_items);
		}
	}

}
