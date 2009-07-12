<?php
require_once(Kohana::find_file('lib', 'nbbc/nbbc'));

/**
 * BBCode library.
 *
 * Uses NBBC Copyright (C) 2008-9, the Phantom Inker. All rights reserved.
 * See official site for more info: http://nbbc.sourceforge.net/
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @copyright  (c) 2008-9, the Phantom Inker
 * @copyright  (c) 2004-2008 AddedBytes.com
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class BB_Core extends BBCode {

	/**
	 * The BBCode formatted text
	 *
	 * @var  string
	 */
	protected $text = null;


	/**
	 * Create new BBCode object and initialize our own settings
	 *
	 */
	public function __construct($text = null) {
		parent::BBCode();

		$this->text = $text;

		// Automagically print hrefs
		$this->SetDetectURLs(true);

		// We have our own smileys
		$this->SetEnableSmileys(false);

		// We handle newlines with Kohana
		$this->SetIgnoreNewlines(true);
		$this->SetPreTrim('a');
		$this->SetPostTrim('a');

		// User our own quote
		$this->AddRule('quote', array(
			'mode'     => BBCODE_MODE_CALLBACK,
			'method'   => array($this, 'bbcode_quote'),
			'class'    => 'block',
			'allow_in' => array('listitem', 'block', 'columns'),
			'content'  => BBCODE_REQUIRED,
		));
	}


	/**
	 * Handle forum quotations
	 *
	 * @param   BBCode  $bbcode
	 * @param   string  $action
	 * @param   string  $name
	 * @param   string  $default
	 * @param   array   $params
	 * @param   string  $content
	 * @return  string
	 */
	public function bbcode_quote($bbcode, $action, $name, $default, $params, $content) {

		// Pass all to 2nd phase
		if ($action == BBCODE_CHECK) {
			return true;
		}

		// Parse parameters
		foreach ($params['_params'] as $param) {
			switch ($param['key']) {

				// Parent post id
				case 'post':
					$post_id = (int)$param['value'];
					$post = ORM::factory('forum_post', $post_id);
					break;

				// Parent post author
				case 'author':
					$author_name = $param['value'];
					$author = ORM::factory('user')->find_user($author_name);
					break;

			}
		}

		// Add parent post
		if (isset($post) && $post->id) {
			$quote = '<blockquote cite="' . url::model($post->forum_topic) . '/' . $post->id . '#post-' . $post->id . '"><p>';

			// Override author
			$author = $post->author;
		} else {
			$quote = '<blockquote><p>';
		}

		$quote .= trim($content);

		// Post author
		if (isset($author) && $author->id) {
			$quote .= '</p><p class="author">' . __('-- :author', array(':author' => html::user($author)));
		} else if (isset($author_name)) {
			$quote .= '</p><p class="author">' . __('-- :author', array(':author' => html::specialchars($author_name)));
		}

		$quote .= '</p></blockquote>';
		return $quote;
	}


	/**
	 * Creates and returns new BBCode object
	 *
	 * @param   string  $text
	 * @return  BB
	 */
	public static function factory($text = null) {
		return new BB($text);
	}


	/**
	 * Return BBCode parsed to HTML
	 *
	 * @return  string
	 */
	public function render() {
		if (is_null($this->text)) {
			return '';
		}

		// Convert old system tags to BBCode
		$this->text = str_replace(array('[link', '[/link]', '[q]', '[/q]'), array('[url', '[/url]', '[quote]', '[/quote]'), $this->text);

		return text::auto_p($this->Parse($this->text));
	}

}
