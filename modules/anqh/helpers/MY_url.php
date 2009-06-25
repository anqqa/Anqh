<?php
/**
 * Anqh extended url helper class.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class url extends url_Core {

	/**
	 * Transforms an database id to file path, 1234567 = 01/23/45
	 *
	 * @param  int $id
	 * @return string
	 */
	public static function id2path($id) {
		// convert numeric id to hex and split to chunks of 2
		$path = str_split(sprintf('%08x', (int)$id), 2);

		// scrap the last chunk, 256 files per dir
		array_pop($path);

		// return aa/bb/cc
		return implode('/', $path);
	}


	/**
	 * Return model specific url
	 *
	 * @param  ORM  $model
	 */
	public static function model(ORM $model) {
		$url = '';

		// sniff base url etc based on given object
		if ($model instanceof Modeler_ORM) {

			// Auto Modeler ORM object given
			$url = $model->url_base or $url = $model->object_name;

		} else if ($model instanceof ORM) {

			// ORM object given
			$url = $model->object_name;

		}

		$url .= '/' . self::title($model->id, $model->name);

		return $url;
	}


	/**
	 * Convert strings to url safe title
	 *
	 * @param   mixed
	 * @return  string
	 */
	public static function title() {
		$title = func_get_args();
		return parent::title(implode(' ', $title));
	}


	/**
	 * Get URL for user
	 *
	 * @param   mixed  $user  id, username, User_Model
	 * @return  string
	 */
	public static function user($user) {
		$prefix = '/member/';

		// id given
		if (is_numeric($user) && (int)$user > 0) {
			$user = new User_Model($user);
			if ($user->id) {
				$user = $user->username;
			}
		}

		// User_Model given
		if ($user instanceof User_Model) {
			$user = $user->username;
		}

		// username given
		if (is_string($user)) {
			return $prefix . urlencode($user);
		}

		return null;
	}

}
