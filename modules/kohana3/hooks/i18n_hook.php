<?php
/**
 * Select language
 */
I18n::$lang = str_replace('_', '-', Session::instance()->get('language', I18n::$default_lang));


/**
 * Kohana translation/internationalization function.
 *
 * __('Welcome back, :user', array(':user' => $username));
 *
 * @param   string  text to translate
 * @param   array   values to replace in the translated text
 * @return  string
 */
function __($string, array $values = NULL) {
  if (I18n::$lang !== I18n::$default_lang) {
    // Get the translation for this string
    $string = I18n::get($string);
  }

  return empty($values) ? $string : strtr($string, $values);
}


/**
 * Plural translation function
 *
 * @param   string   $string
 * @param   string   $string_plural
 * @param   integer  $count
 * @param   array    $values
 * @return  string
 */
function __2($string, $string_plural, $count, array $values = NULL) {
	return $count == 1 ? __($string, $values) : __($string_plural, $values);
}
