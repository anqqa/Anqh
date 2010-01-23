<?php
/**
 * Forum config file
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

// Number of posts per page
$config['posts_per_page']  = 20;

// Number of topics in short topic lists
$config['topics_per_list'] = 15;

// Number of topics in long topic lists
$config['topics_per_page'] = 20;

/**
 * Special settings for bound areas
 */
$config['bind'] = array(
	'events_upcoming' => array(
		'name'  =>__('Upcoming events'),
		'model' => 'event',
		'view'  => 'event',
	),
	'events_past' => array(
		'name'  => __('Past events'),
		'model' => 'event',
		'view'  => 'event',
	),
);
