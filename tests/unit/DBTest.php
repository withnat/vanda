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
use System\DB;
use PHPUnit\Framework\TestCase;

/**
 * Class XmlTest
 * @package Tests\Unit
 */
final class DBTest extends TestCase
{
	protected function setUp() : void
	{
		//$fakePdo = \Mockery::mock(\PDO::class);
		//$statement = \Mockery::mock(\PDOStatement::class);

/*		$fakePdo
			->shouldReceive('query')
			->withArgs(['UPDATE my table SET field2 = :field2 WHERE field1 = :field1'])
			->once()
			->andReturn(true);*/

		$mockedFolder = \Mockery::mock('alias:\System\Folder');
		$mockedFolder->shouldReceive(['create' => true]);
		$mockedFolder->shouldReceive(['delete' => true]);
	}

	protected function getDataSet()
	{
		echo 'a';
	}

	protected function tearDown() : void
	{
		\Mockery::close();
	}

	// Select

	public function testSelectCase1() : void
	{
		$result = DB::select('*');

		$this->assertInstanceOf('System\DB\Platforms\Mysql', $result);
	}

	public function testSelectCase2() : void
	{
		// ตัวอย่าง
		// https://codepoets.co.uk/2019/mockery-test-doubles-mocking-dependencies/
		// lib ที่น่าจะนำมาใช้ได้ https://github.com/jimbojsb/pseudo
		// https://stackoverflow.com/questions/31903097/mocking-pdo-with-phpunit

		$result = DB::select('*')->from('Table')->where(1)->load();

		$this->assertInstanceOf('System\DB\Platforms\Mysql', $result);
	}
}
