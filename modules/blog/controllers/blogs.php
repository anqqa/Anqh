<?php
/**
 * Blog controller
 *
 * @package    Blog
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Blogs_Controller extends Website_Controller {

	/**
	 * New blog controller
	 */
	public function __construct() {
		parent::__construct();

		$this->breadcrumb[] = html::anchor('blogs', __('Blogs'));
		$this->page_title = __('Blogs');
	}


	/**
	 * Comment action
	 *
	 * @param  int     $comment_id
	 * @param  string  $action
	 */
	public function comment($comment_id, $action = false) {
		$this->history = false;

		if ($action) {
			switch ($action) {

				// delete comment
				case 'delete':
					$this->commentdelete($comment_id);
					return;

			}
		}

		url::redirect(empty($_SESSION['history']) ? '/blogs' : $_SESSION['history']);
	}


	/**
	 * Delete comment
	 *
	 * @param  int  $comment_id
	 */
	public function commentdelete($comment_id) {
		$this->history = false;

		// for authenticated users only
		if (!$this->user) url::redirect(empty($_SESSION['history']) ? '/members' : $_SESSION['history']);

		$comment = new Blog_Comment_Model((int)$comment_id);
		if ($comment->id) {

			// allow only to delete one's own comments
			if (in_array($this->user->id, array($comment->author_id, $comment->user_id))) {
				$entry = $comment->blog_entry;
				$entry->comments--;
				$entry->save();
				$comment->delete();

				url::redirect(url::model($entry));
				return;
			}
		}

		url::redirect(empty($_SESSION['history']) ? '/blogs' : $_SESSION['history']);
	}


	/**
	 * Default view
	 */
	public function index() {
		$this->latest();
	}


	/**
	 * Latest blog entries
	 */
	public function latest() {
		widget::add('main', View::factory('blog/entries', array(
			'entries' => ORM::factory('blog_entry')->find_latest(20),
		)));
	}


	/**
	 * Single blog entry
	 *
	 * @param  string  $entry_id
	 */
	public function view($entry_id) {
		$entry = new Blog_Entry_Model((int)$entry_id);
		$errors = $entry->id ? array() : __('Blog entry found :entry', array(':entry' => $entry_id));

		if (empty($errors)) {
			$this->breadcrumb[] = html::anchor(url::model($entry), $entry->name);
			$this->page_title = text::title($entry->name);
			$this->page_subtitle = __('By :user :ago ago', array(
				':user' => html::user($entry->author),
				':ago'  => html::time(date::timespan_short($entry->created), $entry->created)
			));

			// Blog entry
			$entry->views++;
			$entry->save();

			widget::add('main', View::factory('blog/entry', array('entry' => $entry)));

			// Blog comments
			if ($this->visitor->logged_in()) {

				$comment = new Blog_Comment_Model();
				$form_values = $comment->as_array();
				$form_errors = array();

				// check post
				if ($post = $this->input->post()) {
					$post['blog_entry_id'] = $entry->id;
					$post['author_id'] = $this->user->id;
					$post['user_id'] = $entry->author->id;
					if ($comment->validate($post, true, array())) {
						$entry->comments++;
						$entry->newcomments++;
						$entry->save();
						url::redirect(url::current());
					} else {
						$form_errors = $post->errors();
						$form_values = arr::overwrite($form_values, $post->as_array());
					}
				}

				$comments = $entry->find_comments();
				widget::add('main',
					View::factory('member/comments', array(
						'private'    => false,
						'delete'     => '/blogs/comment/%d/delete',
						'comments'   => $comments,
						'errors'     => $form_errors,
						'values'     => $form_values,
						'pagination' => null
					))
				);
			}
		}

		if (count($errors)) {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}
	}

}
