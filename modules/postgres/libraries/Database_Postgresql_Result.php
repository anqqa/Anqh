<?php defined('SYSPATH') or die('No direct script access.');
/**
 * PostgreSQL database result
 *
 * @package  PostgreSQL
 */
class Database_Postgresql_Result_Core extends Database_Result {

	protected $internal_row = 0;
	protected $link;

	/**
	 * @param   resource    from pg_query() or pg_get_result()
	 * @param   string      SQL used to create this result
	 * @param   resource    from pg_connect() or pg_pconnect()
	 * @param   boolean|string
	 * @return  void
	 */
	public function __construct($result, $sql, $link, $return_objects)
	{
		// PGSQL_COMMAND_OK     <- SET client_encoding = 'utf8'
		// PGSQL_TUPLES_OK      <- SELECT table_name FROM information_schema.tables
		// PGSQL_COMMAND_OK     <- INSERT INTO pages (name) VALUES ('gone soon')
		// PGSQL_COMMAND_OK     <- DELETE FROM pages WHERE id = 2
		// PGSQL_COMMAND_OK     <- UPDATE pb_users SET company_id = 1
		// PGSQL_FATAL_ERROR    <- SELECT FROM pages

		switch (pg_result_status($result))
		{
			case PGSQL_EMPTY_QUERY:
				$this->total_rows = 0;
				break;

			case PGSQL_COMMAND_OK:
				$this->total_rows = pg_affected_rows($result);
				break;

			case PGSQL_TUPLES_OK:
				$this->total_rows = pg_num_rows($result);
				break;

			case PGSQL_COPY_OUT:
			case PGSQL_COPY_IN:
				Kohana_Log::add('debug', 'PostgreSQL COPY operations not supported');
				break;

			case PGSQL_BAD_RESPONSE:
			case PGSQL_NONFATAL_ERROR:
			case PGSQL_FATAL_ERROR:
				throw new Database_Exception(':error [ :query ]',
					array(':error' => pg_result_error($result), ':query' => $sql));
		}

		$this->link = $link;
		$this->result = $result;
		$this->return_objects = $return_objects;
		$this->sql = $sql;
	}

	public function __destruct()
	{
		if (is_resource($this->result))
		{
			pg_free_result($this->result);
		}
	}

	public function as_array($return = FALSE)
	{
		// Return arrays rather than objects
		$this->return_objects = FALSE;

		if ( ! $return)
			return $this;

		if ($this->total_rows <= 0)
			return array();

		$this->internal_row = $this->total_rows;

		return pg_fetch_all($this->result);
	}

	public function as_object($class = NULL, $return = FALSE)
	{
		// Return objects of type $class (or stdClass if none given)
		$this->return_objects = ($class !== NULL) ? $class : TRUE;

		if ( ! $return)
			return $this;

		if ($this->total_rows <= 0)
			return array();

		if (is_string($this->return_objects))
		{
			for ($this->internal_row = 0; $this->internal_row < $this->total_rows; ++$this->internal_row)
			{
				$result[] = pg_fetch_object($this->result, $this->internal_row, $this->return_objects);
			}
		}
		else
		{
			pg_result_seek($this->result, 0);

			while ($row = pg_fetch_object($this->result))
			{
				$result[] = $row;
			}

			$this->internal_row = $this->total_rows;
		}

		return $result;
	}

	public function insert_id()
	{
		if ($this->insert_id === NULL)
		{
			$this->insert_id = FALSE;

			if ($result = pg_send_query($this->link, 'SELECT LASTVAL()'))
			{
				$result = pg_get_result($this->link);

				if (is_resource($result))
				{
					if (pg_result_status($result) === PGSQL_TUPLES_OK)
					{
						$this->insert_id = pg_fetch_result($result, 0);
					}

					pg_free_result($result);
				}
			}
		}

		return $this->insert_id;
	}

	/**
	 * SeekableIterator: seek
	 */
	public function seek($offset)
	{
		if ($this->offsetExists($offset) AND pg_result_seek($this->result, $offset))
		{
			// Set the current row to the offset
			$this->current_row = $this->internal_row = $offset;

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Iterator: current
	 */
	public function current()
	{
		if ($this->current_row !== $this->internal_row AND ! $this->seek($this->current_row))
			return NULL;

		++$this->internal_row;

		if ( ! $this->return_objects)
			return pg_fetch_assoc($this->result);

		if (is_string($this->return_objects))
			return pg_fetch_object($this->result, $this->current_row, $this->return_objects);

		return pg_fetch_object($this->result);
	}

}
