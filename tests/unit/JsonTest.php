<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license     MIT
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use ErrorException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Json;

/**
 * Class JsonTest
 * @package Tests\Unit
 */
final class JsonTest extends TestCase
{
	protected static $_array;
	protected static $_object;
	protected static $_dataset;
	protected static $_recordset;
	protected static $_jsonString;
	protected static $_dataTableString;

	protected function setUp() : void
	{
		JsonTest::$_array = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => 'Web Developer',
			'salary' => 10000
		];

		//

		JsonTest::$_object = new stdClass();
		JsonTest::$_object->name = 'Nat';
		JsonTest::$_object->surname = 'Withe';
		JsonTest::$_object->work = 'Web Developer';
		JsonTest::$_object->salary = 10000;

		//

		JsonTest::$_dataset = [
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

		JsonTest::$_recordset = [];

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->work = 'Web Developer';
		$data->salary = 10000;

		JsonTest::$_recordset[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';
		$data->surname = 'SG';
		$data->work = 'Marketing Director';
		$data->salary = 10000;

		JsonTest::$_recordset[] = $data;

		//

		JsonTest::$_jsonString = '{"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000}';

		//

		// Below JsonTest::$_dataTableString is something like this:
		// {
		//   "recordsTotal": 2,
		//   "recordsFiltered": 2,
		//   "data": [
		//     {"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000},
		//     {"name":"Angela","surname":"SG","work":"Marketing Director","salary":10000}
		//   ]
		// }
		JsonTest::$_dataTableString = '{' . "\n";
		JsonTest::$_dataTableString .= "\t" . '"recordsTotal": 2,' . "\n";
		JsonTest::$_dataTableString .= "\t" . '"recordsFiltered": 2,' . "\n";
		JsonTest::$_dataTableString .= "\t" . '"data": [{"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000},';
		JsonTest::$_dataTableString .= '{"name":"Angela","surname":"SG","work":"Marketing Director","salary":10000}]' . "\n";
		JsonTest::$_dataTableString .= '}';
	}

	protected function tearDown() : void
	{
		JsonTest::$_array = null;
		JsonTest::$_object = null;
		JsonTest::$_dataset = null;
		JsonTest::$_recordset = null;
		JsonTest::$_jsonString = null;
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
		$result = Json::isValid(JsonTest::$_jsonString);

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
		$result = Json::encode(JsonTest::$_array);

		$this->assertEquals(JsonTest::$_jsonString, $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase3() : void
	{
		$result = Json::encode(JsonTest::$_object);

		$this->assertEquals(JsonTest::$_jsonString, $result);
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
		$result = Json::encode(true);

		$this->assertEquals('true', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase8() : void
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
		$result = Json::decode(JsonTest::$_jsonString);

		$this->assertIsObject($result);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$compare = ($result === JsonTest::$_array);

		$this->assertTrue($compare);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodDecodeCase3() : void
	{
		$result = Json::decode(JsonTest::$_jsonString, true);

		$this->assertIsArray($result);
		$compare = ($result === JsonTest::$_array);

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

	public function testMethodDataTableCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Json::dataTable(['InvalidDataSource']);
	}

	public function testMethodDataTableCase2() : void
	{
		$result = Json::dataTable(JsonTest::$_dataset);

		$this->assertEquals(JsonTest::$_dataTableString, $result);
	}

	public function testMethodDataTableCase3() : void
	{
		$result = Json::dataTable(JsonTest::$_recordset);

		$this->assertEquals(JsonTest::$_dataTableString, $result);
	}
}
