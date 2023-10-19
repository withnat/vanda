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

use RuntimeException;
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

	public function setUp() : void
	{
		/* Create folder like this:
		 * - test-folder
		 *   - index.html
		 * 	 - file.txt
		 *
		 * 	 - test-sub-folder-1
		 *     - index.html
		 *     - file.txt
		 *
		 *   - test-sub-folder-2
		 *     - index.html
		 *
		 *   - test-sub-folder-3 (no file)
		 */

		if (!is_dir('test-folder'))
		{
			mkdir(BASEPATH . '/test-folder/test-sub-folder-1', 0777, true);
			mkdir(BASEPATH . '/test-folder/test-sub-folder-2', 0777, true);
			mkdir(BASEPATH . '/test-folder/test-sub-folder-3', 0777, true);

			file_put_contents(BASEPATH . '/test-folder/index.html', '<html lang="en"><body></body></html>');
			file_put_contents(BASEPATH . '/test-folder/file.txt', 'test');

			file_put_contents(BASEPATH . '/test-folder/test-sub-folder-1/index.html', '<html lang="en"><body></body></html>');
			file_put_contents(BASEPATH . '/test-folder/test-sub-folder-1/file.txt', 'test');

			file_put_contents(BASEPATH . '/test-folder/test-sub-folder-2/index.html', '<html lang="en"><body></body></html>');
		}
	}

	protected function tearDown() : void
	{
		Mockery::close();

		if (is_dir('test-folder'))
		{
			@unlink(BASEPATH . '/test-folder/index.html');
			@unlink(BASEPATH . '/test-folder/file.txt');

			@unlink(BASEPATH . '/test-folder/test-sub-folder-1/index.html');
			@unlink(BASEPATH . '/test-folder/test-sub-folder-1/file.txt');

			@unlink(BASEPATH . '/test-folder/test-sub-folder-2/index.html');

			@rmdir(BASEPATH . '/test-folder/test-sub-folder-1');
			@rmdir(BASEPATH . '/test-folder/test-sub-folder-2');
			@rmdir(BASEPATH . '/test-folder/test-sub-folder-3');
			@rmdir(BASEPATH . '/test-folder');
		}
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

		$mockedFile = Mockery::mock('alias:\System\File');
		$mockedFile->shouldReceive('write')->once();

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

	// Folder::exists()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodExistsCase1()
	{
		$result = Folder::exists('.');

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodExistsCase2()
	{
		$stubFile = $this->getFunctionMock('System', 'is_dir');
		$stubFile->expects($this->once())->willReturn(true);

		$result = Folder::exists('path');

		$this->assertTrue($result);
	}

	// Folder::countItems()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCountItemsCase1()
	{
		$folder = Mockery::mock('\System\Folder')->makePartial();
		$folder->shouldReceive('listFolders')->andReturn([]);

		$folder->shouldReceive('listFiles')->andReturnUsing(function(){
			$data = new stdClass();
			$data->name = 'index.html';

			return [$data];
		});

		$result = $folder->countItems('path');

		$this->assertEquals(1, $result);
	}

	// Folder::listFolders() & Folder::listFiles() (via Folder::countItems())

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodListFoldersCase1()
	{
		$this->expectException(RuntimeException::class);

		Folder::listFolders('non-exisint-path');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodListFoldersCase2()
	{
		$stubDir = $this->getFunctionMock('System', 'opendir');
		$stubDir->expects($this->once())->willReturn(false);

		$this->expectException(RuntimeException::class);

		Folder::listFolders('test-folder');

	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodListFoldersCase3()
	{
		$stubFile = Mockery::mock('alias:\System\File');
		$stubFile->shouldReceive('getPermission')->andReturn('0644');
		$stubFile->shouldReceive('getOwner')->andReturn('me:me');

		$result = Folder::listFolders('test-folder');

		$this->assertIsArray($result);
		$this->assertArrayHasKey(0, $result);
		$this->assertIsObject($result[0]);
		$this->assertObjectHasAttribute('name', $result[0]);
		$this->assertObjectHasAttribute('size', $result[0]);
		$this->assertObjectHasAttribute('created', $result[0]);
		$this->assertObjectHasAttribute('modified', $result[0]);
		$this->assertObjectHasAttribute('permission', $result[0]);
		$this->assertObjectHasAttribute('owner', $result[0]);
	}

	// Folder::listFiles()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodListFilesCase1()
	{
		$this->expectException(RuntimeException::class);

		Folder::listFiles('non-exisint-path');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodListFilesCase2()
	{
		$stubDir = $this->getFunctionMock('System', 'opendir');
		$stubDir->expects($this->once())->willReturn(false);

		$this->expectException(RuntimeException::class);

		Folder::listFiles('test-folder');
	}

	// Folder::getSize()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetSizeCase1()
	{
		$this->expectException(RuntimeException::class);

		Folder::getSize('non-exisint-path');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetSizeCase2()
	{
		$stubDir = $this->getFunctionMock('System', 'opendir');
		$stubDir->expects($this->once())->willReturn(false);

		$this->expectException(RuntimeException::class);

		Folder::getSize('test-folder');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetSizeCase3()
	{
		$size = Folder::getSize('test-folder');

		$this->assertEquals(12404, $size);
	}

	// Folder::delete()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase1()
	{
		$result = Folder::delete('non-exisint-path');

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase2()
	{
		$stubDir = $this->getFunctionMock('System', 'opendir');
		$stubDir->expects($this->once())->willReturn(false);

		$this->expectException(RuntimeException::class);

		Folder::delete('test-folder');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase3()
	{
		$mockedFile = Mockery::mock('alias:\System\File');
		$mockedFile->shouldReceive('delete')->atLeast()->once();

		Folder::delete('test-folder');

		$this->assertTrue(true);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase4()
	{
		$stubDir = $this->getFunctionMock('System', 'rmdir');
		$stubDir->expects($this->any())->willReturn(false);

		$stubFile = Mockery::mock('alias:\System\File');
		$stubFile->shouldReceive('delete')->andReturnTrue();

		$mockedError = Mockery::mock('alias:\System\Error');
		$mockedError->shouldReceive('getLast')->atLeast()->once();

		$mockedLogger = Mockery::mock('alias:\System\Logger');
		$mockedLogger->shouldReceive('debug')->atLeast()->once();

		$result = Folder::delete('test-folder');

		$this->assertFalse($result);
	}

	// Folder::isEmpty()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsEmptyCase1()
	{
		$this->expectException(RuntimeException::class);

		Folder::isEmpty('non-exisint-path');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsEmptyCase2()
	{
		$stubDir = $this->getFunctionMock('System', 'opendir');
		$stubDir->expects($this->once())->willReturn(false);

		$this->expectException(RuntimeException::class);

		Folder::isEmpty('test-folder');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsEmptyCase3()
	{
		$result = Folder::isEmpty('test-folder');

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsEmptyCase4()
	{
		$stubFile = Mockery::mock('alias:\System\File');
		$stubFile->shouldReceive('read')->andReturn('<html lang="en"><body></body></html>');

		$result = Folder::isEmpty('test-folder/test-sub-folder-2');

		$this->assertTrue($result);
	}
}
