<?php defined('SYSPATH') or die('No direct script access.');
/**
 * PostgreSQL database connection
 *
 * @package  PostgreSQL
 */
class Database_Postgresql_Core extends Database {

	public function connect()
	{
		// Already connected
		if (is_resource($this->connection))
			return;

		extract($this->config['connection']);

		$str = (isset($socket) AND $socket) ? '' : (isset($host) ? "host='$host'" : '');
		$str .= isset($port) ? " port='$port'" : '';
		$str .= isset($user) ? " user='$user'" : '';
		$str .= isset($pass) ? " password='$pass'" : '';
		$str .= isset($database) ? " dbname='$database'" : '';

		// Connect to the database
		$this->connection = ($this->config['persistent'] === TRUE)
			? pg_pconnect($str, PGSQL_CONNECT_FORCE_NEW)
			: pg_connect($str, PGSQL_CONNECT_FORCE_NEW);

		// A descriptive E_WARNING should have been thrown upon error
		// Test the return value as a last resort
		if ( ! is_resource($this->connection))
			throw new Database_Exception('Unable to connect to database');

		if (isset($this->config['character_set']))
		{
			// Set the character set
			$this->set_charset($this->config['character_set']);
		}

		if (empty($this->config['schema']))
		{
			// Assume the default schema without changing the search path
			$this->config['schema'] = 'public';
		}
		else
		{
			$this->schema($this->config['schema']);
		}
	}

	public function disconnect()
	{
		// Already disconnected
		if ( ! is_resource($this->connection))
			return;

		if ( ! pg_close($this->connection))
			throw new Database_Exception(pg_last_error($this->connection));

		$this->connection = NULL;
	}

	public function set_charset($charset)
	{
		if (pg_set_client_encoding($this->connection, $charset) !== 0)
			throw new Database_Exception(pg_last_error($this->connection));
	}

	public function query_execute($sql)
	{
		// Make sure the database is connected
		$this->connect();

		if ($result = pg_send_query($this->connection, $sql))
		{
			$result = pg_get_result($this->connection);
		}

		if ( ! is_resource($result))
			throw new Database_Exception(':error [ :query ]',
				array(':error' => pg_last_error($this->connection), ':query' => $sql));

		// Set the last query
		$this->last_query = $sql;

		if ($this->config['fix_booleans'])
			return Database_Postgresql_Result_Boolean::factory($result, $sql, $this->connection, $this->config['object']);

		return new Database_Postgresql_Result($result, $sql, $this->connection, $this->config['object']);
	}

	public function escape($value)
	{
		// Make sure the database is connected
		$this->connect();

		return pg_escape_string($this->connection, $value);
	}

	public function quote($value)
	{
		if ($this->config['escape'])
		{
			// This format works in boolean and integer columns
			if ($value === TRUE)
				return "'1'";

			if ($value === FALSE)
				return "'0'";
		}

		return parent::quote($value);
	}

	/**
	 * @link http://www.postgresql.org/docs/8.3/static/information-schema.html
	 *
	 * @return  array
	 */
	public function list_constraints($table)
	{
		$prefix = strlen($this->table_prefix());
		$result = array();

		$constraints = $this->query('
			SELECT c.constraint_name, c.constraint_type, k.column_name, fk.table_name AS fk_table, fk.column_name AS fk_column, cc.check_clause
			FROM information_schema.table_constraints c
			LEFT JOIN information_schema.key_column_usage k ON (k.table_schema = c.table_schema AND k.table_name = c.table_name AND k.constraint_name = c.constraint_name)
			LEFT JOIN information_schema.referential_constraints r ON (r.constraint_schema = c.constraint_schema AND r.constraint_name = c.constraint_name)
			LEFT JOIN information_schema.key_column_usage fk ON (fk.constraint_schema = r.unique_constraint_schema AND fk.constraint_name = r.unique_constraint_name AND fk.ordinal_position = k.position_in_unique_constraint)
			LEFT JOIN information_schema.check_constraints cc ON (cc.constraint_schema = c.constraint_schema AND cc.constraint_name = c.constraint_name)
			WHERE c.table_schema = '.$this->quote($this->schema()).'
				AND c.table_name = '.$this->quote($this->table_prefix().$table).'
			ORDER BY k.ordinal_position
		');

		foreach ($constraints->as_array() as $row)
		{
			switch ($row['constraint_type'])
			{
				case 'CHECK':
					$result[$row['constraint_name']] = array($row['constraint_type'], $row['check_clause']);
				break;
				case 'FOREIGN KEY':
					if (isset($result[$row['constraint_name']]))
					{
						$result[$row['constraint_name']][1][] = $row['column_name'];
						$result[$row['constraint_name']][3][] = $row['fk_column'];
					}
					else
					{
						$result[$row['constraint_name']] = array($row['constraint_type'], array($row['column_name']), substr($row['fk_table'], $prefix), array($row['fk_column']));
					}
				break;
				case 'PRIMARY KEY':
				case 'UNIQUE':
					if (isset($result[$row['constraint_name']]))
					{
						$result[$row['constraint_name']][1][] = $row['column_name'];
					}
					else
					{
						$result[$row['constraint_name']] = array($row['constraint_type'], array($row['column_name']));
					}
				break;
			}
		}

		return $result;
	}

	/**
	 * @link http://www.postgresql.org/docs/8.3/static/infoschema-columns.html
	 *
	 * @return  array
	 */
	public function list_fields($table)
	{
		$columns = $this->query('
			SELECT column_name, column_default, is_nullable, data_type,
				character_maximum_length,
				numeric_precision, numeric_scale,
				datetime_precision,
				interval_precision
			FROM information_schema.columns
			WHERE table_schema = '.$this->quote($this->schema()).'
				AND table_name = '.$this->quote($this->table_prefix().$table).'
			ORDER BY ordinal_position
		');

		if (count($columns) === 0)
			throw new Database_Exception('Table :table does not exist', array(':table' => $table));

		$result = array();

		foreach ($columns->as_array() as $row)
		{
			$column = $this->sql_type($row['data_type']);

			$column['default'] = $row['column_default'];
			$column['nullable'] = $row['is_nullable'] === 'YES';
			$column['sequenced'] = ! strncmp($column['default'], 'nextval(', 8);

			switch ($column['sql_type'])
			{
				case 'character':
				case 'character varying':
					$column['length'] = $row['character_maximum_length'];
					break;

				case 'interval':
					$column['precision'] = $row['interval_precision'];
					break;

				case 'numeric':
					$column['precision'] = $row['numeric_precision'];
					$column['scale'] = $row['numeric_scale'];
					break;

				case 'time with time zone':
				case 'time without time zone':
				case 'timestamp with time zone':
				case 'timestamp without time zone':
					$column['precision'] = $row['datetime_precision'];
					break;
			}

			$result[$row['column_name']] = $column;
		}

		return $result;
	}

	/**
	 * @link http://www.postgresql.org/docs/8.3/static/infoschema-tables.html
	 *
	 * @return  array
	 */
	public function list_tables()
	{
		$tables = $this->query('
			SELECT table_name
			FROM information_schema.tables
			WHERE table_schema = '.$this->quote($this->schema()).'
				AND table_name LIKE '.$this->quote($this->table_prefix().'%')
		);

		$prefix = strlen($this->table_prefix());
		$result = array();

		foreach ($tables->as_array() as $row)
		{
			// Strip table_prefix
			$result[] = substr($row['table_name'], $prefix);
		}

		return $result;
	}

	/**
	 * Get the configured schema
	 *
	 * @param   string  Optional new schema to set and use
	 * @return  string
	 */
	public function schema($new_schema = NULL)
	{
		$result = $this->config['schema'];

		if ($new_schema)
		{
			$this->config['schema'] = $new_schema;
			$this->query_execute("SET search_path = $new_schema, pg_catalog");
		}

		return $result;
	}
}
