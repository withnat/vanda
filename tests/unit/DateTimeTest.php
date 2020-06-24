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

use System\DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Override time() in the current namespace for testing.
 *
 * @return int
 */
function time()
{
	return DateTimeTest::$now ?: \time();
}

/**
 * Class DateTimeTest
 * @package Tests\Unit
 * @see https://www.cl.cam.ac.uk/~mgk25/ucs/examples/quickbrown.txt
 */
final class DateTimeTest extends TestCase
{
	public static $now;

	// DateTime::isValid

	public function testMethodIsValidCase1() : void
	{
		$result = DateTime::isValid('');

		$this->assertFalse($result);
	}

	public function testMethodIsValidCase2() : void
	{
		$result = DateTime::isValid('2011-14-01');

		$this->assertFalse($result);
	}

	public function testMethodIsValidCase3() : void
	{
		$result = DateTime::isValid('2011-01-14');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase4() : void
	{
		$result = DateTime::isValid('2011-01-14 11:15');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase5() : void
	{
		$result = DateTime::isValid('2011-01-14 11:15:37');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase6() : void
	{
		$result = DateTime::isValid('2011.01.14');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase7() : void
	{
		$result = DateTime::isValid('2011.01.14 11:15');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase8() : void
	{
		$result = DateTime::isValid('2011.01.14 11:15:37');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase9() : void
	{
		$result = DateTime::isValid('14.01.2011');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase10() : void
	{
		$result = DateTime::isValid('14.01.2011 11:15');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase11() : void
	{
		$result = DateTime::isValid('14.01.2011 11:15:37');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase12() : void
	{
		$result = DateTime::isValid('14/01/2011');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase13() : void
	{
		$result = DateTime::isValid('14/01/2011 11:15');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase14() : void
	{
		$result = DateTime::isValid('14/01/2011 11:15:37');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase15() : void
	{
		$result = DateTime::isValid('20110114');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase16() : void
	{
		$result = DateTime::isValid('201101141115');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase17() : void
	{
		$result = DateTime::isValid('20110114111537');

		$this->assertTrue($result);
	}

	public function testMethodIsValidCase18() : void
	{
		$result = DateTime::isValid('14 Jan 2011', 'd M Y');

		$this->assertTrue($result);
	}

	// DateTime::_

	public function testMethodDefaultCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::_([]);
	}

	public function testMethodDefaultCase2() : void
	{
		$result = DateTime::_('20110114111537');

		$this->assertEquals('2011-01-14 11:15', $result);
	}

	public function testMethodDefaultCase3() : void
	{
		$result = DateTime::_(1294978537);

		$this->assertEquals('2011-01-14 11:15', $result);
	}

	// DateTime::sortable

	public function testMethodSortableCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::sortable([]);
	}

	public function testMethodSortableCase2() : void
	{
		$result = DateTime::sortable('20110114111537');

		$this->assertEquals('2011-01-14 11:15:37', $result);
	}

	public function testMethodSortableCase3() : void
	{
		$result = DateTime::sortable(1294978537);

		$this->assertEquals('2011-01-14 11:15:37', $result);
	}

	// DateTime::mysql

	public function testMethodMysqlCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::sortable([]);
	}

	public function testMethodMysqlCase2() : void
	{
		$result = DateTime::mysql('20110114111537');

		$this->assertEquals('2011-01-14 11:15:37', $result);
	}

	public function testMethodMysqlCase3() : void
	{
		$result = DateTime::mysql(1294978537);

		$this->assertEquals('2011-01-14 11:15:37', $result);
	}

	// DateTime::shortDate

	public function testMethodShortDateCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::sortable([]);
	}

	public function testMethodShortDateCase2() : void
	{
		$result = DateTime::shortDate('20110114111537');

		$this->assertEquals('14/01/2011', $result);
	}

	public function testMethodShortDateCase3() : void
	{
		$result = DateTime::shortDate(1294978537);

		$this->assertEquals('14/01/2011', $result);
	}

	// DateTime::longDate

	public function testMethodLongDateCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::sortable([]);
	}

	public function testMethodLongDateCase2() : void
	{
		$result = DateTime::longDate('20110114111537');

		$this->assertEquals('14 January 2011', $result);
	}

	public function testMethodLongDateCase3() : void
	{
		$result = DateTime::longDate(1294978537);

		$this->assertEquals('14 January 2011', $result);
	}

	// DateTime::shortTime

	public function testMethodShortTimeCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::shortTime([]);
	}

	public function testMethodShortTimeCase2() : void
	{
		$result = DateTime::shortTime('20110114111537');

		$this->assertEquals('11:15', $result);
	}

	public function testMethodShortTimeCase3() : void
	{
		$result = DateTime::shortTime(1294978537);

		$this->assertEquals('11:15', $result);
	}
}
