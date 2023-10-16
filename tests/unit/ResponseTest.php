<?php
/*
 * __      __             _
 * \ \    / /            | |
 *  \ \  / /_ _ _ __   __| | __ _
 *   \ \/ / _` | '_ \ / _` |/ _` |
 *    \  / (_| | | | | (_| | (_| |
 *     \/ \__,_|_| |_|\__,_|\__,_|
 *
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use System\Response;

/**
 * Class ResponseTest
 * @package Tests\Unit
 */
class ResponseTest extends TestCase
{
    protected function tearDown() : void
    {
        Mockery::close();
    }

	// Response::getStatusCode()

	public function testMethodGetStatusCodeCase1() : void
	{
		$result = Response::getStatusCode();

		$this->assertEquals(200, $result);
	}

	// Response::getStatusReason()

	public function testMethodGetStatusReasonCase1() : void
	{
		$result = Response::getStatusReason();

		$this->assertEquals('OK', $result);
	}

	public function testMethodGetStatusReasonCase2() : void
	{
		Response::setStatusCode(404);
		$result = Response::getStatusReason();

		$this->assertEquals('Not Found', $result);
	}

	// Response::setStatusCode()

	public function testMethodSetStatusCodeCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Response::setStatusCode(900);
	}

	public function testMethodSetStatusCodeCase2() : void
	{
		$result = Response::setStatusCode(404);

		$this->assertInstanceOf('System\Response', $result);
	}

	public function testMethodSetStatusCodeCase3() : void
	{
		Response::setStatusCode(404);

		$result = Response::getStatusCode();
		$this->assertEquals(404, $result);

		$result = Response::getStatusReason();
		$this->assertEquals('Not Found', $result);
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
		$expected = 'no-store';

		Response::setHeader('Cache-Control', 'no-cache')
			->setHeader('Cache-Control', 'no-store', true);

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
			['Pragma', 'cache'],
			['Cache-Control', 'no-cache'],
			['Cache-Control', 'no-store']
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
			['Cache-Control', 'no-store'],
			['Cache-Control', 'no-cache']
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
			['Cache-Control', 'no-store'],
			['Cache-Control', 'no-cache']
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
			['Cache-Control', 'no-store']
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
			['Pragma', 'cache']
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

	// Response::getBody()

	public function testMethodGetBodyCase1() : void
	{
		$result = Response::getBody();

		$this->assertEquals('', $result);
	}

	// Response::setBody()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetBodyCase1() : void
	{
		$result = Response::setBody('foo');

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetBodyCase2() : void
	{
		Response::setBody('foo');

		$result = Response::getBody();

		$this->assertEquals('foo', $result);
	}

	// Response::prependBody()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPrependBodyCase1() : void
	{
		$result = Response::prependBody('foo');

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPrependBodyCase2() : void
	{
		Response::setBody('foo');
		Response::prependBody('bar');

		$result = Response::getBody();

		$this->assertEquals('barfoo', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPrependBodyCase3() : void
	{
		Response::setBody('foo')
			->prependBody('bar');

		$result = Response::getBody();

		$this->assertEquals('barfoo', $result);
	}

	// Response::appendBody()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodAppendBodyCase1() : void
	{
		$result = Response::appendBody('foo');

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodAppendBodyCase2() : void
	{
		Response::setBody('foo');
		Response::appendBody('bar');

		$result = Response::getBody();

		$this->assertEquals('foobar', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodAppendBodyCase3() : void
	{
		Response::setBody('foo')
			->appendBody('bar');

		$result = Response::getBody();

		$this->assertEquals('foobar', $result);
	}

	// Response::setBody() & Response::prependBody() & Response::appendBody()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetAndPrependAndAppendBodyCase3() : void
	{
		Response::setBody('foo')
			->appendBody('bar')
			->prependBody('bla');

		$result = Response::getBody();

		$this->assertEquals('blafoobar', $result);
	}

	// Response::clearBody()

	public function testMethodClearBodyCase1() : void
	{
		$result = Response::clearBody();

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodClearBodyCase2() : void
	{
		Response::setBody('foo')
			->appendBody('bar')
			->prependBody('bla');

		Response::clearBody();

		$result = Response::getBody();

		$this->assertEquals('', $result);
	}

	// Response::redirect()

	public function testMethodRedirectCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Response::redirect('user', 900);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodRedirectCase2() : void
	{
		putenv('SIDE=frontend');
		putenv('FRONTEND_SPA_MODE=1');

		$expected = '{"title":"","content":"","redirect":"http:\/\/localhost"}';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnTrue();

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isAjax')->andReturnTrue();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		Response::redirect();

		$this->expectOutputString($expected);

		putenv('SIDE');
		putenv('FRONTEND_SPA_MODE');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodRedirectCase3() : void
	{
		$expected = "<script>document.location.href=\"http://localhost\";</script>\n";

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$response = Mockery::mock('System\Response');
		$response->shouldAllowMockingProtectedMethods()->makePartial();
		$response->shouldReceive('_isHeadersSent')->andReturnTrue();

		$response->redirect();

		$this->expectOutputString($expected);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodRedirectCase4() : void
	{
		$expectedStatusContent = '0; url=http://localhost/user';
		$expectedStatusCode = 302;

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/user');

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('server')->andReturn('Microsoft-IIS/10.0');

		$response = Mockery::mock('System\Response');
		$response->shouldAllowMockingProtectedMethods()->makePartial();
		$response->shouldReceive('_isHeadersSent')->andReturnFalse();

		$response->redirect('user', 302);

		$result = $response->getHeader('Refresh');
		$this->assertEquals($expectedStatusContent, $result);

		$result = $response->getStatusCode();
		$this->assertEquals($expectedStatusCode, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodRedirectCase5() : void
	{
		$expectedStatusContent = 'http://localhost/user';
		$expectedStatusCode = 302;

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/user');

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('server')->andReturn('Apache/2.4.41 (Ubuntu)');

		$response = Mockery::mock('System\Response');
		$response->shouldAllowMockingProtectedMethods()->makePartial();
		$response->shouldReceive('_isHeadersSent')->andReturnFalse();

		$response->redirect('user', 302);

		$result = $response->getHeader('Location');
		$this->assertEquals($expectedStatusContent, $result);

		$result = $response->getStatusCode();
		$this->assertEquals($expectedStatusCode, $result);
	}

	// Response::send() & Response::sendHeaders() & Response::sendBody()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSendCase1() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isCli')->andReturnTrue();

		$result = Response::send();

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSendCase2() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isCli')->andReturnFalse();
		$stubRequest->shouldReceive('protocol')->andReturn('HTTP/1.1');

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('charset', mb_internal_encoding())->andReturn('UTF-8');

		$result = Response::send();

		$this->assertInstanceOf('System\Response', $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSendCase3() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isCli')->andReturnFalse();
		$stubRequest->shouldReceive('protocol')->andReturn('HTTP/1.1');

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('charset', mb_internal_encoding())->andReturn('UTF-8');

		Response::setHeader('Pragma', 'cache');
		Response::setBody('Nat is handsome.');

		Response::send();

		$headers = xdebug_get_headers();

		$this->assertContains('Pragma: cache', $headers);
		$this->assertContains('content-type: text/html; charset=UTF-8', $headers);
		$this->expectOutputString('Nat is handsome.');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSendCase4() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isCli')->andReturnFalse();
		$stubRequest->shouldReceive('protocol')->andReturn('HTTP/1.1');

		Response::setHeader('Pragma', 'cache');
		Response::setHeader('content-type', 'text/html; charset=UTF-8');
		Response::setBody('Nat is handsome.');

		Response::send();

		$headers = xdebug_get_headers();

		$this->assertContains('Pragma: cache', $headers);
		$this->assertContains('content-type: text/html; charset=UTF-8', $headers);
		$this->expectOutputString('Nat is handsome.');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSendCase5() : void
	{
		$response = Mockery::mock('System\Response');
		$response->shouldAllowMockingProtectedMethods()->makePartial();
		$response->shouldReceive('_isHeadersSent')->andReturnTrue();

		$result = $response->sendHeaders();

		$this->assertInstanceOf('System\Response', $result);
	}


	// Response::spa()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSpaCase1() : void
	{
		$expected = '{"title":"Title","content":"Content","flash":null,"redirect":"#http:\/\/localhost"}';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('hashSpa')->andReturn('#http://localhost');

		$data = [
			'title' => 'Title',
			'content' => 'Content',
			'url' => 'http://localhost'
		];

		Response::spa($data);

		$this->expectOutputString($expected);
	}
}
