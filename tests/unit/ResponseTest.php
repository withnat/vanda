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
use System\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class ResponseTest
 * @package Tests\Unit
 */
final class ResponseTest extends TestCase
{
	// Response::getStatus()

	public function testMethodGetStatusCase1() : void
	{
		$result = Response::getStatus();

		$this->assertEquals(200, $result);
	}

	// Response::setStatus()

	public function testMethodSetStatusCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Response::setStatus(900);
	}

	public function testMethodSetStatusCase2() : void
	{
		$result = Response::setStatus(404);

		$this->assertInstanceOf('System\Response', $result);
	}

	public function testMethodSetStatusCase3() : void
	{
		Response::setStatus(404);

		$result = Response::getStatus();

		$this->assertEquals(404, $result);
	}

	// Response::getHeader() & Response::setHeader()

	public function testMethodGetHeaderCase1() : void
	{
		$result = Response::getHeader('test');

		$this->assertNull($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetHeaderCase2() : void
	{
		Response::setHeader('Pragma', 'cache');

		$result = Response::getHeader('Pragma');

		$this->assertEquals('cache', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetHeaderCase3() : void
	{
		$expected = ['no-cache', 'no-store'];

		Response::setHeader('Cache-Control', 'no-cache');
		Response::setHeader('Cache-Control', 'no-store');

		$result = Response::getHeader('Cache-Control');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetHeaderCase4() : void
	{
		$expected = ['no-cache', 'no-store'];

		Response::setHeader('Cache-Control', 'no-cache')
			->setHeader('Cache-Control', 'no-store');

		$result = Response::getHeader('Cache-Control');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Response::setHeader(), additional test for checking instance object.

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetHeaderCase1() : void
	{
		$result = Response::setHeader('Pragma', 'cache');

		$this->assertInstanceOf('System\Response', $result);
	}

	// Response::getHeaderList()

	public function testMethodGetHeaderListCase1() : void
	{
		$result = Response::getHeaderList();

		$this->assertEquals([], $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetHeaderListCase2() : void
	{
		$expected = [
			['Pragma' => 'cache'],
			['Cache-Control' => 'no-cache'],
			['Cache-Control' => 'no-store']
		];

		Response::setHeader('Pragma', 'cache')
			->setHeader('Cache-Control', 'no-cache')
			->setHeader('Cache-Control', 'no-store');

		$result = Response::getHeaderList();
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Response::prependHeader()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPrependHeaderCase1() : void
	{
		$result = Response::prependHeader('Pragma', 'cache');

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPrependHeaderListCase2() : void
	{
		$expected = [
			['Cache-Control' => 'no-store'],
			['Cache-Control' => 'no-cache']
		];

		Response::setHeader('Cache-Control', 'no-cache');
		Response::prependHeader('Cache-Control', 'no-store');

		$result = Response::getHeaderList();
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPrependHeaderListCase3() : void
	{
		$expected = [
			['Cache-Control' => 'no-store'],
			['Cache-Control' => 'no-cache']
		];

		Response::setHeader('Cache-Control', 'no-cache')
			->prependHeader('Cache-Control', 'no-store');

		$result = Response::getHeaderList();
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPrependHeaderListCase4() : void
	{
		$expected = [
			['Cache-Control' => 'no-store']
		];

		Response::prependHeader('Cache-Control', 'no-store');

		$result = Response::getHeaderList();
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Response::appenddHeader()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodAppendHeaderCase1() : void
	{
		$result = Response::appendHeader('Pragma', 'cache');

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodAppendHeaderCase2() : void
	{
		$expected = ['no-cache', 'no-store'];

		Response::setHeader('Cache-Control', 'no-cache')
			->appendHeader('Cache-Control', 'no-store');

		$result = Response::getHeader('Cache-Control');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Response::removedHeader()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodRemoveHeaderCase1() : void
	{
		$result = Response::removeHeader('test');

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodRemoveHeaderCase2() : void
	{
		Response::removeHeader('test');

		$result = Response::getHeaderList();

		$this->assertEquals([], $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodRemoveHeaderCase3() : void
	{
		$expected = [
			['Pragma' => 'cache']
		];

		Response::setHeader('Pragma', 'cache')
			->appendHeader('Cache-Control', 'no-cache')
			->appendHeader('Cache-Control', 'no-store');

		Response::removeHeader('Cache-Control');

		$result = Response::getHeaderList();
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Response::clearHeaders

	public function testMethodClearHeaderCase1() : void
	{
		$result = Response::clearHeaders();

		$this->assertInstanceOf('System\Response', $result);
	}

	public function testMethodClearHeaderCase2() : void
	{
		Response::clearHeaders();

		$result = Response::getHeaderList();

		$this->assertEquals([], $result);
	}
}
