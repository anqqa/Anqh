<?php
include_once(Kohana::find_file('vendor', 'nbbc/nbbc'));

/**
 * BBCode library.
 *
 * Uses NBBC Copyright (C) 2008-9, the Phantom Inker. All rights reserved.
 * See official site for more info: http://nbbc.sourceforge.net/
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class BB extends BBCode {

	/**
	 * The BBCode formatted text
	 *
	 * @var  string
	 */
	public $text = null;


	/**
	 * Create new BBCode object and initialize our own settings
	 *
	 */
	public function __construct($text = null) {
		parent::BBCode();

		$this->text = $text;

		// Automagically print hrefs
		$this->SetDetectURLs(true);

		// We have our own smileys
		$this->SetEnableSmileys(false);

		// We handle newlines with Kohana
		$this->SetIgnoreNewlines(true);
		$this->SetPreTrim('a');
		$this->SetPostTrim('a');

		// User our own quote
		$this->AddRule('quote', array(
			'simple_start' => '<blockquote>',
			'simple_end'   => '</blockquote>',
			'class'        => 'block',
			'allow_in'     => array('listitem', 'block', 'columns'),
		));
	}


	/**
	 * Creates and returns new BBCode object
	 *
	 * @param   string  $text
	 * @return  BB
	 */
	public static function factory($text = null) {
		return new BB($text);
	}


	/**
	 * Return BBCode parsed to HTML
	 *
	 * @return  string
	 */
	public function render() {
		if (is_null($this->text)) {
			return '';
		}

		// Convert old system tags to BBCode
		$this->text = str_replace(array('[link', '[/link]', '[q]', '[/q]'), array('[url', '[/url]', '[quote]', '[/quote]'), $this->text);

		return text::auto_p($this->Parse($this->text));
	}

}
