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
 * A lightweight & flexible PHP web framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use ErrorException;
use InvalidArgumentException;
use Mockery;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Cookie;

/**
 * Class CookieTest
 * @package Tests\Unit
 */
class CookieTest extends TestCase
{
	use PHPMock;

	protected function tearDown() : void
	{
		Mockery::close();
	}

	// Cookie::set()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Cookie::set('name', tmpfile());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetCase2() : void
	{
		Cookie::set('name', 'value');

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetCase3() : void
	{
		Cookie::set('name', ['foo' => 'bar']);

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetCase4() : void
	{
		$value = new stdClass();
		$value->foo = 'bar';

		Cookie::set('name', $value);

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetCase5() : void
	{
		Cookie::set('name', true);

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSetCase6() : void
	{
		$_SERVER['SERVER_PORT'] = 443;

		Cookie::set('name', 'value');

		$this->assertTrue(true);

		unset($_SERVER['SERVER_PORT']);
	}

	// Cookie::get()

	public function testMethodGetCase1() : void
	{
		$result = Cookie::get('name', 'default value');

		$this->assertEquals('default value', $result);
	}

	public function testMethodGetCase2() : void
	{
		$_COOKIE['__vandaCookie_name'] = 'Nat Withe';

		$result = Cookie::get('name');

		$this->assertEquals('Nat Withe', $result);

		unset($_COOKIE['__vandaCookie_name']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 * @throws ErrorException
	 */
	public function testMethodGetCase3() : void
	{
		$stubJson = Mockery::mock('alias:\System\Json');
		$stubJson->shouldReceive('isValid')->andReturnTrue();
		$stubJson->shouldReceive('decode')->once();

		Cookie::get('name');

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 * @throws ErrorException
	 */
	public function testMethodGetCase4() : void
	{
		$expected = [
			'name' => 'Nat Withe',
			'position' => 'Web Developer'
		];

		$stubJson = Mockery::mock('alias:\System\Json');
		$stubJson->shouldReceive('isValid')->andReturnTrue();
		$stubJson->shouldReceive('decode')->andReturn([
			'__vandaCookieDatatype' => 'array',
			'__vandaCookieValue' => [
				'name' => 'Nat Withe',
				'position' => 'Web Developer'
			]
		]);

		$result = Cookie::get('people');

		$this->assertIsArray($result);
		$this->assertEquals($expected['name'], $result['name']);
		$this->assertEquals($expected['position'], $result['position']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 * @throws ErrorException
	 */
	public function testMethodGetCase5() : void
	{
		$stubJson = Mockery::mock('alias:\System\Json');
		$stubJson->shouldReceive('isValid')->andReturnTrue();
		$stubJson->shouldReceive('decode')->andReturn([
			'__vandaCookieDatatype' => 'object',
			'__vandaCookieValue' => [
				'name' => 'Nat Withe',
				'position' => 'Web Developer'
			]
		]);

		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('toObject')->once();

		$result = Cookie::get('people');

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 * @throws ErrorException
	 */
	public function testMethodGetCase6() : void
	{
		$stubJson = Mockery::mock('alias:\System\Json');
		$stubJson->shouldReceive('isValid')->andReturnTrue();
		$stubJson->shouldReceive('decode')->andReturn([
			'__vandaCookieDatatype' => 'bool',
			'__vandaCookieValue' => 'true'
		]);

		$result = Cookie::get('name');

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 * @throws ErrorException
	 */
	public function testMethodGetCase7() : void
	{
		$stubJson = Mockery::mock('alias:\System\Json');
		$stubJson->shouldReceive('isValid')->andReturnTrue();
		$stubJson->shouldReceive('decode')->andReturn([
			'__vandaCookieDatatype' => 'bool',
			'__vandaCookieValue' => 'false'
		]);

		$result = Cookie::get('name');

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase1() : void
	{
		$_COOKIE['__vandaCookie_name'] = 'Nat Withe';
		$_COOKIE['other'] = 'Lorem';

		$stubSetCookie = $this->getFunctionMock('System', 'setcookie');
		$stubSetCookie->expects($this->once());

		Cookie::clear();
	}
}
