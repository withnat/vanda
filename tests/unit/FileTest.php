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

use PHPUnit\Framework\TestCase;
use System\File;

/**
 * Class FileTest
 * @package Tests\Unit
 */
class FileTest extends TestCase
{
	use \phpmock\phpunit\PHPMock;

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
	public function testMethodgetNameWithoutExtensionCase1()
	{
		$expected = 'picture';

		$result = File::getNameWithoutExtension('/path/to/picture.jpg');

		$this->assertEquals($expected, $result);
	}
}
