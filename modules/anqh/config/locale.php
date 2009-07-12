<?php
/**
 * Locale config
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

$config['locales'] = array(
	'en' => array(
		'language' => array('en_US', 'English_United States', 'English'),
		'country' => 'USA',
	),
	'fi' => array(
		'language' => array('fi_FI', 'Finnish_Finnish', 'Finnish'),
		'country' => 'Finland',
	),
);
$config['default'] = 'en';

/**
 * Default language locale name(s).
 * First item must be a valid i18n directory name, subsequent items are alternative locales
 * for OS's that don't support the first (e.g. Windows). The first valid locale in the array will be used.
 * @see http://php.net/setlocale
 */
$config['language'] = $config['locales'][$config['default']]['language'];

/**
 * Default country locale.
 */
$config['country'] = $config['locales'][$config['default']]['language'];

/**
 * Locale timezone. Defaults to use the server timezone.
 * @see http://php.net/timezones
 */
$config['timezone'] = '';

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
