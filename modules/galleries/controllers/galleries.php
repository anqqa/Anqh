<?php
/**
 * Galleries controller
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Galleries_Controller extends Website_Controller {

	/**
	 * New gallery controller
	 */
	public function __construct() {
		parent::__construct();

		$this->breadcrumb[] = html::anchor('galleries', __('Galleries'));
		$this->page_title = __('Galleries');

		$this->tabs = array(
			'latest' => array('link' => 'galleries',        'text' => __('Latest updates')),
			'browse' => array('link' => 'galleries/browse', 'text' => __('Browse galleries')),
		);
	}


	/**
	 * Galleries front page
	 */
	public function index() {
		return $this->latest();
	}


	/**
	 * Browse galleries
	 *
	 * @param  integer  $year
	 * @param  integer  $month
	 */
	public function browse($year = false, $month = false) {
		$this->tab_id = 'browse';

		$months = Gallery_Model::find_months();

		// Default to last month
		if (!$year) {
			$year = max(array_keys($months));
			$month = max(array_keys($months[$year]));
		} else if (!$month) {
			$month = isset($months[$year]) ? min(array_keys($months[$year])) : 1;
		}

		$year = min($year, date('Y'));
		$month = min(12, max(1, $month));

		$this->page_title .= ' - ' . text::title(date('F Y', mktime(null, null, null, $month, 1, $year)));

		// Month browser
		widget::add('wide', View_Mod::factory('galleries/month_browser', array('year' => $year, 'month' => $month, 'months' => $months)));

		// Galleries
		$galleries = Gallery_Model::find_by_year($year, $month);
		if ($galleries->count()) {
			widget::add('wide', View_Mod::factory('galleries/galleries', array('galleries' => $galleries)));
		}

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

				// Delete comment
				case 'delete':
					$this->_comment_delete($comment_id);
					return;

				// Set comment as private
				case 'private':
					$this->_comment_private($comment_id);
					return;

			}
		}

		url::back('galleries');
	}

	/**
	 * Delete comment
	 *
	 * @param  integer  $comment_id
	 */
	public function _comment_delete($comment_id) {
		$this->history = false;

		$comment = new Image_Comment_Model((int)$comment_id);
		if (csrf::valid() && $comment->loaded() && $comment->has_access(Comment_Model::ACCESS_DELETE)) {
			$image = $comment->image;
			$image->comments--;
			$image->save();
			$comment->delete();

			if (request::is_ajax()) {
				return;
			} else {
				$gallery = Gallery_Model::find_by_image($image->id);
				url::redirect(url::model($gallery) . '/' . $image->id);
			}
		}

		if (!request::is_ajax()) {
			url::back('blogs');
		}
	}


	/**
	 * Set comment as private
	 *
	 * @param  integer  $comment_id
	 */
	public function _comment_private($comment_id) {
		$this->history = false;

		$comment = new Image_Comment_Model((int)$comment_id);
		if (csrf::valid() && $comment->loaded() && !$comment->private && $comment->has_access(Comment_Model::ACCESS_PRIVATE)) {
			$image = $comment->image;
			$comment->private = 1;
			$comment->save();

			if (request::is_ajax()) {
				return;
			} else {
				$gallery = Gallery_Model::find_by_image($image->id);
				url::redirect(url::model($gallery) . '/' . $image->id);
			}
		}

		if (!request::is_ajax()) {
			url::back('blogs');
		}
	}


	/**
	 * Gallery
	 *
	 * @param  mixed   $gallery_id
	 * @param  string  $action
	 */
	public function gallery($gallery_id, $action = false) {
		if ($action) {
			switch ($action) {

				// Image
				default:
					if (is_numeric($action)) {
						return $this->image((int)$action);
					}

			}
		}

		$gallery = new Gallery_Model((int)$gallery_id);

		if ($gallery->loaded()) {
			$this->tab_id = 'gallery';
			$this->tabs['browse']['link'] = 'galleries/browse/' . date('Y/n/', strtotime($gallery->event_date));
			$this->tabs['gallery'] = array('link' => url::model($gallery), 'text' => __('Gallery'));

			$this->page_title = text::title($gallery->name);
			$this->page_subtitle = html::time(date::format('DMYYYY', $gallery->event_date), $gallery->event_date, true);

			// Pictures
			widget::add('main', View_Mod::factory('galleries/gallery', array('gallery' => $gallery)));

			// Event information
			if ($gallery->event) {
				widget::add('side', View::factory('events/event_info', array('user' => $this->user, 'event' => $gallery->event)));
			}
		}
	}


	/**
	 * View one image
	 *
	 * @param  mixed    $gallery_id
	 * @param  integer  $image_id
	 */
	public function image($image_id) {
		$gallery = Gallery_Model::find_by_image($image_id);
		if ($gallery->loaded()) {
			$this->tab_id = 'gallery';
			$this->tabs['browse']['link'] = 'galleries/browse/' . date('Y/n/', strtotime($gallery->event_date));
			$this->tabs['gallery'] = array('link' => url::model($gallery), 'text' => __('Gallery'));

			$i = 0;
			$images = $gallery->find_images();

			// Find current, previous and next images
			$previous = $next = $current = null;
			while (is_null($next) && $image = $images->current()) {
				if (!is_null($current)) {

					// Current was found last round
					$next = $image;
					$i--;

				} else if ($image->id == $image_id) {

					// Current found now
					$current = $image;

				} else {

					// No current found
					$previous = $image;

				}
				$i++;
				$images->next();
			}

			$this->page_title = text::title($gallery->name);
			$this->page_subtitle = html::time(date::format('DMYYYY', $gallery->event_date), $gallery->event_date, true);

			if (!is_null($current)) {

				// Image
				$current->views++;
				$current->save();
				widget::add('wide', new View('galleries/image', array(
					'gallery'  => $gallery,
					'images'   => count($images),
					'current'  => $i,
					'image'    => $current,
					'next'     => $next,
					'previous' => $previous,
				)));


				// Image comments
				if ($this->visitor->logged_in()) {
					$comment = new Image_Comment_Model();
					$form_values = $comment->as_array();
					$form_errors = array();

					// Check post
					if (csrf::valid() && $post = $this->input->post()) {
						$comment->image_id  = $current->id;
						$comment->user_id   = $current->author->id;
						$comment->author_id = $this->user->id;
						$comment->comment   = $post['comment'];
						if (isset($post['private'])) {
							$comment->private = 1;
						}

						try {
							$comment->save();
							$current->comments++;
							$current->save();

							if (!$comment->private) {
								newsfeeditem_galleries::comment($this->user, $gallery, $current);
							}

							if (!request::is_ajax()) {
								url::redirect(url::current());
							}
						} catch (ORM_Validation_Exception $e) {
							$form_errors = $e->validation->errors();
							$form_values = arr::overwrite($form_values, $post);
						}
					}

					$comments = $current->find_comments();
					$view = View::factory('generic/comments', array(
						'delete'     => '/image/comment/%d/delete/?token=' . csrf::token(),
						'private'    => '/image/comment/%d/private/?token=' . csrf::token(),
						'comments'   => $comments,
						'errors'     => $form_errors,
						'values'     => $form_values,
						'pagination' => null,
						'user'       => $this->user,
					));
					if (request::is_ajax()) {
						echo $view;
						return;
					}
					widget::add('main', $view);

				} // image comments

				widget::add('side', View::factory('galleries/image_info', array('image' => $current)));

			} // image

		} // gallery
	}


	/**
	 * Galleries with latest updates
	 */
	public function latest() {
		$this->tab_id = 'latest';

		$galleries = ORM::factory('gallery')->find_latest(10);

		if ($galleries->count()) {
			widget::add('wide', new View_Mod('galleries/galleries', array('galleries' => $galleries)));
		}

	}

}
