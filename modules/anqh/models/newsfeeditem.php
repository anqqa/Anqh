<?php
/**
 * News feed item model
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class NewsFeedItem_Model extends Modeler_ORM {

	// ORM
	protected $belongs_to = array('user');
	protected $sorting    = array('id' => 'DESC');

}
