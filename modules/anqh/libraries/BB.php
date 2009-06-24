<?php
include_once(Kohana::find_file('vendor', 'nbbc/nbbc'));

/**
 * BBCode library
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
 */
class BB extends BBCode {

	public $text;


	/**
	 * Create new BBCode object and initialize our own settings
	 *
	 */
	public function __construct() {
		parent::BBCode();

		$this->SetDetectURLs(true);
	}


	/**
	 * Creates and returns new BBCode object
	 *
	 * @chainable
	 * @param   string  text with bbcode
	 * @return  BBCode
	 */
	public static function factory($text = null) {
		$bbcode = new BB;
		$bbcode->text = $text;

		return $bbcode;
	}


	/**
	 * Return BBCode parsed to HTML
	 *
	 * @return string
	 */
	public function render() {
		return empty($this->text) ? '' : $this->Parse($this->text);
	}
}
