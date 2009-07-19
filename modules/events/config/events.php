<?php
/**
 * Events calendar config file
 *
 * @package    Events
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

// Tags
$config['tag_group'] = 'Music';

// Logo
$config['logo'] = array(
	'format' => 'jpg',
	'sizes'  => array(
		'normal' => array('width' => 460, 'height' => 460),
		'thumb'  => array('width' => 88,  'height' => 31),
	),
);

// Flyers
$config['flyer'] = array(
	'format' => 'jpg',
	'sizes'  => array(
		'normal' => array('width' => 460, 'height' => 460),
		'thumb'  => array('width' => 48,  'height' => 48),
	),
);

// Dates
$config['year_min'] = 1999;
$config['year_max'] = date('Y') + 5;
