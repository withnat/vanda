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
use System\Html;
use PHPUnit\Framework\TestCase;

/**
 * Class XmlTest
 * @package Tests\Unit
 */
final class HtmlTest extends TestCase
{
	protected function tearDown() : void
	{
		\Mockery::close();
	}

	// Html::link()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Html::link('user', 'User', new stdClass());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase2() : void
	{
		$expected = '<a href="http://localhost">http://localhost</a>';

		$stubUrl = \Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost']);

		$stubRequest = \Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive(['isSPA' => false]);

		$result = Html::link();

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase3() : void
	{
		$expected = '<a style="font-weight:bold;" href="http://localhost">User Management System</a>';

		$stubUrl = \Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost']);

		$stubRequest = \Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive(['isSPA' => false]);

		$result = Html::link('user', 'User Management System', 'style="font-weight:bold;"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase4() : void
	{
		$expected = '<a style="font-weight:bold;" href="http://localhost">User Management System</a>';

		$stubUrl = \Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost']);

		$stubRequest = \Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive(['isSPA' => false]);

		$result = Html::link('user', 'User Management System', ['style' => 'font-weight:bold;']);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase5() : void
	{
		$expected = '<a href="#user" data-url="http://localhost">http://localhost</a>';

		$stubUrl = \Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost']);
		$stubUrl->shouldReceive(['hashSPA' => '#user']);

		$stubRequest = \Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive(['isSPA' => true]);

		$result = Html::link();

		$this->assertEquals($expected, $result);
	}
}
