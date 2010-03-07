<?php
/**
 * Image model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Image_Model extends Modeler_ORM {

	/**
	 * Deleted image 'd'
	 */
	const DELETED = 'd';

	/**
	 * Hidden image 'h'
	 */
	const HIDDEN = 'h';

	/**
	 * Not accepted (yet) 'n'
	 */
	const NOT_ACCEPTED = 'n';

	/**
	 * Visible image 'v'
	 */
	const VISIBLE = 'v';

	// ORM
	protected $belongs_to = array('author' => 'user');
	protected $has_one    = array('exif');
	protected $has_many   = array('image_comments');

	// Validation
	protected $_rules = array(
		'status'      => array('length[1]', 'chars[ndhv]'),
		'description' => array('length[0, 250]'),
	);


	/**
	 * Create view and thumb images with magic
	 *
	 * @param   mixed   $config  config key or array
	 * @param   string  $upload  filename
	 * @param   int     $author_id
	 * @param   string  $description
	 * @return  Image_Model
	 */
	public static function factory($config, $upload, $author_id = null, $description = null) {

		// does the image actually exists
		if (!file_exists($upload['tmp_name'])) {
			return null;
		}

		// load from config array or config file
		$config = is_array($config) ? $config : Kohana::config($config);
		$sizes = $config['sizes'];
		$format = $config['format'];

		// get image info
		$image_info = getimagesize($upload['tmp_name']);
		$extension = (empty($format) || is_bool($format)) ? Image::$allowed_types[$image_info[2]] : $format;

		// create Image model
		$image_model = new Image_Model();
		$image_model->format = $extension;
		$image_model->original_size = $upload['size'];
		$image_model->original_width = $image_info[0];
		$image_model->original_height = $image_info[1];
		if ($author_id) $image_model->author_id = (int)$author_id;
		if ($description) $image_model->description = trim($description);
		$image_model->save();

		// use the image model id as our base filename
		$filename = $image_model->id;
		$path = DOCROOT . 'images/' . url::id2path($filename) . '/';
		$original = upload::save($upload, $filename . '.' . $extension, $path);

		// create exif model
		$exif_model = Exif_Model::factory($original, $image_model->id);

		// create image sizes
		if (!empty($sizes)) {
			foreach ($sizes as $type => $size) {

				// create new image if needed
				if (!isset($image)) $image = new Image($original);

				// resize image is larger than current size
				if ($image->width > $size['width'] || $image->height > $size['height']) {
					$image->resize($size['width'], $size['height']);
				}

				// image filename is based on the type
				$size_filename = $path . $filename . '_' . ($type == 'normal' ? 'n' : 't') . '.' . $extension;

				$image->save($size_filename);
				$image_info = getimagesize($size_filename);
				switch ($type) {
					case 'normal':
						$image_model->width = $image_info[0];
						$image_model->height = $image_info[1];
						break;
					case 'thumb':
						$image_model->thumb_width = $image_info[0];
						$image_model->thumb_height = $image_info[1];
						break;
				}
			}
		}

		// save image size changes
		$image_model->save();
		return $image_model;
	}


	/**
	 * Get image comments
	 *
	 * @param  int  $page_num
	 * @param  int  $page_size
	 */
	public function find_comments($page_num = 1, $page_size = 25) {

		// Not found from cache, load from DB
		$page_offset = ($page_num - 1) * $page_size;
		$comments = $this->image_comments->find_all($page_size, $page_offset);

		return $comments;
	}


	/**
	 * Build image URL
	 *
	 * @param   string  $size  normal, thumb, original etc
	 * @return  string
	 */
	public function url($size = 'normal') {
		$url = '';

		// the image model must be loaded
		if ($this->loaded()) {

			$path = url::id2path($this->id);

			// if size is found from sizes array, add postfix, otherwise use original id filename
			$postfix = in_array($size, array('normal', 'thumb')) ? '_' . substr($size, 0, 1) : '';
			$filename = $this->id . $postfix . '.' . $this->format;
			$url = 'http://' . Kohana::config('site.image_server') . '/' . $path . '/' . $filename;
		}

		return $url;
	}
}
