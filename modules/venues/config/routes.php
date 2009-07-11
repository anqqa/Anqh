<?php
/**
 * Venue routes
 *
 * Supported Shortcuts:
 *  :any - matches any non-blank string
 *  :num - matches any number
 *
 * @package    Venues
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
*/

$config['venue/(.*)'] = 'venues/venue/$1';
