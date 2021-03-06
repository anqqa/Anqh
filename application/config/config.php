<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Domain name, with the installation directory. Default: localhost/kohana/
 */
$config['site_domain'] = '';

/**
 * Default protocol used to access the website. Default: http
 */
$config['site_protocol'] = 'http';

/**
 * Name of the front controller for this application. Default: index.php
 *
 * This can be removed by using URL rewriting.
 */
$config['index_page'] = '';

/**
 * Fake file extension that will be added to all generated URLs. Example: .html
 */
$config['url_suffix'] = '/';

/**
 * Length of time of the internal cache in seconds. 0 or FALSE means no caching.
 * The internal cache stores file paths and config entries across requests and
 * can give significant speed improvements at the expense of delayed updating.
 */
$config['internal_cache'] = 60;

/**
 * Internal cache directory.
 */
$config['internal_cache_path'] = APPPATH . '../cache/';

/**
 * Enable internal cache encryption - speed/processing loss
 * is neglible when this is turned on. Can be turned off
 * if application directory is not in the webroot.
 */
$config['internal_cache_encrypt'] = FALSE;

/**
 * Encryption key for the internal cache, only used
 * if internal_cache_encrypt is TRUE.
 *
 * Make sure you specify your own key here!
 *
 * The cache is deleted when/if the key changes.
 */
$config['internal_cache_key'] = 'foobar-changeme';

/**
 * Enable or disable gzip output compression. This can dramatically decrease
 * server bandwidth usage, at the cost of slightly higher CPU usage. Set to
 * the compression level (1-9) that you want to use, or FALSE to disable.
 *
 * Do not enable this option if you are using output compression in php.ini!
 */
$config['output_compression'] = 5;

/**
 * Enable or disable global XSS filtering of GET, POST, and SERVER data. This
 * option also accepts a string to specify a specific XSS filtering tool.
 */
$config['global_xss_filtering'] = TRUE;

/**
 * Enable or disable hooks. Setting this option to TRUE will enable
 * all hooks. By using an array of hook filenames, you can control
 * which hooks are enabled. Setting this option to FALSE disables hooks.
 */
$config['enable_hooks'] = TRUE;

/**
 * Enable or disable displaying of Kohana error pages. This will not affect
 * logging. Turning this off will disable ALL error pages.
 */
$config['display_errors'] = !IN_PRODUCTION;

/**
 * Enable or display statistics in the final output. Stats are replaced via
 * specific strings, such as {execution_time}.
 *
 * @see http://doc.kohanaphp.com/general/configuration/config
 */
$config['render_stats'] = TRUE;

/**
 * Filename prefixed used to determine extensions. For example, an
 * extension to the Controller class would be named MY_Controller.php.
 */
$config['extension_prefix'] = 'MY_';

/**
 * An optional list of Config Drivers to use, they "fallback" to the one below them if they
 * dont work so the first driver is tried then so on until it hits the built in "array" driver and fails
 */
$config['config_drivers'] = array();

/**
 * Additional resource paths, or "modules". Each path can either be absolute
 * or relative to the docroot. Modules can include any resource that can exist
 * in your application directory, configuration files, controllers, views, etc.
 */
$config['modules'] = array(
	'core'       => MODPATH . 'anqh',       // Anqh core

	'postgres'   => MODPATH . 'postgres',   // PostgreSQL support
	'event'      => MODPATH . 'event',      // Event from 2.3.4
	'calendar'   => MODPATH . 'calendar',   // Calendar from 2.3.4
	'pagination' => MODPATH . 'pagination', // Pagination from 2.3.4
	'gmaps'      => MODPATH . 'gmaps',      // Google Maps integration

	'events'     => MODPATH . 'events',     // Event calendar for Anqh
	'forum'      => MODPATH . 'forum',      // Forum for Anqh
	'blog'       => MODPATH . 'blog',       // Blogs for Anqh
	'galleries'  => MODPATH . 'galleries',  // Galleries for Anqh
	'venues'     => MODPATH . 'venues',     // Venues for Anqh
);
