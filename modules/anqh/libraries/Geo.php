<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Geo library to handle GeoNames etc geographical
 *
 * @package    Geo
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Geo_Core {

	const BASE_URL = 'http://ws.geonames.org';

	/**
	 * City cache
	 *
	 * @var  array
	 */
	static $cities = array();

	/**
	 * Country cache
	 *
	 * @var  array
	 */
	static $countries = array();


	/**
	 * Get from GeoNames by id
	 *
	 * @static
	 * @param   integer  $id
	 * @return  array
	 */
	private static function _country_info($code, $lang = 'en') {
		try {
			return new SimpleXMLElement(self::BASE_URL . '/countryInfo?country=' . $code . '&lang=' . $lang, null, true);
		} catch (Exception $e) { }

		return false;
	}


	/**
	 * Get city by id
	 *
	 * @static
	 * @param   integer  $id
	 * @return  Geo_City_Model
	 */
	public static function find_city($id, $lang = 'en') {
		$id = (int)$id;
		if (!$id) {
			return false;
		}

		// Try local cache first
		if (!isset(self::$cities[$id])) {

			// Not found from cache, load from db if preferred
			$city = new Geo_City_Model($id);
			if (!$city->loaded()) {

				// Still not loaded, load from GeoNames
				if ($page = self::_get($id, $lang)) {
					if ($country = self::find_country((string)$page->countryCode, $lang)) {
						$city->id = (int)$page->geonameId;
						$city->name = (string)$page->toponymName;
						$city->latitude = (float)$page->lat;
						$city->longitude = (float)$page->lng;
						$city->population = (int)$page->population;
						$city->geo_country_id = $country->id;
						$city->geo_timezone_id = (string)$page->timezone;
						$city->created = time();
						try {
							$city->save();
						} catch (ORM_Validation_Exception $e) {
							return false;
						}
					}
				}

			}

			self::$cities[$city->id] = $city;
		}

		// Localization
		if (!$city->get_name($lang)) {
			if (empty($page)) {
				$page = self::_get($city->id, $lang);
			}

			$name = new Geo_City_Name_Model();
			$name->geo_city_id = $city->id;
			$name->lang = strtolower($lang);
			$name->name = (string)$city->name;
			$name->save();
		}

		return self::$cities[$id];
	}


	/**
	 * Get country by id or country code
	 *
	 * @static
	 * @param   string|integer  $code  country geonameId or country code
	 * @return  Geo_Country_Model
	 */
	public static function find_country($code, $lang = 'en') {
		$id = is_numeric($code) ? (int)$code : strtoupper($code);
		if (!$id) {
			return false;
		}

		// Try local cache first
		if (!isset(self::$countries[$id])) {

			// Not found from cache, load from db if preferred
			$country = new Geo_Country_Model($id);
			if (!$country->loaded()) {

				// Still not loaded, load from GeoNames
				if (is_int($id)) {

					// GeoName id given
					if ($details = self::_get((int)$id, $lang)) {
						$info = self::_country_info((string)$details->countryCode, $lang);
					}

				} else {

					// Country code given
					if ($info = self::_country_info($id, $lang)) {
						$details = self::_get((int)$info->country->geonameId, $lang);
					}

				}

				if (!empty($info) && !empty($details)) {
					$country->id         = (int)$info->country->geonameId;
					$country->name       = (string)$details->toponymName;
					$country->code       = strtoupper((string)$details->countryCode);
					$country->currency   = (string)$info->country->currencyCode;
					$country->population = (int)$details->population;
					$country->created    = time();

					try {
						$country->save();
					} catch (ORM_Validation_Exception $e) {
						return false;
					}
				}
			}

			self::$countries[$country->id] = self::$countries[$country->code] = $country;
		}

		// Localization
		if (!$country->get_name($lang)) {
			if (empty($info)) {
				$info = self::_country_info($country->code, $lang);
			}

			$name = new Geo_Country_Name_Model();
			$name->geo_country_id = $country->id;
			$name->code = $country->code;
			$name->lang = strtolower($lang);
			$name->name = (string)$info->country->countryName;
			$name->save();
		}

		return self::$countries[$id];
	}


	/**
	 * Get from GeoNames by id
	 *
	 * @static
	 * @param   integer  $id
	 * @return  array
	 */
	private static function _get($id) {
		try {
			return new SimpleXMLElement(self::BASE_URL . '/get?geonameId=' . (int)$id . '&style=full', null, true);
		} catch (Exception $e) { }

		return false;
	}

}
