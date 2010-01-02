<?php defined('SYSPATH') or die('No direct script access.');
/**
 * PostgreSQL database result which transforms boolean values
 *
 * @package  PostgreSQL
 */
class Database_Postgresql_Result_Boolean_Core extends Database_Postgresql_Result {

	protected $booleans;

	/**
	 * @param   resource    from pg_query() or pg_get_result()
	 * @param   string      SQL used to create this result
	 * @param   resource    from pg_connect() or pg_pconnect()
	 * @param   boolean|string
	 * @return  void
	 */
	public static function factory($result, $sql, $link, $return_objects)
	{
		// Detect errors, initialize values
		$postgresql_result = new Database_Postgresql_Result($result, $sql, $link, $return_objects);

		// No rows, nothing to  transform
		if ($postgresql_result->total_rows === 0)
			return $postgresql_result;

		$booleans = FALSE;

		// Create list of boolean field names
		for ($i = pg_num_fields($result) - 1; $i >= 0; --$i)
		{
			if (pg_field_type($result, $i) === 'bool')
			{
				$booleans[] = pg_field_name($result, $i);
			}
		}

		// No booleans to transform, regular result set is fastest
		if ($booleans === FALSE)
			return $postgresql_result;

		return new Database_Postgresql_Result_Boolean($postgresql_result, $booleans);
	}

	/**
	 * @param   Database_Postgresql_Result
	 * @param   array   list of boolean fields
	 * @return  void
	 */
	public function __construct($postgresql_result, $booleans)
	{
		$class = new ReflectionClass($postgresql_result);

		// Copy values
		foreach ($class->getProperties() as $property)
		{
			$property = $property->getName();

			$this->$property = $postgresql_result->$property;
		}

		$this->booleans = $booleans;

		$postgresql_result->result = NULL;
	}

	public function as_array($return = FALSE)
	{
		// Return arrays rather than objects
		$this->return_objects = FALSE;

		if ( ! $return)
			return $this;

		if ($this->total_rows <= 0)
			return array();

		// Transform boolean fields
		pg_result_seek($this->result, 0);

		while ($row = pg_fetch_assoc($this->result))
		{
			foreach ($this->booleans as $field)
			{
				if ($row[$field] !== NULL)
				{
					$row[$field] = ($row[$field] === 't');
				}
			}

			$result[] = $row;
		}

		$this->internal_row = $this->total_rows;

		return $result;
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
				$row = pg_fetch_object($this->result, $this->internal_row, $this->return_objects);

				// Transform boolean fields
				foreach ($this->booleans as $field)
				{
					if ($row->$field !== NULL)
					{
						$row->$field = ($row->$field === 't');
					}
				}

				$result[] = $row;
			}
		}
		else
		{
			pg_result_seek($this->result, 0);

			while ($row = pg_fetch_object($this->result))
			{
				// Transform boolean fields
				foreach ($this->booleans as $field)
				{
					if ($row->$field !== NULL)
					{
						$row->$field = ($row->$field === 't');
					}
				}

				$result[] = $row;
			}

			$this->internal_row = $this->total_rows;
		}

		return $result;
	}

	public function current()
	{
		if ($this->current_row !== $this->internal_row AND ! $this->seek($this->current_row))
			return NULL;

		++$this->internal_row;

		if ( ! $this->return_objects)
		{
			$result = pg_fetch_assoc($this->result);

			// Transform boolean fields
			foreach ($this->booleans as $field)
			{
				if ($result[$field] !== NULL)
				{
					$result[$field] = ($result[$field] === 't');
				}
			}

			return $result;
		}

		$result = is_string($this->return_objects)
			? pg_fetch_object($this->result, $this->current_row, $this->return_objects)
			: pg_fetch_object($this->result);

		// Transform boolean fields
		foreach ($this->booleans as $field)
		{
			if ($result->$field !== NULL)
			{
				$result->$field = ($result->$field === 't');
			}
		}

		return $result;
	}
}
