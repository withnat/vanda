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
use System\Csv;
use PHPUnit\Framework\TestCase;

/**
 * Class CsvTest
 * @package Tests\Unit
 */
final class CsvTest extends TestCase
{
	protected static $_dataset;
	protected static $_recordset;
	protected static $_csvString;

	protected function setUp() : void
	{
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

		static::$_csvString = '"name","surname","work","salary"' . "\n";
		static::$_csvString .= '"Nat","Withe","Web Developer","10000"' . "\n";
		static::$_csvString .= '"Angela","SG","Marketing Director","10000"' . "\n";
	}

	protected function tearDown() : void
	{
		static::$_dataset = null;
		static::$_recordset = null;
		static::$_csvString = null;

        \Mockery::close();
	}

	// Csv::fromDataset()

	public function testMethodFromDatasetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Csv::fromDataset(['string']);
	}

	public function testMethodFromDatasetCase2() : void
	{
		$result = Csv::fromDataset(static::$_dataset);

		$this->assertEquals(static::$_csvString, $result);
	}

	// Csv::fromRecordset()

	public function testMethodFromRecordsetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Csv::fromRecordset(['string']);
	}

	public function testMethodFromRecordsetCase2() : void
	{
		$result = Csv::fromRecordset(static::$_recordset);

		$this->assertEquals(static::$_csvString, $result);
	}

	// Csv::toArray()

	public function testMethodToArrayCase1() : void
	{
		$expected = [
			[
				'name',
				'surname',
				'work',
				'salary'
			],
			[
				'Nat',
				'Withe',
				'Web Developer',
				'10000' // Csv::toArray() will converts number to string.
			],
			[
				'Angela',
				'SG',
				'Marketing Director',
				'10000' // Csv::toArray() will converts number to string.
			]
		];

		$result = Csv::toArray(static::$_csvString);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::toDataset()

	public function testMethodToAssociativeCase1() : void
	{
		// Csv::toArray() will converts number to string.
		$expected = static::$_dataset;
		$expected[0]['salary'] = '10000';
		$expected[1]['salary'] = '10000';

		$result = Csv::toDataset(static::$_csvString);

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::toRecordset()

	public function testMethodToRecordsetCase1() : void
	{
		$expected = static::$_recordset;
		$expected = (array)$expected;

		$expected[0] = (array)$expected[0];
		$expected[1] = (array)$expected[1];

		// Csv::toArray() will converts number to string.
		$expected[0]['salary'] = '10000';
		$expected[1]['salary'] = '10000';

		$result = Csv::toRecordset(static::$_csvString);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[0] = (array)$result[0];
		$result[1] = (array)$result[1];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::safe() (tested via another methods)
}
