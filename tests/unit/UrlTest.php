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

use Mockery;
use PHPUnit\Framework\TestCase;
use System\Url;

/**
 * Class UrlTest
 * @package Tests\Unit
 */
class UrlTest extends TestCase
{
	protected static $_url = 'http://username:password@hostname:9090/path?arg=value#anchor';

    protected function tearDown() : void
    {
        Mockery::close();
    }

	public static function createBackendUrlWithSefProvider() : array
	{
		return [
			[null, false, 'http://localhost/admin'],
			[null, true, 'https://localhost/admin'],
			['', false, 'http://localhost/admin'],
			['', true, 'https://localhost/admin'],
			['foo', false, 'http://localhost/admin/foo'],
			['foo', true, 'https://localhost/admin/foo'],
			['foo/bar', false, 'http://localhost/admin/foo/bar'],
			['foo/bar', true, 'https://localhost/admin/foo/bar'],
		];
	}

	public static function createBackendUrlWithoutSefProvider() : array
	{
		return [
			[null, false, 'http://localhost/index.php/admin'],
			[null, true, 'https://localhost/index.php/admin'],
			['', false, 'http://localhost/index.php/admin'],
			['', true, 'https://localhost/index.php/admin'],
			['foo', false, 'http://localhost/index.php/admin/foo'],
			['foo', true, 'https://localhost/index.php/admin/foo'],
			['foo/bar', false, 'http://localhost/index.php/admin/foo/bar'],
			['foo/bar', true, 'https://localhost/index.php/admin/foo/bar'],
		];
	}

	public static function createFrontendUrlWithSefProvider() : array
	{
		return [
			[null, '', false, 'http://localhost'],
			[null, '', true, 'https://localhost'],
			['', '', false, 'http://localhost'],
			['', '', true, 'https://localhost'],
			[null, 'en', false, 'http://localhost/en'],
			[null, 'en', true, 'https://localhost/en'],
			['', 'en', false, 'http://localhost/en'],
			['', 'en', true, 'https://localhost/en'],
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

	public static function createFrontendUrlWithoutSefProvider() : array
	{
		return [
			[null, '', false, 'http://localhost/index.php'],
			[null, '', true, 'https://localhost/index.php'],
			['', '', false, 'http://localhost/index.php'],
			['', '', true, 'https://localhost/index.php'],
			[null, 'en', false, 'http://localhost/index.php/en'],
			[null, 'en', true, 'https://localhost/index.php/en'],
			['', 'en', false, 'http://localhost/index.php/en'],
			['', 'en', true, 'https://localhost/index.php/en'],
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

	// Url::base()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodBaseCase1()
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('host')->andReturn('http://localhost');
		$stubRequest->shouldReceive('basePath')->andReturn('');

		$expected = 'http://localhost';
		$result = Url::base();

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodBaseCase2()
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('host')->andReturn('https://localhost');
		$stubRequest->shouldReceive('basePath')->andReturn('');

		$expected = 'http://localhost';
		$result = Url::base(false);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodBaseCase3()
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('host')->andReturn('http://localhost');
		$stubRequest->shouldReceive('basePath')->andReturn('/vanda');

		$expected = 'https://localhost/vanda';
		$result = Url::base(true);

		$this->assertEquals($expected, $result);
	}

	// Url::create()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFrontendUrlFromEmpty()
	{
		putenv('APP_SIDE=frontend');

		$url = Mockery::mock('\System\Url')->makePartial();
		$url->shouldReceive('base')->andReturn('http://localhost/vanda');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')->with('sef')->andReturnTrue();

		$expected = 'http://localhost/vanda';
		$result = $url->create();

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateBackendUrlFromEmpty()
	{
		putenv('APP_SIDE=backend');

		$url = Mockery::mock('\System\Url')->makePartial();
		$url->shouldReceive('base')->andReturn('http://localhost/vanda');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')
			->andReturnUsing(function ($arg)
			{
				switch ($arg)
				{
					case 'sef':
						return true;
					case 'backendpath':
						return '/admin';
					default:
						return '';
				}
			});

		$expected = 'http://localhost/vanda/admin';
		$result = $url->create();

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
	}

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
	 * @param $uri
	 * @param $secure
	 * @param $expected
	 * @dataProvider createBackendUrlWithSefProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateBackenUrldWithSef($uri, $secure, $expected) : void
	{
		putenv('APP_SIDE=backend');

		$url = Mockery::mock('\System\Url')->makePartial();
		$url->shouldReceive('base')->andReturn('http://localhost');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')
			->andReturnUsing(function ($arg)
			{
				switch ($arg)
				{
					case 'sef':
						return true;
					case 'backendpath':
						return '/admin';
					default:
						return '';
				}
			});

		$result = $url->create($uri, $secure);

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
	}

	/**
	 * @param $uri
	 * @param $secure
	 * @param $expected
	 * @dataProvider createBackendUrlWithoutSefProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateBackendUrlWithoutSef($uri, $secure, $expected) : void
	{
		putenv('APP_SIDE=backend');

		$url = Mockery::mock('\System\Url')->makePartial();
		$url->shouldReceive('base')->andReturn('http://localhost');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')
			->andReturnUsing(function ($arg)
			{
				switch ($arg)
				{
					case 'sef':
						return false;
					case 'backendpath':
						return '/admin';
					default:
						return '';
				}
			});

		$result = $url->create($uri, $secure);

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
	}

	/**
	 * @param $uri
	 * @param $lang
	 * @param $secure
	 * @param $expected
	 * @dataProvider createFrontendUrlWithSefProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFrontenUrldWithSef($uri, $lang, $secure, $expected) : void
	{
		putenv('APP_SIDE=frontend');
		putenv('APP_LANG=' . $lang);

		$url = Mockery::mock('\System\Url')->makePartial();
		$url->shouldReceive('base')->andReturn('http://localhost');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')->with('sef')->andReturnTrue();

		$result = $url->create($uri, $secure);

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
		putenv('APP_LANG');
	}

	/**
	 * @param $uri
	 * @param $lang
	 * @param $secure
	 * @param $expected
	 * @dataProvider createFrontendUrlWithoutSefProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFrontendUrlWithoutSef($uri, $lang, $secure, $expected) : void
	{
		putenv('APP_SIDE=frontend');
		putenv('APP_LANG=' . $lang);

		$url = Mockery::mock('\System\Url')->makePartial();
		$url->shouldReceive('base')->andReturn('http://localhost');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')->with('sef')->andReturnFalse();

		$result = $url->create($uri, $secure);

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
		putenv('APP_LANG');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFullUrlWithPathParam() : void
	{
		putenv('APP_SIDE=frontend');
		putenv('APP_LANG=en');

		Url::setScheme('http');
		Url::setUser('user');
		Url::setPass('pass');
		Url::setHost('hostname');
		Url::setPort(9090);
		Url::setQuery('arg=value');
		Url::setFragment('anchor');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')->with('sef')->andReturnTrue();

		$expected = 'http://user:pass@hostname:9090/en/contact?arg=value#anchor';
		$result = Url::create('contact', false);

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
		putenv('APP_LANG');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFullUrlWithOutPathParam() : void
	{
		putenv('APP_SIDE=frontend');
		putenv('APP_LANG=en');

		Url::setScheme('http');
		Url::setUser('user');
		Url::setPass('pass');
		Url::setHost('hostname');
		Url::setPort(9090);
		Url::setPath('contact');
		Url::setQuery('arg=value');
		Url::setFragment('anchor');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')->with('sef')->andReturnTrue();

		$expected = 'http://user:pass@hostname:9090/en/contact?arg=value#anchor';
		$result = Url::create(null, false);

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
		putenv('APP_LANG');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFullUrlWithUserHasNoPass() : void
	{
		putenv('APP_SIDE=frontend');
		putenv('APP_LANG=en');

		Url::setScheme('http');
		Url::setUser('user');
		Url::setHost('hostname');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')->with('sef')->andReturnTrue();

		$expected = 'http://user@hostname/en';
		$result = Url::create(null, false);

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
		putenv('APP_LANG');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testCreateFullUrlWithPassButNoUser() : void
	{
		putenv('APP_SIDE=frontend');
		putenv('APP_LANG=en');

		Url::setScheme('http');
		Url::setPass('pass');
		Url::setHost('hostname');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')->with('sef')->andReturnTrue();

		$expected = 'http://:pass@hostname/en';
		$result = Url::create(null, false);

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
		putenv('APP_LANG');
	}

	// Url::createFromAction()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateFromActionThatHasOneDotInUri()
	{
		putenv('APP_SIDE=backend');

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('host')->andReturn('https://localhost');
		$stubRequest->shouldReceive('basePath')->andReturn('');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')
			->andReturnUsing(function ($arg)
			{
				switch ($arg)
				{
					case 'sef':
						return true;
					case 'backendpath':
						return '/admin';
					default:
						return '';
				}
			});

		$expected = 'https://localhost/admin/user/add';
		$result = Url::createFromAction('user.add');

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateFromActionThatHasTwoDotsInUri()
	{
		putenv('APP_SIDE=backend');

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('host')->andReturn('https://localhost');
		$stubRequest->shouldReceive('basePath')->andReturn('');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')
			->andReturnUsing(function ($arg)
			{
				switch ($arg)
				{
					case 'sef':
						return true;
					case 'backendpath':
						return '/admin';
					default:
						return '';
				}
			});

		$expected = 'https://localhost/admin/user/group/add';

		$result = Url::createFromAction('user.group.add');
		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateFromActionThatModuleIsSameAsController()
	{
		putenv('APP_SIDE=backend');
		putenv('APP_MODULE=user');
		putenv('APP_CONTROLLER=user');

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('host')->andReturn('https://localhost');
		$stubRequest->shouldReceive('basePath')->andReturn('');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')
			->andReturnUsing(function ($arg)
			{
				switch ($arg)
				{
					case 'sef':
						return true;
					case 'backendpath':
						return '/admin';
					default:
						return '';
				}
			});

		$expected = 'https://localhost/admin/user/add';
		$result = Url::createFromAction('add');

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
		putenv('APP_MODULE');
		putenv('APP_CONTROLLER');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateFromActionThatModuleIsNotSameAsController()
	{
		putenv('APP_SIDE=backend');
		putenv('APP_MODULE=user');
		putenv('APP_CONTROLLER=group');

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('host')->andReturn('https://localhost');
		$stubRequest->shouldReceive('basePath')->andReturn('');

		$stubSetting = Mockery::mock('alias:\Setting');
		$stubSetting->shouldReceive('get')
			->andReturnUsing(function ($arg)
			{
				switch ($arg)
				{
					case 'sef':
						return true;
					case 'backendpath':
						return '/admin';
					default:
						return '';
				}
			});

		$expected = 'https://localhost/admin/user/group/add';
		$result = Url::createFromAction('add');

		$this->assertEquals($expected, $result);

		putenv('APP_SIDE');
		putenv('APP_MODULE');
		putenv('APP_CONTROLLER');
	}

	// Url::isValid()

	public function testMethodIsValidCase1()
	{
		$result = Url::isValid('');

		$this->assertFalse($result);
	}

	public function testMethodIsValidCase2()
	{
		$result = Url::isValid('foo/bar');

		$this->assertFalse($result);
	}

	public function testMethodIsValidCase3()
	{
		$result = Url::isValid('http://localhost');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase4()
	{
		$result = Url::isValid('https://localhost');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase5()
	{
		$result = Url::isValid('mailto:nat@withnat.com');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase6()
	{
		$result = Url::isValid('tel:0816386632');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase7()
	{
		$result = Url::isValid('sms:0816386632');

		$this->assertTrue($result);
	}

	// Url::hashSpa()

	public function testMethodHashSPACase1()
	{
		$expected = '#user';
		$result = Url::hashSpa('user');

		$this->assertEquals($expected, $result);
	}

	public function testMethodHashSPACase2()
	{
		$expected = '#user:1';
		$result = Url::hashSpa('user?id=1');

		$this->assertEquals($expected, $result);
	}

	public function testMethodHashSPACase3()
	{
		$expected = '#user?id=1&param=value';
		$result = Url::hashSpa('user?id=1&param=value');

		$this->assertEquals($expected, $result);
	}

	public function testMethodHashSPACase4()
	{
		$expected = '#http://localhost';
		$result = Url::hashSpa('http://localhost');

		$this->assertEquals($expected, $result);
	}

	public function testMethodHashSPACase5()
	{
		$expected = '#http://localhost/index.php';
		$result = Url::hashSpa('http://localhost/index.php');

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
		putenv('APP_MODULE=user');
		putenv('APP_CONTROLLER=user');

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('url')->andReturn('https://localhost/vanda/admin/user/user/modify?id=1');

		$expected = 'httpslocalhostvandaadminusermodify';
		$result = Url::toContext();

		$this->assertEquals($expected, $result);

		putenv('APP_MODULE');
		putenv('APP_CONTROLLER');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodtoContextCase3()
	{
		putenv('APP_MODULE=user');
		putenv('APP_CONTROLLER=group');

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('url')->andReturn('https://localhost/vanda/admin/user/group/modify?id=1');

		$expected = 'httpslocalhostvandaadminusergroupmodify';
		$result = Url::toContext();

		$this->assertEquals($expected, $result);

		putenv('APP_MODULE');
		putenv('APP_CONTROLLER');
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

		$result = Url::parse(static::$_url);

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
		$expected = 'http';
		$result = Url::getScheme(static::$_url);

		$this->assertEquals($expected, $result);
	}

	// Url::setScheme()

	public function testMethodSetSchemeCase1()
	{
		$value = 'value';

		Url::setScheme($value);

		$result = Url::getScheme();

		$this->assertEquals($value, $result);
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
		$expected = 'username';
		$result = Url::getUser(static::$_url);

		$this->assertEquals($expected, $result);
	}

	// Url::setUser()

	public function testMethodSetUserCase1()
	{
		$value = 'value';

		Url::setUser($value);

		$result = Url::getUser();

		$this->assertEquals($value, $result);
	}

	// Url::getPass()

	public function testMethodGetPassCase1()
	{
		$result = Url::getPass();

		$this->assertNull($result);
	}

	public function testMethodGetPassCase2()
	{
		$result = Url::getPass('');

		$this->assertNull($result);
	}

	public function testMethodGetPassCase3()
	{
		$expected = 'password';
		$result = Url::getPass(static::$_url);

		$this->assertEquals($expected, $result);
	}

	// Url::setPass()

	public function testMethodSetPassCase1()
	{
		$value = 'value';

		Url::setPass($value);

		$result = Url::getPass();

		$this->assertEquals($value, $result);
	}

	// Url::getHost()

	public function testMethodGetHostCase1()
	{
		$result = Url::getHost();

		$this->assertNull($result);
	}

	public function testMethodGetHostCase2()
	{
		$result = Url::getHost('');

		$this->assertNull($result);
	}

	public function testMethodGetHostCase3()
	{
		$expected = 'hostname';
		$result = Url::getHost(static::$_url);

		$this->assertEquals($expected, $result);
	}

	// Url::setHost()

	public function testMethodSetHostCase1()
	{
		$value = 'value';

		Url::setHost($value);

		$result = Url::getHost();

		$this->assertEquals($value, $result);
	}

	// Url::getPort()

	public function testMethodGetPortCase1()
	{
		$result = Url::getPort();

		$this->assertNull($result);
	}

	public function testMethodGetPortCase2()
	{
		$result = Url::getPort('');

		$this->assertNull($result);
	}

	public function testMethodGetPortCase3()
	{
		$expected = 9090;
		$result = Url::getPort(static::$_url);

		$this->assertIsInt($result);
		$this->assertEquals($expected, $result);
	}

	// Url::setPort()

	public function testMethodSetPortCase1()
	{
		$value = 80;

		Url::setPort($value);

		$result = Url::getPort();

		$this->assertEquals($value, $result);
	}

	// Url::getPath()

	public function testMethodGetPathCase1()
	{
		$result = Url::getPath();

		$this->assertNull($result);
	}

	public function testMethodGetPathCase2()
	{
		$result = Url::getPath('');

		$this->assertNull($result);
	}

	public function testMethodGetPathCase3()
	{
		$expected = '/path';
		$result = Url::getPath(static::$_url);

		$this->assertEquals($expected, $result);
	}

	// Url::setPath()

	public function testMethodSetPathCase1()
	{
		$value = 'value';

		Url::setPath($value);

		$result = Url::getPath();

		$this->assertEquals($value, $result);
	}

	// Url::getQuery()

	public function testMethodGetQueryCase1()
	{
		$result = Url::getQuery();

		$this->assertNull($result);
	}

	public function testMethodGetQueryCase2()
	{
		$result = Url::getQuery('');

		$this->assertNull($result);
	}

	public function testMethodGetQueryCase3()
	{
		$expected = 'arg=value';
		$result = Url::getQuery(static::$_url);

		$this->assertEquals($expected, $result);
	}

	// Url::setQuery()

	public function testMethodSetQueryCase1()
	{
		$value = 'value';

		Url::setQuery($value);

		$result = Url::getQuery();

		$this->assertEquals($value, $result);
	}

	// Url::getFragment()

	public function testMethodGetFragmentCase1()
	{
		$result = Url::getFragment();

		$this->assertNull($result);
	}

	public function testMethodGetFragmentCase2()
	{
		$result = Url::getFragment('');

		$this->assertNull($result);
	}

	public function testMethodGetFragmentCase3()
	{
		$expected = 'anchor';
		$result = Url::getFragment(static::$_url);

		$this->assertEquals($expected, $result);
	}

	// Url::setFragment()

	public function testMethodSetFragmentCase1()
	{
		$value = 'value';

		Url::setFragment($value);

		$result = Url::getFragment();

		$this->assertEquals($value, $result);
	}
}
