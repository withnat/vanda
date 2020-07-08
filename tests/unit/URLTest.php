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
use System\URL;
use PHPUnit\Framework\TestCase;

/**
 * Class URLTest
 * @package Tests\Unit
 */
final class URLTest extends TestCase
{
	public function routeBackendWithSefProvider()
	{
		return [
			['foo', false, 'http://localhost/admin/foo'],
			['foo', true, 'https://localhost/admin/foo'],
			['foo/bar', false, 'http://localhost/admin/foo/bar'],
			['foo/bar', true, 'https://localhost/admin/foo/bar'],
		];
	}

	public function routeBackendWithoutSefProvider()
	{
		return [
			['foo', false, 'http://localhost/index.php/admin/foo'],
			['foo', true, 'https://localhost/index.php/admin/foo'],
			['foo/bar', false, 'http://localhost/index.php/admin/foo/bar'],
			['foo/bar', true, 'https://localhost/index.php/admin/foo/bar'],
		];
	}

	public function routeFrontendWithSefProvider()
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

	public function routeFrontendWithoutSefProvider()
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

	// URL::route()

	public function testRouteWithFullUrl()
	{
		$url = 'https://google.com';
		$expected = 'https://google.com';

		$result = URL::route($url);

		$this->assertEquals($expected, $result);
	}

	public function testRouteWithFullUrlAndForceToSecure()
	{
		$url = 'http://google.com';
		$expected = 'https://google.com';

		$result = URL::route($url, true);

		$this->assertEquals($expected, $result);
	}

	public function testRouteWithFullUrlAndForceToNotSecure()
	{
		$url = 'https://google.com';
		$expected = 'http://google.com';

		$result = URL::route($url, false);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @param string $string
	 * @param bool   $secure
	 * @param string $expected
	 * @dataProvider routeBackendWithSefProvider
	 * @runInSeparateProcess
	 */
	public function testRouteBackendWithSef(string $string, bool $secure, string $expected) : void
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

		$result = URL::route($string, $secure);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	/**
	 * @param string $string
	 * @param bool   $secure
	 * @param string $expected
	 * @dataProvider routeBackendWithoutSefProvider
	 * @runInSeparateProcess
	 */
	public function testRouteBackendWithoutSef(string $string, bool $secure, string $expected) : void
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

		$result = URL::route($string, $secure);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	/**
	 * @param string $string
	 * @param string $lang
	 * @param bool   $secure
	 * @param string $expected
	 * @dataProvider routeFrontendWithSefProvider
	 * @runInSeparateProcess
	 */
	public function testRouteFrontendWithSef(string $string, string $lang, bool $secure, string $expected) : void
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

		$result = URL::route($string, $secure);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	/**
	 * @param string $string
	 * @param string $lang
	 * @param bool   $secure
	 * @param string $expected
	 * @dataProvider routeFrontendWithoutSefProvider
	 * @runInSeparateProcess
	 */
	public function testRouteFrontendWithoutSef(string $string, string $lang, bool $secure, string $expected) : void
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

		$result = URL::route($string, $secure);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testRouteWithSecure() : void
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
		$result = URL::route('contact', false);
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('LANG');
	}

	// URL::routeByAction()

	/**
	 * @runInSeparateProcess
	 */
	public function testMethodRouteByActionThatHasOneDotInUri()
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
		$result = URL::routeByAction('user.add');
		$this->assertEquals($expected, $result);

		putenv('SIDE');
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testMethodRouteByActionThatHasTwoDotsInUri()
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
		$result = URL::routeByAction('user.group.add');
		$this->assertEquals($expected, $result);

		putenv('SIDE');
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testMethodRouteByActionThatModuleIsSameAsController()
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
		$result = URL::routeByAction('add');
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('MODULE');
		putenv('CONTROLLER');
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testMethodRouteByActionThatModuleIsNotSameAsController()
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
		$result = URL::routeByAction('add');
		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('MODULE');
		putenv('CONTROLLER');
	}

	// URL::hashSPA()

	public function testMethodHashSPACase1()
	{
		$expected = '#user';

		$result = URL::hashSPA('user');

		$this->assertEquals($expected, $result);
	}

	public function testMethodHashSPACase2()
	{
		$expected = '#user:1';

		$result = URL::hashSPA('user?id=1');

		$this->assertEquals($expected, $result);
	}

	public function testMethodHashSPACase3()
	{
		$expected = '#user?id=1&param=value';

		$result = URL::hashSPA('user?id=1&param=value');

		$this->assertEquals($expected, $result);
	}

	// URL::getContext()

	/**
	 * @runInSeparateProcess
	 */
	public function testMethodGetContextCase1()
	{
		putenv('SIDE=admin');
		putenv('MODULE=user');
		putenv('CONTROLLER=user');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['url' => 'https://localhost/vanda/admin/user/user/modify?id=1']);

		$expected = 'httpslocalhostvandaadminusermodify';

		$result = URL::getContext();

		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('MODULE');
		putenv('CONTROLLER');
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testMethodGetContextCase2()
	{
		putenv('SIDE=admin');
		putenv('MODULE=user');
		putenv('CONTROLLER=group');

		$mockedRequest = \Mockery::mock('alias:\System\Request');
		$mockedRequest->shouldReceive(['url' => 'https://localhost/vanda/admin/user/group/modify?id=1']);

		$expected = 'httpslocalhostvandaadminusergroupmodify';

		$result = URL::getContext();

		$this->assertEquals($expected, $result);

		putenv('SIDE');
		putenv('MODULE');
		putenv('CONTROLLER');
	}

	// URL::encode()

	public function testMethodEncodeCase1()
	{
		$expected = 'aHR0cHM6Ly9sb2NhbGhvc3QvdmFuZGE_bmFtZT1OYXQrV2l0aGU=';

		$result = URL::encode('https://localhost/vanda?name=Nat+Withe');

		$this->assertEquals($expected, $result);
	}

	// URL::decode()

	public function testMethodDecodeCase1()
	{
		$expected = 'https://localhost/vanda?name=Nat+Withe';

		$result = URL::decode('aHR0cHM6Ly9sb2NhbGhvc3QvdmFuZGE_bmFtZT1OYXQrV2l0aGU=');

		$this->assertEquals($expected, $result);
	}
}
