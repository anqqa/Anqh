<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  PostgreSQL
 *
 * Database connection settings, defined as arrays, or "groups". If no group
 * name is used when loading the database library, the group named "default"
 * will be used.
 *
 * Each group can be connected to independently, and multiple groups can be
 * connected at once.
 *
 * Group Options:
 *  benchmark     - Enable or disable database benchmarking
 *  persistent    - Enable or disable a persistent connection
 *  connection    - Array of connection specific parameters
 *  character_set - Database character set
 *  table_prefix  - Database table prefix
 *  object        - Enable or disable object results
 *  cache         - Enable or disable query caching
 *  escape        - Enable automatic query builder escaping
 *  schema        - Database schema
 *  fix_booleans  - Post-process query results to transform PgSQL boolean fields
 *                  into PHP boolean values. This is a workaround for PHP
 *                  feature/bug #29213, and has a significant performance penalty.
 *                  @link http://dev.kohanaphp.com/projects/pgsql/wiki/Booleans
 */
$config['default'] = array
(
	'benchmark'     => TRUE,
	'persistent'    => FALSE,
	'connection'    => array
	(
		'type'      => 'postgresql',
		'user'      => 'dbuser',
		'pass'      => 'p@ssw0rd',
		'host'      => 'localhost',
		'port'      => FALSE,
		'socket'    => FALSE,
		'database'  => 'kohana'
	),
	'character_set' => 'utf8',
	'table_prefix'  => '',
	'object'        => TRUE,
	'cache'         => FALSE,
	'escape'        => TRUE,
	'schema'        => '',
	'fix_booleans'  => FALSE,
);
