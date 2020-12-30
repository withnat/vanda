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

	// Request::server()

	public function testMethodServerCase1() : void
	{
		$result = Request::server('xxx');

		$this->assertNull($result);
	}

	public function testMethodServerCase2() : void
	{
		$result = Request::server('argv');

		$this->assertIsArray($result);
	}

	// Request::method()

	public function testMethodMethodCase1() : void
	{
		$result = Request::method();

		$this->assertNull($result);
	}

	public function testMethodMethodCase2() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'get';

		$result = Request::method();

		$this->assertEquals('get', $result);

		unset($_SERVER['REQUEST_METHOD']);
	}

	// Reqeust::ip()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase1() : void
	{
		$mockedRequest = \Mockery::mock('alias:\System\Validator');
		$mockedRequest->shouldReceive(['isValidIp' => false]);

		$result = Request::ip();

		$this->assertEquals('0.0.0.0', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase2() : void
	{
		putenv('HTTP_X_FORWARDED_FOR=75.184.124.93, 10.194.95.79');

		$mockedRequest = \Mockery::mock('alias:\System\Validator');
		$mockedRequest->shouldReceive(['isValidIp' => true]);

		$result = Request::ip();

		$this->assertEquals('75.184.124.93', $result);

		putenv('HTTP_X_FORWARDED_FOR');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase3() : void
	{
		putenv('REMOTE_ADDR=223.24.187.34');

		$mockedRequest = \Mockery::mock('alias:\System\Validator');
		$mockedRequest->shouldReceive(['isValidIp' => true]);

		$result = Request::ip();

		$this->assertEquals('223.24.187.34', $result);

		putenv('REMOTE_ADDR');
	}

	// Request::host()

	public function testMethodHostCase1() : void
	{
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['SERVER_PORT'] = 80;

		$result = Request::host();

		$this->assertEquals('http://localhost', $result);

		unset($_SERVER['HTTP_HOST']);
		unset($_SERVER['SERVER_PORT']);
	}

	public function testMethodHostCase2() : void
	{
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['SERVER_PORT'] = 443;

		$result = Request::host();

		$this->assertEquals('https://localhost', $result);

		unset($_SERVER['HTTP_HOST']);
		unset($_SERVER['SERVER_PORT']);
	}

	// Request::basePath()

	public function testMethodBasePathCase1() : void
	{
		$_SERVER['SCRIPT_NAME'] = '/home/me/www/index.php';

		$result = Request::basePath();

		$this->assertEquals('/home/me/www', $result);

		unset($_SERVER['SCRIPT_NAME']);
	}

	// Request::uri()

	public function testMethodUriCase1() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.4.41 (Ubuntu)';
		$_SERVER['REQUEST_URI'] = '/index.php?arg=value';

		$result = Request::uri();

		$this->assertEquals('/index.php?arg=value', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['REQUEST_URI']);
	}

	public function testMethodUriCase2() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'IIS';
		$_SERVER['PHP_SELF'] = '/index.php?arg=value';

		$result = Request::uri();

		$this->assertEquals('/index.php?arg=value', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['PHP_SELF']);
	}

	// Request::isSecure()

	public function testMethodIsSecureCase1() : void
	{
		$_SERVER['HTTPS'] = '0';
		$_SERVER['SERVER_PORT'] = 80;

		$result = Request::isSecure();

		$this->assertFalse($result);

		unset($_SERVER['HTTPS']);
		unset($_SERVER['SERVER_PORT']);
	}

	public function testMethodIsSecureCase2() : void
	{
		$_SERVER['HTTPS'] = '1';

		$result = Request::isSecure();

		$this->assertTrue($result);

		unset($_SERVER['HTTPS']);
	}

	public function testMethodIsSecureCase3() : void
	{
		$_SERVER['HTTPS'] = 'on';

		$result = Request::isSecure();

		$this->assertTrue($result);

		unset($_SERVER['HTTPS']);
	}

	public function testMethodIsSecureCase4() : void
	{
		$_SERVER['SERVER_PORT'] = 443;

		$result = Request::isSecure();

		$this->assertTrue($result);

		unset($_SERVER['SERVER_PORT']);
	}

	// Request::isGet()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsGetCase1() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$result = Request::isGet();

		$this->assertTrue($result);

		unset($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsGetCase2() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$result = Request::isGet();

		$this->assertFalse($result);

		unset($_SERVER['REQUEST_METHOD']);
	}

	// Request::isPost()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPostCase1() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$result = Request::isPost();

		$this->assertTrue($result);

		unset($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPostCase2() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$result = Request::isPost();

		$this->assertFalse($result);

		unset($_SERVER['REQUEST_METHOD']);
	}

	// Request::isAjax()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsAjaxCase1() : void
	{
		$result = Request::isAjax();

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsAjaxCase2() : void
	{
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

		$result = Request::isAjax();

		$this->assertTrue($result);

		unset($_SERVER['HTTP_X_REQUESTED_WITH']);
	}

	// Request::isCli()

	public function testMethodIsCliCase1() : void
	{
		$result = Request::isCli();

		$this->assertTrue($result);
	}
}
