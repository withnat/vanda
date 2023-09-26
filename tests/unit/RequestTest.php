<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2021, Nat Withe. All rights reserved.
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Request;

/**
 * Class RequestTest
 * @package Tests\Unit
 */
class RequestTest extends TestCase
{
    protected function tearDown() : void
    {
        Mockery::close();
    }

	// Request::set()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string, int or float');

		$this->expectException(InvalidArgumentException::class);

		Request::set('arg', new stdClass());
	}

	public function testMethodSetCase2() : void
	{
		$this->expectException(InvalidArgumentException::class);

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

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string, int, float, array, object or null');

		$this->expectException(InvalidArgumentException::class);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($getValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($getValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($getValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($getValues);

		$result = Request::get();

		$this->assertNull($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetCase4() : void
	{
		$getValues = new stdClass();
		$getValues->arg = 'value';

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($getValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($getValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($getValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($getValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($getValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($getValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($getValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($getValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($getValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($getValues);

		$result = Request::get('form.product.price', 'default');

		$this->assertEquals('default', $result);
	}

	// Request::post()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string, int, float, array, object or null');

		$this->expectException(InvalidArgumentException::class);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($postValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

		$result = Request::post();

		$this->assertEquals($postValues, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase3() : void
	{
		$getValues = new stdClass();

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($getValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($getValues);

		$result = Request::post();

		$this->assertNull($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPostCase4() : void
	{
		$postValues = new stdClass();
		$postValues->arg = 'value';

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($postValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($postValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($postValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($postValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn($postValues);

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

		$result = Request::post('form.product.price', 'default');

		$this->assertEquals('default', $result);
	}

	// Request::switcher()

	public function testMethodSwitcherCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Request::switcher('status', 'InvalidMethod');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSwitcherCase2() : void
	{
		$postValues = new stdClass();

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn(new stdClass());

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn(new stdClass());

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn(new stdClass());

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn(new stdClass());

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn(new stdClass());

		$stubSecurity = Mockery::mock('alias:\System\Security');
		$stubSecurity->shouldReceive('xssClean')->andReturn($postValues);

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
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnTrue();
		$request->shouldReceive('header')->andReturn('PUT');

		$result = $request->method();

		$this->assertEquals('PUT', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMethodCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->method();

		$this->assertEquals('', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMethodCase3() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'get';

		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->method();

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
		$stubValidator = Mockery::mock('alias:\System\Validator');
		$stubValidator->shouldReceive('isValidIp')->andReturnFalse();

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

		$stubValidator = Mockery::mock('alias:\System\Validator');
		$stubValidator->shouldReceive('isValidIp')->andReturnTrue();

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

		$stubValidator = Mockery::mock('alias:\System\Validator');
		$stubValidator->shouldReceive('isValidIp')->andReturnTrue();

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

		$stubValidator = Mockery::mock('alias:\System\Validator');
		$stubValidator->shouldReceive('isValidIp')->andReturnTrue();

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

		$stubValidator = Mockery::mock('alias:\System\Validator');
		$stubValidator->shouldReceive('isValidIp')->andReturnTrue();

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

		$stubValidator = Mockery::mock('alias:\System\Validator');
		$stubValidator->shouldReceive('isValidIp')->andReturnTrue();

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

		$stubValidator = Mockery::mock('alias:\System\Validator');
		$stubValidator->shouldReceive('isValidIp')->andReturnTrue();

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

	/**
	 * @runInSeparateProcess Prevent store static property $_isSecure in the Request class.
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

	// Request::header()

	public function testMethodHeaderCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([
			'Host' => 'localhost',
			'Connection' => 'keep-alive'
		]);

		$result = $request->header('host');

		$this->assertEquals('localhost', $result);
	}

	public function testMethodHeaderCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([
			'Host' => 'localhost',
			'Connection' => 'keep-alive'
		]);

		$result = $request->header('NotExistKey');

		$this->assertNull($result);
	}

	public function testMethodHeaderCase3() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([
			'Host' => 'localhost',
			'Connection' => 'keep-alive'
		]);

		$result = $request->header('NotExistKey', 'default');

		$this->assertEquals('default', $result);
	}

	// Request::hasHeader()

	public function testMethodHasHeaderCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([
			'Host' => 'localhost',
			'Connection' => 'keep-alive'
		]);

		$result = $request->hasHeader('host');

		$this->assertTrue($result);
	}

	public function testMethodHasHeaderCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([]);

		$result = $request->hasHeader('host');

		$this->assertFalse($result);
	}

	// Request::hasAnyHeader()

	public function testMethodHasAnyHeaderCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([
			'Host' => 'localhost',
			'Connection' => 'keep-alive'
		]);

		$result = $request->hasAnyHeader(['host', 'NotExistKey']);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyHeaderCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([
			'Host' => 'localhost',
			'Connection' => 'keep-alive'
		]);

		$result = $request->hasAnyHeader(['NotExistKey']);

		$this->assertFalse($result);
	}

	// Request::hasAllHeaders()

	public function testMethodHasAllHeadersCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([
			'Host' => 'localhost',
			'Connection' => 'keep-alive'
		]);

		$result = $request->hasAllHeaders(['host', 'NotExistKey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllHeadersCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('allHeaders')->andReturn([
			'Host' => 'localhost',
			'Connection' => 'keep-alive'
		]);

		$result = $request->hasAllHeaders(['host', 'connection']);

		$this->assertTrue($result);
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

		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isGet();

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

		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isGet();

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
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnTrue();
		$request->shouldReceive('header')->andReturn('OPTIONS');

		$result = $request->isOptions();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsOptionsCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isOptions();

		$this->assertFalse($result);
	}

	// Request::isHead()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsHeadCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnTrue();
		$request->shouldReceive('header')->andReturn('HEAD');

		$result = $request->isHead();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsHeadCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isHead();

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

		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isPost();

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

		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isPost();

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
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnTrue();
		$request->shouldReceive('header')->andReturn('DELETE');

		$result = $request->isDelete();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsDeleteCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isDelete();

		$this->assertFalse($result);
	}

	// Request::isPut()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPutCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnTrue();
		$request->shouldReceive('header')->andReturn('PUT');

		$result = $request->isPut();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPutCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isPut();

		$this->assertFalse($result);
	}

	// Request::isPatch()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPatchCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnTrue();
		$request->shouldReceive('header')->andReturn('PATCH');

		$result = $request->isPatch();

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPatchCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('hasHeader')->andReturnFalse();

		$result = $request->isPatch();

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

	// Request::isPjax()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPjaxCase1() : void
	{
		$result = Request::isPjax();

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsPjaxCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isAjax')->andReturnTrue();
		$request->shouldReceive('hasHeader')->andReturnTrue();

		$result = $request->isPjax();

		$this->assertTrue($result);
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
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isGet')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsGet();

		// If this test fails, it will stop before returning true below.
		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsGetCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isGet')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsGet();

		$this->assertTrue(true);
	}

	// Request::ensureIsOptions()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsOptionsCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isOptions')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsOptions();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsOptionsCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isOptions')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsOptions();

		$this->assertTrue(true);
	}

	// Request::ensureIsHead()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsHeadCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isHead')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsHead();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsHeadCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isHead')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsHead();

		$this->assertTrue(true);
	}

	// Request::ensureIsPost()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsPostCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isPost')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsPost();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsPostCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isPost')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsPost();

		$this->assertTrue(true);
	}

	// Request::ensureIsDelete()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsDeleteCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isDelete')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsDelete();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsDeleteCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isDelete')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsDelete();

		$this->assertTrue(true);
	}

	// Request::ensureIsPut()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsPutCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isPut')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsPut();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsPutCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isPut')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsPut();

		$this->assertTrue(true);
	}

	// Request::ensureIsPatch()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsPatchCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isPatch')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsPatch();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsPatchCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isPatch')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsPatch();

		$this->assertTrue(true);
	}

	// Request::ensureIsAjax()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsAjaxCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isAjax')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsAjax();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsAjaxCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isAjax')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsAjax();

		$this->assertTrue(true);
	}

	// Request::ensureIsPjax()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsPjaxCase1() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isPjax')->andReturnFalse();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->once();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('default')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$request->ensureIsPjax();

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodEnsureIsPjaxCase2() : void
	{
		$request = Mockery::mock('\System\Request')->makePartial();
		$request->shouldReceive('isPjax')->andReturnTrue();

		$mockedResponse = Mockery::mock('alias:\System\Response');
		$mockedResponse->shouldReceive('redirect')->never();

		$request->ensureIsPjax();

		$this->assertTrue(true);
	}
}
