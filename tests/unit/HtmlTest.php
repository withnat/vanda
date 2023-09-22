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
use System\Html;

/**
 * Class HtmlTest
 * @package Tests\Unit
 */
class HtmlTest extends TestCase
{
	protected function tearDown() : void
	{
		Mockery::close();
	}

	// Html::link()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase1() : void
	{
		$mockedInflector = Mockery::mock('alias:\System\Inflector');
		$mockedInflector->shouldReceive(['sentence' => 'string, array or null']);

		$this->expectException(InvalidArgumentException::class);

		Html::link('user', 'User', new stdClass());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase2() : void
	{
		$expected = '<a href="http://localhost">http://localhost</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost']);

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive(['isBlank' => true]);

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive(['isSpa' => false]);

		$result = Html::link();

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase3() : void
	{
		$expected = '<a style="font-weight:bold;" href="http://localhost/user">Users</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost/user']);

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive(['isBlank' => false]);

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive(['isSpa' => false]);

		$result = Html::link('user', 'Users', 'style="font-weight:bold;"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase4() : void
	{
		$expected = '<a style="font-weight:bold;" href="http://localhost/user">Users</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost/user']);

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive(['toString' => 'style="font-weight:bold;"']);

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive(['isBlank' => false]);

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive(['isSpa' => false]);

		$result = Html::link('user', 'Users', ['style' => 'font-weight:bold;']);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase5() : void
	{
		$expected = '<a href="#user" data-url="http://localhost/user">http://localhost/user</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost/user']);
		$stubUrl->shouldReceive(['hashSpa' => '#user']);

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive(['isBlank' => true]);

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive(['isSpa' => true]);

		$result = Html::link('user');

		$this->assertEquals($expected, $result);
	}
}
