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
 * @link         http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use System\Number;

/**
 * Class NumberTest
 * @package Tests\Unit
 */
class NumberTest extends TestCase
{
	protected function tearDown() : void
	{
		Mockery::close();
	}

	// Number::byteFormat()

	public function testMethodByteFormatCase1() : void
	{
		$result = Number::byteFormat(1000, 2);

		$this->assertEquals('1,000 B', $result);
	}

	public function testMethodByteFormatCase2() : void
	{
		$result = Number::byteFormat(1024, 2);

		$this->assertEquals('1 KB', $result);
	}

	public function testMethodByteFormatCase3() : void
	{
		$result = Number::byteFormat(1000, 2, 'KB');

		$this->assertEquals('0.98 KB', $result);
	}

	public function testMethodByteFormatCase4() : void
	{
		$result = Number::byteFormat(100000, 2, 'MB');

		$this->assertEquals('0.1 MB', $result);
	}

	public function testMethodByteFormatCase5() : void
	{
		$result = Number::byteFormat(1000000000, 2, 'GB');

		$this->assertEquals('0.93 GB', $result);
	}

	public function testMethodByteFormatCase6() : void
	{
		$result = Number::byteFormat(1000000000000, 2, 'TB');

		$this->assertEquals('0.91 TB', $result);
	}

	// Number::getUnitByFileSize()

	public function testMethodGetUnitByFileSizeCase1() : void
	{
		$result = Number::getUnitByFileSize(1000);

		$this->assertEquals('B', $result);
	}

	public function testMethodGetUnitByFileSizeCase2() : void
	{
		$result = Number::getUnitByFileSize(1000000);

		$this->assertEquals('KB', $result);
	}

	public function testMethodGetUnitByFileSizeCase3() : void
	{
		$result = Number::getUnitByFileSize(1000000000);

		$this->assertEquals('MB', $result);
	}

	public function testMethodGetUnitByFileSizeCase4() : void
	{
		$result = Number::getUnitByFileSize(1000000000000);

		$this->assertEquals('GB', $result);
	}

	public function testMethodGetUnitByFileSizeCase5() : void
	{
		$result = Number::getUnitByFileSize(1000000000000000);

		$this->assertEquals('TB', $result);
	}

	// Number::getFileSizeByUnit() (tested via above test case for Number::byteFormat())

	// Number::inrange()

		/**
		 * @runInSeparateProcess
		 * @preserveGlobalState disabled
		 */
	public function testMethodInRangeCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('int or float');

		$this->expectException(InvalidArgumentException::class);

		Number::inrange('value', 1, 100);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodInRangeCase2() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('int or float');

		$this->expectException(InvalidArgumentException::class);

		Number::inrange(50, 'value', 100);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodInRangeCase3() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('int or float');

		$this->expectException(InvalidArgumentException::class);

		Number::inrange(50, 1, 'value');
	}

	public function testMethodInRangeCase4() : void
	{
		$result = Number::inrange(50, 1, 100);

		$this->assertEquals(50, $result);
	}

	public function testMethodInRangeCase5() : void
	{
		$result = Number::inrange(120, 1, 100);

		$this->assertEquals(100, $result);
	}

	public function testMethodInRangeCase6() : void
	{
		$result = Number::inrange(-20, 1, 100);

		$this->assertEquals(1, $result);
	}
}
