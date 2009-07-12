<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Supported Shortcuts:
 *  :any - matches any non-blank string
 *  :num - matches any number
 *
 * Events routes
 *
 * @package    Events
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

$config['event/(.*)'] = 'events/event/$1';
