<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Blog routes
 *
 * Supported Shortcuts:
 *  :any - matches any non-blank string
 *  :num - matches any number
 *
 * @package    Blog
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

// Entries
$config['blog/(.*)'] = 'blogs/entry/$1';
