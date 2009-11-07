<?php
/**
 * NewsFeed item helper for Forum events
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class newsfeeditem_forum extends newsfeeditem {

	/**
	 * Reply to a topic
	 *
	 * Data: topic_id, post_id
	 */
	const TYPE_REPLY = 'reply';

	/**
	 * Start a new topic
	 *
	 * Data: topic_id
	 */
	const TYPE_TOPIC = 'topic';


	/**
	 * Get newsfeed item as HTML
	 *
	 * @param   Newsfeed_Model  $item
	 * @return  string
	 */
	public static function get(NewsFeedItem_Model $item) {
		$text = '';

		switch ($item->type) {

			case self::TYPE_REPLY:
				$topic = new Forum_Topic_Model($item->data['topic_id']);
				if ($topic->id) {
					$text = __('replied to topic :topic', array(':topic' => html::anchor(url::model($topic), text::title($topic->name), array('title' => $topic->name))));
				}
				break;

			case self::TYPE_TOPIC:
				$topic = new Forum_Topic_Model($item->data['topic_id']);
				if ($topic->id) {
					$text = __('started a new topic :topic', array(':topic' => html::anchor(url::model($topic), text::title($topic->name), array('title' => $topic->name))));
				}
				break;

		}

		return $text;
	}


	/**
	 * Reply to a topic
	 *
	 * @param  User_Model        $user
	 * @param  Forum_Post_Model  $post
	 */
	public static function reply(User_Model $user = null, Forum_Post_Model $post = null) {
		if ($user && $post) {
			newsfeeditem::add($user->id, 'forum', self::TYPE_REPLY, array('topic_id' => (int)$post->forum_topic_id, 'post_id' => (int)$post->id));
		}
	}


	/**
	 * Start a new topic
	 *
	 * @param  User_Model         $user
	 * @param  Forum_Topic_Model  $topic
	 */
	public static function topic(User_Model $user = null, Forum_Topic_Model $topic = null) {
		if ($user && $topic) {
			newsfeeditem::add($user->id, 'forum', self::TYPE_TOPIC, array('topic_id' => (int)$topic->id));
		}
	}

}
