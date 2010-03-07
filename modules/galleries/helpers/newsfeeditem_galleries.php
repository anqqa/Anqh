<?php
/**
 * NewsFeed item helper for Galleries events
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class newsfeeditem_galleries extends newsfeeditem {

	/**
	 * Comment an entry
	 *
	 * Data: entry_id
	 */
	const TYPE_COMMENT = 'comment';


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
				$gallery = new Gallery_Model($item->data['gallery_id']);
				$image = new Image_Model($item->data['image_id']);
				if ($gallery->loaded() && $image->loaded()) {
					$text = __('commented to an image in :gallery', array(':gallery' => html::anchor(url::model($gallery) . '/' . $image->id, text::title($gallery->name), array('title' => $gallery->name))));
				}
				break;

		}

		return $text;
	}


	/**
	 * Comment an image
	 *
	 * @param  User_Model     $user
	 * @param  Gallery_Model  $gallery
	 * @param  Image_Model    $image
	 */
	public static function comment(User_Model $user = null, Gallery_Model $gallery = null, Image_Model $image = null) {
		if ($user && $gallery && $image) {
			newsfeeditem::add($user->id, 'galleries', self::TYPE_COMMENT, array('gallery_id' => (int)$gallery->id, 'image_id' => (int)$image->id));
		}
	}

}
