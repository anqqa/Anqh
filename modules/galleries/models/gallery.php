<?php
/**
 * Gallery model
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Gallery_Model_Core extends Modeler_ORM {

	// ORM
	protected $has_and_belongs_to_many = array('images');
	protected $belongs_to              = array('event', 'default_image' => 'image');

	// Validation
	protected $rules = array(
		'*'                => array('pre_filter' => 'trim'),
		'id'               => array('rules' => array('valid::digit')),
		'name'             => array('rules' => array('required', 'length[3,250]')),
		'event_id'         => array('rules' => array('valid::digit')),
		'event_date'       => array('rules' => array('required', 'length[4,10]', 'valid::date')),
		'links'            => array('rules' => array()),
		'default_image_id' => array('rules' => array('valid::digit')),
	);


	/**
	 * Find gallery by image id
	 *
	 * @param   integer  $image_id
	 * @return  Gallery_Model
	 */
	public static function find_by_image($image_id) {
		return ORM::factory('gallery')->inner_join('galleries_images', 'gallery_id', 'id')->where('image_id', '=', (int)$image_id)->find();
	}


	/**
	 * Find galleries by year and month
	 *
	 * @param   integer  $year
	 * @param   integer  $month
	 * @return  ORM_Iterator
	 */
	public static function find_by_year($year, $month) {
		return ORM::factory('gallery')
			->where('image_count', '>', 0)
			->where(new Database_Expression('EXTRACT(MONTH FROM event_date) = ' . (int)$month))
			->where(new Database_Expression('EXTRACT(YEAR FROM event_date) = ' . (int)$year))
			->order_by('event_date', 'DESC')
			->find_all();
	}


	/**
	 * Find images for loaded gallery
	 *
	 * @return  ORM_Iterator
	 */
	public function find_images() {
		return $this->loaded() ? $this->images->where('status', '=', Image_Model::VISIBLE)->find_all() : null;
	}


	/**
	 * Find galleries with latest updates
	 *
	 * @param   integer  $limit
	 * @return  ORM_Iterator
	 */
	public static function find_latest($limit = 10) {
		return ORM::factory('gallery')->where('image_count', '>', 0)->limit($limit)->order_by('updated', 'DESC')->find_all();
	}


	/**
	 * Find months with galleries
	 *
	 * @return  array  year => month => count
	 */
	public static function find_months() {
		$months = array();

		// Build counts
		$galleries = ORM::factory('gallery')->where('image_count', '>', 0)->find_all();
		foreach ($galleries as $gallery) {
			list($year, $month) = explode(' ', date('Y n', strtotime($gallery->event_date)));

			if (!isset($months[$year])) {
				$months[$year] = array();
			}
			if (!isset($months[$year][$month])) {
				$months[$year][$month] = 1;
			} else {
				$months[$year][$month]++;
			}
		}

		// Sort years
		krsort($months);
		foreach ($months as &$year) {
			krsort($year);
		}

		return $months;
	}

}
