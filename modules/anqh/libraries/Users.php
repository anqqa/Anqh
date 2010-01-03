<?php
/**
 * Users library
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Users_Core {

	/**
	 * Returns birthday heroes
	 *
	 * @return array
	 */
	public static function get_birthdays() {
		$cache = Cache::instance();
		
		$key = $cache->key('users', 'birthdays');
		$users = $cache->get($key);
		
		if (empty($birthdays)) {
			$users = array();
			if ($result = Database::instance()->query('
				SELECT id, username, EXTRACT(YEAR FROM AGE(dob)) AS age, dob FROM users 
				WHERE EXTRACT(MONTH FROM dob) = EXTRACT(MONTH FROM CURRENT_TIMESTAMP) AND EXTRACT(DAY FROM dob) = EXTRACT(DAY FROM CURRENT_TIMESTAMP) 
				ORDER BY age DESC, username ASC
			')) {
				foreach ($result as $user)
					$users[$user->age][] = $user;
				$cache->set($key, $users, null, 3600);
			}
		}
		
		return $users;
	}

	
	/**
	 * Returns new users
	 *
	 * @param	int|string	$limit week, 50
	 */
	public static function get_new($max = 'week') {
		$where = '';
		$limit = '';
		switch ($max) {
			case 'week':
				$where = " WHERE registered > CURRENT_DATE - INTERVAL '1 week' ";
				break;
			default:
				if (is_numeric($max)) {
					$limit = ' LIMIT ' . $max;
				} else {
					throw new Kohana_Exception('member.error_invalid_type');
				}
		}
		
		$cache = Cache::instance();
				
		$key = $cache->key('users', $max);
		$users = $cache->get($key);
		
		if (empty($users)) {
			$users = array();
			if ($result = Database::instance()->query("SELECT id, username, registered, DATE_TRUNC('day', registered) AS day FROM users " . $where . ' ORDER BY id DESC ' . $limit)) {
				foreach ($result as $user)
					$users[$user->day][] = $user;
				$cache->set($key, $users, null, 3600);
			}
		}
		
		return $users;
	}

}
