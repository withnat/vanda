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
use System\Cookie;

/**
 * Class CookieTest
 * @package Tests\Unit
 */
class CookieTest extends TestCase
{
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
}
