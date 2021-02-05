<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2021, Nat Withe. All rights reserved.
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use ErrorException;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Json;

/**
 * Class JsonTest
 * @package Tests\Unit
 */
class JsonTest extends TestCase
{
	protected static $_array;
	protected static $_object;
	protected static $_dataset;
	protected static $_recordset;
	protected static $_jsonString;
	protected static $_dataTableString;

	protected function setUp() : void
	{
		static::$_array = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => 'Web Developer',
			'salary' => 10000
		];

		//

		static::$_object = new stdClass();
		static::$_object->name = 'Nat';
		static::$_object->surname = 'Withe';
		static::$_object->work = 'Web Developer';
		static::$_object->salary = 10000;

		//

		static::$_dataset = [
			[
				'name' => 'Nat',
				'surname' => 'Withe',
				'work' => 'Web Developer',
				'salary' => 10000
			],
			[
				'name' => 'Angela',
				'surname' => 'SG',
				'work' => 'Marketing Director',
				'salary' => 10000
			]
		];

		//

		static::$_recordset = [];

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->work = 'Web Developer';
		$data->salary = 10000;

		static::$_recordset[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';
		$data->surname = 'SG';
		$data->work = 'Marketing Director';
		$data->salary = 10000;

		static::$_recordset[] = $data;

		//

		static::$_jsonString = '{"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000}';

		//

		// Below static::$_dataTableString is something like this:
		// {
		//   "recordsTotal": 2,
		//   "recordsFiltered": 2,
		//   "data": [
		//     {"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000},
		//     {"name":"Angela","surname":"SG","work":"Marketing Director","salary":10000}
		//   ]
		// }
		static::$_dataTableString = '{' . "\n";
		static::$_dataTableString .= "\t" . '"recordsTotal": 2,' . "\n";
		static::$_dataTableString .= "\t" . '"recordsFiltered": 2,' . "\n";
		static::$_dataTableString .= "\t" . '"data": [{"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000},';
		static::$_dataTableString .= '{"name":"Angela","surname":"SG","work":"Marketing Director","salary":10000}]' . "\n";
		static::$_dataTableString .= '}';
	}

	protected function tearDown() : void
	{
		static::$_array = null;
		static::$_object = null;
		static::$_dataset = null;
		static::$_recordset = null;
		static::$_jsonString = null;

		Mockery::close();
	}

	// Json::isValid()

	public function testMethodIsValidCase1() : void
	{
		$result = Json::isValid('');

		$this->assertFalse($result);
	}

	public function testMethodIsValidCase2() : void
	{
		$result = Json::isValid('string');

		$this->assertFalse($result);
	}

	public function testMethodIsValidCase3() : void
	{
		$result = Json::isValid(static::$_jsonString);

		$this->assertTrue($result);
	}

	// Json::encode()

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Json::encode(tmpfile());
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase2() : void
	{
		$result = Json::encode(static::$_array);

		$this->assertEquals(static::$_jsonString, $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase3() : void
	{
		$result = Json::encode(static::$_object);

		$this->assertEquals(static::$_jsonString, $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase4() : void
	{
		$result = Json::encode([]);

		$this->assertEquals('[]', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase5() : void
	{
		$result = Json::encode('Nat');

		$this->assertEquals('"Nat"', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase6() : void
	{
		$result = Json::encode(13);

		$this->assertEquals('13', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase7() : void
	{
		$result = Json::encode(3.14);

		$this->assertEquals('3.14', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase8() : void
	{
		$result = Json::encode(true);

		$this->assertEquals('true', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase9() : void
	{
		$result = Json::encode(null);

		$this->assertEquals('null', $result);
	}

	// Json::decode()

	/**
	 * @throws ErrorException
	 */
	public function testMethodDecodeCase1() : void
	{
		$this->expectException(ErrorException::class);

		Json::decode('InvalidJsonString');
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodDecodeCase2() : void
	{
		$result = Json::decode(static::$_jsonString);

		$this->assertIsObject($result);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$compare = ($result === static::$_array);

		$this->assertTrue($compare);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodDecodeCase3() : void
	{
		$result = Json::decode(static::$_jsonString, true);

		$this->assertIsArray($result);
		$compare = ($result === static::$_array);

		$this->assertTrue($compare);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodDecodeCase4() : void
	{
		$this->expectException(ErrorException::class);

		Json::decode('{"j": 1 ] }');
	}

	// Json::dataTable()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDataTableCase1() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('isDataset')->andReturnFalse();
		$mockedArr->shouldReceive('isRecordset')->andReturnFalse();

		$this->expectException(InvalidArgumentException::class);

		Json::dataTable(['InvalidDataSource']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDataTableCase2() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('isDataset')->andReturnTrue();
		$mockedArr->shouldReceive('isRecordset')->andReturnFalse();

		$result = Json::dataTable(static::$_dataset);

		$this->assertEquals(static::$_dataTableString, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDataTableCase3() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('isDataset')->andReturnFalse();
		$mockedArr->shouldReceive('isRecordset')->andReturnTrue();

		$result = Json::dataTable(static::$_recordset);

		$this->assertEquals(static::$_dataTableString, $result);
	}
}
