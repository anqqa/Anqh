<?php
/**
 * Anqh extended HTML helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    MIT
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
			return '<div class="grid-1 icon' . ($mini ? 24 : 48) . ' avatar">' . html::image(array('src' => $avatar), 'Avatar') . '</div>';
		} else {
			return '<div class="grid-1 icon' . ($mini ? 24 : 48) . ' avatar">' . html::anchor(url::user($title), html::image(array('src' => $avatar, 'title' => $title), $title)) . '</div>';
		}
	}


	/**
	 * Prints date box
	 *
	 * @param		string|int	$date
	 * @return	string
	 */
	public static function box_day($date) {
		is_string($date) and $date = strtotime($date);
		return '<div class="grid-1 date"><span class="weekday">' . Kohana::lang('calendar.' . strtolower(date('D', $date))) . '</span> <span class="day">' . date('d', $date) . '</span> <span class="month">' . Kohana::lang('calendar.' . strtolower(date('M', $date))) . '</span></div>';
	}


	/**
	 * Prints step / rank box
	 *
	 * @param  mixed  $content
	 */
	public static function box_step($content) {
		return '<div class="grid-1 step">' . $content . '</div>';
	}


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
	 * Confirm dialog
	 *
	 * @param  string  $selector
	 * @param  string  $dialog_id
	 * @param  string  $title
	 * @param  string  $text
	 * @param  string  $ok      text for OK
	 * @param  string  $cancel  text for Cancel
	 */
	public static function confirm($selector, $dialog_id, $title, $text, $ok = 'OK', $cancel = 'Cancel') {
		$dialog_id = 'dialog-' . $dialog_id;
		ob_start();
?>
$(function() {

	$("#<?= $dialog_id ?>").dialog({
		autoOpen: false,
		height: 100,
		modal: true,
		buttons: {
			'<?= $ok ?>': function() {
				var action = $('<?= $selector ?>');
				if (action.is('a')) {
					window.location = action.attr('href');
				} else if (action.is('button') || action.is('input')) {
					action.parent('form').submit();
				}
			},
			'<?= $cancel ?>': function() {
				$(this).dialog('close');
			}
		}
	});

	$('<?= $selector ?>').click(function() {
		$('#<?= $dialog_id ?>').dialog('open');
		return false;
	});

});
<?php
		widget::add('foot', html::script_source(ob_get_clean()));
		widget::add('foot', '<div id="' . $dialog_id . '" title="' . self::specialchars($title) . '">' . $text . '</div>');
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

		// translate
		foreach ($error as &$string)
			$string = Kohana::lang('form_errors.' . $string);

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
		$gravatar = new Gravatar($email, ($mini ? 24 : 40), url::site('/avatar/unknown.png'));

		if (empty($title)) {
			return '<div class="grid-1 icon' . ($mini ? 24 : 48) . ' avatar">' . html::image(array('src' => $gravatar->get_url()), 'Gravatar') . '</div>';
		} else {
			return '<div class="grid-1 icon' . ($mini ? 24 : 48) . ' avatar">' . html::anchor(url::user($title), html::image(array('src' => $gravatar->get_url(), 'title' => $title), $title)) . '</div>';
		}
	}


	/**
	 * Creates image link
	 *
	 * @param   Image_Model|int  $image  or image_id
	 * @param   string           $size   thumb|normal
	 * @param   string|array     $alt
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
		$attributes = array(
			'width'  => $image->{$size_prefix . 'width'},
			'height' => $image->{$size_prefix . 'height'},
			'alt'    => Kohana::lang('generic.image_' . $size) . ' ' . Kohana::lang('generic.image_size', $image->{$size_prefix . 'width'}, $image->{$size_prefix . 'height'}),
		);

		return html::image($image->url($size), $attributes);
	}


	/**
	 * Returns nick link
	 *
	 * @param	  User_Model  $user  or uid
	 * @param	  string      $nick
	 * @return  string
	 */
	public static function nick($user, $nick = null) {
		if (empty($nick)) {
			if (!($user instanceof User_Model)) {
				$user = ORM::factory('user')->find_user($user);
			}
			$nick = $user->username;
		}

		return html::anchor(url::user($nick), $nick);
	}


	/**
	 * Parse klubitus code
	 *
	 * @param		string	text to parse
	 * @param		bool		inline images
	 * @return	string
	 * @deprecated
	 */
	public static function parse_code($text, $pics = false) {
		$codes = array(
			"/\[p\](.*?)\[\/p\]/si",
			"/\[b\](.*?)\[\/b\]/si",
			"/\[u\](.*?)\[\/u\]/si",
			"/\[i\](.*?)\[\/i\]/si",
			"/\[q\](.*?)\[\/q\]/si",
			"/\[o\](.*?)\[\/o\]/si",
			"/\[url\](.*?)\[\/url\]/i",
			"/\[url=(.*?)\](.*?)\[\/url\]/i",
			"/\[sup\](.*?)\[\/sup\]/si",
			"/\[sub\](.*?)\[\/sub\]/si",
			"/\[code\](.*?)\[\/code\]/si",
			"/\[big\](.*?)\[\/big\]/si",
			"/\[small\](.*?)\[\/small\]/si",
			"/\[ol\](.*?)\[\/ol\]/si",
			"/\[ul\](.*?)\[\/ul\]/si",
			"/\[li\](.*?)\[\/li\]/si",
			"/\[spoiler\](.*)\[\/spoiler\]/si",
			"/\[email\](.*?)\[\/email\]/si",
			"/\[img=(left|right)\](.*?)\[\/img\]/si",
			"/\[list\](.*?)\[\/list\]/si",
			"/\[list=(1|a|i)\](.*?)\[\/list\]/si",
			"/\[\*\]/si",
			"/\[size=([1-2]?[0-9])\](.*?)\[\/size\]/si",
			"/\[color=(.*?)\](.*?)\[\/color\]/si"
		);
		$html = array(
			'<p>$1</p>',
			'<strong>$1</strong>',
			'<u>$1</u>',
			'<em>$1</em>',
			'<blockquote>$1</blockquote>',
			'<del>$1</del>',
			'<a href="$1">$1</a>',
			'<a href="$1">$2</a>',
			'<sup>$1</sup>',
			'<sub>$1</sub>',
			'<code>$1</code>',
			'<big>$1</big>',
			'<small>$1</small>',
			'<ol>$1</ol>',
			'<ul>$1</ul>',
			'<li>$1</li>',
			'<span class="spoiler">$1</span>',
			'<a href="mailto:$1">$1</a>',
			'<img src="$2" alt="$2" align="$1" />',
			'<ul>$1</ul>',
			'<ol type="$1">$2</ol>',
			'<li>',
			'<span style="font-size: $1px">$2</span>',
			'<span style="color: $1">$2</span>
		');
		if ($pics) {
			$codes[] = "/\[img\](.*?)\[\/img\]/si";
			$html[] = '<img src="$1" alt="$1" />';
		}
		$text = preg_replace($codes, $html, $text);
		return $text;
	}


	/**
	 * Parse links
	 *
	 * @param		string	$text
	 * @return	string
	 * @deprecated
	 */
	public static function parse_links($text) {
		$text = str_replace(array('[link', '[/link]'), array('[url', '[/url]'), $text); // deprecated link to url
		$text = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", '$1http://$2', $text); // add http to www.
		$text = preg_replace("/\[url\](?!http|https|ftp|news|mailto)(.*?)\[\/url\]/i", '[url]http://$1[/url]', $text);
		$text = preg_replace("/\[url=(?!http|https|ftp|news|mailto)(.*?)\]/i", '[url=http://$1]', $text);
		$text = preg_replace("/((?<![\]|=])(http|https|ftp|news|mailto):\/\/[\w-?&;:#~=\.\/\@\+%]+[\w\/][?]?)/i", '[url]$1[/url]', $text);
		//$text = preg_replace("/((?<![\]|=])(http|https|ftp|news|mailto):\/\/[\w-?&;:#~=\.\/\@\+%]+[\w\/][?]?)/i", '<a href="$1">$1</a>', $text);
		$text = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?))/i", '[email]$1[/email]', $text);
		return $text;
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
			$compiled = implode("\n", array('<script type="text/javascript">', '//<![CDATA[', $source, '//]]>', '</script>'));
		}
		return $compiled;
	}
}
