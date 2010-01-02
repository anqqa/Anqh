<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  PostgreSQL
 *
 * Data types specific to PostgreSQL
 */
$config = array
(
	'bytea' => array('type' => 'string', 'binary' => TRUE),
	'cidr'  => array('type' => 'string'),
	'inet'  => array('type' => 'string'),
	'int2'  => array('type' => 'int', 'min' => -32768, 'max' => 32767),
	'int4'  => array('type' => 'int', 'min' => -2147483648, 'max' => 2147483647),
	'int8'  => array('type' => 'int', 'min' => -9223372036854775808, 'max' => 9223372036854775807),
	'macaddr' => array('type' => 'string'),
	'money' => array('type' => 'float', 'exact' => TRUE, 'min' => -92233720368547758.08, 'max' => 92233720368547758.07),
	'uuid'  => array('type' => 'string'),
);

$config['tsquery'] = $config['tsvector'] = $config['bytea'];
