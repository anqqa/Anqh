<?php
/**
 * Cache settings, defined as arrays, or "groups". If no group name is
 * used when loading the cache library, the group named "default" will be used.
 *
 * Each group can be used independently, and multiple groups can be used at once.
 *
 * Group Options:
 *  driver   - Cache backend driver. Kohana comes with file, database, and memcache drivers.
 *              > File cache is fast and reliable, but requires many filesystem lookups.
 *              > Database cache can be used to cache items remotely, but is slower.
 *              > Memcache is very high performance, but prevents cache tags from being used.
 *
 *  params   - Driver parameters, specific to each driver.
 *
 *  lifetime - Default lifetime of caches in seconds. By default caches are stored for
 *             thirty minutes. Specific lifetime can also be set when creating a new cache.
 *             Setting this to 0 will never automatically delete caches.
 *
 *  requests - Average number of cache requests that will processed before all expired
 *             caches are deleted. This is commonly referred to as "garbage collection".
 *             Setting this to a negative number will disable automatic garbage collection.
 *
 * Anqh cache config file
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
 $config['xcache'] = array(
	'driver'		=> 'xcache',
	'params'		=> '',
	'lifetime'	=> 3600,
	'requests'	=> 1000,
	'prefix'		=> IN_PRODUCTION ? 'anqh' : 'dev_anqh',
);

$config['memcache'] = array(
	'driver'		=> 'memcache',
	'params'		=> '',
	'lifetime'	=> 3600,
	'requests'	=> 1000,
	'prefix'		=> IN_PRODUCTION ? 'anqh' : 'dev_anqh',
);

$config['default'] = $config['memcache'];
