<?php
require_once(Kohana::find_file('lib', 'lessphp/lessc.inc', true, 'php'));

/**
 * LESS for PHP
 *
 * @package    Anqh
 * @author     Antti Qvickström (Kohana port)
 * @copyright  (c) 2009 Antti Qvickström
 *
 * @author     Leaf Corcoran (LESS)
 * @copyright  (c) 2009 Leaf Corcoran
 * @license    http://www.apache.org/licenses/ Apache License
 */
class less_Core extends lessc {

	/**
	 * compile functions turn data into css code
	 *
	 * @param   array  $rtags  CSS selectors
	 * @param   array  $env    CSS properties
	 * @return  string
	 */
	protected function compileBlock($rtags, $env) {

		// Skip functions
		foreach ($rtags as $i => $tag) {
			if (preg_match('/( |^)%/', $tag)) {
				unset($rtags[$i]);
			}
		}
		if (empty($rtags)) return '';

		// Build all the properties
		$properties = array();
		$duplicates = array();
		foreach ($env as $name => $value) {
			if (isset($value[0]) && $name{0} != '@' && $name != '__args') {

				// Add properties to array for sort, add order to keep duplicates maintaining order
				$property = $name;
				if (isset($properties[$name])) {
					$property .= ' ' . count($properties);
					$duplicates[$property] = $name;
				}
				$properties[$property] = $this->compileProperty($name, $value, 1);

			}
		}
		if (count($properties) == 0) return true;

		// Sort properties alphabetically
		ksort($properties);

		// Pad last selector to 40 chars
		array_push($rtags, str_pad(array_pop($rtags), 39));

		return implode(", \n", $rtags) . '{ ' . implode(' ', $properties) . " }\n";
	}


	/**
	 * Compile CSS properties
	 *
	 * @param   string  $name   Property name, e.g. padding
	 * @param   array   $value  Property values, e.g. 2px
	 * @param   int     $level  Property depth
	 * @return  string
	 */
	protected function compileProperty($name, $value, $level = 0) {

		// Compile all repeated properties
		$props = array();
		foreach ($value as $v) {
			$props[] = $name . ': ' . $this->compileValue($v) . ';';
		}

		return implode(' ', $props);
	}


	/**
	 * Creates a stylesheet link with LESS support
	 *
	 * @param   string|array  $style    filename, or array of filenames to match to array of medias
	 * @param   string|array  $media    media type of stylesheet, or array to match filenames
	 * @param   boolean       $index    include the index_page in the link
	 * @param   array         $imports  compare file date for these too, CSS and LESS in style @import
	 * @return  string
	 */
	public static function stylesheet($style, $media = false, $index = false, $imports = null) {
		$style = (array)$style;
		$imports = (array)$imports;
		$compiled = array();

		// Loop through styles and compile less if necessary
		foreach ($style as $_style) {

			// Detect suffix
			if (substr_compare($_style, '.less', -5, 5, FALSE) === 0) {
				$_style = substr_replace($_style, '', -5, 5);
				$less = $_style . '.less';
				$css = $_style . '.css';
				try {

					// Check if included files have changed
					$compile = false;
					if (!empty($imports)) {
						foreach ($imports as $import) {
							if (!is_file($css) || filemtime($import) > filemtime($css)) {
								$compile = true;
								break;
							}
						}
					}

					// Compile LESS
					if ($compile || !is_file($css) || filemtime($less) > filemtime($css)) {
						$compiler = new less($less);
						file_put_contents($css, $compiler->parse());
					}
					$_style = $css;

				} catch (Exception $e) {
					Kohana::log('error', __METHOD__ . ': Error compiling LESS file ' . $less . ', ' . $e->getMessage());
					continue;
				}

			} else if (strpos($_style, '.css') === false) {

				// Require suffix to support timestamp cache overrides
				$_style .= '.css';

			}

			// Add to compiled, with timestamp
			$compiled[] = $_style . '?' . filemtime($_style);
		}

		return html::link($compiled, 'stylesheet', 'text/css', false, $media, $index);
	}

}
