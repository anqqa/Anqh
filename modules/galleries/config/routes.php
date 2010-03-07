<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Gallery routes
 *
 * Supported Shortcuts:
 *  :any - matches any non-blank string
 *  :num - matches any number
 *
 * @package    Galleries
 * @author     Antti Qvickström
 * @copyright  (c) 2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

// Galleries
$config['gallery/([0-9]+.*)'] = 'galleries/gallery/$1';

// Image comments
$config['image/comment/([0-9]+.*)'] = 'galleries/comment/$1';
