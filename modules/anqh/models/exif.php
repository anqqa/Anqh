<?php
/**
 * Exif model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Exif_Model extends Modeler_ORM {

	// ORM
	protected $belongs_to = array('image');

	// Validation
	protected $rules = array(
		'image_id'      => array('valid::digit'),
		'make'          => array('length[0, 64]'),
		'model'         => array('length[0, 64]'),
		'exposure'      => array('length[0, 64]'),
		'program'       => array('length[0, 16]'),
		'aperture'      => array('length[0, 10]'),
		'focal'         => array('length[0, 10]'),
		'iso'           => array('valid::digit'),
		'taken'         => array('valid::date'),
		'metering'      => array('length[0, 64]'),
	  'flash'         => array('length[0, 64]'),
		'latitude' 	    => array('valid::numeric', 'length[0, 10]'),
		'latitude_ref'  => array('length[0, 1]'),
		'longitude'     => array('valid::numeric', 'length[0, 10]'),
    'longitude_ref' => array('length[0, 1]'),
		'altitude'      => array('length[0, 16]'),
		'altitude_ref'  => array('length[0, 16]'),
		'lens'          => array('length[0, 64]'),
	);


	/**
	 * Create new exif model with exif data
	 *
	 * @param   string  $filename
	 * @param   int     $image_id of parent image
	 * @return  Exif_Model
	 */
	public static function factory($filename = false, $image_id = false) {
		$exif_model = new Exif_Model;

		if ($filename) {

			// read exif data from file
			$exif_data = Exif::factory($filename)->read();

			if (!empty($exif_data)) {

				// set image_id if given
				if ($image_id) {
					$exif_data['image_id'] = (int)$image_id;
				}

				// validate
				if ($exif_model->validate($exif_data)) {

					// save our model only if image_id was given
					if ($exif_model->image_id) {
						$exif_model->save();
					}

				}
			}
		}

		return $exif_model;
	}

}
