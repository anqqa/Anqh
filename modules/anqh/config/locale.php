<?php
/**
 * Locale config
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

/**
 * Available languages
 */
$config['default_language'] = 'en';
$config['languages'] = array(
	'en' => array('en_US', 'English_United States', 'English'),
	'fi' => array('fi_FI', 'Finnish_Finnish', 'Suomi'),
);

/**
 * Available countries
 */
$config['default_country'] = 'fi';
$config['countries'] = array(
	'fi' => array('fi_FI', 'Finland'),
);

/**
 * Default language locale name(s).
 * First item must be a valid i18n directory name, subsequent items are alternative locales
 * for OS's that don't support the first (e.g. Windows). The first valid locale in the array will be used.
 * @see http://php.net/setlocale
 */
$config['language'] = $config['languages'][$config['default_language']];

/**
 * Default country locale.
 */
$config['country'] = $config['countries'][$config['default_country']];

/**
 * Locale timezone. Defaults to use the server timezone.
 * @see http://php.net/timezones
 */
$config['timezone'] = ini_get('date.timezone');

/**
 * First day of the week
 */
$config['start_monday'] = true;
/*$config['firstdayofweek'] = 1;
$config['firstdayofweek_name'] = 'monday';*/

/**
 * Currency
 */
$config['currency'] = array(
	'long'   => 'Euro',
	'short'  => 'Eur',
	'symbol' => '€',
);
