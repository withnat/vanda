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
use stdClass;
use System\JSON;
use PHPUnit\Framework\TestCase;

/**
 * Class JSONTest
 * @package Tests\Unit
 */
final class JSONTest extends TestCase
{
	protected static $_array;
	protected static $_object;
	protected static $_jsonString;

	protected function setUp()
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

		static::$_jsonString = '{"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000}';
	}

	protected function tearDown()
	{
		static::$_array = null;
		static::$_object = null;
		static::$_jsonString = null;
	}

	// JSON::isValid()

	public function testMethodIsValidCase1() : void
	{
		$result = JSON::isValid('string');

		$this->assertFalse($result);
	}

	public function testMethodFromDatasetCase2() : void
	{
		$result = JSON::isValid(static::$_jsonString);

		$this->assertTrue($result);
	}

	// JSON::encode()

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		JSON::encode(tmpfile());
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase2() : void
	{
		$result = JSON::encode(static::$_array);

		$this->assertEquals(static::$_jsonString, $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase3() : void
	{
		$result = JSON::encode(static::$_object);

		$this->assertEquals(static::$_jsonString, $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase4() : void
	{
		$result = JSON::encode([]);

		$this->assertEquals([], $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase5() : void
	{
		$result = JSON::encode('Nat');

		$this->assertEquals('"Nat"', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase6() : void
	{
		$result = JSON::encode(13);

		$this->assertEquals('13', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase7() : void
	{
		$result = JSON::encode(true);

		$this->assertEquals('true', $result);
	}

	/**
	 * @throws ErrorException
	 */
	public function testMethodEncodeCase8() : void
	{
		$result = JSON::encode(null);

		$this->assertEquals('null', $result);
	}
}
