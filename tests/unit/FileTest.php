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

use org\bovigo\vfs\vfsStream;
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

	private $fs;

	protected function setUp() : void
	{
		/* Create folder like this:
		 *
		 * - assets
		 *     - index.html
		 *
		 *     - css
		 *         - index.html
		 *
		 *     - js
		 *         - index.html
		 *
		 *     - images
		 *         - index.html
		 *         - picture.jpg
		 *
		 *         - resize
		 *             - index.html
		 *
		 *             - 100x100
		 *                 - index.html
		 *                 - picture.jpg
		 *
		 *             - 200x200
		 *                 - index.html
		 *                 - picture.jpg
		 *
		 * - system
		 *     - index.html
		 *
		 *     - assets
		 *         - index.html
		 *
		 * 	       - css
		 *             - index.html
		 *
		 *         - js
		 *             - index.html
		 *
		 *         - images
		 *             - index.html
		 *             - picture.jpg
		 *
		 *             - resize
		 *                 - 100x100
		 *                     - index.html
		 *                     - picture.jpg
		 *
		 *                 - 200x200
		 *                     - index.html
		 *                     - picture.jpg
		 *
		 * - themes
		 *     - index.html
		 *
		 *     - backend
		 *         - index.html
		 *
		 *         - vanda
		 *             - index.html
		 *
		 * 		       - assets
		 *                 - index.html
		 *
		 * 		           - css
		 *                     - index.html
		 *
		 * 	               - js
		 *                     - index.html
		 *
		 * 	               - images
		 *                     - index.html
		 *                     - picture.jpg
		 *
		 *                     - resize
		 *                         - 100x100
		 *                             - index.html
		 *                             - picture.jpg
		 *
		 *                         - 200x200
		 *                             - index.html
		 *                             - picture.jpg
		 */

		$htmlContent = '<html lang="en"><body></body></html>';
		$assetFileStructure = [
			'index.html' => $htmlContent,

			'css' => [
				'index.html' => $htmlContent
			],

			'js' => [
				'index.html' => $htmlContent
			],

			'images' => [
				'index.html' => $htmlContent,
				'picture.jpg' => 'picture.jpg',

				'resize' => [
					'index.html' => $htmlContent,

					'100x100' => [
						'index.html' => $htmlContent,
						'picture.jpg' => 'picture.jpg'
					],

					'200x200' => [
						'index.html' => $htmlContent,
						'picture.jpg' => 'picture.jpg'
					]
				]
			]
		];

		$baseDir = vfsStream::setup('project');
		$structure = [
			'assets' => $assetFileStructure,
			'system' => [
				'index.html' => $htmlContent,
				'assets' => $assetFileStructure
			],
			'themes' => [
				'index.html' => $assetFileStructure,
				'backend' => [
					'index.html' => $assetFileStructure,
					'vanda' => [
						'index.html' => $assetFileStructure,
						'assets' => $assetFileStructure
					]
				]
			]
		];

		vfsStream::create($structure, $baseDir);
		$this->fs = vfsStream::url('project');
	}

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
		$result = File::delete('/not-existing-file.jpg');

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase11()
	{
		$result = File::delete($this->fs . '/assets/images/picture.jpg');

		$this->assertTrue($result);
		$this->assertDirectoryNotExists($this->fs . '/assets/images/resize');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodDeleteCase3()
	{
		$stubFile = $this->getFunctionMock('System', 'unlink');
		$stubFile->expects($this->once())->willReturn(false);

		$mockedError = Mockery::mock('alias:\System\Error');
		$mockedError->shouldReceive('getLast')->atLeast()->once();

		$mockedLogger = Mockery::mock('alias:\System\Logger');
		$mockedLogger->shouldReceive('debug')->atLeast()->once();

		$result = File::delete($this->fs . '/assets/images/picture.jpg');

		$this->assertFalse($result);
	}

	// File::getAssetPath() & File::_getPossibleAssetPaths()

	/*
	 * $possibleAssetPaths = [
	 *     'themeAssetFolder',
	 *     'themeAssetRootFolder',
	 *
	 *     'appAssetFolder',
	 *     'appAssetRootFolder',
	 *
	 *     'systemAssetFolder',
	 *     'systemAssetRootFolder'
	 * ];
	 */

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase1()
	{
		$expected = 'themes/backend/vanda/assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->once())->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', THEME_PATH . '/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase2()
	{
		$expected = 'themes/backend/vanda/assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', THEME_PATH . '/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase3()
	{
		$expected = 'assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', THEME_PATH . '/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase4()
	{
		$expected = 'assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', THEME_PATH . '/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase5()
	{
		$expected = 'system/assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(false);
		$stubFile->expects($this->at(4))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', THEME_PATH . '/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase6()
	{
		$expected = 'system/assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(false);
		$stubFile->expects($this->at(4))->willReturn(false);
		$stubFile->expects($this->at(5))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', THEME_PATH . '/index.php');

		$this->assertEquals($expected, $result);
	}

	/*
	 * $possibleAssetPaths = [
	 *     'appPackageAssetFolder',
	 *     'appPackageAssetRootFolder',
	 *
	 *     'systemPackageAssetFolder',
	 *     'systemPackageAssetRootFolder',
	 *
	 *     'appAssetFolder',
	 *     'appAssetRootFolder',
	 *
	 *     'systemAssetFolder',
	 *     'systemAssetRootFolder'
	 * ];
	 */

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase7()
	{
		$expected = 'packages/user/assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase8()
	{
		$expected = 'packages/user/assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase9()
	{
		$expected = 'system/packages/user/assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase10()
	{
		$expected = 'system/packages/user/assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase11()
	{
		$expected = 'assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(false);
		$stubFile->expects($this->at(4))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase12()
	{
		$expected = 'assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(false);
		$stubFile->expects($this->at(4))->willReturn(false);
		$stubFile->expects($this->at(5))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase13()
	{
		$expected = 'system/assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(false);
		$stubFile->expects($this->at(4))->willReturn(false);
		$stubFile->expects($this->at(5))->willReturn(false);
		$stubFile->expects($this->at(6))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase14()
	{
		$expected = 'system/assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(false);
		$stubFile->expects($this->at(4))->willReturn(false);
		$stubFile->expects($this->at(5))->willReturn(false);
		$stubFile->expects($this->at(6))->willReturn(false);
		$stubFile->expects($this->at(7))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/*
	 * $possibleAssetPaths = [
	 *     'systemPackageAssetFolder',
	 *     'systemPackageAssetRootFolder',
	 *
	 *     'systemAssetFolder',
	 *     'systemAssetRootFolder',
	 *
	 *     'appAssetFolder',
	 *     'appAssetRootFolder'
	 * ];
	 */

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase15()
	{
		$expected = 'system/packages/user/assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE_SYSTEM . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase16()
	{
		$expected = 'system/packages/user/assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE_SYSTEM . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase17()
	{
		$expected = 'system/assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE_SYSTEM . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase18()
	{
		$expected = 'system/assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE_SYSTEM . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase19()
	{
		$expected = 'assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(false);
		$stubFile->expects($this->at(4))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE_SYSTEM . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase20()
	{
		$expected = 'assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(false);
		$stubFile->expects($this->at(4))->willReturn(false);
		$stubFile->expects($this->at(5))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_PACKAGE_SYSTEM . '/' . PACKAGE . '/backend/modules/views/user/index.php');

		$this->assertEquals($expected, $result);
	}

	/*
	 * $possibleAssetPaths = [
	 *     'systemAssetFolder',
	 *     'systemAssetRootFolder',
	 *
	 *     'appAssetFolder',
	 *     'appAssetRootFolder'
	 * ];
	 */

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase21()
	{
		$expected = 'system/assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_SYSTEM . '/Mvc/View.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase22()
	{
		$expected = 'system/assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_SYSTEM . '/Mvc/View.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase23()
	{
		$expected = 'assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_SYSTEM . '/Mvc/View.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase24()
	{
		$expected = 'assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);
		$stubFile->expects($this->at(2))->willReturn(false);
		$stubFile->expects($this->at(3))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', PATH_SYSTEM . '/Mvc/View.php');

		$this->assertEquals($expected, $result);
	}

	/*
	 * $possibleAssetPaths = [
	 *     'appAssetFolder',
	 *     'appAssetRootFolder'
	 * ];
	 */

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase25()
	{
		$expected = 'assets/images/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', 'index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase26()
	{
		$expected = 'assets/picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(true);

		$result = File::getAssetPath('picture.jpg', 'images', 'index.php');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetAssetPathCase27()
	{
		$expected = 'picture.jpg';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->at(0))->willReturn(false);
		$stubFile->expects($this->at(1))->willReturn(false);

		$result = File::getAssetPath('picture.jpg', 'images', 'index.php');

		$this->assertEquals($expected, $result);
	}

	// File::getExactPath()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetExactPathCase1()
	{
		$expected = $this->fs . '/assets/images/picture.jpg';

		$paths = [
			$this->fs . '/picture.jpg',
			$this->fs . '/assets/images/picture.jpg',
		];

		$result = File::getExactPath($paths);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodGetExactPathCase2()
	{
		$paths = [
			$this->fs . '/not-existing-file.jpg',
			$this->fs . '/assets/images/not-existing-file.jpg',
		];

		$result = File::getExactPath($paths);

		$this->assertFalse($result);
	}
}
