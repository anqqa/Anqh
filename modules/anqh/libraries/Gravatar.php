<?php
/**
 * Gravatar library
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Gravatar_Core {

	/**
	 * Gravatar API url
	 *
	 * @var  string
	 */
	const GRAVATAR_URL = 'http://www.gravatar.com/avatar/';

	/**
	 * Default Gravatar type
	 *
	 * @var  string
	 */
	const
		DEFAULT_IDENTICON = 'identicon',
		DEFAULT_MONSTERID = 'monsterid',
		DEFAULT_WAVATAR   = 'wavatar';

	/**
	 * Gravatar ratings
	 *
	 * @var  string
	 */
	const
		RATING_G  = 'g',
		RATING_PG = 'pg',
		RATING_R  = 'r',
		RATING_X  = 'x';

	/**
	 * Default image if Gravatar not found
	 *
	 * @var  string
	 */
	protected $default;

	/**
	 * Generated Gravatar id
	 *
	 * @var  string
	 */
	protected $id;

	/**
	 * Gravatar rating
	 *
	 * @var  string
	 */
	protected $rating;

	/**
	 * Gravatar size
	 *
	 * @var  integer
	 */
	protected $size;


	/**
	 * Create new Gravatar
	 *
	 * @param  string   $email
	 * @param  integer  $size
	 * @param  string   $default
	 */
	public function __construct($email = null, $size = null, $default = null) {
		$this->set_email($email);
		$this->set_size($size);
		$this->set_default($default);
	}


	/**
	 * Build current Gravatar url
	 *
	 * @return  string
	 */
	public function get_url() {
		$url = '';
		if ($this->size) {
			$url .= (empty($url) ? '?' : '&') . 's=' . $this->size;
		}
		if ($this->rating) {
			$url .= (empty($url) ? '?' : '&') . 'r=' . $this->rating;
		}
		if ($this->default) {
			$url .= (empty($url) ? '?' : '&') . 'd=' . $this->default;
		}
		return self::GRAVATAR_URL . $this->id . $url;
	}


	/**
	 * Set default avatar type or url
	 *
	 * @param   string  $default
	 * @return  Gravatar
	 */
	public function set_default($default) {
		$default = (string)$default;
		if (in_array($default, array(self::DEFAULT_IDENTICON, self::DEFAULT_MONSTERID, self::DEFAULT_WAVATAR))) {
			$this->default = $default;
		} else if (valid::url($default)) {
			$this->default = urlencode($default);
		}
		return $this;
	}

	/**
	 * Set Gravatar email
	 *
	 * @param   string  $email
	 * @return  Gravatar
	 */
	public function set_email($email) {
		if (valid::email($email)) {
			$this->id = md5(strtolower($email));
		}
		return $this;
	}


	/**
	 * Set rating
	 *
	 * @param   string  $rating
	 * @return  Gravatar
	 */
	public function set_rating($rating) {
		$rating = (string)$rating;
		if (in_array($rating, array(self::RATING_G, self::RATING_PG, self::RATING_R, self::RATING_X))) {
			$this->rating = $rating;
		}
		return $this;
	}


	/**
	 * Set Gravatar size in pixels, 1-512
	 *
	 * @param   integer  $size
	 * @return  Gravatar
	 */
	public function set_size($size) {
		$size = (int)$size;
		if ($size >= 1 && $size <= 512) {
			$this->size = $size;
		}
		return $this;
	}


	/**
	 * Print HTML
	 */
	public function __toString() {
		echo '<img src="' . $this->get_url() . '" width="' . $this->size . '" height="' . $this->size . '" alt="Gravatar" />';
	}

}
