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
 * @package		Vanda
 * @author		Nat Withe <nat@withnat.com>
 * @copyright	Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license		MIT
 * @link		http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use stdClass;
use System\Arr;
use PHPUnit\Framework\TestCase;

/**
 * Class ArrTest
 * @package Tests\Unit
 */
final class ArrTest extends TestCase
{
	protected static $_array;
	protected static $_arrayMulti;
	protected static $_assocArray;
	protected static $_datasetArray;
	protected static $_recordsetArray;
	protected static $_object;
	protected static $_objectEmpty;

	protected function setUp()
	{
		static::$_array = [
			10,
			20,
			'A', // upper case
			'b', // lower case
			null,
			true,
			100
		];
		 
		//
		
		static::$_arrayMulti = [
			10,
			20,
			'A', // upper case
			'b', // lower case
			['x', 'y'], // lower case
			null,
			true,
			100
		];

		//

		static::$_assocArray = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		//

		static::$_datasetArray = [
			[
				'name' => 'Nat',
				'surname' => 'Withe',
				'job' => [
					'title' => 'Web Developer',
					'salary' => 10000
				]
			],
			[
				'name' => 'Angela',
				'surname' => 'SG',
				'job' => [
					'title' => 'Marketing Director',
					'salary' => 10000
				]
			]
		];

		//

		static::$_recordsetArray = [];

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->job = 'Web Developer';
		$data->salary = 10000;

		static::$_recordsetArray[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';
		$data->surname = 'SG';
		$data->job = 'Marketing Director';
		$data->salary = 10000;

		static::$_recordsetArray[] = $data;

		//

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->job = 'Web Developer';
		$data->salary = 10000;

		$address = new stdClass();
		$address->province = 'Chonburi';
		$address->country = 'Thailand';
		$address->postcode = '20270';

		$data->address = $address;

		static::$_object = $data;

		//

		static::$_objectEmpty = new stdClass();
	}

	protected function tearDown()
	{
		static::$_arrayMulti = null;
		static::$_assocArray = null;
		static::$_datasetArray = null;
		static::$_recordsetArray = null;
		static::$_object = null;
		static::$_objectEmpty = null;
	}

	// Arr::get()

	public function testMethodGetCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Arr::get([], 3.14);
	}

    public function testMethodGetCase2() : void
    {
		$result = Arr::get([], 'missingkey');

		$this->assertNull($result);
    }

    public function testMethodGetCase3() : void
    {
		$result = Arr::get(static::$_arrayMulti, 'missingkey');

		$this->assertNull($result);
    }

    public function testMethodGetCase4() : void
    {
		$result = Arr::get(static::$_arrayMulti, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
    }

	public function testMethodGetCase5() : void
	{
		$result = Arr::get(static::$_arrayMulti, 0);

		$this->assertIsInt($result);
		$this->assertEquals(10, $result);
	}

	public function testMethodGetCase6() : void
	{
		$result = Arr::get(static::$_arrayMulti, '4.0');

		$this->assertEquals('x', $result);
	}

	public function testMethodGetCase7() : void
	{
		$result = Arr::get(static::$_assocArray, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase8() : void
	{
		$result = Arr::get(static::$_assocArray, 'name');

		$this->assertEquals('Nat', $result);
	}

	public function testMethodGetCase9() : void
	{
		$result = Arr::get(static::$_assocArray, 'job.salary');

		$this->assertIsInt($result);
		$this->assertEquals(10000, $result);
	}

	public function testMethodGetCase10() : void
	{
		$result = Arr::get(static::$_assocArray, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	// Arr::getKey()

	public function testMethodGetKeyCase1() : void
	{
		$result = Arr::getKey([], 'missingvalue');

		$this->assertNull($result);
	}

	public function testMethodGetKeyCase2() : void
	{
		$result = Arr::getKey(static::$_arrayMulti, 10);

		$this->assertIsInt($result);
		$this->assertEquals(0, $result);
	}

	public function testMethodGetKeyCase3() : void
	{
		$result = Arr::getKey(static::$_arrayMulti, null);

		$this->assertIsInt($result);
		$this->assertEquals(5, $result);
	}

	public function testMethodGetKeyCase4() : void
	{
		$result = Arr::getKey(static::$_assocArray, 'missingvalue');

		$this->assertNull($result);
	}

	public function testMethodGetKeyCase5() : void
	{
		$result = Arr::getKey(static::$_assocArray, 38);

		$this->assertEquals('age', $result);
	}

	// Arr::column()

	public function testMethodColumnCase1() : void
	{
		$result = Arr::column([], 'missingkey');

		$this->assertEquals([], $result);
	}

	public function testMethodColumnCase2() : void
	{
		$expected = [
			'Web Developer',
			'Marketing Director'
		];

		$result = Arr::column(static::$_datasetArray, 'job.title');

		$this->assertEquals($expected, $result);
	}

	public function testMethodColumnCase3() : void
	{
		$expected = [
			'Nat' => 'Web Developer',
			'Angela' => 'Marketing Director'
		];

		$result = Arr::column(static::$_datasetArray, 'job.title', 'name');

		$this->assertEquals($expected, $result);
	}

	public function testMethodColumnCase4() : void
	{
		$expected = [
			'Web Developer',
			'Marketing Director'
		];

		$result = Arr::column(static::$_recordsetArray, 'job');

		$this->assertEquals($expected, $result);
	}

	public function testMethodColumnCase5() : void
	{
		$expected = [
			'Nat' => 'Web Developer',
			'Angela' => 'Marketing Director'
		];

		$result = Arr::column(static::$_recordsetArray, 'job', 'name');

		$this->assertEquals($expected, $result);
	}

	// Arr::first()

	public function testMethodFirstCase1() : void
	{
		$result = Arr::first([]);

		$this->assertNull($result);
	}

	public function testMethodFirstCase2() : void
	{
		$result = Arr::first(static::$_arrayMulti);

		$this->assertIsInt($result);
		$this->assertEquals(10, $result);
	}

	public function testMethodFirstCase3() : void
	{
		$result = Arr::first(static::$_arrayMulti, 1);

		$this->assertEquals([10], $result);
	}

	public function testMethodFirstCase4() : void
	{
		$result = Arr::first(static::$_arrayMulti, 2);

		$this->assertEquals([10, 20], $result);
	}

	public function testMethodFirstCase5() : void
	{
		$result = Arr::first(static::$_assocArray);

		$this->assertEquals('Nat', $result);
	}

	public function testMethodFirstCase6() : void
	{
		$result = Arr::first(static::$_assocArray, 1);

		$this->assertEquals(['name' => 'Nat'], $result);
	}

	public function testMethodFirstCase7() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::first(static::$_assocArray, 2);

		$this->assertEquals($expected, $result);
	}

	// Arr::last()

	public function testMethodLastCase1() : void
	{
		$result = Arr::last([]);

		$this->assertNull($result);
	}

	public function testMethodLastCase2() : void
	{
		$result = Arr::last(static::$_arrayMulti);

		$this->assertIsInt($result);
		$this->assertEquals(100, $result);
	}

	public function testMethodLastCase3() : void
	{
		$result = Arr::last(static::$_arrayMulti, 1);

		$this->assertEquals([100], $result);
	}

	public function testMethodLastCase4() : void
	{
		$result = Arr::last(static::$_arrayMulti, 2);

		$this->assertEquals([true, 100], $result);
	}

	public function testMethodLastCase5() : void
	{
		$result = Arr::last(static::$_assocArray);

		$this->assertNull($result);
	}

	public function testMethodLastCase6() : void
	{
		$result = Arr::last(static::$_assocArray, 1);

		$this->assertEquals(['extra' => null], $result);
	}

	public function testMethodLastCase7() : void
	{
		$expected = [
			'other' => '',
			'extra' => null
		];

		$result = Arr::last(static::$_assocArray, 2);

		$this->assertEquals($expected, $result);
	}

	// Arr::firstKey()

	public function testMethodFirstKeyCase1() : void
	{
		$result = Arr::firstKey([]);

		$this->assertNull($result);
	}

	public function testMethodFirstKeyCase2() : void
	{
		$result = Arr::firstKey(static::$_arrayMulti);

		$this->assertIsInt($result);
		$this->assertEquals(0, $result);
	}

	public function testMethodFirstKeyCase3() : void
	{
		$result = Arr::firstKey(static::$_assocArray);

		$this->assertEquals('name', $result);
	}

	// Arr::lastKey()

	public function testMethodLastKeyCase1() : void
	{
		$result = Arr::lastKey([]);

		$this->assertNull($result);
	}

	public function testMethodLastKeyCase2() : void
	{
		$result = Arr::lastKey(static::$_arrayMulti);

		$this->assertIsInt($result);
		$this->assertEquals(7, $result);
	}

	public function testMethodLastKeyCase3() : void
	{
		$result = Arr::lastKey(static::$_assocArray);

		$this->assertEquals('extra', $result);
	}

	// Arr::only()

	public function testMethodOnlyCase1() : void
	{
		$result = Arr::only([], 'missingkey');

		$this->assertEquals([], $result);
	}

	// Test Case : One dimensional array.
	public function testMethodOnlyCase2() : void
	{
		// Convert multi dimensional array to one dimensional array.
		$array = static::$_arrayMulti;
		unset($array[4]);

		$result = Arr::only($array, '0,1');

		$this->assertEquals([10, 20], $result);
	}

	public function testMethodOnlyCase3() : void
	{
		$result = Arr::only(static::$_arrayMulti, '0,1');

		$this->assertEquals([10, 20], $result);
	}

	public function testMethodOnlyCase4() : void
	{
		$result = Arr::only(static::$_assocArray, 'name');

		$this->assertEquals(['name' => 'Nat'], $result);
	}

	public function testMethodOnlyCase5() : void
	{
		$expected = [
			'name' => 'Nat',
			'job' => [
				'title' => 'Web Developer'
			]
		];

		$result = Arr::only(static::$_assocArray, 'name,job.title');

		$this->assertEquals($expected, $result);
	}

	// Arr::pull()

	public function testMethodPullCase1() : void
	{
		$this->assertCount(8, static::$_arrayMulti);

		$result = Arr::pull(static::$_arrayMulti, '0');

		$this->assertEquals(10, $result);
		$this->assertCount(7, static::$_arrayMulti);
		$this->assertArrayNotHasKey(0, static::$_arrayMulti);
	}

	public function testMethodPullCase2() : void
	{
		$this->assertCount(8, static::$_arrayMulti);

		$result = Arr::pull(static::$_arrayMulti, '0,1');

		$this->assertEquals([10, 20], $result);
		$this->assertCount(6, static::$_arrayMulti);
		$this->assertArrayNotHasKey(0, static::$_arrayMulti);
		$this->assertArrayNotHasKey(1, static::$_arrayMulti);
	}

	public function testMethodPullCase3() : void
	{
		$this->assertCount(10, static::$_assocArray);

		$result = Arr::pull(static::$_assocArray, 'name');

		$this->assertEquals('Nat', $result);
		$this->assertCount(9, static::$_assocArray);
		$this->assertArrayNotHasKey('name', static::$_assocArray);
	}

	public function testMethodPullCase4() : void
	{
		$this->assertCount(10, static::$_assocArray);

		$expected = [
			'title' => 'Web Developer',
			'salary' => 10000,
			'hrscore' => 9.8,
			'excellent' => true,
			'other' => ''
		];

		$result = Arr::pull(static::$_assocArray, 'job');

		$this->assertEquals($expected, $result);
		$this->assertCount(9, static::$_assocArray);
		$this->assertArrayNotHasKey('job', static::$_assocArray);
	}

	public function testMethodPullCase5() : void
	{
		$this->assertCount(10, static::$_assocArray);

		$expected = [
			'name' => 'Nat',
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			]
		];

		$result = Arr::pull(static::$_assocArray, 'name,job');

		$this->assertEquals($expected, $result);
		$this->assertCount(8, static::$_assocArray);
		$this->assertArrayNotHasKey('name', static::$_assocArray);
		$this->assertArrayNotHasKey('job', static::$_assocArray);
	}

	// todo
	// Arr::map()

	// Arr::set()

	public function testMethodSetCase1() : void
	{
		$result = Arr::set([], 'key', 'value');

		$this->assertEquals(['key' => 'value'], $result);
	}

	public function testMethodSetCase2() : void
	{
		$expected = [
			'key' => [
				'subkey' => 'value'
			]
		];

		$result = Arr::set([], 'key.subkey', 'value');

		$this->assertEquals($expected, $result);
	}

	// Arr::formatKeySyntax()

	public function testMethodFormatSyntaxCase1() : void
	{
		$result = Arr::formatKeySyntax('key');

		$this->assertEquals("['key']", $result);
	}

	public function testMethodFormatSyntaxCase2() : void
	{
		$result = Arr::formatKeySyntax('key.subkey');

		$this->assertEquals("['key']['subkey']", $result);
	}

	public function testMethodFormatSyntaxCase3() : void
	{
		$result = Arr::formatKeySyntax(' key . subkey ');

		$this->assertEquals("['key']['subkey']", $result);
	}

	// Arr::insert()

	public function testMethodInsertCase1() : void
	{
		$result = Arr::insert([], 'value');

		$this->assertEquals(['value'], $result);
	}

	public function testMethodInsertCase2() : void
	{
		$expected = ['key' => 'value'] + static::$_assocArray;
		$result = Arr::insert(static::$_assocArray, 'value', 'key');

		$this->assertEquals($expected, $result);
	}

	// todo
	// Arr::index()

	// Arr::has()

	public function testMethodHasCase1() : void
	{
		$result = Arr::has([], 'missingvalue');

		$this->assertFalse($result);
	}

	public function testMethodHasCase2() : void
	{
		$result = Arr::has(static::$_arrayMulti, 'a');

		$this->assertFalse($result);
	}

	public function testMethodHasCase3() : void
	{
		$result = Arr::has(static::$_arrayMulti, 'A');

		$this->assertTrue($result);
	}

	public function testMethodHasCase4() : void
	{
		$result = Arr::has(static::$_arrayMulti, 'a', false);

		$this->assertTrue($result);
	}

	// Arr::hasAny()

	public function testMethodHasAnyCase1() : void
	{
		$result = Arr::hasAny([], ['missingvalue']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyCase2() : void
	{
		$result = Arr::hasAny(static::$_arrayMulti, ['x', 'b']);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyCase3() : void
	{
		$result = Arr::hasAny(static::$_arrayMulti, ['a']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyCase4() : void
	{
		$result = Arr::hasAny(static::$_arrayMulti, ['A'], false);

		$this->assertTrue($result);
	}

	// Arr::hasAll()

	public function testMethodHasAllCase1() : void
	{
		$result = Arr::hasAll([], ['missingvalue']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllCase2() : void
	{
		$result = Arr::hasAll(static::$_arrayMulti, ['x', 'b']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllCase3() : void
	{
		$result = Arr::hasAll(static::$_arrayMulti, ['a']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllCase4() : void
	{
		$result = Arr::hasAll(static::$_arrayMulti, ['a', 'b', null], false);

		$this->assertTrue($result);
	}

	// Arr::hasKey()

	public function testMethodHasKeyCase1() : void
	{
		$result = Arr::hasKey([], 'missingkey');

		$this->assertFalse($result);
	}

	public function testMethodHasKeyCase2() : void
	{
		$result = Arr::hasKey(static::$_arrayMulti, 'missingkey');

		$this->assertFalse($result);
	}

	public function testMethodHasKeyCase3() : void
	{
		$result = Arr::hasKey(static::$_arrayMulti, '0');

		$this->assertTrue($result);
	}

	public function testMethodHasKeyCase4() : void
	{
		$result = Arr::hasKey(static::$_assocArray, 'missingkey');

		$this->assertFalse($result);
	}

	public function testMethodHasKeyCase5() : void
	{
		$result = Arr::hasKey(static::$_assocArray, 'name');

		$this->assertTrue($result);
	}

	public function testMethodHasKeyCase6() : void
	{
		$result = Arr::hasKey(static::$_assocArray, 'job.title');

		$this->assertTrue($result);
	}

	public function testMethodHasKeyCase7() : void
	{
		$result = Arr::hasKey(static::$_assocArray, 'job.missingkey');

		$this->assertFalse($result);
	}

	// Arr::hasAnyKey()

	public function testMethodHasAnyKeyCase1() : void
	{
		$result = Arr::hasAnyKey([], ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyKeyCase2() : void
	{
		$result = Arr::hasAnyKey(static::$_arrayMulti, ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyKeyCase3() : void
	{
		$result = Arr::hasAnyKey(static::$_arrayMulti, [0, 1]);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyKeyCase4() : void
	{
		$result = Arr::hasAnyKey(static::$_assocArray, ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyKeyCase5() : void
	{
		$result = Arr::hasAnyKey(static::$_assocArray, ['name', 'surname']);

		$this->assertTrue($result);
	}

	// Arr::random()

	public function testMethodRandomCase1() : void
	{
		$result = Arr::random([]);

		$this->assertNull($result);
	}

	public function testMethodRandomCase2() : void
	{
		$result = Arr::random([1, 2, 3]);

		$this->assertThat(
			$result,
			$this->logicalAnd(
				$this->greaterThanOrEqual(1),
				$this->lessThanOrEqual(3)
			)
		);
	}

	// Arr::randomKey()

	public function testMethodRandomKeyCase1() : void
	{
		$result = Arr::randomKey([]);

		$this->assertNull($result);
	}

	public function testMethodRandomKeyCase2() : void
	{
		$result = Arr::randomKey(static::$_assocArray);
		$result = (is_int($result) or is_string($result));

		$this->assertTrue($result);
	}

	// Arr::shuffle()

	public function testMethodShuffleCase1() : void
	{
		$result = Arr::shuffle([]);

		$this->assertIsArray($result);
	}

	// Arr::sort()

	public function testMethodSortCase1() : void
	{
		$result = Arr::sort([]);

		$this->assertEquals([], $result);
	}

	public function testMethodSortCase2() : void
	{
		$array = [1, 2, 3];
		$expected = $array;
		$result = Arr::sort($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortCase3() : void
	{
		$array = [1, 2, 3];
		$expected = $array;
		$result = Arr::sort($array, 'asc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortCase4() : void
	{
		$array = [1, 2, 3];
		$expected = [3, 2, 1];
		$result = Arr::sort($array, 'desc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortCase5() : void
	{
		$array = [
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'n' => [1, 2, 3]
		];

		$expected = $array;
		$result = Arr::sort($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortCase6() : void
	{
		$array = [
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'n' => [1, 2, 3]
		];

		$expected = $array;
		$result = Arr::sort($array, 'asc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortCase7() : void
	{
		$array = [
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'n' => [1, 2, 3]
		];

		$expected = [
			'n' => [3, 2, 1],
			'c' => 'C',
			'b' => 'B',
			'a' => 'A'
		];

		$result = Arr::sort($array, 'desc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortCase8() : void
	{
		$array = [
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'n' => [1, 2, 3]
		];

		$expected = [
			'n' => [1, 2, 3],
			'c' => 'C',
			'b' => 'B',
			'a' => 'A'
		];

		$result = Arr::sort($array, 'desc', false);

		$this->assertEquals($expected, $result);
	}

	// Arr::sortKey()

	public function testMethodSortKeyCase1() : void
	{
		$result = Arr::sortKey([]);

		$this->assertEquals([], $result);
	}

	public function testMethodSortKeyCase2() : void
	{
		$array = [3, 2, 1];
		$expected = [3, 2, 1];
		$result = Arr::sortKey($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortKeyCase3() : void
	{
		$array = [3, 2, 1];
		$expected = [3, 2, 1];
		$result = Arr::sortKey($array, 'asc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortKeyCase4() : void
	{
		$array = [1, 2, 3];
		$expected = [2 => 3, 1 => 2, 0 => 1];
		$result = Arr::sortKey($array, 'desc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortKeyCase5() : void
	{
		$array = [
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'c' => 'C',
			'b' => 'B',
			'a' => 'A'
		];

		$expected = [
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'n' => [
				'x' => 'X',
				'y' => 'Y',
				'z' => 'Z'
			]
		];

		$result = Arr::sortKey($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortKeyCase6() : void
	{
		$array = [
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'c' => 'C',
			'b' => 'B',
			'a' => 'A'
		];

		$expected = [
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'n' => [
				'x' => 'X',
				'y' => 'Y',
				'z' => 'Z'
			]
		];

		$result = Arr::sortKey($array, 'asc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortKeyCase7() : void
	{
		$array = [
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'n' => [
				'x' => 'X',
				'y' => 'Y',
				'z' => 'Z'
			]
		];

		$expected = [
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'c' => 'C',
			'b' => 'B',
			'a' => 'A'
		];

		$result = Arr::sortKey($array, 'desc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortKeyCase8() : void
	{
		$array = [
			'a' => 'A',
			'b' => 'B',
			'c' => 'C',
			'n' => [
				'x' => 'X',
				'y' => 'Y',
				'z' => 'Z'
			]
		];

		$expected = [
			'n' => [
				'x' => 'X',
				'y' => 'Y',
				'z' => 'Z'
			],
			'c' => 'C',
			'b' => 'B',
			'a' => 'A'
		];

		$result = Arr::sortKey($array, 'desc', false);

		$this->assertEquals($expected, $result);
	}

	// Arr::sortRecordset()

	public function testMethodSortRecordsetCase1() : void
	{
		$result = Arr::sortRecordset([], 'missingkey');

		$this->assertEquals([], $result);
	}

	public function testMethodSortRecordsetCase2() : void
	{
		$expected[0] = static::$_recordsetArray[1];
		$expected[1] = static::$_recordsetArray[0];

		$result = Arr::sortRecordset(static::$_recordsetArray, 'name');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortRecordsetCase3() : void
	{
		$expected[0] = static::$_recordsetArray[1];
		$expected[1] = static::$_recordsetArray[0];

		$result = Arr::sortRecordset(static::$_recordsetArray, 'name', 'asc');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSortRecordsetCase4() : void
	{
		$result = Arr::sortRecordset(static::$_recordsetArray, 'name', 'desc');

		// static::$_recordsetArray is already sorted by name.
		$this->assertEquals(static::$_recordsetArray, $result);
	}

	// Arr::implode

	public function testMethodImplodeCase1() : void
	{
		$result = Arr::implode([]);

		$this->assertEquals('', $result);
	}

	public function testMethodImplodeCase2() : void
	{
		$expected = 'Nat_Withe_38_181_87.5_1';
		$result = Arr::implode(static::$_assocArray, '_', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodImplodeCase3() : void
	{
		$expected = 'Nat_Withe_38_Web Developer_10000_9.8_1_181_87.5_1';
		$result = Arr::implode(static::$_assocArray, '_');

		$this->assertEquals($expected, $result);
	}

	// Arr::flatten

	public function testMethodFlattenCase1() : void
	{
		$result = Arr::flatten([]);

		$this->assertEquals([], $result);
	}

	public function testMethodFlattenCase2() : void
	{
		$expected = [
			'Nat',
			'Withe',
			38,
			'Web Developer',
			10000,
			9.8,
			true,
			'',
			181,
			87.5,
			true,
			false,
			'',
			null
		];

		$result = Arr::flatten(static::$_assocArray);

		$this->assertEquals($expected, $result);
	}

	// Arr::dot

	public function testMethodDotCase1() : void
	{
		$result = Arr::dot([]);

		$this->assertEquals([], $result);
	}

	public function testMethodDotCase2() : void
	{
		$expected = [
			0 => 10,
			1 => 20,
			2 => 'A',
			3 => 'b',
			'4.0' => 'x',
			'4.1' => 'y',
			5 => null,
			6 => true,
			7 => 100
		];

		$result = Arr::dot(static::$_arrayMulti);

		$this->assertEquals($expected, $result);
	}

	public function testMethodDotCase3() : void
	{
		$expected = [
			'0.name' => 'Nat',
			'0.surname' => 'Withe',
			'0.job.title' => 'Web Developer',
			'0.job.salary' => 10000,
			'1.name' => 'Angela',
			'1.surname' => 'SG',
			'1.job.title' => 'Marketing Director',
			'1.job.salary' => 10000
		];

		$result = Arr::dot(static::$_datasetArray);

		$this->assertEquals($expected, $result);
	}

	public function testMethodDotCase4() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job.title' => 'Web Developer',
			'job.salary' => 10000,
			'job.hrscore' => 9.8,
			'job.excellent' => true,
			'job.other' => '',
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::dot(static::$_assocArray);

		$this->assertEquals($expected, $result);
	}

	// Arr::isRecordset

	public function testMethodIsRecordsetCase1() : void
	{
		$result = Arr::isRecordset(null);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase2() : void
	{
		$result = Arr::isRecordset('');

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase3() : void
	{
		$result = Arr::isRecordset(static::$_objectEmpty);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase4() : void
	{
		$result = Arr::isRecordset([]);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase5() : void
	{
		$result = Arr::isRecordset(static::$_arrayMulti);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase6() : void
	{
		$result = Arr::isRecordset(static::$_assocArray);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase7() : void
	{
		$result = Arr::isRecordset(static::$_datasetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase8() : void
	{
		$result = Arr::isRecordset(static::$_recordsetArray);

		$this->assertTrue($result);
	}

	// Arr::isAssociative

	public function testMethodIsAssociativeCase1() : void
	{
		$result = Arr::isAssociative(null);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase2() : void
	{
		$result = Arr::isAssociative('');

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase3() : void
	{
		$result = Arr::isAssociative(static::$_objectEmpty);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase4() : void
	{
		$result = Arr::isAssociative([]);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase5() : void
	{
		$result = Arr::isAssociative(static::$_arrayMulti);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase6() : void
	{
		$result = Arr::isAssociative(static::$_assocArray);

		$this->assertTrue($result);
	}

	public function testMethodIsAssociativeCase7() : void
	{
		$result = Arr::isAssociative(static::$_datasetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase8() : void
	{
		$result = Arr::isAssociative(static::$_recordsetArray);

		$this->assertFalse($result);
	}

	// Arr::isMultidimensional

	public function testMethodIsMultidimensionalCase1() : void
	{
		$result = Arr::isMultidimensional(null);

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase2() : void
	{
		$result = Arr::isMultidimensional('');

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase3() : void
	{
		$result = Arr::isMultidimensional(static::$_objectEmpty);

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase4() : void
	{
		$result = Arr::isMultidimensional([]);

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase5() : void
	{
		$result = Arr::isMultidimensional(static::$_arrayMulti);

		$this->assertTrue($result);
	}

	public function testMethodIsMultidimensionalCase6() : void
	{
		$result = Arr::isMultidimensional(static::$_assocArray);

		$this->assertTrue($result);
	}

	public function testMethodIsMultidimensionalCase7() : void
	{
		$result = Arr::isMultidimensional(static::$_datasetArray);

		$this->assertTrue($result);
	}

	public function testMethodIsMultidimensionalCase8() : void
	{
		$result = Arr::isMultidimensional(static::$_recordsetArray);

		$this->assertFalse($result);
	}

	// Arr::fromObject

	public function testMethodFromObjectCase1() : void
	{
		$result = Arr::fromObject(static::$_objectEmpty);

		$this->assertEquals([], $result);
	}

	public function testMethodFromObjectCase2() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => 'Web Developer',
			'salary' => 10000,
			'address' => [
				'province' => 'Chonburi',
				'country' => 'Thailand',
				'postcode' => '20270'
			]
		];

		$result = Arr::fromObject(static::$_object);

		$this->assertEquals($expected, $result);
	}

	public function testMethodFromObjectCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => 'Web Developer',
			'salary' => 10000,
			'address' => []
		];

		$result = Arr::fromObject(static::$_object, false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodFromObjectCase4() : void
	{
		$expected = [
			'address' => []
		];

		$result = Arr::fromObject(static::$_object, false, 'address');

		$this->assertEquals($expected, $result);
	}

	public function testMethodFromObjectCase5() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'address' => [
				'province' => 'Chonburi',
				'country' => 'Thailand',
				'postcode' => '20270'
			]
		];

		$result = Arr::fromObject(static::$_object, true, 'name,surname,address');

		$this->assertEquals($expected, $result);
	}

	public function testMethodFromObjectCase6() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'address' => []
		];

		$result = Arr::fromObject(static::$_object, false, 'name,surname,address');

		$this->assertEquals($expected, $result);
	}

	// Arr::fromString

	public function testMethodFromStringCase1() : void
	{
		$result = Arr::fromString('');

		$this->assertEquals([], $result);
	}

	public function testMethodFromStringCase2() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::fromString('name=Nat&surname=Withe');

		$this->assertEquals($expected, $result);
	}

	// Arr::toArray

	public function testMethodToArrayCase1() : void
	{
		$result = Arr::toArray(static::$_objectEmpty);

		$this->assertEquals([], $result);
	}

	public function testMethodToArrayCase2() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => 'Web Developer',
			'salary' => 10000,
			'address' => [
				'province' => 'Chonburi',
				'country' => 'Thailand',
				'postcode' => '20270'
			]
		];

		$result = Arr::toArray(static::$_object);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToArrayCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => 'Web Developer',
			'salary' => 10000,
			'address' => []
		];

		$result = Arr::toArray(static::$_object, false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToArrayCase4() : void
	{
		$expected = [
			'address' => []
		];

		$result = Arr::toArray(static::$_object, false, 'address');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToArrayCase5() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'address' => [
				'province' => 'Chonburi',
				'country' => 'Thailand',
				'postcode' => '20270'
			]
		];

		$result = Arr::toArray(static::$_object, true, 'name,surname,address');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToArrayCase6() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'address' => []
		];

		$result = Arr::toArray(static::$_object, false, 'name,surname,address');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToArrayCase7() : void
	{
		$result = Arr::toArray(static::$_arrayMulti);

		$this->assertEquals(static::$_arrayMulti, $result);
	}

	public function testMethodToArrayCase8() : void
	{
		$result = Arr::toArray(static::$_assocArray);

		$this->assertEquals(static::$_assocArray, $result);
	}

	public function testMethodToArrayCase9() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => []
		];

		$result = Arr::toArray(static::$_assocArray, false, 'name,surname,job');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToArrayCase10() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
		];

		$result = Arr::toArray(static::$_assocArray, true, 'name,surname,job');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToArrayCase11() : void
	{
		$result = Arr::toArray([]);

		$this->assertEquals([], $result);
	}

	public function testMethodToArrayCase12() : void
	{
		$result = Arr::toArray(null);

		$this->assertEquals([null], $result);
	}

	public function testMethodToArrayCase13() : void
	{
		$result = Arr::toArray('Nat');

		$this->assertEquals(['Nat'], $result);
	}

	public function testMethodToArrayCase14() : void
	{
		$result = Arr::toArray(true);

		$this->assertEquals([true], $result);
	}

	// Arr::toObject

	public function testMethodToObjectCase1() : void
	{
		$expected = new stdClass();
		$result = Arr::toObject([]);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase2() : void
	{
		$expected = new stdClass();
		$expected->{'0'} = 10;
		$expected->{'1'} = 20;
		$expected->{'2'} = 'A';
		$expected->{'3'} = 'b';
		$expected->{'4'} = new stdClass();
		$expected->{'4'}->{'0'} = 'x';
		$expected->{'4'}->{'1'} = 'y';
		$expected->{'5'} = null;
		$expected->{'6'} = true;
		$expected->{'7'} = 100;

		$result = Arr::toObject(static::$_arrayMulti);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase3() : void
	{
		$expected = new stdClass();
		$expected->{'0'} = 10;
		$expected->{'1'} = 20;
		$expected->{'2'} = 'A';
		$expected->{'3'} = 'b';
		$expected->{'4'} = new stdClass();
		$expected->{'5'} = null;
		$expected->{'6'} = true;
		$expected->{'7'} = 100;

		$result = Arr::toObject(static::$_arrayMulti, 'stdClass', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase4() : void
	{
		$expected = new stdClass();
		$expected->{'0'} = 10;
		$expected->{'1'} = 20;
		$expected->{'2'} = 'A';
		$expected->{'3'} = 'b';
		$expected->{'4'} = new stdClass();
		$expected->{'4'}->{'0'} = 'x';
		$expected->{'4'}->{'1'} = 'y';

		$result = Arr::toObject(static::$_arrayMulti, 'stdClass', true, '0,1,2,3,4');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase5() : void
	{
		$expected = new stdClass();
		$expected->name = 'Nat';
		$expected->surname = 'Withe';
		$expected->age = 38;
		$expected->job = new stdClass();
		$expected->job->title = 'Web Developer';
		$expected->job->salary = 10000;
		$expected->job->hrscore = 9.8;
		$expected->job->excellent = true;
		$expected->job->other = '';
		$expected->height = 181;
		$expected->weight = 87.5;
		$expected->handsome = true;
		$expected->ugly = false;
		$expected->other = '';
		$expected->extra = null;

		$result = Arr::toObject(static::$_assocArray);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase6() : void
	{
		$expected = new stdClass();
		$expected->name = 'Nat';
		$expected->surname = 'Withe';
		$expected->age = 38;
		$expected->job = new stdClass();
		$expected->height = 181;
		$expected->weight = 87.5;
		$expected->handsome = true;
		$expected->ugly = false;
		$expected->other = '';
		$expected->extra = null;

		$result = Arr::toObject(static::$_assocArray, 'stdClass', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase7() : void
	{
		$expected = new stdClass();
		$expected->name = 'Nat';
		$expected->job = new stdClass();
		$expected->job->title = 'Web Developer';
		$expected->job->salary = 10000;
		$expected->job->hrscore = 9.8;
		$expected->job->excellent = true;
		$expected->job->other = '';

		$result = Arr::toObject(static::$_assocArray, 'stdClass', true, 'name,job');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase8() : void
	{
		$expected = new stdClass();
		$expected->{'0'} = new stdClass();
		$expected->{'0'}->name = 'Nat';
		$expected->{'0'}->surname = 'Withe';
		$expected->{'0'}->job = new stdClass();
		$expected->{'0'}->job->title = 'Web Developer';
		$expected->{'0'}->job->salary = 10000;
		$expected->{'1'} = new stdClass();
		$expected->{'1'}->name = 'Angela';
		$expected->{'1'}->surname = 'SG';
		$expected->{'1'}->job = new stdClass();
		$expected->{'1'}->job->title = 'Marketing Director';
		$expected->{'1'}->job->salary = 10000;

		$result = Arr::toObject(static::$_datasetArray);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase9() : void
	{
		$expected = new stdClass();
		$expected->{'0'} = new stdClass();
		$expected->{'1'} = new stdClass();

		$result = Arr::toObject(static::$_datasetArray, 'stdClass', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase10() : void
	{
		$expected = new stdClass();
		$expected->{'0'} = new stdClass();
		$expected->{'0'}->name = 'Nat';
		$expected->{'0'}->surname = 'Withe';
		$expected->{'0'}->job = new stdClass();
		$expected->{'0'}->job->title = 'Web Developer';
		$expected->{'0'}->job->salary = 10000;

		$result = Arr::toObject(static::$_datasetArray, 'stdClass', true, '0');

		$this->assertEquals($expected, $result);
	}

	// Arr::toString

	public function testMethodToStringCase1() : void
	{
		$result = Arr::toString([]);

		$this->assertEquals('', $result);
	}

	public function testMethodToStringCase2() : void
	{
		$expected = '0="10" 1="20" 2="A" 3="b" 0="x" 1="y" 5="" 6="1" 7="100"';
		$result = Arr::toString(static::$_arrayMulti);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase3() : void
	{
		$expected = '0=\'10\' 1=\'20\' 2=\'A\' 3=\'b\' 5=\'\' 6=\'1\' 7=\'100\'';
		$result = Arr::toString(static::$_arrayMulti, '=', ' ', '\'', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase4() : void
	{
		$expected = '0="10" 1="20"';
		$result = Arr::toString(static::$_arrayMulti, '=', ' ', '"', false, '0,1');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase5() : void
	{
		$expected = 'name="Nat" title="Web Developer" salary="10000" hrscore="9.8" excellent="1" other=""';
		$result = Arr::toString(static::$_assocArray, '=', ' ', '"', true, 'name,job');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase6() : void
	{
		$expected = 'name="Nat"';
		$result = Arr::toString(static::$_assocArray, '=', ' ', '"', false, 'name,job');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase7() : void
	{
		$result = Arr::toString(static::$_datasetArray, '=', ' ', '"', true, 'name');

		$this->assertEquals('', $result);
	}

	public function testMethodToStringCase8() : void
	{
		$result = Arr::toString(static::$_datasetArray, '=', ' ', '"', false);

		$this->assertEquals('', $result);
	}

	public function testMethodToStringCase9() : void
	{
		$expected = 'name="Nat" surname="Withe" title="Web Developer" salary="10000" '
			. 'name="Angela" surname="SG" title="Marketing Director" salary="10000"';

		$result = Arr::toString(static::$_datasetArray);

		$this->assertEquals($expected, $result);
	}

	// Arr::toRecordset

	public function testMethodToRecordsetCase1() : void
	{
		$data = new stdClass();
		$data->{'0'} = '';

		$expected = [$data];

		$result = Arr::toRecordset('');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToRecordsetCase2() : void
	{
		$data = new stdClass();
		$data->{'0'} = 'value';

		$expected = [$data];

		$result = Arr::toRecordset('value');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToRecordsetCase3() : void
	{
		$data = new stdClass();
		$data->{'0'} = 'value';

		$expected = [$data];

		$result = Arr::toRecordset(['value']);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToRecordsetCase4() : void
	{
		$data = new stdClass();
		$data->key = 'value';

		$expected = [$data];

		$result = Arr::toRecordset(['key' => 'value']);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToRecordsetCase5() : void
	{
		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';

		$expected = [$data];

		$result = Arr::toRecordset($data);

		$this->assertEquals($expected, $result);
	}

	// Arr::toMultidimensional

	public function testMethodToMultidimensionalCase1() : void
	{
		$expected = [
			['']
		];

		$result = Arr::toMultidimensional('');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToMultidimensionalCase2() : void
	{
		$expected = [
			['value']
		];

		$result = Arr::toMultidimensional('value');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToMultidimensionalCase3() : void
	{
		$expected = [
			[null]
		];

		$result = Arr::toMultidimensional(null);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToMultidimensionalCase4() : void
	{
		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';

		$expected = [
			[
				'name' => 'Nat',
				'surname' => 'Withe'
			]
		];

		$result = Arr::toMultidimensional($data);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToMultidimensionalCase5() : void
	{
		$array = ['value'];
		$expected = [
			['value']
		];

		$result = Arr::toMultidimensional($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToMultidimensionalCase6() : void
	{
		$array = ['key' => 'value'];
		$expected = [
			['key' => 'value']
		];

		$result = Arr::toMultidimensional($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToMultidimensionalCase7() : void
	{
		$array = [
			['value']
		];

		$expected = [
			['value']
		];

		$result = Arr::toMultidimensional($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToMultidimensionalCase8() : void
	{
		$array = [
			['key' => 'value']
		];

		$expected = [
			['key' => 'value']
		];

		$result = Arr::toMultidimensional($array);

		$this->assertEquals($expected, $result);
	}

	// Arr::toSequential

	public function testMethodToSequentialCase1() : void
	{
		$result = Arr::toSequential('');

		$this->assertEquals([''], $result);
	}

	public function testMethodToSequentialCase2() : void
	{
		$result = Arr::toSequential('value');

		$this->assertEquals(['value'], $result);
	}

	public function testMethodToSequentialCase3() : void
	{
		$result = Arr::toSequential(null);

		$this->assertEquals([null], $result);
	}

	public function testMethodToSequentialCase4() : void
	{
		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';

		$expected = [
			'Nat',
			'Withe'
		];

		$result = Arr::toSequential($data);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToSequentialCase5() : void
	{
		$array = ['value'];
		$expected = ['value'];

		$result = Arr::toSequential($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToSequentialCase6() : void
	{
		$array = ['key' => 'value'];
		$expected = ['value'];

		$result = Arr::toSequential($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToSequentialCase7() : void
	{
		$array = [
			['value']
		];

		$expected = [
			['value']
		];

		$result = Arr::toSequential($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToSequentialCase8() : void
	{
		$array = [
			['key' => 'value']
		];

		$expected = [
			['value']
		];

		$result = Arr::toSequential($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToSequentialCase9() : void
	{
		$expected = [
			'Nat',
			'Withe',
			38,
			[
				'Web Developer',
				10000,
				9.8,
				true,
				''
			],
			181,
			87.5,
			true,
			false,
			'',
			null
		];

		$result = Arr::toSequential(static::$_assocArray);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToSequentialCase10() : void
	{
		$expected = [
			'Nat',
			'Withe',
			'Web Developer',
			10000,
			[
				'Chonburi',
				'Thailand',
				'20270'
			]
		];

		$result = Arr::toSequential(static::$_object);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToSequentialCase11() : void
	{
		$result = Arr::toSequential(static::$_objectEmpty);

		$this->assertEquals([], $result);
	}

	// Arr::toJSON

	public function testMethodToJsonCase1() : void
	{
		$result = Arr::toJSON([]);

		$this->assertEquals('[]', $result);
	}

	public function testMethodToJsonCase2() : void
	{
		$expected = '[10,20,"A","b",["x","y"],null,true,100]';

		$result = Arr::toJSON(static::$_arrayMulti);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToJsonCase3() : void
	{
		$expected = '{"name":"Nat","surname":"Withe","age":38,"job":{"title":"Web Developer",'
			. '"salary":10000,"hrscore":9.8,"excellent":true,"other":""},"height":181,'
			. '"weight":87.5,"handsome":true,"ugly":false,"other":"","extra":null}';

		$result = Arr::toJSON(static::$_assocArray);

		$this->assertEquals($expected, $result);
	}

	// Arr::remove

	public function testMethodRemoveCase1() : void
	{
		$result = Arr::remove([], 'string');

		$this->assertEquals([], $result);
	}

	public function testMethodRemoveCase2() : void
	{
		$array = [
			'name' => 'Nat',
			'surename' => 'Withe'
		];

		$expected = [
			'name' => 'Nat',
		];

		$result = Arr::remove($array, 'Withe');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveCase3() : void
	{
		$array = [
			'name' => 'Nat',
			'surename' => 'Withe'
		];

		$expected = [
			'name' => 'Nat',
			'surename' => 'Withe'
		];

		$result = Arr::remove($array, 'withe');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveCase4() : void
	{
		$array = [
			'name' => 'Nat',
			'surename' => 'Withe'
		];

		$expected = [
			'name' => 'Nat',
		];

		$result = Arr::remove($array, 'withe', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveCase5() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::remove(static::$_assocArray, 'Web Developer');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveCase6() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'salary' => 10000,
				'hrscore' => 9.8,
				'other' => ''
			],
			'height' => 181,
			'ugly' => false,
			'other' => ''
		];

		$result = Arr::remove(static::$_assocArray, ['Web Developer', 87.5, true, null]);

		$this->assertEquals($expected, $result);
	}

	// Arr::removeKey

	public function testMethodRemoveKeyCase1() : void
	{
		$result = Arr::removeKey([], 'string');

		$this->assertEquals([], $result);
	}

	public function testMethodRemoveKeyCase2() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeKey(static::$_assocArray, 'job');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveKeyCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::removeKey(static::$_assocArray, 'age,job,height,weight,handsome,ugly,other,extra');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveKeyCase4() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'extra' => null
		];

		$result = Arr::removeKey(static::$_assocArray, 'other', false);

		$this->assertEquals($expected, $result);
	}

	// Arr::removeType

	public function testMethodRemoveTypeCase1() : void
	{
		$result = Arr::removeType([], 'string');

		$this->assertEquals([], $result);
	}

	public function testMethodRemoveTypeCase2() : void
	{
		$expected = [
			'age' => 38,
			'job' => [
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true
			],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'string');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => [
				'title' => 'Web Developer',
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'int');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase4() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => [
				'title' => 'Web Developer',
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'integer');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase5() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'excellent' => true,
				'other' => ''
			],
			'height' => 181,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'float');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase6() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'excellent' => true,
				'other' => ''
			],
			'height' => 181,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'double');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase7() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'array', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase8() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'bool');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase9() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'boolean');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase10() : void
	{
		$testData = static::$_assocArray;
		$data = new stdClass();
		$testData['object'] = $data;

		$expected = static::$_assocArray;
		$result = Arr::removeType($testData, 'object');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase11() : void
	{
		$testData = [
			'a' => 'A',
			'resource' => tmpfile() //fopen('php://memory', 'r')
		];

		$result = Arr::removeType($testData, 'resource');

		$expected = [
			'a' => 'A'
		];

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase12() : void
	{
		$testData = [
			'a' => 'A',
			'b' => null
		];

		$expected = [
			'a' => 'A'
		];

		$result = Arr::removeType($testData, 'null');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase13() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(static::$_assocArray, 'int', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveTypeCase14() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'job' => [
				'title' => 'Web Developer',
				'other' => ''
			],
			'other' => ''
		];

		$result = Arr::removeType(static::$_assocArray, 'int,float,bool,null');

		$this->assertEquals($expected, $result);
	}

	// Arr::removeBlank

	public function testMethodRemoveBlankCase1() : void
	{
		$result = Arr::removeBlank([]);

		$this->assertEquals([], $result);
	}

	public function testMethodRemoveBlankCase2() : void
	{
		$expected = [
			0 => 10,
			1 => 20,
			2 => 'A',
			3 => 'b',
			4 => ['x', 'y'],
			6 => true,
			7 => 100
		];

		$result = Arr::removeBlank(static::$_arrayMulti);

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveBlankCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true
			],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true
		];

		$result = Arr::removeBlank(static::$_assocArray);

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveBlankCase4() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'job' => [
				'title' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true
		];

		$result = Arr::removeBlank(static::$_assocArray, false);

		$this->assertEquals($expected, $result);
	}

	// todo
	// Arr::pullColumn

	// Arr::removeColumn

	public function testMethodRemoveColumnCase1() : void
	{
		$result = Arr::removeColumn([], 'missingkey');

		$this->assertEquals([], $result);
	}

	public function testMethodRemoveColumnCase2() : void
	{
		$expected = [
			['name' => 'Nat'],
			['name' => 'Angela']
		];

		$result = Arr::removeColumn(static::$_datasetArray, 'surname,job');

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveColumnCase3() : void
	{
		$expected = [];

		$data = new stdClass();
		$data->name = 'Nat';

		$expected[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';

		$expected[] = $data;

		$result = Arr::removeColumn(static::$_recordsetArray, 'surname,job,salary');

		$this->assertEquals($expected, $result);
	}

	// todo เพิ่มเคส removecolumn

	// Arr::explode

	public function testMethodExplodeCase1() : void
	{
		$result = Arr::explode(null, ',');

		$this->assertEquals([], $result);
	}

	public function testMethodExplodeCase2() : void
	{
		$expected = ['a', 'b', 'c'];
		$result = Arr::explode(' a , b , c ', ',');

		$this->assertEquals($expected, $result);
	}

	public function testMethodExplodeCase3() : void
	{
		$expected = ['a', 'b'];
		$result = Arr::explode(' a , b , c ', ',', 2);

		$this->assertEquals($expected, $result);
	}

	public function testMethodExplodeCase4() : void
	{
		$result = Arr::explode(' a , b , c ', ',', 0);

		$this->assertEquals([], $result);
	}

	// todo
	// Arr::split

	// Arr::limit

	public function testMethodLimitCase1() : void
	{
		$result = Arr::limit([], 1);

		$this->assertEquals([], $result);
	}

	public function testMethodLimitCase2() : void
	{
		$result = Arr::limit(static::$_arrayMulti, 1);

		$this->assertEquals([10], $result);
	}

	public function testMethodLimitCase3() : void
	{
		$result = Arr::limit(static::$_arrayMulti, 100);

		$this->assertEquals(static::$_arrayMulti, $result);
	}

	public function testMethodLimitCase4() : void
	{
		$result = Arr::limit(static::$_arrayMulti, -1);

		$this->assertEquals([100], $result);
	}

	public function testMethodLimitCase5() : void
	{
		$result = Arr::limit(static::$_arrayMulti, -100);

		$this->assertEquals(static::$_arrayMulti, $result);
	}

	// Arr::slice

	public function testMethodSliceCase1() : void
	{
		$result = Arr::slice([], 0);

		$this->assertEquals([], $result);
	}

	public function testMethodSliceCase2() : void
	{
		$expected = [2 => 3, 3 => 4];
		$result = Arr::slice([1 ,2 ,3, 4, 5], 2, 2);

		$this->assertEquals($expected, $result);
	}

	public function testMethodSliceCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::slice(static::$_assocArray, 0, 2);

		$this->assertEquals($expected, $result);
	}

	// Arr::unique

	public function testMethodUniqueCase1() : void
	{
		$result = Arr::unique([]);

		$this->assertEquals([], $result);
	}

	public function testMethodUniqueCase2() : void
	{
		$expected = [1, 'a', 'b', 'c'];
		$result = Arr::unique([1, 1, 'a', 'a', 'b', 'c']);

		$this->assertEquals($expected, $result);
	}

	public function testMethodUniqueCase3() : void
	{
		$expected = [
			0 => 1,
			2 => 'a',
			4 => 'b',
			5 => 'c'
		];

		$result = Arr::unique([1, 1, 'a', 'a', 'b', 'c'], false, false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodUniqueCase4() : void
	{
		$array = [
			'a' => 'Nat',
			'b' => 'Nat',
			'c' => 'Angela',
			'd' => 'Angela',
			'e' => 'Jetty'
		];

		$expected = [
			'a' => 'Nat',
			'c' => 'Angela',
			'e' => 'Jetty'
		];

		$result = Arr::unique($array);

		$this->assertEquals($expected, $result);
	}

	public function testMethodUniqueCase5() : void
	{
		$array = [
			'a' => 'Nat',
			'b' => 'Nat',
			'c' => 'Angela',
			'd' => 'Angela',
			'e' => [1, 1, 'a', 'a', 'b', 'c']
		];

		$expected = [
			'a' => 'Nat',
			'c' => 'Angela',
			'e' => [1, 'a', 'b', 'c']
		];

		$result = Arr::unique($array);

		$this->assertEquals($expected, $result);
	}
}
