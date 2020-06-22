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

use stdClass;
use System\Data;
use PHPUnit\Framework\TestCase;

/**
 * Class DataTest
 * @package Tests\Unit
 */
final class DataTest extends TestCase
{
	protected static $_dataArray;
	protected static $_dataArrayAssoc;
	protected static $_dataObject;

	protected function setUp()
	{
		static::$_dataArray = [
			'A',
			'B',
			[
				'X',
				'Y'
			]
		];

		$job = new stdClass();
		$job->title = 'Web Developer';
		$job->salary = 10000;

		static::$_dataArrayAssoc = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => $job
		];

		static::$_dataObject = new stdClass();
		static::$_dataObject->name = 'Nat';
		static::$_dataObject->surname = 'Withe';
		static::$_dataObject->age = 38;
		static::$_dataObject->job = [
			'title' => 'Web Developer',
			'salary' => 10000
		];
	}

	protected function tearDown()
	{
		static::$_dataArray = null;
		static::$_dataArrayAssoc = null;
		static::$_dataObject = null;
	}

	// Data::get

	public function testMethodGetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Data::get('', 3.14);
	}

	public function testMethodGetCase2() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Data::get([], 3.14);
	}

	public function testMethodGetCase3() : void
	{
		$result = Data::get([], 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase4() : void
	{
		$result = Data::get([], 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase5() : void
	{
		$result = Data::get(static::$_dataArray, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase6() : void
	{
		$result = Data::get(static::$_dataArray, 0);

		$this->assertEquals('A', $result);
	}

	public function testMethodGetCase7() : void
	{
		$result = Data::get(static::$_dataArray, '2.0.missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase8() : void
	{
		$result = Data::get(static::$_dataArray, '2.0');

		$this->assertEquals('X', $result);
	}

	public function testMethodGetCase9() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase10() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase11() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'name');

		$this->assertEquals('Nat', $result);
	}

	public function testMethodGetCase12() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'job.missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase13() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'job.title.missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase14() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'job.title');

		$this->assertEquals('Web Developer', $result);
	}

	public function testMethodGetCase15() : void
	{
		$result = Data::get(static::$_dataObject, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase16() : void
	{
		$result = Data::get(static::$_dataObject, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase17() : void
	{
		$result = Data::get(static::$_dataObject, 'name');

		$this->assertEquals('Nat', $result);
	}

	public function testMethodGetCase18() : void
	{
		$result = Data::get(static::$_dataObject, 'job.missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase19() : void
	{
		$result = Data::get(static::$_dataObject, 'job.missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase20() : void
	{
		$result = Data::get(static::$_dataObject, 'job.title');

		$this->assertEquals('Web Developer', $result);
	}
}
