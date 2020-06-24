<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license     MIT
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use System\Number;
use PHPUnit\Framework\TestCase;

/**
 * Class NumberTest
 * @package Tests\Unit
 */
final class NumberTest extends TestCase
{
	// Number::byteFormat

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

	// Number::getUnitByFileSize

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

	// Number::getFileSizeByUnit (tested via above test case for Number::byteFormat())
}
