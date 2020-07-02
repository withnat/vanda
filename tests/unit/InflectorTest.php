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

use System\Inflector;
use PHPUnit\Framework\TestCase;

/**
 * Class InflectorTest
 * @package Tests\Unit
 * @see https://www.cl.cam.ac.uk/~mgk25/ucs/examples/quickbrown.txt
 */
final class InflectorTest extends TestCase
{
	public function ordinalizeProvider()
	{
		return [
			[1, 'st'],
			[21, 'st'],
			[2, 'nd'],
			[22, 'nd'],
			[3, 'rd'],
			[23, 'rd'],
			[4, 'th'],
			[24, 'th'],
			[111, 'th'],
			[112, 'th'],
			[113, 'th']
		];
	}

	// Inflector::isCountable()

	public function testMethodIsCountableCase1() : void
	{
		$result = Inflector::isCountable('audio');

		$this->assertFalse($result);
	}

	public function testMethodIsCountableCase2() : void
	{
		$result = Inflector::isCountable('AUDIO');

		$this->assertFalse($result);
	}

	public function testMethodIsCountableCase3() : void
	{
		$result = Inflector::isCountable('Dog');

		$this->assertTrue($result);
	}

	// Inflector::pluralize()

	public function testMethodPluralizeCase1() : void
	{
		$result = Inflector::pluralize('audio');

		$this->assertEquals('audio', $result);
	}

	public function testMethodPluralizeCase2() : void
	{
		$result = Inflector::pluralize('quiz');

		$this->assertEquals('quizzes', $result);
	}

	public function testMethodPluralizeCase3() : void
	{
		$result = Inflector::pluralize('ox');

		$this->assertEquals('oxen', $result);
	}

	public function testMethodPluralizeCase4() : void
	{
		$result = Inflector::pluralize('mouse');

		$this->assertEquals('mice', $result);
	}

	public function testMethodPluralizeCase5() : void
	{
		$result = Inflector::pluralize('matrix');

		$this->assertEquals('matrices', $result);
	}

	public function testMethodPluralizeCase6() : void
	{
		$result = Inflector::pluralize('search');

		$this->assertEquals('searches', $result);
	}

	public function testMethodPluralizeCase7() : void
	{
		$result = Inflector::pluralize('query');

		$this->assertEquals('queries', $result);
	}

	public function testMethodPluralizeCase8() : void
	{
		$result = Inflector::pluralize('archive');

		$this->assertEquals('archives', $result);
	}

	public function testMethodPluralizeCase9() : void
	{
		$result = Inflector::pluralize('half');

		$this->assertEquals('halves', $result);
	}

	public function testMethodPluralizeCase10() : void
	{
		$result = Inflector::pluralize('basis');

		$this->assertEquals('bases', $result);
	}

	public function testMethodPluralizeCase11() : void
	{
		$result = Inflector::pluralize('datum');

		$this->assertEquals('data', $result);
	}

	public function testMethodPluralizeCase12() : void
	{
		$result = Inflector::pluralize('person');

		$this->assertEquals('people', $result);
	}

	public function testMethodPluralizeCase13() : void
	{
		$result = Inflector::pluralize('man');

		$this->assertEquals('men', $result);
	}

	public function testMethodPluralizeCase14() : void
	{
		$result = Inflector::pluralize('child');

		$this->assertEquals('children', $result);
	}

	public function testMethodPluralizeCase15() : void
	{
		$result = Inflector::pluralize('buffalo');

		$this->assertEquals('buffaloes', $result);
	}

	public function testMethodPluralizeCase16() : void
	{
		$result = Inflector::pluralize('bus');

		$this->assertEquals('buses', $result);
	}

	public function testMethodPluralizeCase17() : void
	{
		$result = Inflector::pluralize('alias');

		$this->assertEquals('aliases', $result);
	}

	public function testMethodPluralizeCase18() : void
	{
		$result = Inflector::pluralize('octopus');

		$this->assertEquals('octopi', $result);
	}

	public function testMethodPluralizeCase19() : void
	{
		$result = Inflector::pluralize('axis');

		$this->assertEquals('axes', $result);
	}

	// Inflector::singularize()

	public function testMethodSingularizeCase1() : void
	{
		$result = Inflector::singularize('matrices');

		$this->assertEquals('matrix', $result);
	}

	public function testMethodSingularizeCase2() : void
	{
		$result = Inflector::singularize('vertices');

		$this->assertEquals('vertex', $result);
	}

	public function testMethodSingularizeCase3() : void
	{
		$result = Inflector::singularize('oxen');

		$this->assertEquals('ox', $result);
	}

	public function testMethodSingularizeCase4() : void
	{
		$result = Inflector::singularize('aliases');

		$this->assertEquals('alias', $result);
	}

	public function testMethodSingularizeCase5() : void
	{
		$result = Inflector::singularize('octopi');

		$this->assertEquals('octopus', $result);
	}

	public function testMethodSingularizeCase6() : void
	{
		$result = Inflector::singularize('crises');

		$this->assertEquals('crisis', $result);
	}

	public function testMethodSingularizeCase7() : void
	{
		$result = Inflector::singularize('shoes');

		$this->assertEquals('shoe', $result);
	}

	public function testMethodSingularizeCase8() : void
	{
		$result = Inflector::singularize('oes');

		$this->assertEquals('o', $result);
	}

	public function testMethodSingularizeCase9() : void
	{
		$result = Inflector::singularize('buses');

		$this->assertEquals('bus', $result);
	}

	public function testMethodSingularizeCase10() : void
	{
		$result = Inflector::singularize('mice');

		$this->assertEquals('mouse', $result);
	}

	public function testMethodSingularizeCase11() : void
	{
		$result = Inflector::singularize('xes');

		$this->assertEquals('x', $result);
	}

	public function testMethodSingularizeCase12() : void
	{
		$result = Inflector::singularize('movies');

		$this->assertEquals('movie', $result);
	}

	public function testMethodSingularizeCase13() : void
	{
		$result = Inflector::singularize('series');

		$this->assertEquals('series', $result);
	}

	public function testMethodSingularizeCase14() : void
	{
		$result = Inflector::singularize('tives');

		$this->assertEquals('tive', $result);
	}

	public function testMethodSingularizeCase15() : void
	{
		$result = Inflector::singularize('hives');

		$this->assertEquals('hive', $result);
	}

	public function testMethodSingularizeCase16() : void
	{
		$result = Inflector::singularize('people');

		$this->assertEquals('person', $result);
	}

	public function testMethodSingularizeCase17() : void
	{
		$result = Inflector::singularize('men');

		$this->assertEquals('man', $result);
	}

	public function testMethodSingularizeCase18() : void
	{
		$result = Inflector::singularize('statuses');

		$this->assertEquals('status', $result);
	}

	public function testMethodSingularizeCase19() : void
	{
		$result = Inflector::singularize('children');

		$this->assertEquals('child', $result);
	}

	public function testMethodSingularizeCase20() : void
	{
		$result = Inflector::singularize('news');

		$this->assertEquals('news', $result);
	}

	public function testMethodSingularizeCase21() : void
	{
		$result = Inflector::singularize('quizzes');

		$this->assertEquals('quiz', $result);
	}

		// Inflector::camelize()

	public function testMethodCamelizeCase1() : void
	{
		$result = Inflector::camelize('Some Day');

		$this->assertEquals('SomeDay', $result);
	}

	public function testMethodCamelizeCase2() : void
	{
		$result = Inflector::camelize('some_day');

		$this->assertEquals('SomeDay', $result);
	}

	public function testMethodCamelizeCase3() : void
	{
		$result = Inflector::camelize('She\'s hot');

		$this->assertEquals('SheSHot', $result);
	}

	// Inflector::underscore()

	public function testMethodUnderscoreCase1() : void
	{
		$result = Inflector::underscore('FooBar');

		$this->assertEquals('foo_bar', $result);
	}

	public function testMethodUnderscoreCase2() : void
	{
		$result = Inflector::underscore('foo bar');

		$this->assertEquals('foo_bar', $result);
	}

	// Inflector::explode()

	public function testMethodExplodeCase1() : void
	{
		$result = Inflector::explode('FooBar');

		$this->assertEquals(['foo', 'bar'], $result);
	}

	// Inflector::implode()

	public function testMethodImplodeCase1() : void
	{
		$result = Inflector::implode(['foo', 'bar']);

		$this->assertEquals('FooBar', $result);
	}

	// Inflector::humanize()

	public function testMethodHumanizeCase1() : void
	{
		$result = Inflector::humanize('I had my car fixed_yesTerday');

		$this->assertEquals('I Had My Car Fixed Yesterday', $result);
	}

	// Inflector::variablize()

	public function testMethodVariablizeCase1() : void
	{
		$result = Inflector::variablize('Some Day');

		$this->assertEquals('someDay', $result);
	}

	public function testMethodVariablizeCase2() : void
	{
		$result = Inflector::variablize('some_day');

		$this->assertEquals('someDay', $result);
	}

	public function testMethodVariablizeCase3() : void
	{
		$result = Inflector::variablize('She\'s hot');

		$this->assertEquals('sheSHot', $result);
	}

	// Inflector::slugify()

	public function testMethodSlugifyCase3() : void
	{
		$result = Inflector::slugify('Vanda PHP Web Framework');

		$this->assertEquals('vanda-php-web-framework', $result);
	}

	public function testMethodSlugifyCase2() : void
	{
		$result = Inflector::slugify('Vanda PHP Web Framework', '_', false);

		$this->assertEquals('Vanda_PHP_Web_Framework', $result);
	}

	// Inflector::ordinalize()

	/**
	 * @param $number
	 * @param $suffix
	 * @dataProvider ordinalizeProvider
	 */
	public function testMethodOrdinalizeCase1($number, $suffix) : void
	{
		$this->assertEquals($number.$suffix, Inflector::ordinalize($number));
	}

	//Inflector::sentence()

	public function testMethodSentenceCase1() : void
	{
		$result = Inflector::sentence([]);

		$this->assertEquals('', $result);
	}

	public function testMethodSentenceCase2() : void
	{
		$expected = 'Nat and Angela';

		$words = ['Nat', 'Angela'];
		$result = Inflector::sentence($words);

		$this->assertEquals($expected, $result);
	}
}