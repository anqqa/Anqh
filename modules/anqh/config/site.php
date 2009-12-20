<?php
/**
 * Site basic configuration
 *
 * @package  Anqh
 */
$config = array(

	/**
	 * Site name
	 */
	'site_name'    => 'Anqh',

	/**
	 * Domain name for static images, for CDN like
	 */
	'image_server' => 'images.domain.tld',

	/**
	 * Set the site as invite only
	 */
	'inviteonly'   => false,

	/**
	 * E-mail address of the invitation sending
	 */
	'email_invitation' => 'noreply@domain.tld',

	/**
	 * Google configs
	 */
	'google_api_key' => false,
	'google_analytics' => false,  // UA-123456-7

	/**
	 * Main menu
	 */
	'menu' => array(
		'home'    => array('url' => '',        'text' => 'Home'),
		'events'  => array('url' => 'events',  'text' => 'Events'),
		'venues'  => array('url' => 'venues',  'text' => 'Venues'),
		'photos'  => array('url' => 'photos',  'text' => 'Photos'),
		'music'   => array('url' => 'music',   'text' => 'Music'),
		'forum'   => array('url' => 'forum',   'text' => 'Forum'),
		'blogs'   => array('url' => 'blogs',   'text' => 'Blogs'),
		'groups'  => array('url' => 'groups',  'text' => 'Groups'),
		'members' => array('url' => 'members', 'text' => 'Members'),
	),

	/**
	 * Default skin
	 */
	'skin' => 'dark',

	/**
	 * Array of supported countries
	 */
	'countries' => array('Finland'),

	/**
	 * Ad zones
	 */
	'ads' => array(
		'enabled' => true,
		'slots' => array(
			'header' => 'head',
			'side'   => 'side_ads',
		),
	),

	/**
	 * Smileys
	 */
	'smiley' => array(
		'dir' => 'smiley',
		'smileys' => array(
			'smileyname'     => array('src' => 'smileyname.gif'),
		),
	),

);
