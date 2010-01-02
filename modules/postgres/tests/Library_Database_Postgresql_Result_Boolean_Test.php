<?php
/**
 * PostgreSQL Result Library Unit Tests
 *
 * @package     PostgreSQL
 * @author      Chris Bandy
 * @group   postgresql
 * @group   postgresql.result_boolean
 */
class Library_Database_Postgresql_Result_Boolean_Test extends PHPUnit_Framework_TestCase
{
	protected static $database;

	protected function setUp()
	{
		if (($config = Kohana::config('database.testing')) === NULL)
			$this->markTestSkipped('No database.testing config found.');

		if ($config['connection']['type'] !== 'postgresql')
			$this->markTestSkipped('database.testing config not PostgreSQL.');

		// FIXME this should be in setUpBeforeClass() in PHPUnit 3.4
		self::$database = new Library_Database_Postgresql_Result_Boolean_Test_Database($config);
	}

	protected function tearDown()
	{
		// FIXME this should be in tearDownAfterClass() in PHPUnit 3.4
		self::$database = NULL;
	}

	/**
	 * @group postgresql.result_boolean
	 * @test
	 */
	public function test_iteration()
	{
		$result = db::query('SELECT FALSE AS value UNION SELECT TRUE')->execute(self::$database)->as_array();

		$this->assertEquals(0, $result->key());
		$this->assertEquals(array('value' => FALSE), $result->current());
		$this->assertTrue($result->valid());

		$result->next();

		$this->assertEquals(1, $result->key());
		$this->assertEquals(array('value' => TRUE), $result->current());
		$this->assertTrue($result->valid());

		$result->next();

		$this->assertFalse($result->valid());

		$result->rewind();

		$this->assertEquals(0, $result->key());
		$this->assertEquals(array('value' => FALSE), $result->current());
		$this->assertTrue($result->valid());
	}

	/**
	 * @group postgresql.result_boolean
	 * @test
	 */
	public function test_array()
	{
		$result = db::query('SELECT FALSE AS value UNION SELECT TRUE')->execute(self::$database)->as_array(TRUE);

		$this->assertEquals(array(array('value' => FALSE), array('value' => TRUE)), $result);
	}

	/**
	 * @group postgresql.result_boolean
	 * @test
	 */
	public function test_object_array()
	{
		$result = db::query('SELECT FALSE AS value UNION SELECT TRUE')->execute(self::$database)->as_object(NULL, TRUE);

		$this->assertEquals(array((object) array('value' => FALSE), (object) array('value' => TRUE)), $result);
	}

	/**
	 * @group postgresql.result_boolean
	 * @test
	 */
	public function test_class_array()
	{
		$result = db::query('SELECT FALSE AS value UNION SELECT TRUE')->execute(self::$database)->as_object('Library_Database_Postgresql_Result_Boolean_Test_Class', TRUE);

		$this->assertTrue(is_array($result));
		$this->assertEquals(2, count($result));

		$row = current($result);

		$this->assertTrue($row instanceof Library_Database_Postgresql_Result_Boolean_Test_Class);
		$this->assertObjectHasAttribute('value', $row);
		$this->assertEquals(FALSE, $row->value);

		$row = next($result);

		$this->assertTrue($row instanceof Library_Database_Postgresql_Result_Boolean_Test_Class);
		$this->assertObjectHasAttribute('value', $row);
		$this->assertEquals(TRUE, $row->value);
	}

}

/**
 * Used to test object fetching and store database instance
 */
final class Library_Database_Postgresql_Result_Boolean_Test_Class {}

/**
 * Used to access Database::__construct and enable boolean features
 */
final class Library_Database_Postgresql_Result_Boolean_Test_Database extends Database_Postgresql
{
	public function __construct($config)
	{
		$config['fix_booleans'] = TRUE;

		parent::__construct($config);
	}
}
