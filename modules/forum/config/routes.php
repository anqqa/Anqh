<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Forum routes
 *
 * Supported Shortcuts:
 *  :any - matches any non-blank string
 *  :num - matches any number
 *
 * @package    Forum
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */

// Areas
$config['forum/([0-9]+.*)'] = 'forum/area/$1';

// Topics
$config['topic/([0-9]+.*)'] = 'forum/topic/$1';
