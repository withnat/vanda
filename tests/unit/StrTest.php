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

use System\Str;
use PHPUnit\Framework\TestCase;

/**
 * Class StrTest
 * @package Tests\Unit
 * @see https://www.cl.cam.ac.uk/~mgk25/ucs/examples/quickbrown.txt
 */
final class StrTest extends TestCase
{
	protected static $_enString = 'Nat is so tall, and handsome as hell.';
	protected static $_thString = 'นัททั้งสูง และหล่ออ้ออ๊อย';

	// Str::length

	public function testMethodLengthCase1() : void
	{
		$result = Str::length('');

		$this->assertEquals(0, $result);
	}

	public function testMethodLengthCase2() : void
	{
		$result = Str::length(static::$_enString);

		$this->assertEquals(37, $result);
	}

	public function testMethodLengthCase3() : void
	{
		$result = Str::length(static::$_thString);

		$this->assertEquals(25, $result);
	}

	// Str::count

	public function testMethodCountCase1() : void
	{
		$result = Str::count(static::$_enString, 'n');

		$this->assertEquals(2, $result);
	}

	public function testMethodCountCase2() : void
	{
		$result = Str::count(static::$_enString, 'n', false);

		$this->assertEquals(3, $result);
	}

	public function testMethodCountCase3() : void
	{
		$result = Str::count(static::$_thString, 'อ');

		$this->assertEquals(5, $result);
	}

	public function testMethodCountCase4() : void
	{
		$result = Str::count(static::$_thString, 'อ', false);

		$this->assertEquals(5, $result);
	}

	// Str::countWords

	public function testMethodCountwordsCase1() : void
	{
		$result = Str::countWords(static::$_enString);

		$this->assertEquals(8, $result);
	}

	// Str::left

	public function testMethodLeftCase1() : void
	{
		$result = Str::left('', 0);

		$this->assertEquals('', $result);
	}

	public function testMethodLeftCase2() : void
	{
		$result = Str::left(static::$_enString, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodLeftCase3() : void
	{
		$result = Str::left(static::$_enString, 14);

		$this->assertEquals('Nat is so tall', $result);
	}

	public function testMethodLeftCase4() : void
	{
		$result = Str::left(static::$_enString, -9);

		$this->assertEquals('Nat is so tall, and handsome', $result);
	}

	public function testMethodLeftCase5() : void
	{
		$result = Str::left(static::$_enString, 100);

		$this->assertEquals(static::$_enString, $result);
	}

	public function testMethodLeftCase6() : void
	{
		$result = Str::left(static::$_enString, -100);

		$this->assertEquals('', $result);
	}

	public function testMethodLeftCase7() : void
	{
		$result = Str::left(static::$_thString, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodLeftCase8() : void
	{
		$result = Str::left(static::$_thString, 10);

		$this->assertEquals('นัททั้งสูง', $result);
	}

	public function testMethodLeftCase9() : void
	{
		$result = Str::left(static::$_thString, -7);

		$this->assertEquals('นัททั้งสูง และหล่อ', $result);
	}

	public function testMethodLeftCase10() : void
	{
		$result = Str::left(static::$_thString, 100);

		$this->assertEquals(static::$_thString, $result);
	}

	public function testMethodLeftCase11() : void
	{
		$result = Str::left(static::$_thString, -100);

		$this->assertEquals('', $result);
	}

	// Str::right

	public function testMethodRightCase1() : void
	{
		$result = Str::right('', 0);

		$this->assertEquals('', $result);
	}

	public function testMethodRightCase2() : void
	{
		$result = Str::right(static::$_enString, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodRightCase3() : void
	{
		$result = Str::right(static::$_enString, 17);

		$this->assertEquals('handsome as hell.', $result);
	}

	public function testMethodRightCase4() : void
	{
		$result = Str::right(static::$_enString, -9);

		$this->assertEquals(' tall, and handsome as hell.', $result);
	}

	public function testMethodRightCase5() : void
	{
		$result = Str::right(static::$_enString, 100);

		$this->assertEquals(static::$_enString, $result);
	}

	public function testMethodRightCase6() : void
	{
		$result = Str::right(static::$_enString, -100);

		$this->assertEquals('', $result);
	}

	public function testMethodRightCase7() : void
	{
		$result = Str::right(static::$_thString, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodRightCase8() : void
	{
		$result = Str::right(static::$_thString, 10);

		$this->assertEquals('ล่ออ้ออ๊อย', $result);
	}

	public function testMethodRightCase9() : void
	{
		$result = Str::right(static::$_thString, -7);

		$this->assertEquals('สูง และหล่ออ้ออ๊อย', $result);
	}

	public function testMethodRightCase10() : void
	{
		$result = Str::right(static::$_thString, 100);

		$this->assertEquals(static::$_thString, $result);
	}

	public function testMethodRightCase11() : void
	{
		$result = Str::right(static::$_thString, -100);

		$this->assertEquals('', $result);
	}

	// Str::at

	public function testMethodAtCase1() : void
	{
		$result = Str::at('', 5);

		$this->assertEquals('', $result);
	}

	public function testMethodAtCase2() : void
	{
		$result = Str::at(static::$_enString, 5);

		$this->assertEquals('s', $result);
	}

	public function testMethodAtCase3() : void
	{
		$result = Str::at(static::$_enString, -5);

		$this->assertEquals('h', $result);
	}

	public function testMethodAtCase4() : void
	{
		$result = Str::at(static::$_enString, 100);

		$this->assertEquals('', $result);
	}

	public function testMethodAtCase5() : void
	{
		$result = Str::at(static::$_enString, -100);

		$this->assertEquals('', $result);
	}

	public function testMethodAtCase6() : void
	{
		$result = Str::at(static::$_thString, 6);

		$this->assertEquals('ง', $result);
	}

	public function testMethodAtCase7() : void
	{
		$result = Str::at(static::$_thString, -10);

		$this->assertEquals('ล', $result);
	}

	public function testMethodAtCase8() : void
	{
		$result = Str::at(static::$_thString, 100);

		$this->assertEquals('', $result);
	}

	public function testMethodAtCase9() : void
	{
		$result = Str::at(static::$_thString, -100);

		$this->assertEquals('', $result);
	}

	// Str::slice

	// Test Case
	// $start	$length
	// 0 		+
	// 0		-
	// +		+
	// +		-
	// -		+
	// -		-

	public function testMethodSliceCase1() : void
	{
		$result = Str::slice('', 0, 3);

		$this->assertEquals('', $result);
	}

	public function testMethodSliceCase2() : void
	{
		$result = Str::slice(static::$_enString, 0, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodSliceCase3() : void
	{
		$result = Str::slice(static::$_enString, 0, 3);

		$this->assertEquals('Nat', $result);
	}

	public function testMethodSliceCase4() : void
	{
		$result = Str::slice(static::$_enString, 0, -5);

		$this->assertEquals('Nat is so tall, and handsome as ', $result);
	}

	public function testMethodSliceCase5() : void
	{
		$result = Str::slice(static::$_enString, 10, 4);

		$this->assertEquals('tall', $result);
	}

	public function testMethodSliceCase6() : void
	{
		$result = Str::slice(static::$_enString, 10, -9);

		$this->assertEquals('tall, and handsome', $result);
	}

	public function testMethodSliceCase7() : void
	{
		$result = Str::slice(static::$_enString, -8, 2);

		$this->assertEquals('as', $result);
	}

	public function testMethodSliceCase8() : void
	{
		$result = Str::slice(static::$_enString, -8, -3);

		$this->assertEquals('as he', $result);
	}

	public function testMethodSliceCase9() : void
	{
		$result = Str::slice(static::$_thString, 0, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodSliceCase10() : void
	{
		$result = Str::slice(static::$_thString, 0, 3);

		$this->assertEquals('นัท', $result);
	}

	public function testMethodSliceCase11() : void
	{
		$result = Str::slice(static::$_thString, 0, -4);

		$this->assertEquals('นัททั้งสูง และหล่ออ้อ', $result);
	}

	public function testMethodSliceCase12() : void
	{
		$result = Str::slice(static::$_thString, 14, 4);

		$this->assertEquals('หล่อ', $result);
	}

	public function testMethodSliceCase13() : void
	{
		$result = Str::slice(static::$_thString, 11, -7);

		$this->assertEquals('และหล่อ', $result);
	}

	public function testMethodSliceCase14() : void
	{
		$result = Str::slice(static::$_thString, -11, 4);

		$this->assertEquals('หล่อ', $result);
	}

	public function testMethodSliceCase15() : void
	{
		$result = Str::slice(static::$_thString, -14, -7);

		$this->assertEquals('และหล่อ', $result);
	}

	// Str::limit
	// Test for English only as this method does not work with Thai correctly.

	public function testMethodLimitCase1() : void
	{
		$result = Str::limit(static::$_enString, -1);

		$this->assertEquals('...', $result);
	}

	public function testMethodLimitCase2() : void
	{
		$result = Str::limit(static::$_enString, 0);

		$this->assertEquals('...', $result);
	}

	public function testMethodLimitCase3() : void
	{
		$result = Str::limit(static::$_enString, 11);

		$this->assertEquals('Nat is so tall,...', $result);
	}

	public function testMethodLimitCase4() : void
	{
		$result = Str::limit(static::$_enString, 15);

		$this->assertEquals('Nat is so tall,...', $result);
	}

	public function testMethodLimitCase5() : void
	{
		$result = Str::limit(static::$_enString, 100);

		$this->assertEquals(static::$_enString, $result);
	}

	// Str::limitWords
	// Test for English only as this method does not work with Thai correctly.

	public function testMethodLimitwordsCase1() : void
	{
		$result = Str::limitWords(static::$_enString, -1);

		$this->assertEquals('...', $result);
	}

	public function testMethodLimitwordsCase2() : void
	{
		$result = Str::limitWords(static::$_enString, 0);

		$this->assertEquals('...', $result);
	}

	public function testMethodLimitwordsCase3() : void
	{
		$result = Str::limitWords(static::$_enString, 4);

		$this->assertEquals('Nat is so tall,...', $result);
	}

	public function testMethodLimitwordsCase4() : void
	{
		$result = Str::limitWords(static::$_enString, 6);

		$this->assertEquals('Nat is so tall, and handsome...', $result);
	}

	public function testMethodLimitwordsCase5() : void
	{
		$result = Str::limitWords(static::$_enString, 100);

		$this->assertEquals(static::$_enString, $result);
	}

	// Str::position

	public function testMethodPositionCase1() : void
	{
		$result = Str::position('', 'a');

		$this->assertFalse($result);
	}

	public function testMethodPositionCase2() : void
	{
		$result = Str::position(static::$_enString, 'x');

		$this->assertFalse($result);
	}

	public function testMethodPositionCase3() : void
	{
		$result = Str::position(static::$_enString, 'a');

		$this->assertEquals(1, $result);
	}

	public function testMethodPositionCase4() : void
	{
		$result = Str::position(static::$_enString, 'a', 4);

		$this->assertEquals(11, $result);
	}

	public function testMethodPositionCase5() : void
	{
		$result = Str::position(static::$_enString, 'a', -10);

		$this->assertEquals(29, $result);
	}

	public function testMethodPositionCase6() : void
	{
		$result = Str::position(static::$_thString, 'อ');

		$this->assertEquals(17, $result);
	}

	public function testMethodPositionCase7() : void
	{
		$result = Str::position(static::$_thString, 'อ', 17);

		$this->assertEquals(17, $result);
	}

	public function testMethodPositionCase8() : void
	{
		$result = Str::position(static::$_thString, 'อ', -5);

		$this->assertEquals(20, $result);
	}

	// Str::lastPosition

	public function testMethodLastpositionCase1() : void
	{
		$result = Str::lastPosition('', 'a');

		$this->assertFalse($result);
	}

	public function testMethodLastpositionCase2() : void
	{
		$result = Str::lastPosition(static::$_enString, 'x');

		$this->assertFalse($result);
	}

	public function testMethodLastpositionCase3() : void
	{
		$result = Str::lastPosition(static::$_enString, 'h');

		$this->assertEquals(32, $result);
	}

	// todo
	/*
	public function testMethodLastpositionCase4() : void
	{
		$result = Str::lastPosition(static::$_enString, 'h', -10);

		$this->assertEquals(29, $result);
	}*/

	// todo
	// Str::between

	// Str::trim

	public function testMethodLastpositionCasexxx() : void
	{
		$result = Str::lastPosition(static::$_enString, 'h');

		$this->assertEquals(32, $result);
	}
}
