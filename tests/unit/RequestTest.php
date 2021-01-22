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
use System\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 * @package Tests\Unit
 */
final class RequestTest extends TestCase
{
    protected function tearDown() : void
    {
        \Mockery::close();
    }

	// Request::set()

	public function testMethodSetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Request::set('arg', new stdClass());
	}

	public function testMethodSetCase2() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Request::set('arg', 'value', 'InvalidMethod');
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
		Request::set('arg', 'value');

		$result = $_POST['arg'];

		$this->assertEquals('value', $result);
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

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $getValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $getValues]);

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

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $getValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get(null, null);

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

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $getValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $getValues]);

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

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $getValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get('arg', 'default');

		$this->assertEquals('default', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase6() : void
	{
		$product = new stdClass();
		$product->price = 100;

		$form = new stdClass();
		$form->product = $product;

		$getValues = new stdClass();
		$getValues->form = $form;

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $getValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get('form.product.price');

		$this->assertEquals(100, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase7() : void
	{
		$product = new stdClass();
		$product->price = 100;

		$form = new stdClass();
		$form->product = $product;

		$getValues = new stdClass();
		$getValues->form = $form;

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $getValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get('form.product');
		$result = $result->price;

		$this->assertEquals(100, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase8() : void
	{
		$getValues = new stdClass();

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $getValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $getValues]);

		$result = Request::get('form.product.price', 'default');

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
		$postValues = new stdClass();
		$postValues->arg = 'value';

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $postValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::post();

		$this->assertEquals($postValues, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase3() : void
	{
		$postValues = new stdClass();
		$postValues->arg = 'value';

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $postValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::post(null, null);

		$this->assertEquals($postValues, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase4() : void
	{
		$postValues = new stdClass();
		$postValues->arg = 'value';

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $postValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::post('arg');

		$this->assertEquals('value', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase5() : void
	{
		$postValues = new stdClass();

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $postValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::post('arg', 'default');

		$this->assertEquals('default', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase6() : void
	{
		$product = new stdClass();
		$product->price = 100;

		$form = new stdClass();
		$form->product = $product;

		$postValues = new stdClass();
		$postValues->form = $form;

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $postValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::post('form.product.price');

		$this->assertEquals(100, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase7() : void
	{
		$product = new stdClass();
		$product->price = 100;

		$form = new stdClass();
		$form->product = $product;

		$postValues = new stdClass();
		$postValues->form = $form;

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $postValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::post('form.product');
		$result = $result->price;

		$this->assertEquals(100, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase8() : void
	{
		$postValues = new stdClass();

		$mockedArr = \Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive(['toObject' => $postValues]);

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::post('form.product.price', 'default');

		$this->assertEquals('default', $result);
	}

	// Request::switcher()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSwitcherCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Request::switcher('status', 'InvalidMethod');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSwitcherCase2() : void
	{
		$postValues = new stdClass();

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::switcher('status');

		$this->assertEquals(0, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSwitcherCase3() : void
	{
		$postValues = new stdClass();

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::switcher('status', 'post');

		$this->assertEquals(0, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSwitcherCase4() : void
	{
		$postValues = new stdClass();
		$postValues->status = 'something';

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::switcher('status', 'post');

		$this->assertEquals(1, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSwitcherCase5() : void
	{
		$postValues = new stdClass();

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::switcher('status', 'get');

		$this->assertEquals(0, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSwitcherCase6() : void
	{
		$postValues = new stdClass();
		$postValues->status = 'something';

		$mockedSecurity = \Mockery::mock('alias:\System\Security');
		$mockedSecurity->shouldReceive(['xssClean' => $postValues]);

		$result = Request::switcher('status', 'get');

		$this->assertEquals(1, $result);
	}

	// Request::method()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMethodCase1() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(true);
		$mockedRequest->shouldReceive('header')->andReturn('PUT');

		$result = $mockedRequest->method();

		$this->assertEquals('PUT', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMethodCase2() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->method();

		$this->assertEquals('', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMethodCase3() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'get';

		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->method();

		$this->assertEquals('GET', $result);

		unset($_SERVER['REQUEST_METHOD']);
	}

	// Reqeust::ip()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase1() : void
	{
		$mockedValidator = \Mockery::mock('alias:\System\Validator');
		$mockedValidator->shouldReceive(['isValidIp' => false]);

		$result = Request::ip();

		$this->assertEquals('0.0.0.0', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase2() : void
	{
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '75.184.124.93, 10.194.95.79';

		$mockedValidator = \Mockery::mock('alias:\System\Validator');
		$mockedValidator->shouldReceive(['isValidIp' => true]);

		$result = Request::ip();

		$this->assertEquals('75.184.124.93', $result);

		unset($_SERVER['HTTP_X_FORWARDED_FOR']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase3() : void
	{
		$_SERVER['HTTP_CLIENT_IP'] = '75.184.124.93, 10.194.95.79';

		$mockedValidator = \Mockery::mock('alias:\System\Validator');
		$mockedValidator->shouldReceive(['isValidIp' => true]);

		$result = Request::ip();

		$this->assertEquals('75.184.124.93', $result);

		unset($_SERVER['HTTP_CLIENT_IP']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase4() : void
	{
		$_SERVER['REMOTE_ADDR'] = '223.24.187.34';

		$mockedValidator = \Mockery::mock('alias:\System\Validator');
		$mockedValidator->shouldReceive(['isValidIp' => true]);

		$result = Request::ip();

		$this->assertEquals('223.24.187.34', $result);

		unset($_SERVER['REMOTE_ADDR']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase5() : void
	{
		putenv('HTTP_X_FORWARDED_FOR=75.184.124.93, 10.194.95.79');

		$mockedValidator = \Mockery::mock('alias:\System\Validator');
		$mockedValidator->shouldReceive(['isValidIp' => true]);

		$result = Request::ip();

		$this->assertEquals('75.184.124.93', $result);

		putenv('HTTP_X_FORWARDED_FOR');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase6() : void
	{
		putenv('HTTP_CLIENT_IP=75.184.124.93, 10.194.95.79');

		$mockedValidator = \Mockery::mock('alias:\System\Validator');
		$mockedValidator->shouldReceive(['isValidIp' => true]);

		$result = Request::ip();

		$this->assertEquals('75.184.124.93', $result);

		putenv('HTTP_CLIENT_IP');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIpCase7() : void
	{
		putenv('REMOTE_ADDR=223.24.187.34');

		$mockedValidator = \Mockery::mock('alias:\System\Validator');
		$mockedValidator->shouldReceive(['isValidIp' => true]);

		$result = Request::ip();

		$this->assertEquals('223.24.187.34', $result);

		putenv('REMOTE_ADDR');
	}

	// Request::host()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodHostCase1() : void
	{
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['SERVER_PORT'] = 80;

		$result = Request::host();

		$this->assertEquals('http://localhost', $result);

		unset($_SERVER['HTTP_HOST']);
		unset($_SERVER['SERVER_PORT']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodHostCase2() : void
	{
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['SERVER_PORT'] = 443;

		$result = Request::host();

		$this->assertEquals('https://localhost', $result);

		unset($_SERVER['HTTP_HOST']);
		unset($_SERVER['SERVER_PORT']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodHostCase3() : void
	{
		$result = Request::host();

		$this->assertEmpty('', $result);
	}

	// Request::basePath()

	public function testMethodBasePathCase1() : void
	{
		$_SERVER['SCRIPT_NAME'] = '/vanda/index.php';

		$result = Request::basePath();

		$this->assertEquals('/vanda', $result);

		unset($_SERVER['SCRIPT_NAME']);
	}

	// Request::uri()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase1() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.4.41 (Ubuntu)';
		$_SERVER['SCRIPT_NAME'] = '/index.php'; // for method Request::basePath()
		$_SERVER['REQUEST_URI'] = '/index.php?arg=value';

		$result = Request::uri();

		$this->assertEquals('/index.php?arg=value', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['SCRIPT_NAME']);
		unset($_SERVER['REQUEST_URI']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase11() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.4.41 (Ubuntu)';
		$_SERVER['SCRIPT_NAME'] = '/index.php'; // for method Request::basePath()
		$_SERVER['REQUEST_URI'] = '/index.php';

		$result = Request::uri();

		$this->assertEquals('/index.php', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['SCRIPT_NAME']);
		unset($_SERVER['REQUEST_URI']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase2() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.4.41 (Ubuntu)';
		$_SERVER['SCRIPT_NAME'] = '/vanda/index.php'; // for method Request::basePath()
		$_SERVER['REQUEST_URI'] = '/vanda/index.php?arg=value';

		$result = Request::uri();

		$this->assertEquals('/index.php?arg=value', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['SCRIPT_NAME']);
		unset($_SERVER['REQUEST_URI']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase3() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.4.41 (Ubuntu)';
		$_SERVER['SCRIPT_NAME'] = '/vanda/index.php'; // for method Request::basePath()
		$_SERVER['REQUEST_URI'] = '/vanda/index.php';

		$result = Request::uri();

		$this->assertEquals('/index.php', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['SCRIPT_NAME']);
		unset($_SERVER['REQUEST_URI']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase4() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Microsoft-IIS/10.0';
		$_SERVER['SCRIPT_NAME'] = '/index.php'; // for method Request::basePath() and IIS
		$_SERVER['QUERY_STRING'] = 'arg=value';

		$result = Request::uri();

		$this->assertEquals('/index.php?arg=value', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['SCRIPT_NAME']);
		unset($_SERVER['QUERY_STRING']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase5() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Microsoft-IIS/10.0';
		$_SERVER['SCRIPT_NAME'] = '/index.php'; // for method Request::basePath() and IIS

		$result = Request::uri();

		$this->assertEquals('/index.php', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['SCRIPT_NAME']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase6() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Microsoft-IIS/10.0';
		$_SERVER['SCRIPT_NAME'] = '/vanda/index.php'; // for method Request::basePath() and IIS
		$_SERVER['QUERY_STRING'] = 'arg=value';

		$result = Request::uri();

		$this->assertEquals('/index.php?arg=value', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['SCRIPT_NAME']);
		unset($_SERVER['QUERY_STRING']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase7() : void
	{
		$_SERVER['SERVER_SOFTWARE'] = 'Microsoft-IIS/10.0';
		$_SERVER['SCRIPT_NAME'] = '/vanda/index.php'; // for method Request::basePath() and IIS

		$result = Request::uri();

		$this->assertEquals('/index.php', $result);

		unset($_SERVER['SERVER_SOFTWARE']);
		unset($_SERVER['SCRIPT_NAME']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodUriCase8() : void
	{
		// CLI mode must return ''
		$result = Request::uri();

		$this->assertEquals('', $result);
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

	// Request::isSecure()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsSecureCase1() : void
	{
		$_SERVER['HTTPS'] = '0';
		$_SERVER['SERVER_PORT'] = '80';

		$result = Request::isSecure();

		$this->assertFalse($result);

		unset($_SERVER['HTTPS']);
		unset($_SERVER['SERVER_PORT']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsSecureCase2() : void
	{
		$_SERVER['HTTPS'] = '1';

		$result = Request::isSecure();

		$this->assertTrue($result);

		unset($_SERVER['HTTPS']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsSecureCase3() : void
	{
		$_SERVER['HTTPS'] = 'on';

		$result = Request::isSecure();

		$this->assertTrue($result);

		unset($_SERVER['HTTPS']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsSecureCase4() : void
	{
		$_SERVER['SERVER_PORT'] = '443';

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

		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isGet();

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

		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isGet();

		$this->assertFalse($result);

		unset($_SERVER['REQUEST_METHOD']);
	}

	// Request::isOptions()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsOptionsCase1() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(true);
		$mockedRequest->shouldReceive('header')->andReturn('OPTIONS');

		$result = $mockedRequest->isOptions();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsOptionsCase2() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isOptions();

		$this->assertFalse($result);
	}

	// Request::isHead()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsHeadCase1() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(true);
		$mockedRequest->shouldReceive('header')->andReturn('HEAD');

		$result = $mockedRequest->isHead();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsHeadCase2() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isHead();

		$this->assertFalse($result);
	}

	// Request::isPost()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPostCase1() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isPost();

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

		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isPost();

		$this->assertFalse($result);

		unset($_SERVER['REQUEST_METHOD']);
	}

	// Request::isDelete()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsDeleteCase1() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(true);
		$mockedRequest->shouldReceive('header')->andReturn('DELETE');

		$result = $mockedRequest->isDelete();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsDeleteCase2() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isDelete();

		$this->assertFalse($result);
	}

	// Request::isPut()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPutCase1() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(true);
		$mockedRequest->shouldReceive('header')->andReturn('PUT');

		$result = $mockedRequest->isPut();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPutCase2() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isPut();

		$this->assertFalse($result);
	}

	// Request::isPatch()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPatchCase1() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(true);
		$mockedRequest->shouldReceive('header')->andReturn('PATCH');

		$result = $mockedRequest->isPatch();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPatchCase2() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('hasHeader')->andReturn(false);

		$result = $mockedRequest->isPatch();

		$this->assertFalse($result);
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

	// Request::ensureIsGet()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsGetCase1() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('isGet')->andReturn(false);

		$mockedResponse = \Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$mockedUrl = \Mockery::mock('alias:\System\Url');
		$mockedUrl->shouldReceive('default')->andReturn('http://localhost');
		$mockedUrl->shouldReceive('create')->andReturn('http://localhost');

		$mockedRequest->ensureIsGet();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsGetCase2() : void
	{
		$mockedRequest = \Mockery::mock('\System\Request')->makePartial();
		$mockedRequest->shouldReceive('isGet')->andReturn(true);

		$mockedResponse = \Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$mockedUrl = \Mockery::mock('alias:\System\Url');
		$mockedUrl->shouldReceive('default')->andReturn('http://localhost');
		$mockedUrl->shouldReceive('create')->andReturn('http://localhost');

		$mockedRequest->ensureIsGet();

		$this->assertTrue(true);
	}
}
