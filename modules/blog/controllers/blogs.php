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
		if (!$this->user || !csrf::valid()) url::redirect(empty($_SESSION['history']) ? '/members' : $_SESSION['history']);

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
	 * Single blog entry
	 *
	 * @param  mixed   $entry_id or add
	 * @param  string  $action
	 */
	public function entry($entry_id, $action = false) {

		// Add new entry
		if ($entry_id == 'add') {
			$this->_entry_edit();
			return;

		} else if ($action) {
			switch ($action) {

				// Delete entry
				case 'delete':
					$this->_entry_delete($entry_id);
					return;

				// Edit event
				case 'edit':
					$this->_entry_edit($entry_id);
					return;

			}
		}

		$entry = new Blog_Entry_Model((int)$entry_id);
		$errors = $entry->id ? array() : __('Blog entry found :entry', array(':entry' => $entry_id));

		if (empty($errors)) {
			$this->breadcrumb[] = html::anchor(url::model($entry), $entry->name);
			$this->page_title = text::title($entry->name);
			$this->page_subtitle = __('By :user :ago ago', array(
				':user' => html::user($entry->author),
				':ago'  => html::time(date::timespan_short($entry->created), $entry->created)
			));

			if ($entry->is_author() || $this->visitor->logged_in('admin')) {
				$this->page_actions[] = array('link' => url::model($entry) . '/delete/?token=' . csrf::token($this->user->id), 'text' => __('Delete entry'), 'class' => 'entry-delete');
				$this->page_actions[] = array('link' => url::model($entry) . '/edit', 'text' => __('Edit entry'), 'class' => 'entry-edit');
			}

			if (!$entry->is_author()) {
				$entry->views++;
				$entry->save();
			}

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
						'delete'     => '/blogs/comment/%d/delete/?token=' . csrf::token(),
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


	/**
	 * Delete entry
	 *
	 * @param  integer|string  $entry_id
	 */
	public function _entry_delete($entry_id) {
		$this->history = false;

		$entry = new Blog_Entry_Model((int)$entry_id);
		if ($this->user && $entry->id && csrf::valid($this->input->get('token'), $this->user->id) && ($entry->is_author() || $this->visitor->logged_in('admin'))) {
			$entry->delete();
			url::redirect('/blogs');
		}

		url::back('/blogs');
	}


	/**
	 * Edit entry
	 *
	 * @param  integer|string  $entry_id
	 */
	public function _entry_edit($entry_id = false) {
		$this->history = false;

		$entry = new Blog_Entry_Model((int)$entry_id);

		// For authenticated users only
		if (!$this->user || (!$entry->is_author() && !$this->visitor->logged_in('admin'))) {
			url::redirect(empty($_SESSION['history']) ? '/blogs' : $_SESSION['history']);
		}

		$errors = $form_errors = array();
		$form_messages = '';
		$form_values = $entry->as_array();


		/***** CHECK POST *****/

		if (request::method() == 'post') {
			$post = $this->input->post();

			// update
			$editing = (bool)$entry->id;
			if ($editing) {
				$extra['modified'] = date::unix2sql(time());
				$extra['modifies'] = (int)$entry->modifies + 1;
			} else {
				$extra['author_id'] = $this->user->id;
			}

			if ($entry->validate($post, true, $extra)) {

				// News feed event
				if (!$editing) {
					newsfeeditem_blog::entry($this->user, $entry);
				}

				url::redirect(url::model($entry));
			} else {
				$form_errors = $post->errors();
				$form_messages = $post->message();
			}
			$form_values = arr::overwrite($form_values, $post->as_array());
		}

		/***** /CHECK POST *****/


		/***** SHOW FORM *****/

		if ($entry->id) {
			$this->page_actions[] = array('link' => url::model($entry) . '/delete?token=' . csrf::token($this->user->id), 'text' => __('Delete entry'), 'class' => 'entry-delete');
			$this->page_title = text::title($entry->name);
			$this->page_subtitle = __('Edit entry');
		} else {
			$this->page_title = __('New entry');
		}

		$form = $entry->get_form();

		if (empty($errors)) {
			widget::add('head', html::script(array('js/jquery.markitup.pack', 'js/markitup.bbcode')));
			widget::add('main',
				View::factory('blog/entry_edit', array(
					'form'     => $form,
					'values'   => $form_values,
					'errors'   => $form_errors,
					'messages' => $form_messages
				))
			);
		} else {
			$this->_error(Kohana::lang('generic.error'), $errors);
		}

		/***** /SHOW FORM *****/

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

		// Actions
		if ($this->user) {
			$this->page_actions[] = array('link' => 'blog/add', 'text' => __('New blog entry'), 'class' => 'topic-add');
		}

		widget::add('main', View::factory('blog/entries', array(
			'entries' => ORM::factory('blog_entry')->find_latest(20),
		)));
	}

}
