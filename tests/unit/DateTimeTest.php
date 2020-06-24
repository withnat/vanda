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

		DateTime::mysql([]);
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

		DateTime::shortDate([]);
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

		DateTime::longDate([]);
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

	// DateTime::longTime

	public function testMethodLongTimeCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::longTime([]);
	}

	public function testMethodLongTimeCase2() : void
	{
		$result = DateTime::longTime('20110114111537');

		$this->assertEquals('11:15:37', $result);
	}

	public function testMethodLongTimeCase3() : void
	{
		$result = DateTime::longTime(1294978537);

		$this->assertEquals('11:15:37', $result);
	}

	// DateTime::fullDateTime

	public function testMethodFullDateTimeCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::fullDateTime([]);
	}

	public function testMethodFullDateTimeCase2() : void
	{
		$result = DateTime::fullDateTime('20110114111537');

		$this->assertEquals('14 January 2011 11:15', $result);
	}

	public function testMethodFullDateTimeCase3() : void
	{
		$result = DateTime::fullDateTime(1294978537);

		$this->assertEquals('14 January 2011 11:15', $result);
	}

	// DateTime::fullLongDateTime

	public function testMethodFullLongDateTimeCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::fullLongDateTime([]);
	}

	public function testMethodFullLongDateTimeCase2() : void
	{
		$result = DateTime::fullLongDateTime('20110114111537');

		$this->assertEquals('14 January 2011 11:15:37', $result);
	}

	public function testMethodFullLongDateTimeCase3() : void
	{
		$result = DateTime::fullLongDateTime(1294978537);

		$this->assertEquals('14 January 2011 11:15:37', $result);
	}

	// DateTime::dayMonth

	public function testMethodDayMonthCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::dayMonth([]);
	}

	public function testMethodDayMonthCase2() : void
	{
		$result = DateTime::dayMonth('20110114111537');

		$this->assertEquals('14 January', $result);
	}

	public function testMethodDayMonthCase3() : void
	{
		$result = DateTime::dayMonth(1294978537);

		$this->assertEquals('14 January', $result);
	}

	// DateTime::monthYear

	public function testMethodMonthYearCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::monthYear([]);
	}

	public function testMethodMonthYearCase2() : void
	{
		$result = DateTime::monthYear('20110114111537');

		$this->assertEquals('January 2011', $result);
	}

	public function testMethodMonthYearCase3() : void
	{
		$result = DateTime::monthYear(1294978537);

		$this->assertEquals('January 2011', $result);
	}

	// DateTime::day

	public function testMethodDayCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::day([]);
	}

	public function testMethodDayCase2() : void
	{
		$result = DateTime::day('20110114111537');

		$this->assertEquals('14', $result);
	}

	public function testMethodDayCase3() : void
	{
		$result = DateTime::day(1294978537);

		$this->assertEquals('14', $result);
	}

	// DateTime::shortDayName

	public function testMethodShortDayNameCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::shortDayName([]);
	}

	public function testMethodShortDayNameCase2() : void
	{
		$result = DateTime::shortDayName('20110114111537');

		$this->assertEquals('Fri', $result);
	}

	public function testMethodShortDayNameCase3() : void
	{
		$result = DateTime::shortDayName(1294978537);

		$this->assertEquals('Fri', $result);
	}

	// DateTime::fullDayName

	public function testMethodFullDayNameCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::fullDayName([]);
	}

	public function testMethodFullDayNameCase2() : void
	{
		$result = DateTime::fullDayName('20110114111537');

		$this->assertEquals('Friday', $result);
	}

	public function testMethodFullDayNameCase3() : void
	{
		$result = DateTime::fullDayName(1294978537);

		$this->assertEquals('Friday', $result);
	}

	// DateTime::hour

	public function testMethodHourCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::hour([]);
	}

	public function testMethodHourCase2() : void
	{
		$result = DateTime::hour('20110114111537');

		$this->assertEquals('11', $result);
	}

	public function testMethodHourCase3() : void
	{
		$result = DateTime::hour(1294978537);

		$this->assertEquals('11', $result);
	}

	// DateTime::hour24

	public function testMethodHour24Case1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::hour24([]);
	}

	public function testMethodHour24Case2() : void
	{
		$result = DateTime::hour24('20110114201537');

		$this->assertEquals('20', $result);
	}

	public function testMethodHour24Case3() : void
	{
		$result = DateTime::hour24(1295010937);

		$this->assertEquals('20', $result);
	}

	// DateTime::minute

	public function testMethodMinuteCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::minute([]);
	}

	public function testMethodMinuteCase2() : void
	{
		$result = DateTime::minute('20110114111537');

		$this->assertEquals('15', $result);
	}

	public function testMethodMinuteCase3() : void
	{
		$result = DateTime::minute(1294978537);

		$this->assertEquals('15', $result);
	}

	// DateTime::second

	public function testMethodSecondCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::second([]);
	}

	public function testMethodSecondCase2() : void
	{
		$result = DateTime::second('20110114111537');

		$this->assertEquals('37', $result);
	}

	public function testMethodSecondCase3() : void
	{
		$result = DateTime::second(1294978537);

		$this->assertEquals('37', $result);
	}

	// DateTime::month

	public function testMethodMonthCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::month([]);
	}

	public function testMethodMonthCase2() : void
	{
		$result = DateTime::month('20110114111537');

		$this->assertEquals('01', $result);
	}

	public function testMethodMonthCase3() : void
	{
		$result = DateTime::month(1294978537);

		$this->assertEquals('01', $result);
	}

	// DateTime::shortMonthName

	public function testMethodShortMonthNameCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::shortMonthName([]);
	}

	public function testMethodShortMonthNameCase2() : void
	{
		$result = DateTime::shortMonthName('20110114111537');

		$this->assertEquals('Jan', $result);
	}

	public function testMethodShortMonthNameCase3() : void
	{
		$result = DateTime::shortMonthName(1294978537);

		$this->assertEquals('Jan', $result);
	}

	// DateTime::monthName

	public function testMethodMonthNameCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::monthName([]);
	}

	public function testMethodMonthNameCase2() : void
	{
		$result = DateTime::monthName('20110114111537');

		$this->assertEquals('January', $result);
	}

	public function testMethodMonthNameCase3() : void
	{
		$result = DateTime::monthName(1294978537);

		$this->assertEquals('January', $result);
	}

	// DateTime::apm

	public function testMethodApmNameCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::apm([]);
	}

	public function testMethodApmNameCase2() : void
	{
		$result = DateTime::apm('20110114111537');

		$this->assertEquals('AM', $result);
	}

	public function testMethodApmNameCase3() : void
	{
		$result = DateTime::apm(1294978537);

		$this->assertEquals('AM', $result);
	}

	// DateTime::year

	public function testMethodYearCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::year([]);
	}

	public function testMethodYearCase2() : void
	{
		$result = DateTime::year('20110114111537');

		$this->assertEquals('2011', $result);
	}

	public function testMethodYearCase3() : void
	{
		$result = DateTime::year(1294978537);

		$this->assertEquals('2011', $result);
	}

	// DateTime::shortYear

	public function testMethodShortYearCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		DateTime::shortYear([]);
	}

	public function testMethodShortYearCase2() : void
	{
		$result = DateTime::shortYear('20110114111537');

		$this->assertEquals('11', $result);
	}

	public function testMethodShortYearCase3() : void
	{
		$result = DateTime::shortYear(1294978537);

		$this->assertEquals('11', $result);
	}
}
