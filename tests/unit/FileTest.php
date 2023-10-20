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

use Mockery;
use PHPUnit\Framework\TestCase;
use System\File;

/**
 * Class FileTest
 * @package Tests\Unit
 */
class FileTest extends TestCase
{
	use \phpmock\phpunit\PHPMock;

	protected function tearDown() : void
	{
		Mockery::close();
	}

	// File::getName()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetNameCase1()
	{
		$expected = 'picture.jpg';

		$result = File::getName('/path/to/picture.jpg');

		$this->assertEquals($expected, $result);
	}

	// File::getNameWithoutExtension() & File::getExtension()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetNameWithoutExtensionCase1()
	{
		$expected = 'picture';

		$result = File::getNameWithoutExtension('/path/to/picture.jpg');

		$this->assertEquals($expected, $result);
	}

	// File::getPath()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetPathCase1()
	{
		$expected = '/path/to';

		$result = File::getPath('/path/to/picture.jpg');

		$this->assertEquals($expected, $result);
	}

	// File::changeExtension()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodChangeExtensionCase1()
	{
		$expected = 'picture.png';

		$result = File::changeExtension('picture.jpg', 'png'); // without dot

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodChangeExtensionCase2()
	{
		$expected = 'picture.png';

		$result = File::changeExtension('picture.jpg', '.png'); // with dot

		$this->assertEquals($expected, $result);
	}

	// File::makeSafe()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMakeSafeCase1()
	{
		$expected = 'picture.jpg';

		$result = File::makeSafe('pic%ture.jpg');

		$this->assertEquals($expected, $result);
	}

	// File::removeExtension()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodRemoveExtensionCase1()
	{
		$expected = '/path/to/picture';

		$result = File::removeExtension('/path/to/picture.jpg');

		$this->assertEquals($expected, $result);
	}

	// File::delete()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase1()
	{
		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->once())->willReturn(false);

		$result = File::delete('/not-existing-file.jpg');

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase2()
	{
		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->once())->willReturn(true);

		$stubFile = $this->getFunctionMock('System', 'chmod');
		$stubFile->expects($this->once())->willReturn(true);

		$stubFile = $this->getFunctionMock('System', 'unlink');
		$stubFile->expects($this->once())->willReturn(true);

		$result = File::delete('/path/to/picture.jpg');

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase3()
	{
		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->once())->willReturn(true);

		$stubFile = $this->getFunctionMock('System', 'chmod');
		$stubFile->expects($this->once())->willReturn(true);

		$stubFile = $this->getFunctionMock('System', 'unlink');
		$stubFile->expects($this->once())->willReturn(false);

		$mockedError = Mockery::mock('alias:\System\Error');
		$mockedError->shouldReceive('getLast')->atLeast()->once();

		$mockedLogger = Mockery::mock('alias:\System\Logger');
		$mockedLogger->shouldReceive('debug')->atLeast()->once();

		$result = File::delete('/path/to/picture.jpg');

		$this->assertFalse($result);
	}
}
