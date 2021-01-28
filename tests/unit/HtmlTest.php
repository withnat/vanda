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
 * Class XmlTest
 * @package Tests\Unit
 */
final class HtmlTest extends TestCase
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

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive(['isSPA' => false]);

		$result = Html::link();

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase3() : void
	{
		$expected = '<a style="font-weight:bold;" href="http://localhost">User Management System</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost']);

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive(['isSPA' => false]);

		$result = Html::link('user', 'User Management System', 'style="font-weight:bold;"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase4() : void
	{
		$expected = '<a style="font-weight:bold;" href="http://localhost">User Management System</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost']);

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive(['isSPA' => false]);

		$result = Html::link('user', 'User Management System', ['style' => 'font-weight:bold;']);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase5() : void
	{
		$expected = '<a href="#user" data-url="http://localhost">http://localhost</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive(['create' => 'http://localhost']);
		$stubUrl->shouldReceive(['hashSPA' => '#user']);

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive(['isSPA' => true]);

		$result = Html::link();

		$this->assertEquals($expected, $result);
	}
}
