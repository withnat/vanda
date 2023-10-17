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

use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Folder;

/**
 * Class FolderTest
 * @package Tests\Unit
 */
class FolderTest extends TestCase
{
	use \phpmock\phpunit\PHPMock;

	protected function tearDown() : void
	{
		Mockery::close();
	}

	// Folder::getSeparator()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetSeparatorCase1()
	{
		$expected = '/';

		$result = Folder::getSeparator('/path/to/file');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetSeparatorCase2()
	{
		$expected = '\\';

		$result = Folder::getSeparator('\\path\\to\\file');

		$this->assertEquals($expected, $result);
	}

	// Folder::create()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateCase1()
	{
		$folder = Mockery::mock('\System\Folder')->makePartial();
		$folder->shouldReceive('exists')->andReturnTrue();

		$result = $folder->create('path');

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateCase2()
	{
		$stubMkdir = $this->getFunctionMock('System', 'mkdir');
		$stubMkdir->expects($this->once())->willReturn(true);

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->once())->willReturn(false);

		$stubFile = Mockery::mock('alias:\System\File');
		$stubFile->shouldReceive('write')->andReturnTrue();

		$folder = Mockery::mock('\System\Folder')->makePartial();
		$folder->shouldReceive('exists')->andReturnFalse();

		$result = $folder->create('path');

		$this->assertTrue($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCreateCase3()
	{
		$stubMkdir = $this->getFunctionMock('System', 'mkdir');
		$stubMkdir->expects($this->once())->willReturn(false);

		$folder = Mockery::mock('\System\Folder')->makePartial();
		$folder->shouldReceive('exists')->andReturnFalse();

		$result = $folder->create('path');

		$this->assertFalse($result);
	}
}
