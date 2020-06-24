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
use System\XML;
use PHPUnit\Framework\TestCase;

/**
 * Class XMLTest
 * @package Tests\Unit
 */
final class XMLTest extends TestCase
{
	protected static $_dataset;
	protected static $_recordset;
	protected static $_xmlString;

	protected function setUp()
	{
		static::$_dataset = [
			[
				'name' => 'Nat',
				'surname' => 'Withe',
				'job' => 'Web Developer',
				'salary' => 10000
			],
			[
				'name' => 'Angela',
				'surname' => 'SG',
				'job' => 'Marketing Director',
				'salary' => 10000
			]
		];

		//

		static::$_recordset = [];

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->job = 'Web Developer';
		$data->salary = 10000;

		static::$_recordset[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';
		$data->surname = 'SG';
		$data->job = 'Marketing Director';
		$data->salary = 10000;

		static::$_recordset[] = $data;

		//

		static::$_xmlString = "<root>\n";
		static::$_xmlString .= "\t<element>\n";
		static::$_xmlString .= "\t\t<name>Nat</name>\n";
		static::$_xmlString .= "\t\t<surname>Withe</surname>\n";
		static::$_xmlString .= "\t\t<job>Web Developer</job>\n";
		static::$_xmlString .= "\t\t<salary>10000</salary>\n";
		static::$_xmlString .= "\t</element>\n";
		static::$_xmlString .= "\t<element>\n";
		static::$_xmlString .= "\t\t<name>Angela</name>\n";
		static::$_xmlString .= "\t\t<surname>SG</surname>\n";
		static::$_xmlString .= "\t\t<job>Marketing Director</job>\n";
		static::$_xmlString .= "\t\t<salary>10000</salary>\n";
		static::$_xmlString .= "\t</element>\n";
		static::$_xmlString .= "</root>\n";
	}

	protected function tearDown()
	{
		static::$_dataset = null;
		static::$_recordset = null;
		static::$_xmlString = null;
	}

	// XML::fromDataset

	public function testMethodFromDatasetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		XML::fromDataset(['string']);
	}

	public function testMethodFromDatasetCase2() : void
	{
		$result = XML::fromDataset(static::$_dataset);

		$this->assertEquals(static::$_xmlString, $result);
	}

	// XML::fromRecordset

	public function testMethodFromRecordsetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		XML::fromRecordset(['string']);
	}

	public function testMethodFromRecordsetCase2() : void
	{
		$result = XML::fromRecordset(static::$_recordset);

		$this->assertEquals(static::$_xmlString, $result);
	}
}
