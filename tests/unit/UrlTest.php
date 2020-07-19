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
use System\Url;
use PHPUnit\Framework\TestCase;

/**
 * Class UrlTest
 * @package Tests\Unit
 */
final class UrlTest extends TestCase
{
	public function createBackendUrlWithSefProvider()
	{
		return [
			['foo', false, 'http://localhost/admin/foo'],
			['foo', true, 'https://localhost/admin/foo'],
			['foo/bar', false, 'http://localhost/admin/foo/bar'],
			['foo/bar', true, 'https://localhost/admin/foo/bar'],
		];
	}

	public function createBackendUrlWithoutSefProvider()
	{
		return [
			['foo', false, 'http://localhost/index.php/admin/foo'],
			['foo', true, 'https://localhost/index.php/admin/foo'],
			['foo/bar', false, 'http://localhost/index.php/admin/foo/bar'],
			['foo/bar', true, 'https://localhost/index.php/admin/foo/bar'],
		];
	}

	public function createFrontendUrlWithSefProvider()
	{
		return [
			['foo', '', false, 'http://localhost/foo'],
			['foo', '', true, 'https://localhost/foo'],
			['foo/bar', '', false, 'http://localhost/foo/bar'],
			['foo/bar', '', true, 'https://localhost/foo/bar'],
			['foo', 'en', false, 'http://localhost/en/foo'],
			['foo', 'en', true, 'https://localhost/en/foo'],
			['foo/bar', 'en', false, 'http://localhost/en/foo/bar'],
			['foo/bar', 'en', true, 'https://localhost/en/foo/bar']
		];
	}

	public function createFrontendUrlWithoutSefProvider()
	{
		return [
			['foo', '', false, 'http://localhost/index.php/foo'],
			['foo', '', true, 'https://localhost/index.php/foo'],
			['foo/bar', '', false, 'http://localhost/index.php/foo/bar'],
			['foo/bar', '', true, 'https://localhost/index.php/foo/bar'],
			['foo', 'en', false, 'http://localhost/index.php/en/foo'],
			['foo', 'en', true, 'https://localhost/index.php/en/foo'],
			['foo/bar', 'en', false, 'http://localhost/index.php/en/foo/bar'],
			['foo/bar', 'en', true, 'https://localhost/index.php/en/foo/bar']
		];
	}

	// Url::create()

	public function testCreateFromUrl()
	{
		$url = 'https://google.com';
		$expected = 'https://google.com';

		$result = Url::create($url);

		$this->assertEquals($expected, $result);
	}

	public function testCreateFromUrlAndForceToSecure()
	{
		$url = 'http://google.com';
		$expected = 'https://google.com';

		$result = Url::create($url, true);

		$this->assertEquals($expected, $result);
	}

	public function testCreateFromUrlAndForceToNotSecure()
	{
		$url = 'https://google.com';
		$expected = 'http://google.com';

		$result = Url::create($url, false);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @param string $string
	 * @param bool   $secure
	 * @param string $expected
	 * @dataProvider createBackendUrlWithSefProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateBackenUrldWithSef(string $string, bool $secure, string $expected) : void
	{
		putenv('SIDE=backend');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'http://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '1';
				elseif ($arg == 'backendpath')
					return '/admin';
				else
					return '';
			});

		$result = Url::create($string, $secure);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	/**
	 * @param string $string
	 * @param bool   $secure
	 * @param string $expected
	 * @dataProvider createBackendUrlWithoutSefProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateBackendUrlWithoutSef(string $string, bool $secure, string $expected) : void
	{
		putenv('SIDE=backend');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'http://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '0';
				elseif ($arg == 'backendpath')
					return '/admin';
				else
					return '';
			});

		$result = Url::create($string, $secure);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	/**
	 * @param string $string
	 * @param string $lang
	 * @param bool   $secure
	 * @param string $expected
	 * @dataProvider createFrontendUrlWithSefProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFrontenUrldWithSef(string $string, string $lang, bool $secure, string $expected) : void
	{
		putenv('SIDE=frontend');
		putenv('LANG=' . $lang);

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'http://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '1';
				else
					return '';
			});

		$result = Url::create($string, $secure);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	/**
	 * @param string $string
	 * @param string $lang
	 * @param bool   $secure
	 * @param string $expected
	 * @dataProvider createFrontendUrlWithoutSefProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFrontendUrlWithoutSef(string $string, string $lang, bool $secure, string $expected) : void
	{
		putenv('SIDE=frontend');
		putenv('LANG=' . $lang);

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'http://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '0';
				else
					return '';
			});

		$result = Url::create($string, $secure);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateUrlWithSecure() : void
	{
		putenv('SIDE=frontend');
		putenv('LANG=en');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'https://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '1';
				else
					return '';
			});

		$expected = 'http://localhost/en/contact';
		$result = Url::create('contact', false);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	// Url::createFromAction()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateFromActionThatHasOneDotInUri()
	{
		putenv('SIDE=admin');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'https://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '1';
				elseif ($arg == 'backendpath')
					return '/admin';
				else
					return '';
			});

		$expected = 'https://localhost/admin/user/add';
		$result = Url::createFromAction('user.add');
		$this->assertEquals($expected, $result);

		putenv('SIDE');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateFromActionThatHasTwoDotsInUri()
	{
		putenv('SIDE=admin');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'https://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '1';
				elseif ($arg == 'backendpath')
					return '/admin';
				else
					return '';
			});

		$expected = 'https://localhost/admin/user/group/add';
		$result = Url::createFromAction('user.group.add');
		$this->assertEquals($expected, $result);

		putenv('SIDE');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateFromActionThatModuleIsSameAsController()
	{
		putenv('SIDE=admin');
		putenv('MODULE=user');
		putenv('CONTROLLER=user');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'https://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '1';
				elseif ($arg == 'backendpath')
					return '/admin';
				else
					return '';
			});

		$expected = 'https://localhost/admin/user/add';
		$result = Url::createFromAction('add');
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('MODULE');
		putenv('CONTROLLER');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateFromActionThatModuleIsNotSameAsController()
	{
		putenv('SIDE=admin');
		putenv('MODULE=user');
		putenv('CONTROLLER=group');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['baseurl' => 'https://localhost']);

		$mockedSetting = \Mockery::mock('alias:\Setting');
		$mockedSetting->shouldReceive('get')
			->once()
			->andReturnUsing(function ($arg)
			{
				if ($arg == 'sef')
					return '1';
				elseif ($arg == 'backendpath')
					return '/admin';
				else
					return '';
			});

		$expected = 'https://localhost/admin/user/group/add';
		$result = Url::createFromAction('add');
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('MODULE');
		putenv('CONTROLLER');
	}

	// Url::hashSPA()

	public function testMethodHashSPACase1()
	{
		$expected = '#user';

		$result = Url::hashSPA('user');

		$this->assertEquals($expected, $result);
	}

	public function testMethodHashSPACase2()
	{
		$expected = '#user:1';

		$result = Url::hashSPA('user?id=1');

		$this->assertEquals($expected, $result);
	}

	public function testMethodHashSPACase3()
	{
		$expected = '#user?id=1&param=value';

		$result = Url::hashSPA('user?id=1&param=value');

		$this->assertEquals($expected, $result);
	}

	// Url::toContext()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodtoContextCase1()
	{
		$expected = 'httpslocalhost';

		$result = Url::toContext('https://localhost');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodtoContextCase2()
	{
		putenv('SIDE=admin');
		putenv('MODULE=user');
		putenv('CONTROLLER=user');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['url' => 'https://localhost/vanda/admin/user/user/modify?id=1']);

		$expected = 'httpslocalhostvandaadminusermodify';

		$result = Url::toContext();

		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('MODULE');
		putenv('CONTROLLER');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodtoContextCase3()
	{
		putenv('SIDE=admin');
		putenv('MODULE=user');
		putenv('CONTROLLER=group');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['url' => 'https://localhost/vanda/admin/user/group/modify?id=1']);

		$expected = 'httpslocalhostvandaadminusergroupmodify';

		$result = Url::toContext();

		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('MODULE');
		putenv('CONTROLLER');
	}

	// Url::encode()

	public function testMethodEncodeCase1()
	{
		$expected = 'aHR0cHM6Ly9sb2NhbGhvc3QvdmFuZGE_bmFtZT1OYXQrV2l0aGU=';

		$result = Url::encode('https://localhost/vanda?name=Nat+Withe');

		$this->assertEquals($expected, $result);
	}

	// Url::decode()

	public function testMethodDecodeCase1()
	{
		$expected = 'https://localhost/vanda?name=Nat+Withe';

		$result = Url::decode('aHR0cHM6Ly9sb2NhbGhvc3QvdmFuZGE_bmFtZT1OYXQrV2l0aGU=');

		$this->assertEquals($expected, $result);
	}

	// Url::parse()

	public function testMethodParseCase1()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';

		$expected = [
			'scheme' => 'http',
			'host' => 'hostname',
			'port' => '9090',
			'user' => 'username',
			'pass' => 'password',
			'path' => '/path',
			'query' => 'arg=value',
			'fragment' => 'anchor',
		];

		$result = Url::parse($url);

		$this->assertEquals($expected, $result);
	}

	// Url::getScheme()

	public function testMethodGetSchemeCase1()
	{
		$result = Url::getScheme();

		$this->assertNull($result);
	}

	public function testMethodGetSchemeCase2()
	{
		$result = Url::getScheme('');

		$this->assertNull($result);
	}

	public function testMethodGetSchemeCase3()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';
		$expected = 'http';

		$result = Url::getScheme($url);

		$this->assertEquals($expected, $result);
	}

	// Url::getUser()

	public function testMethodGetUserCase1()
	{
		$result = Url::getUser();

		$this->assertNull($result);
	}

	public function testMethodGetUserCase2()
	{
		$result = Url::getUser('');

		$this->assertNull($result);
	}

	public function testMethodGetUserCase3()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';
		$expected = 'username';

		$result = Url::getUser($url);

		$this->assertEquals($expected, $result);
	}

	// Url::getPass()

	public function testMethodGetPassCase1()
	{
		$result = Url::getPass('');

		$this->assertNull($result);
	}

	public function testMethodGetPassCase2()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';
		$expected = 'password';

		$result = Url::getPass($url);

		$this->assertEquals($expected, $result);
	}

	// Url::getHost()

	public function testMethodGetHostCase1()
	{
		$result = Url::getHost('');

		$this->assertNull($result);
	}

	public function testMethodGetHostCase2()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';
		$expected = 'hostname';

		$result = Url::getHost($url);

		$this->assertEquals($expected, $result);
	}

	// Url::getPort()

	public function testMethodGetPortCase1()
	{
		$result = Url::getPort('');

		$this->assertNull($result);
	}

	public function testMethodGetPortCase2()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';
		$expected = 9090;

		$result = Url::getPort($url);

		$this->assertIsInt($result);
		$this->assertEquals($expected, $result);
	}

	// Url::getPath()

	public function testMethodGetPathCase1()
	{
		$result = Url::getPath('');

		$this->assertNull($result);
	}

	public function testMethodGetPathCase2()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';
		$expected = '/path';

		$result = Url::getPath($url);

		$this->assertEquals($expected, $result);
	}

	// Url::getQuery()

	public function testMethodGetQueryCase1()
	{
		$result = Url::getQuery('');

		$this->assertNull($result);
	}

	public function testMethodGetQueryCase2()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';
		$expected = 'arg=value';

		$result = Url::getQuery($url);

		$this->assertEquals($expected, $result);
	}

	// Url::getFragment()

	public function testMethodGetFragmentCase1()
	{
		$result = Url::getFragment('');

		$this->assertNull($result);
	}

	public function testMethodGetFragmentCase2()
	{
		$url = 'http://username:password@hostname:9090/path?arg=value#anchor';
		$expected = 'anchor';

		$result = Url::getFragment($url);

		$this->assertEquals($expected, $result);
	}
}
