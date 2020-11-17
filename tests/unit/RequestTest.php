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
use System\Request;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 * @package Tests\Unit
 */
final class RequestTest extends TestCase
{
	// Request::set()

	public function testMethodSetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Request::set('arg', new stdClass());
	}

	public function testMethodSetCase2() : void
	{
		Request::set('arg', 'value');

		$result = $_GET['arg'];

		$this->assertEquals('value', $result);
	}

	public function testMethodSetCase3() : void
	{
		Request::set('arg', 'value', 'get');

		$result = $_GET['arg'];

		$this->assertEquals('value', $result);
	}

	public function testMethodSetCase4() : void
	{
		Request::set('arg', 'value', 'post');

		$result = $_POST['arg'];

		$this->assertEquals('value', $result);
	}

	public function testMethodSetCase5() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Request::set('arg', 'value', 'x');
	}

	// Request::get()

	public function testMethodGetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Request::get('arg', tmpfile());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase2() : void
	{
		$getValues = new stdClass();
		$getValues->arg = 'value';

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get();

		$this->assertEquals($getValues, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase3() : void
	{
		$getValues = new stdClass();
		$getValues->arg = 'value';

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get(null, null, false);

		$this->assertEquals($getValues, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase4() : void
	{
		$getValues = new stdClass();
		$getValues->arg = 'value';

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get('arg');

		$this->assertEquals('value', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase5() : void
	{
		$getValues = new stdClass();

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get('arg', 'default');

		$this->assertEquals('default', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase6() : void
	{
		$getValues = new stdClass();

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get('arg', 'default', false);

		$this->assertEquals('default', $result);
	}

	// Request::post()

	public function testMethodPostCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Request::post('arg', tmpfile());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase2() : void
	{
		$getValues = new stdClass();
		$getValues->arg = 'value';

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::post();

		$this->assertEquals($getValues, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase3() : void
	{
		$getValues = new stdClass();
		$getValues->arg = 'value';

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::post(null, null, false);

		$this->assertEquals($getValues, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase4() : void
	{
		$getValues = new stdClass();
		$getValues->arg = 'value';

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::post('arg');

		$this->assertEquals('value', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase5() : void
	{
		$getValues = new stdClass();

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::post('arg', 'default');

		$this->assertEquals('default', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase6() : void
	{
		$getValues = new stdClass();

		$mockedRequest = \Mockery::mock('alias:\System\Arr');
		$mockedRequest->shouldReceive(['toObject' => $getValues]);

		$mockedRequest = \Mockery::mock('alias:\System\Security');
		$mockedRequest->shouldReceive(['xssClean' => $getValues]);

		$result = Request::post('arg', 'default', false);

		$this->assertEquals('default', $result);
	}
}
