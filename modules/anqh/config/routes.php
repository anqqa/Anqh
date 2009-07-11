<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Supported Shortcuts:
 *  :any - matches any non-blank string
 *  :num - matches any number
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

/**
 * Permitted URI characters. Note that "?", "#", and "=" are URL characters, and
 * should not be added here.
 */
$config['_allowed'] = '-a-zA-Z0-9_';

/*
 * Default route to use when no URI segments are available.
 */
$config['_default'] = 'index';

// Roles
$config['role/(.*)'] = 'roles/role/$1';

// Tags
$config['tags/([0-9]+.*)'] = 'tags/group/$1';
$config['tag/(add|edit|delete)/(.*)'] = 'tags/tag$1/$2';
$config['tag/(.*)'] = 'tags/tag/$1';
