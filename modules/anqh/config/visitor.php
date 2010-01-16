<?php
/**
 * User library configuration.
 *
 * In order to log a user in, a user must have the "login" role. You may create
 * and assign any other role to your users.
 */

/**
 * Type of hash to use for passwords. Any algorithm supported by the hash function
 * can be used here. Note that the length of your password is determined by the
 * hash type + the number of salt characters.
 *
 * @see http://php.net/hash
 * @see http://php.net/hash_algos
 */
$config['hash_method'] = 'sha1';

/**
 * Defines the hash offsets to insert the salt at. The password hash length
 * will be increased by the total number of offsets.
 */
$config['salt_pattern'] = array(1, 3, 5, 9, 14, 15, 20, 21, 28, 30);

/**
 * Set the auto-login (remember me) cookie lifetime, in seconds. The default
 * lifetime is two weeks.
 */
$config['lifetime'] = 1209600;

/**
 * Set the session key that will be used to store the current user.
 */
$config['session_key'] = 'user';

/**
 * Auto-login cookie name.
 */
$config['cookie_name'] = 'autologin';

/**
 * Username restrictions
 */
$config['username'] = array(
	'chars'      => 'a-zA-Z0-9_\-\^\. ',
	'length_min' => 3,
	'length_max' => 20,
);

/**
 * Password restrictions
 */
$config['password'] = array(
	'length_min' => 5,
);
