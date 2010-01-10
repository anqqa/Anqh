<?php
/**
 * Anqh extended HTML5 helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009-2010 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class html extends html_Core {

	/**
	 * Print user avatar
	 *
	 * @param   string  $avatar
	 * @param   string  $title
	 * @param   bool    $mini
	 * @return  string
	 */
	public static function avatar($avatar, $title = '', $mini = false) {
		if (empty($avatar) || strpos($avatar, ':') || strpos($avatar, '/') === false) $avatar = 'avatar/unknown.png';

		if (empty($title)) {
			return '<div class="avatar">' . html::image(array('src' => $avatar), 'Avatar') . '</div>';
		} else {
			return '<div class="avatar">' . html::anchor(url::user($title), html::image(array('src' => $avatar, 'title' => $title), $title)) . '</div>';
		}
	}


	/**
	 * Prints date box
	 *
	 * @param   string|integer  $date
	 * @param   boolean         $year
	 * @return  string
	 */
	public static function box_day($date, $year = false) {
		if (!is_numeric($date)) {
			$date = strtotime($date);
		}

		$weekday = strtolower(date('D', $date));
		$day = date('d', $date);
		$month = strtolower(date('M', $date));
		if ($year) {
			$month .= " '" . date('y', $date);
		}

		// Today?
		if (date('Y-m-d', $date) == date('Y-m-d')) {
			$weekday = __('Today');
			return <<<DATE
<div class="date today">
	<span class="weekday">$weekday</span>
	<span class="day">$day</span>
	<span class="month">$month</span>
</div>
DATE;
		} else {
			return <<<DATE
<div class="date">
	<span class="weekday">$weekday</span>
	<span class="day">$day</span>
	<span class="month">$month</span>
</div>
DATE;
		}
	}


	/**
	 * Prints step / rank box
	 *
	 * @param  mixed  $content
	 */
	public static function box_step($content) {
		return '<div class="grid-1 step">' . $content . '</div>';
	}


	/**
	 * UI button
	 *
	 * @param   string  $uri
	 * @param   string  $title
	 * @param   array   $attributes
	 * @return  string
	 */
	public static function button($uri, $title, $attributes = null) {
		if (empty($attributes)) {
			$attributes = array('class' => 'button');
		} else {
			$attributes['class'] = trim($attributes['class'] . ' button');
		}
		$attributes['class'] .= ' medium';

		return html::anchor($uri, $title, $attributes);
	}


	/**
	 * Returns errors
	 *
	 * @param  string|array $error or $errors array
	 * @param  string $filter, $filter, ...
	 * @return string
	 */
	public static function error($errors = false) {

		// no errors given
		if (empty($errors))
			return '';

		// more than one argument = filters
		if (func_num_args() > 1) {
			$argv = func_get_args();
			$filters = is_array(next($argv)) ? current($argv) : array_slice($argv, 1);
		}

		$error = array();

		// single error
		if (!is_array($errors)) {
			$error[] = $errors;
		} else {

			// show error only if found in filters or no filters at all
			if (!empty($filters)) {
				foreach ($filters as $error_id) {
					if (isset($errors[$error_id])) $error[] = $errors[$error_id];
				}
			} else {
				$error = $errors;
			}

		}

		return empty($error) ? '' : '<span class="info">' . implode('<br />', $error). '</span>';
	}


	/**
	 * Print user Gravatar
	 *
	 * @param   string   $email
	 * @param   string   $title
	 * @param   boolean  $mini
	 * @return  string
	 */
	public static function gravatar($email, $title = '', $mini = false) {
		$gravatar = new Gravatar($email, ($mini ? 25 : 50), url::site('/avatar/unknown.png'));

		if (empty($title)) {
			return '<div class="avatar">' . html::image(array('src' => $gravatar->get_url()), 'Gravatar') . '</div>';
		} else {
			return '<div class="avatar">' . html::anchor(url::user($title), html::image(array('src' => $gravatar->get_url(), 'title' => $title), $title)) . '</div>';
		}
	}


	/**
	 * Creates image link
	 *
	 * @param   Image_Model|int  $image  or image_id
	 * @param   string           $size   thumb|normal
	 * @param   string|array     $alt    alt attribute or array of attributes
	 * @return  string
	 */
	public static function img($image = null, $size = 'normal', $alt = null) {
		if (!($image instanceof Image_Model)) {
			$image = new Image_Model((int)$image);
		}

		switch ($size) {
			case 'original': $size_prefix = 'original_'; break;
			case 'normal':   $size_prefix = '';          break;
			case 'thumb':    $size_prefix = 'thumb_';    break;
		}

		$attributes = is_array($alt) ? $alt : array(
			'width'  => $image->{$size_prefix . 'width'},
			'height' => $image->{$size_prefix . 'height'},
			'alt'    => is_string($alt) ? $alt : (($size == 'thumb' ? __('Thumb') : __('Image')) . ' ' . sprintf('[%dx%d]', $image->{$size_prefix . 'width'}, $image->{$size_prefix . 'height'})),
		);

		return html::image($image->url($size), $attributes);
	}


	/**
	 * Returns nick link
	 *
	 * @param	  User_Model  $user  or uid
	 * @param	  string      $nick
	 * @return  string
	 *
	 * @deprecated  User html::user instead
	 */
	public static function nick($user, $nick = null) {
		return self::user($user, $nick);
	}


	/**
	 * Creates a script link.
	 *
	 * @param   string|array  filename
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function script($script, $index = FALSE) {
		return str_replace(' type="text/javascript"', '', parent::script($script, $index));
	}


	/**
	 * JavaScript source code block
	 *
	 * @param   string  $source
	 * @return  string
	 */
	public static function script_source($source) {
		$complied = '';

		if (is_array($source)) {
			foreach ($source as $script)
				$compiled .= html::script_source($script);
		} else {
			$compiled = implode("\n", array('<script>', /*'// <![CDATA[',*/ trim($source), /*'// ]]>',*/ '</script>'));
		}
		return $compiled;
	}


	/**
	 * Convert special characters to HTML entities
	 *
	 * @param   string   string to convert
	 * @param   boolean  encode existing entities
	 * @return  string
	 */
	public static function specialchars($str) {
		return self::chars($str);
	}


	/**
	 * Return formatted <time> tag
	 *
	 * @param  string        $str
	 * @param  array|string  $attributes  handled as time if not an array
	 * @param  boolean       $short       use only date
	 */
	public static function time($str, $attributes = null, $short = false) {

		// Extract datetime
		$datetime = (is_array($attributes)) ? arr::remove('datetime', $attributes) : $attributes;
		if ($datetime) {
			$time = is_int($datetime) ? $datetime : strtotime($datetime);
			$datetime = date::format($short ? date::DATE_8601 : date::TIME_8601, $time);
			if (is_array($attributes)) {
				$attributes['datetime'] = $datetime;
			} else {
				$attributes = array('datetime' => $datetime);
			}

			// Set title if not the same as content
			if (!isset($attributes['title'])) {
				$title = date::format($short ? 'DMYYYY' : 'DMYYYY_HM', $time);
				if ($title != $str) {
					$attributes['title'] = date::format($short ? 'DMYYYY' : 'DMYYYY_HM', $time);
				}
			}

		}

		return '<time' . html::attributes($attributes) . '>' . $str . '</time>';
	}


	/**
	 * Returns user link
	 *
	 * @param	  User_Model  $user  or uid
	 * @param	  string      $nick
	 * @param   string      $class
	 * @return  string
	 */
	public static function user($user, $nick = null, $class = null) {
		$class = $class ? array($class, 'user') : array('user');

		if (empty($nick)) {
			if (!($user instanceof User_Model)) {
				$user = ORM::factory('user')->find_user($user);
			}
			$nick = $user->username;
			if ($user->gender) {
				$class[] = $user->gender == 'f' ? 'female' : 'male';
			}
		}

		return html::anchor(url::user($nick), $nick, array('class' => implode(' ', $class)));
	}

}
