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

use ErrorException;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Arr;

/**
 * Class ArrTest
 * @package Tests\Unit
 */
final class ArrTest extends TestCase
{
	protected static $_array;
	protected static $_arrayMulti;
	protected static $_assocArray;
	protected static $_assocArrayMulti;
	protected static $_datasetArray;
	protected static $_anotherDatasetArray;
	protected static $_fakeRecordsetArray;
	protected static $_recordsetArray;
	protected static $_object;
	protected static $_objectEmpty;

	protected static $_expectedSortDatasetByNameAsc;
	protected static $_expectedSortDatasetByNameDesc;
	protected static $_expectedSortRecordsetByNameAsc;
	protected static $_expectedSortRecordsetByNameDesc;

	protected function setUp() : void
	{
		ArrTest::$_array = [
			10,
			20,
			'A', // upper case
			'b', // lower case
			null,
			true,
			100
		];

		//

		ArrTest::$_arrayMulti = [
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

		ArrTest::$_assocArray = [
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

		ArrTest::$_assocArrayMulti = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
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

		ArrTest::$_datasetArray = [
			[
				'name' => 'Nat',
				'surname' => 'Withe',
				'work' => [
					'position' => 'Web Developer',
					'salary' => 10000
				]
			],
			[
				'name' => 'Rosie',
				'surname' => 'Marshman',
				'work' => [
					'position' => 'Staff',
					'salary' => 8000
				]
			],
			[
				'name' => 'Emma',
				'surname' => 'McCormick',
				'work' => [
					'position' => 'Staff',
					'salary' => 8000
				]
			],
			[
				'name' => 'Emma',
				'surname' => 'Miller',
				'work' => [
					'position' => 'Project Coordinator',
					'salary' => 10000
				]
			],
			[
				'name' => 'Angela',
				'surname' => 'SG',
				'work' => [
					'position' => 'Marketing Director',
					'salary' => 10000
				]
			]
		];

		//

		ArrTest::$_anotherDatasetArray = [
			[
				'0.1',
				'0.2'
			],
			[
				'1.1',
				'1.2'
			]
		];

		//

		ArrTest::$_recordsetArray = [];

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->work = 'Web Developer';
		$data->salary = 10000;

		ArrTest::$_recordsetArray[] = $data;

		$data = new stdClass();
		$data->name = 'Rosie';
		$data->surname = 'Marshman';
		$data->work = 'Staff';
		$data->salary = 8000;

		ArrTest::$_recordsetArray[] = $data;

		$data = new stdClass();
		$data->name = 'Emma';
		$data->surname = 'McCormick';
		$data->work = 'Staff';
		$data->salary = 8000;

		ArrTest::$_recordsetArray[] = $data;

		$data = new stdClass();
		$data->name = 'Emma';
		$data->surname = 'Miller';
		$data->work = 'Project Coordinator';
		$data->salary = 10000;

		ArrTest::$_recordsetArray[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';
		$data->surname = 'SG';
		$data->work = 'Marketing Director';
		$data->salary = 10000;

		ArrTest::$_recordsetArray[] = $data;

		//

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->work = 'Web Developer';
		$data->salary = 10000;

		$address = new stdClass();
		$address->province = 'Chonburi';
		$address->country = 'Thailand';
		$address->postcode = '20270';

		$data->address = $address;

		// object > array > object, recursive test case
		$objectUnderArray = new stdClass();
		$objectUnderArray->foo = 'Foo';
		$objectUnderArray->bar = 'Bar';

		$data->arrayUnderObject = [
			'objectUnderArray' => $objectUnderArray
		];

		ArrTest::$_object = $data;

		//

		ArrTest::$_objectEmpty = new stdClass();

		// Place some expected result here to reduce duplicated code flagment.

		ArrTest::$_expectedSortDatasetByNameAsc[0] = ArrTest::$_datasetArray[4];
		ArrTest::$_expectedSortDatasetByNameAsc[1] = ArrTest::$_datasetArray[2];
		ArrTest::$_expectedSortDatasetByNameAsc[2] = ArrTest::$_datasetArray[3];
		ArrTest::$_expectedSortDatasetByNameAsc[3] = ArrTest::$_datasetArray[0];
		ArrTest::$_expectedSortDatasetByNameAsc[4] = ArrTest::$_datasetArray[1];

		ArrTest::$_expectedSortDatasetByNameDesc[0] = ArrTest::$_datasetArray[1];
		ArrTest::$_expectedSortDatasetByNameDesc[1] = ArrTest::$_datasetArray[0];
		ArrTest::$_expectedSortDatasetByNameDesc[2] = ArrTest::$_datasetArray[2];
		ArrTest::$_expectedSortDatasetByNameDesc[3] = ArrTest::$_datasetArray[3];
		ArrTest::$_expectedSortDatasetByNameDesc[4] = ArrTest::$_datasetArray[4];

		ArrTest::$_expectedSortRecordsetByNameAsc[0] = ArrTest::$_recordsetArray[4];
		ArrTest::$_expectedSortRecordsetByNameAsc[1] = ArrTest::$_recordsetArray[2];
		ArrTest::$_expectedSortRecordsetByNameAsc[2] = ArrTest::$_recordsetArray[3];
		ArrTest::$_expectedSortRecordsetByNameAsc[3] = ArrTest::$_recordsetArray[0];
		ArrTest::$_expectedSortRecordsetByNameAsc[4] = ArrTest::$_recordsetArray[1];

		ArrTest::$_expectedSortRecordsetByNameDesc[0] = ArrTest::$_recordsetArray[1];
		ArrTest::$_expectedSortRecordsetByNameDesc[1] = ArrTest::$_recordsetArray[0];
		ArrTest::$_expectedSortRecordsetByNameDesc[2] = ArrTest::$_recordsetArray[2];
		ArrTest::$_expectedSortRecordsetByNameDesc[3] = ArrTest::$_recordsetArray[3];
		ArrTest::$_expectedSortRecordsetByNameDesc[4] = ArrTest::$_recordsetArray[4];
	}

	protected function tearDown() : void
	{
		ArrTest::$_arrayMulti = null;
		ArrTest::$_assocArrayMulti = null;
		ArrTest::$_datasetArray = null;
		ArrTest::$_recordsetArray = null;
		ArrTest::$_object = null;
		ArrTest::$_objectEmpty = null;

		ArrTest::$_expectedSortDatasetByNameAsc = null;
		ArrTest::$_expectedSortDatasetByNameDesc = null;
		ArrTest::$_expectedSortRecordsetByNameAsc = null;
		ArrTest::$_expectedSortRecordsetByNameDesc = null;

        Mockery::close();
	}

	// Arr::get()

	public function testMethodGetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::get([], 3.14);
	}

    public function testMethodGetCase2() : void
    {
		$result = Arr::get([], 'missingkey');

		$this->assertNull($result);
    }

	public function testMethodGetCase3() : void
	{
		$result = Arr::get([], 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

    public function testMethodGetCase4() : void
    {
		$result = Arr::get(ArrTest::$_array, 'missingkey');

		$this->assertNull($result);
    }

    public function testMethodGetCase5() : void
    {
		$result = Arr::get(ArrTest::$_array, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
    }

	public function testMethodGetCase6() : void
	{
		$result = Arr::get(ArrTest::$_array, 0);

		$this->assertEquals(10, $result);
	}

	public function testMethodGetCase7() : void
	{
		$result = Arr::get(ArrTest::$_arrayMulti, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase8() : void
	{
		$result = Arr::get(ArrTest::$_arrayMulti, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase9() : void
	{
		$expected = [
			'x',
			'y'
		];

		$result = Arr::get(ArrTest::$_arrayMulti, '4');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodGetCase10() : void
	{
		$result = Arr::get(ArrTest::$_arrayMulti, '4.0');

		$this->assertEquals('x', $result);
	}

	public function testMethodGetCase11() : void
	{
		$result = Arr::get(ArrTest::$_arrayMulti, '4.missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase12() : void
	{
		$result = Arr::get(ArrTest::$_assocArray, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase13() : void
	{
		$result = Arr::get(ArrTest::$_assocArray, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase14() : void
	{
		$result = Arr::get(ArrTest::$_assocArray, 'name');

		$this->assertEquals('Nat', $result);
	}

	public function testMethodGetCase15() : void
	{
		$result = Arr::get(ArrTest::$_assocArrayMulti, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase16() : void
	{
		$result = Arr::get(ArrTest::$_assocArrayMulti, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase17() : void
	{
		$result = Arr::get(ArrTest::$_assocArrayMulti, 'work.missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase18() : void
	{
		$result = Arr::get(ArrTest::$_assocArrayMulti, 'work.salary');

		$this->assertEquals(10000, $result);
	}

	// Arr::getKey()

	public function testMethodGetKeyCase1() : void
	{
		$result = Arr::getKey([], 'missingvalue');

		$this->assertNull($result);
	}

	public function testMethodGetKeyCase2() : void
	{
		$result = Arr::getKey(ArrTest::$_array, 10);

		$this->assertEquals(0, $result);
	}

	public function testMethodGetKeyCase3() : void
	{
		$result = Arr::getKey(ArrTest::$_array, null);

		$this->assertEquals(4, $result);
	}

	public function testMethodGetKeyCase4() : void
	{
		$result = Arr::getKey(ArrTest::$_assocArray, 'missingvalue');

		$this->assertNull($result);
	}

	public function testMethodGetKeyCase5() : void
	{
		$result = Arr::getKey(ArrTest::$_assocArray, 38);

		$this->assertEquals('age', $result);
	}

	// Arr::column()

	public function testMethodColumnCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::column([], 'missingkey');
	}

	public function testMethodColumnCase2() : void
	{
		$expected = [
			'Web Developer',
			'Staff',
			'Staff',
			'Project Coordinator',
			'Marketing Director'
		];

		$result = Arr::column(ArrTest::$_datasetArray, 'work.position');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodColumnCase3() : void
	{
		$expected = [
			'Nat' => 'Web Developer',
			'Rosie' => 'Staff',
			'Emma' => 'Project Coordinator',
			'Angela' => 'Marketing Director'
		];

		$result = Arr::column(ArrTest::$_datasetArray, 'work.position', 'name');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodColumnCase4() : void
	{
		$expected = [
			'Web Developer',
			'Staff',
			'Staff',
			'Project Coordinator',
			'Marketing Director'
		];

		$result = Arr::column(ArrTest::$_recordsetArray, 'work');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodColumnCase5() : void
	{
		$expected = [
			'Nat' => 'Web Developer',
			'Rosie' => 'Staff',
			'Emma' => 'Project Coordinator',
			'Angela' => 'Marketing Director'
		];

		$result = Arr::column(ArrTest::$_recordsetArray, 'work', 'name');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::first()

	public function testMethodFirstCase1() : void
	{
		$result = Arr::first([]);

		$this->assertNull($result);
	}

	public function testMethodFirstCase2() : void
	{
		$result = Arr::first(ArrTest::$_array);

		$this->assertEquals(10, $result);
	}

	public function testMethodFirstCase3() : void
	{
		$result = Arr::first(ArrTest::$_array, 1);

		$this->assertEquals([10], $result);
	}

	public function testMethodFirstCase4() : void
	{
		$expected = [
			10,
			20
		];

		$result = Arr::first(ArrTest::$_array, 2);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodFirstCase5() : void
	{
		$expected = [
			10,
			20,
			'A',
			'b',
			[
				'x',
				'y'
			]
		];

		$result = Arr::first(ArrTest::$_arrayMulti, 5);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodFirstCase6() : void
	{
		$result = Arr::first(ArrTest::$_assocArray);

		$this->assertEquals('Nat', $result);
	}

	public function testMethodFirstCase7() : void
	{
		$result = Arr::first(ArrTest::$_assocArray, 1);

		$this->assertEquals(['name' => 'Nat'], $result);
	}

	public function testMethodFirstCase8() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::first(ArrTest::$_assocArray, 2);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodFirstCase9() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			]
		];

		$result = Arr::first(ArrTest::$_assocArrayMulti, 4);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodFirstCase10() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000
			]
		];

		$result = Arr::first(ArrTest::$_datasetArray);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodFirstCase11() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => 'Web Developer',
			'salary' => 10000
		];

		$result = Arr::first(ArrTest::$_recordsetArray);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::last()

	public function testMethodLastCase1() : void
	{
		$result = Arr::last([]);

		$this->assertNull($result);
	}

	public function testMethodLastCase2() : void
	{
		$result = Arr::last(ArrTest::$_array);

		$this->assertEquals(100, $result);
	}

	public function testMethodLastCase3() : void
	{
		$result = Arr::last(ArrTest::$_array, 1);

		$this->assertEquals([100], $result);
	}

	public function testMethodLastCase4() : void
	{
		$expected = [
			true,
			100
		];

		$result = Arr::last(ArrTest::$_array, 2);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodLastCase5() : void
	{
		$expected = [
			[
				'x',
				'y'
			],
			null,
			true,
			100
		];

		$result = Arr::last(ArrTest::$_arrayMulti, 4);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodLastCase6() : void
	{
		$result = Arr::last(ArrTest::$_assocArray);

		$this->assertNull($result);
	}

	public function testMethodLastCase7() : void
	{
		$result = Arr::last(ArrTest::$_assocArray, 1);

		$this->assertEquals(['extra' => null], $result);
	}

	public function testMethodLastCase8() : void
	{
		$expected = [
			'other' => '',
			'extra' => null
		];

		$result = Arr::last(ArrTest::$_assocArray, 2);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodLastCase9() : void
	{
		$expected = [
			'work' => [
				'position' => 'Web Developer',
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

		$result = Arr::last(ArrTest::$_assocArrayMulti, 7);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodLastCase10() : void
	{
		$expected = [
			'name' => 'Angela',
			'surname' => 'SG',
			'work' => [
				'position' => 'Marketing Director',
				'salary' => 10000
			]
		];

		$result = Arr::last(ArrTest::$_datasetArray);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodLastCase11() : void
	{
		$expected = [
			'name' => 'Angela',
			'surname' => 'SG',
			'work' => 'Marketing Director',
			'salary' => 10000
		];

		$result = Arr::last(ArrTest::$_recordsetArray);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::firstKey()

	public function testMethodFirstKeyCase1() : void
	{
		$result = Arr::firstKey([]);

		$this->assertNull($result);
	}

	public function testMethodFirstKeyCase2() : void
	{
		$result = Arr::firstKey(ArrTest::$_array);

		$this->assertEquals(0, $result);
	}

	public function testMethodFirstKeyCase3() : void
	{
		$result = Arr::firstKey(ArrTest::$_arrayMulti);

		$this->assertEquals(0, $result);
	}

	public function testMethodFirstKeyCase4() : void
	{
		$result = Arr::firstKey(ArrTest::$_assocArray);

		$this->assertEquals('name', $result);
	}

	public function testMethodFirstKeyCase5() : void
	{
		$result = Arr::firstKey(ArrTest::$_assocArrayMulti);

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
		$result = Arr::lastKey(ArrTest::$_array);

		$this->assertEquals(6, $result);
	}

	public function testMethodLastKeyCase3() : void
	{
		$result = Arr::lastKey(ArrTest::$_arrayMulti);

		$this->assertEquals(7, $result);
	}

	public function testMethodLastKeyCase4() : void
	{
		$result = Arr::lastKey(ArrTest::$_assocArray);

		$this->assertEquals('extra', $result);
	}

	public function testMethodLastKeyCase5() : void
	{
		$result = Arr::lastKey(ArrTest::$_assocArrayMulti);

		$this->assertEquals('extra', $result);
	}

	// Arr::only()

	public function testMethodOnlyCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::only([], 0.1);
	}

	public function testMethodOnlyCase2() : void
	{
		$result = Arr::only([], 'missingkey');

		$this->assertEquals([], $result);
	}

	public function testMethodOnlyCase3() : void
	{
		$result = Arr::only(ArrTest::$_array, '1');

		$this->assertEquals(['1' => 20], $result);
	}

	public function testMethodOnlyCase4() : void
	{
		$result = Arr::only(ArrTest::$_array, 1);

		$this->assertEquals(['1' => 20], $result);
	}

	public function testMethodOnlyCase5() : void
	{
		$expected = [
			10,
			20
		];

		$result = Arr::only(ArrTest::$_array, '0,1');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodOnlyCase6() : void
	{
		$result = Arr::only(ArrTest::$_arrayMulti, '1');

		$this->assertEquals(['1' => 20], $result);
	}

	public function testMethodOnlyCase7() : void
	{
		$result = Arr::only(ArrTest::$_arrayMulti, 1);

		$this->assertEquals(['1' => 20], $result);
	}

	public function testMethodOnlyCase8() : void
	{
		$expected = [
			10,
			20
		];

		$result = Arr::only(ArrTest::$_arrayMulti, '0,1');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodOnlyCase9() : void
	{
		$expected = [
			'0' => 10,
			'1' => 20,
			'4' => [
				'1' => 'y'
			]
		];
		$result = Arr::only(ArrTest::$_arrayMulti, '0,1,4.1');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodOnlyCase10() : void
	{
		$result = Arr::only(ArrTest::$_assocArray, 'name');

		$this->assertEquals(['name' => 'Nat'], $result);
	}

	public function testMethodOnlyCase11() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::only(ArrTest::$_assocArray, 'name,surname');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodOnlyCase12() : void
	{
		$result = Arr::only(ArrTest::$_assocArrayMulti, 'name');

		$this->assertEquals(['name' => 'Nat'], $result);
	}

	public function testMethodOnlyCase13() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::only(ArrTest::$_assocArrayMulti, 'name,surname');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodOnlyCase14() : void
	{
		$expected = [
			'name' => 'Nat',
			'work' => [
				'position' => 'Web Developer'
			]
		];

		$result = Arr::only(ArrTest::$_assocArrayMulti, 'name,work.position');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::pull()

	public function testMethodPullCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::pull(ArrTest::$_array, 0.1);
	}

	public function testMethodPullCase2() : void
	{
		$this->assertCount(7, ArrTest::$_array);

		$result = Arr::pull(ArrTest::$_array, '0');

		$this->assertEquals(10, $result);
		$this->assertCount(6, ArrTest::$_array);
		$this->assertArrayNotHasKey(0, ArrTest::$_array);
	}

	public function testMethodPullCase3() : void
	{
		$this->assertCount(7, ArrTest::$_array);

		$result = Arr::pull(ArrTest::$_array, 0);

		$this->assertEquals(10, $result);
		$this->assertCount(6, ArrTest::$_array);
		$this->assertArrayNotHasKey(0, ArrTest::$_array);
	}

	public function testMethodPullCase4() : void
	{
		$this->assertCount(7, ArrTest::$_array);

		$result = Arr::pull(ArrTest::$_array, '0,1');

		$this->assertEquals([10, 20], $result);
		$this->assertCount(5, ArrTest::$_array);
		$this->assertArrayNotHasKey(0, ArrTest::$_array);
		$this->assertArrayNotHasKey(1, ArrTest::$_array);
	}

	public function testMethodPullCase5() : void
	{
		$this->assertCount(8, ArrTest::$_arrayMulti);

		$expected = [
			'0' => 10,
			'4' => ['x', 'y']
		];

		$result = Arr::pull(ArrTest::$_arrayMulti, '0,4');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
		$this->assertCount(6, ArrTest::$_arrayMulti);
		$this->assertArrayNotHasKey(0, ArrTest::$_arrayMulti);
		$this->assertArrayNotHasKey(4, ArrTest::$_arrayMulti);
	}

	public function testMethodPullCase6() : void
	{
		$this->assertCount(9, ArrTest::$_assocArray);

		$result = Arr::pull(ArrTest::$_assocArray, 'name');

		$this->assertEquals('Nat', $result);
		$this->assertCount(8, ArrTest::$_assocArray);
		$this->assertArrayNotHasKey('name', ArrTest::$_assocArray);
	}

	public function testMethodPullCase7() : void
	{
		$this->assertCount(9, ArrTest::$_assocArray);

		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::pull(ArrTest::$_assocArray, 'name,surname');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
		$this->assertCount(7, ArrTest::$_assocArray);
		$this->assertArrayNotHasKey('name', ArrTest::$_assocArray);
		$this->assertArrayNotHasKey('surname', ArrTest::$_assocArray);
	}

	public function testMethodPullCase8() : void
	{
		$this->assertCount(10, ArrTest::$_assocArrayMulti);

		$result = Arr::pull(ArrTest::$_assocArrayMulti, 'name');

		$this->assertEquals('Nat', $result);
		$this->assertCount(9, ArrTest::$_assocArrayMulti);
		$this->assertArrayNotHasKey('name', ArrTest::$_assocArrayMulti);
	}

	public function testMethodPullCase9() : void
	{
		$this->assertCount(10, ArrTest::$_assocArrayMulti);

		$expected = [
			'name' => 'Nat',
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			]
		];

		$result = Arr::pull(ArrTest::$_assocArrayMulti, 'name,work');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
		$this->assertCount(8, ArrTest::$_assocArrayMulti);
		$this->assertArrayNotHasKey('name', ArrTest::$_assocArrayMulti);
		$this->assertArrayNotHasKey('work', ArrTest::$_assocArrayMulti);
	}

	public function testMethodPullCase10() : void
	{
		$this->assertCount(10, ArrTest::$_assocArrayMulti);
		$this->assertCount(5, ArrTest::$_assocArrayMulti['work']);

		$expected = [
			'name' => 'Nat',
			'work' => [
				'position' => 'Web Developer'
			]
		];

		$result = Arr::pull(ArrTest::$_assocArrayMulti, 'name,work.position');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
		$this->assertCount(9, ArrTest::$_assocArrayMulti);
		$this->assertCount(4, ArrTest::$_assocArrayMulti['work']);
		$this->assertArrayNotHasKey('name', ArrTest::$_assocArrayMulti);
		$this->assertArrayNotHasKey('position', ArrTest::$_assocArrayMulti['work']);
	}

	// Arr::map()

	public function testMethodMapCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::map([], 'missingkeyFrom', 'missingkeyTo');
	}

	public function testMethodMapCase2() : void
	{
		$expected = [
			'Nat' => 'Withe',
			'Rosie' => 'Marshman',
			'Emma' => 'Miller',
			'Angela' => 'SG'
		];

		$result = Arr::map(ArrTest::$_datasetArray, 'name', 'surname');

		$this->assertEquals($expected, $result);
	}

	public function testMethodMapCase3() : void
	{
		$expected = [
			'Nat' => 'Withe',
			'Rosie' => 'Marshman',
			'Emma' => 'Miller',
			'Angela' => 'SG'
		];

		$result = Arr::map(ArrTest::$_recordsetArray, 'name', 'surname');

		$this->assertEquals($expected, $result);
	}

	public function testMethodMapCase4() : void
	{
		$expected = [
			'Web Developer' => [
				'Nat' => 'Withe'
			],
			'Staff' => [
				'Rosie' => 'Marshman',
				'Emma' => 'McCormick'
			],
			'Project Coordinator' => [
				'Emma' => 'Miller'
			],
			'Marketing Director' => [
				'Angela' => 'SG'
			]
		];

		$result = Arr::map(ArrTest::$_recordsetArray, 'name', 'surname', 'work');

		$this->assertEquals($expected, $result);
	}

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
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSetCase3() : void
	{
		$expected = [
			'3' => [
				'14' => 'value'
			]
		];

		$result = Arr::set([], '3.14', 'value');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSetCase4() : void
	{
		$expected = [
			0 => [
				1 => [
					2 => [
						3 => 'value'
					]
				]
			]
		];

		$result = Arr::set([], '0.1.2.3', 'value');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSetCase5() : void
	{
		$expected = [
			0 => [
				1 => [
					2 => [
						3 => 'value'
					]
				]
			],
			1 => 20,
			2 => 'A',
			3 => 'b',
			4 => ['x', 'y'],
			5 => null,
			6 => true,
			7 => 100
		];

		$result = Arr::set(ArrTest::$_arrayMulti, '0.1.2.3', 'value');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSetCase6() : void
	{
		$expected = [
			0 => 10,
			1 => 20,
			2 => 'A',
			3 => 'b',
			4 => ['value', 'y'],
			5 => null,
			6 => true,
			7 => 100
		];

		$result = Arr::set(ArrTest::$_arrayMulti, '4.0', 'value');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::formatKeySyntax()

	public function testMethodFormatSyntaxCase1() : void
	{
		$result = Arr::formatKeySyntax('');

		$this->assertEquals('', $result);
	}

	public function testMethodFormatSyntaxCase2() : void
	{
		$result = Arr::formatKeySyntax('key');

		$this->assertEquals("['key']", $result);
	}

	public function testMethodFormatSyntaxCase3() : void
	{
		$result = Arr::formatKeySyntax('key.subkey');

		$this->assertEquals("['key']['subkey']", $result);
	}

	public function testMethodFormatSyntaxCase4() : void
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
		$expected = ['key' => 'value'] + ArrTest::$_assocArrayMulti;
		$result = Arr::insert(ArrTest::$_assocArrayMulti, 'value', 'key');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::has()

	public function testMethodHasCase1() : void
	{
		$result = Arr::has([], 'missingvalue');

		$this->assertFalse($result);
	}

	public function testMethodHasCase2() : void
	{
		$result = Arr::has(ArrTest::$_array, 'missingvalue');

		$this->assertFalse($result);
	}

	public function testMethodHasCase3() : void
	{
		$result = Arr::has(ArrTest::$_array, 10);

		$this->assertTrue($result);
	}

	public function testMethodHasCase4() : void
	{
		$result = Arr::has(ArrTest::$_array, '10');

		$this->assertFalse($result);
	}

	public function testMethodHasCase5() : void
	{
		$result = Arr::has(ArrTest::$_array, 'a');

		$this->assertFalse($result);
	}

	public function testMethodHasCase6() : void
	{
		$result = Arr::has(ArrTest::$_array, 'A');

		$this->assertTrue($result);
	}

	public function testMethodHasCase7() : void
	{
		$result = Arr::has(ArrTest::$_array, 'a', false);

		$this->assertTrue($result);
	}

	public function testMethodHasCase8() : void
	{
		$result = Arr::has(ArrTest::$_arrayMulti, ['x', 'y'], false);

		$this->assertTrue($result);
	}

	public function testMethodHasCase9() : void
	{
		$result = Arr::has(ArrTest::$_assocArray, 'Nat');

		$this->assertTrue($result);
	}

	public function testMethodHasCase10() : void
	{
		$search = [
			'position' => 'Web Developer',
			'salary' => 10000,
			'hrscore' => 9.8,
			'excellent' => true,
			'other' => ''
		];

		$result = Arr::has(ArrTest::$_assocArrayMulti, $search);

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
		$result = Arr::hasAny(ArrTest::$_array, ['missingvalue']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyCase3() : void
	{
		$result = Arr::hasAny(ArrTest::$_array, ['missingvalue', 'b']);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyCase4() : void
	{
		$result = Arr::hasAny(ArrTest::$_array, ['10', 20]);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyCase5() : void
	{
		$result = Arr::hasAny(ArrTest::$_array, ['10', '20']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyCase6() : void
	{
		$result = Arr::hasAny(ArrTest::$_array, ['a']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyCase7() : void
	{
		$result = Arr::hasAny(ArrTest::$_array, ['a'], false);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyCase8() : void
	{
		$result = Arr::hasAny(ArrTest::$_arrayMulti, ['missingvalue', ['x', 'y']]);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyCase9() : void
	{
		$result = Arr::hasAny(ArrTest::$_assocArray, ['missingvalue', 'Nat']);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyCase10() : void
	{
		$result = Arr::hasAny(ArrTest::$_assocArray, ['38', '181']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyCase11() : void
	{
		$result = Arr::hasAny(ArrTest::$_assocArray, ['38', 181]);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyCase12() : void
	{
		$search = [
			'missingvalue',
			[
				'position' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			]
		];

		$result = Arr::hasAny(ArrTest::$_assocArrayMulti, $search);

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
		$result = Arr::hasAll(ArrTest::$_array, ['missingvalue']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllCase3() : void
	{
		$result = Arr::hasAll(ArrTest::$_array, ['missingvalue', 'b']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllCase4() : void
	{
		$result = Arr::hasAll(ArrTest::$_array, ['a']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllCase5() : void
	{
		$result = Arr::hasAll(ArrTest::$_array, ['a', 'b', null], false);

		$this->assertTrue($result);
	}

	public function testMethodHasAllCase6() : void
	{
		$result = Arr::hasAll(ArrTest::$_arrayMulti, ['a', 'b', ['x', 'y'], null], false);

		$this->assertTrue($result);
	}

	// Arr::hasKey()

	public function testMethodHasKeyCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::hasKey([], 3.14);
	}

	public function testMethodHasKeyCase2() : void
	{
		$result = Arr::hasKey([], 'missingkey');

		$this->assertFalse($result);
	}

	public function testMethodHasKeyCase3() : void
	{
		$result = Arr::hasKey(ArrTest::$_array, 'missingkey');

		$this->assertFalse($result);
	}

	public function testMethodHasKeyCase4() : void
	{
		$result = Arr::hasKey(ArrTest::$_array, '0');

		$this->assertTrue($result);
	}

	public function testMethodHasKeyCase5() : void
	{
		$result = Arr::hasKey(ArrTest::$_arrayMulti, '4.missingkey');

		$this->assertFalse($result);
	}

	public function testMethodHasKeyCase6() : void
	{
		$result = Arr::hasKey(ArrTest::$_arrayMulti, '4.0');

		$this->assertTrue($result);
	}

	public function testMethodHasKeyCase7() : void
	{
		$result = Arr::hasKey(ArrTest::$_assocArrayMulti, 'missingkey');

		$this->assertFalse($result);
	}

	public function testMethodHasKeyCase8() : void
	{
		$result = Arr::hasKey(ArrTest::$_assocArrayMulti, 'name');

		$this->assertTrue($result);
	}

	public function testMethodHasKeyCase9() : void
	{
		$result = Arr::hasKey(ArrTest::$_assocArrayMulti, 'work.missingkey');

		$this->assertFalse($result);
	}

	public function testMethodHasKeyCase10() : void
	{
		$result = Arr::hasKey(ArrTest::$_assocArrayMulti, 'work.position');

		$this->assertTrue($result);
	}

	// Arr::hasAnyKey()

	public function testMethodHasAnyKeyCase1() : void
	{
		$result = Arr::hasAnyKey([], ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyKeyCase2() : void
	{
		$result = Arr::hasAnyKey(ArrTest::$_array, ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyKeyCase3() : void
	{
		$result = Arr::hasAnyKey(ArrTest::$_array, ['missingkey', 1]);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyKeyCase4() : void
	{
		$result = Arr::hasAnyKey(ArrTest::$_arrayMulti, ['missingkey', '4.missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyKeyCase5() : void
	{
		$result = Arr::hasAnyKey(ArrTest::$_arrayMulti, ['missingkey', '4.0']);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyKeyCase6() : void
	{
		$result = Arr::hasAnyKey(ArrTest::$_assocArray, ['missingkey', 'name']);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyKeyCase7() : void
	{
		$result = Arr::hasAnyKey(ArrTest::$_assocArrayMulti, ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAnyKeyCase8() : void
	{
		$result = Arr::hasAnyKey(ArrTest::$_assocArrayMulti, ['name', 'surname']);

		$this->assertTrue($result);
	}

	public function testMethodHasAnyKeyCase9() : void
	{
		$result = Arr::hasAnyKey(ArrTest::$_assocArrayMulti, ['missingkey', 'work.position']);

		$this->assertTrue($result);
	}

	// Arr::hasAllKey()

	public function testMethodHasAllKeyCase1() : void
	{
		$result = Arr::hasAllKey([], ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllKeyCase2() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_array, ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllKeyCase3() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_array, ['missingkey', 1]);

		$this->assertFalse($result);
	}

	public function testMethodHasAllKeyCase3x() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_array, ['missingkey', 1]);

		$this->assertFalse($result);
	}

	public function testMethodHasAllKeyCase4() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_arrayMulti, ['missingkey', '4.missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllKeyCase5() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_arrayMulti, ['missingkey', '4.0']);

		$this->assertTrue($result);
	}

	public function testMethodHasAllKeyCase6() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_assocArray, ['missingkey', 'name']);

		$this->assertTrue($result);
	}

	public function testMethodHasAllKeyCase7() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_assocArrayMulti, ['missingkey']);

		$this->assertFalse($result);
	}

	public function testMethodHasAllKeyCase8() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_assocArrayMulti, ['name', 'surname']);

		$this->assertTrue($result);
	}

	public function testMethodHasAllKeyCase9() : void
	{
		$result = Arr::hasAllKey(ArrTest::$_assocArrayMulti, ['missingkey', 'work.position']);

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

		$this->assertContains($result, [1, 2, 3]);
	}

	// Arr::randomKey()

	public function testMethodRandomKeyCase1() : void
	{
		$result = Arr::randomKey([]);

		$this->assertNull($result);
	}

	public function testMethodRandomKeyCase2() : void
	{
		$result = Arr::randomKey(ArrTest::$_array);

		$this->assertThat(
			$result,
			$this->logicalAnd(
				$this->greaterThanOrEqual(0),
				$this->lessThanOrEqual(6)
			)
		);
	}

	public function testMethodRandomKeyCase3() : void
	{
		$key = Arr::randomKey(ArrTest::$_assocArray);

		$possibleValues = [
			'name',
			'surname',
			'age',
			'height',
			'weight',
			'handsome',
			'ugly',
			'other',
			'extra'
		];

		$result = in_array($key, $possibleValues);
		$this->assertTrue($result);
	}

	// Arr::shuffle()

	public function testMethodShuffleCase1() : void
	{
		$result = Arr::shuffle([]);

		$this->assertEquals([], $result);
	}

	public function testMethodShuffleCase2() : void
	{
		$result = Arr::shuffle(ArrTest::$_array);

		$this->assertIsArray($result);
		$this->assertCount(7, $result);
	}

	// Arr::sort()

	public function testMethodSortCase1() : void
	{
		$result = Arr::sort([]);

		$this->assertEquals([], $result);
	}

	public function testMethodSortCase2() : void
	{
		$array = [3, 2, 1];
		$expected = [1, 2, 3];

		$result = Arr::sort($array);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortCase3() : void
	{
		$array = [3, 2, 1];
		$expected = [1, 2, 3];

		$result = Arr::sort($array, 'asc');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortCase4() : void
	{
		$array = [1, 2, 3];
		$expected = [3, 2, 1];

		$result = Arr::sort($array, 'desc');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortCase5() : void
	{
		$array = [
			'a' => 'A',
			'x' => 'X',
			'o' => 'O',
			'n' => [3, 1, 2]
		];

		$expected = [
			'a' => 'A',
			'o' => 'O',
			'x' => 'X',
			'n' => [1, 2, 3]
		];

		$result = Arr::sort($array);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortCase6() : void
	{
		$array = [
			'a' => 'A',
			'x' => 'X',
			'o' => 'O',
			'n' => [3, 1, 2]
		];

		$expected = [
			'a' => 'A',
			'o' => 'O',
			'x' => 'X',
			'n' => [1, 2, 3]
		];


		$result = Arr::sort($array, 'asc');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortCase7() : void
	{
		$array = [
			'a' => 'A',
			'x' => 'X',
			'o' => 'O',
			'n' => [3, 1, 2]
		];

		$expected = [
			'a' => 'A',
			'o' => 'O',
			'x' => 'X',
			'n' => [3, 1, 2]
		];

		$result = Arr::sort($array, 'asc', false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortCase8() : void
	{
		$array = [
			'a' => 'A',
			'x' => 'X',
			'o' => 'O',
			'n' => [3, 1, 2]
		];

		$expected = [
			'n' => [3, 2, 1],
			'x' => 'X',
			'o' => 'O',
			'a' => 'A'
		];

		$result = Arr::sort($array, 'desc');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortCase9() : void
	{
		$array = [
			'a' => 'A',
			'x' => 'X',
			'o' => 'O',
			'n' => [3, 1, 2]
		];

		$expected = [
			'n' => [3, 1, 2],
			'x' => 'X',
			'o' => 'O',
			'a' => 'A'
		];

		$result = Arr::sort($array, 'desc', false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
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
		$expected = $array;

		$result = Arr::sortKey($array);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortKeyCase3() : void
	{
		$array = [3, 2, 1];
		$expected = $array;

		$result = Arr::sortKey($array, 'asc');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortKeyCase4() : void
	{
		$array = [1, 2, 3];
		$expected = [2 => 3, 1 => 2, 0 => 1];

		$result = Arr::sortKey($array, 'desc');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortKeyCase5() : void
	{
		$array = [
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'a' => 'A',
			'x' => 'X',
			'o' => 'O'
		];

		$expected = [
			'a' => 'A',
			'n' => [
				'x' => 'X',
				'y' => 'Y',
				'z' => 'Z'
			],
			'o' => 'O',
			'x' => 'X'
		];

		$result = Arr::sortKey($array);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortKeyCase6() : void
	{
		$array = [
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'a' => 'A',
			'x' => 'X',
			'o' => 'O'
		];

		$expected = [
			'a' => 'A',
			'n' => [
				'x' => 'X',
				'y' => 'Y',
				'z' => 'Z'
			],
			'o' => 'O',
			'x' => 'X'
		];

		$result = Arr::sortKey($array, 'asc');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortKeyCase6x() : void
	{
		$array = [
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'a' => 'A',
			'x' => 'X',
			'o' => 'O'
		];

		$expected = [
			'a' => 'A',
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'o' => 'O',
			'x' => 'X'
		];

		$result = Arr::sortKey($array, 'asc', false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortKeyCase7() : void
	{
		$array = [
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'a' => 'A',
			'x' => 'X',
			'o' => 'O'
		];

		$expected = [
			'x' => 'X',
			'o' => 'O',
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'a' => 'A'
		];

		$result = Arr::sortKey($array, 'desc');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSortKeyCase8() : void
	{
		$array = [
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'a' => 'A',
			'x' => 'X',
			'o' => 'O'
		];

		$expected = [
			'x' => 'X',
			'o' => 'O',
			'n' => [
				'z' => 'Z',
				'y' => 'Y',
				'x' => 'X'
			],
			'a' => 'A'
		];

		$result = Arr::sortKey($array, 'desc', false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::sortDataset()

	public function testMethodSortDatasetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::sortDataset([], 'missingkey');
	}

	public function testMethodSortDatasetCase2() : void
	{
		$result = Arr::sortDataset(ArrTest::$_datasetArray, 'name');
		$compare = ($result === ArrTest::$_expectedSortDatasetByNameAsc);

		$this->assertTrue($compare);
	}

	public function testMethodSortDatasetCase3() : void
	{
		$result = Arr::sortDataset(ArrTest::$_datasetArray, 'name', 'asc');
		$compare = ($result === ArrTest::$_expectedSortDatasetByNameAsc);

		$this->assertTrue($compare);
	}

	public function testMethodSortDatasetCase4() : void
	{
		$result = Arr::sortDataset(ArrTest::$_datasetArray, 'name', 'desc');
		$compare = ($result === ArrTest::$_expectedSortDatasetByNameDesc);

		$this->assertTrue($compare);
	}

	// Arr::sortRecordset()

	public function testMethodSortRecordsetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::sortRecordset([], 'missingkey');
	}

	public function testMethodSortRecordsetCase2() : void
	{
		$result = Arr::sortRecordset(ArrTest::$_recordsetArray, 'name');
		$compare = ($result === ArrTest::$_expectedSortRecordsetByNameAsc);

		$this->assertTrue($compare);
	}

	public function testMethodSortRecordsetCase3() : void
	{
		$result = Arr::sortRecordset(ArrTest::$_recordsetArray, 'name', 'asc');
		$compare = ($result === ArrTest::$_expectedSortRecordsetByNameAsc);

		$this->assertTrue($compare);
	}

	public function testMethodSortRecordsetCase4() : void
	{
		$result = Arr::sortRecordset(ArrTest::$_recordsetArray, 'name', 'desc');
		$compare = ($result === ArrTest::$_expectedSortRecordsetByNameDesc);

		$this->assertTrue($compare);
	}

	// Arr::implode()

	public function testMethodImplodeCase1() : void
	{
		$result = Arr::implode([]);

		$this->assertEquals('', $result);
	}

	public function testMethodImplodeCase2() : void
	{
		$expected = 'Nat_Withe_38_181_87.5_1';
		$result = Arr::implode(ArrTest::$_assocArrayMulti, '_', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodImplodeCase3() : void
	{
		$expected = 'Nat_Withe_38_Web Developer_10000_9.8_1_181_87.5_1';
		$result = Arr::implode(ArrTest::$_assocArrayMulti, '_');

		$this->assertEquals($expected, $result);
	}

	// Arr::flatten()

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

		$result = Arr::flatten(ArrTest::$_assocArrayMulti);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::dot()

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

		$result = Arr::dot(ArrTest::$_arrayMulti);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodDotCase3() : void
	{
		$expected = [
			'_0' => 10,
			'_1' => 20,
			'_2' => 'A',
			'_3' => 'b',
			'_4.0' => 'x',
			'_4.1' => 'y',
			'_5' => null,
			'_6' => true,
			'_7' => 100
		];

		$result = Arr::dot(ArrTest::$_arrayMulti, '_');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodDotCase4() : void
	{
		$expected = [
			'0.name' => 'Nat',
			'0.surname' => 'Withe',
			'0.work.position' => 'Web Developer',
			'0.work.salary' => 10000,
			'1.name' => 'Rosie',
			'1.surname' => 'Marshman',
			'1.work.position' => 'Staff',
			'1.work.salary' => 8000,
			'2.name' => 'Emma',
			'2.surname' => 'McCormick',
			'2.work.position' => 'Staff',
			'2.work.salary' => 8000,
			'3.name' => 'Emma',
			'3.surname' => 'Miller',
			'3.work.position' => 'Project Coordinator',
			'3.work.salary' => 10000,
			'4.name' => 'Angela',
			'4.surname' => 'SG',
			'4.work.position' => 'Marketing Director',
			'4.work.salary' => 10000
		];

		$result = Arr::dot(ArrTest::$_datasetArray);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodDotCase5() : void
	{
		$expected = [
			'_0.name' => 'Nat',
			'_0.surname' => 'Withe',
			'_0.work.position' => 'Web Developer',
			'_0.work.salary' => 10000,
			'_1.name' => 'Rosie',
			'_1.surname' => 'Marshman',
			'_1.work.position' => 'Staff',
			'_1.work.salary' => 8000,
			'_2.name' => 'Emma',
			'_2.surname' => 'McCormick',
			'_2.work.position' => 'Staff',
			'_2.work.salary' => 8000,
			'_3.name' => 'Emma',
			'_3.surname' => 'Miller',
			'_3.work.position' => 'Project Coordinator',
			'_3.work.salary' => 10000,
			'_4.name' => 'Angela',
			'_4.surname' => 'SG',
			'_4.work.position' => 'Marketing Director',
			'_4.work.salary' => 10000
		];

		$result = Arr::dot(ArrTest::$_datasetArray, '_');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodDotCase6() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work.position' => 'Web Developer',
			'work.salary' => 10000,
			'work.hrscore' => 9.8,
			'work.excellent' => true,
			'work.other' => '',
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::dot(ArrTest::$_assocArrayMulti);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodDotCase7() : void
	{
		$expected = [
			'_name' => 'Nat',
			'_surname' => 'Withe',
			'_age' => 38,
			'_work.position' => 'Web Developer',
			'_work.salary' => 10000,
			'_work.hrscore' => 9.8,
			'_work.excellent' => true,
			'_work.other' => '',
			'_height' => 181,
			'_weight' => 87.5,
			'_handsome' => true,
			'_ugly' => false,
			'_other' => '',
			'_extra' => null
		];

		$result = Arr::dot(ArrTest::$_assocArrayMulti, '_');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::isDataset()

	public function testMethodIsDatasetCase1() : void
	{
		$result = Arr::isDataset(null);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase2() : void
	{
		$result = Arr::isDataset('');

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase3() : void
	{
		$result = Arr::isDataset([]);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase4() : void
	{
		$result = Arr::isDataset(ArrTest::$_objectEmpty);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase5() : void
	{
		$result = Arr::isDataset(ArrTest::$_array);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase6() : void
	{
		$result = Arr::isDataset(ArrTest::$_arrayMulti);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase7() : void
	{
		$result = Arr::isDataset(ArrTest::$_assocArray);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase8() : void
	{
		$result = Arr::isDataset(ArrTest::$_assocArrayMulti);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase9() : void
	{
		$result = Arr::isDataset(ArrTest::$_recordsetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase10() : void
	{
		// Test different number of columns for each row.
		unset(ArrTest::$_datasetArray[1]['work']);

		$result = Arr::isDataset(ArrTest::$_datasetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase11() : void
	{
		// Test different column name for each row.
		unset(ArrTest::$_datasetArray[1]['work']);
		ArrTest::$_datasetArray[1]['xxx'] = 'xxx';

		$result = Arr::isDataset(ArrTest::$_datasetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase12() : void
	{
		// Some row is not an array.
		ArrTest::$_datasetArray[1] = 'value';

		$result = Arr::isDataset(ArrTest::$_datasetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsDatasetCase13() : void
	{
		$result = Arr::isDataset(ArrTest::$_datasetArray);

		$this->assertTrue($result);
	}

	public function testMethodIsDatasetCase14() : void
	{
		$result = Arr::isDataset(ArrTest::$_anotherDatasetArray);

		$this->assertTrue($result);
	}

	// Arr::isRecordset()

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
		$result = Arr::isRecordset([]);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase4() : void
	{
		$result = Arr::isRecordset(ArrTest::$_objectEmpty);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase5() : void
	{
		$result = Arr::isRecordset(ArrTest::$_array);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase6() : void
	{
		$result = Arr::isRecordset(ArrTest::$_arrayMulti);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase7() : void
	{
		$result = Arr::isRecordset(ArrTest::$_assocArray);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase8() : void
	{
		$result = Arr::isRecordset(ArrTest::$_assocArrayMulti);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase9() : void
	{
		$result = Arr::isRecordset(ArrTest::$_datasetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase10() : void
	{
		// Test different number of columns for each row.
		unset(ArrTest::$_recordsetArray[4]->salary);

		$result = Arr::isRecordset(ArrTest::$_recordsetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase11() : void
	{
		// Test different column name for each row.
		unset(ArrTest::$_recordsetArray[4]->salary);
		ArrTest::$_recordsetArray[4]->xxx = 'xxx';

		$result = Arr::isRecordset(ArrTest::$_recordsetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase12() : void
	{
		// Some row is not an object.
		ArrTest::$_recordsetArray[4] = 'value';

		$result = Arr::isRecordset(ArrTest::$_recordsetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsRecordsetCase13() : void
	{
		$result = Arr::isRecordset(ArrTest::$_recordsetArray);

		$this->assertTrue($result);
	}

	// Arr::isAssociative()

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
		$result = Arr::isAssociative([]);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase4() : void
	{
		$result = Arr::isAssociative(ArrTest::$_objectEmpty);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase5() : void
	{
		$result = Arr::isAssociative(ArrTest::$_object);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase6() : void
	{
		$result = Arr::isAssociative(ArrTest::$_array);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase7() : void
	{
		$result = Arr::isAssociative(ArrTest::$_arrayMulti);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase8() : void
	{
		$result = Arr::isAssociative(ArrTest::$_assocArray);

		$this->assertTrue($result);
	}

	public function testMethodIsAssociativeCase9() : void
	{
		$result = Arr::isAssociative(ArrTest::$_assocArrayMulti);

		$this->assertTrue($result);
	}

	public function testMethodIsAssociativeCase10() : void
	{
		$result = Arr::isAssociative(ArrTest::$_datasetArray);

		$this->assertFalse($result);
	}

	public function testMethodIsAssociativeCase11() : void
	{
		$result = Arr::isAssociative(ArrTest::$_recordsetArray);

		$this->assertFalse($result);
	}

	// Arr::isMultidimensional()

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
		$result = Arr::isMultidimensional([]);

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase4() : void
	{
		$result = Arr::isMultidimensional(ArrTest::$_objectEmpty);

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase5() : void
	{
		$result = Arr::isMultidimensional(ArrTest::$_object);

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase6() : void
	{
		$result = Arr::isMultidimensional(ArrTest::$_array);

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase7() : void
	{
		$result = Arr::isMultidimensional(ArrTest::$_arrayMulti);

		$this->assertTrue($result);
	}

	public function testMethodIsMultidimensionalCase8() : void
	{
		$result = Arr::isMultidimensional(ArrTest::$_assocArray);

		$this->assertFalse($result);
	}

	public function testMethodIsMultidimensionalCase9() : void
	{
		$result = Arr::isMultidimensional(ArrTest::$_assocArrayMulti);

		$this->assertTrue($result);
	}

	public function testMethodIsMultidimensionalCase10() : void
	{
		$result = Arr::isMultidimensional(ArrTest::$_datasetArray);

		$this->assertTrue($result);
	}

	public function testMethodIsMultidimensionalCase11() : void
	{
		$result = Arr::isMultidimensional(ArrTest::$_recordsetArray);

		$this->assertFalse($result);
	}

	// Arr::fromObject()

	public function testMethodFromObjectCase1() : void
	{
		$result = Arr::fromObject(ArrTest::$_objectEmpty);

		$this->assertEquals([], $result);
	}

	public function testMethodFromObjectCase2() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => 'Web Developer',
			'salary' => 10000,
			'address' => [
				'province' => 'Chonburi',
				'country' => 'Thailand',
				'postcode' => '20270'
			],
			'arrayUnderObject' => [
				'objectUnderArray' => [
					'foo' => 'Foo',
					'bar' => 'Bar'
				]
			]
		];

		$result = Arr::fromObject(ArrTest::$_object);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodFromObjectCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => 'Web Developer',
			'salary' => 10000,
			'address' => [],
			'arrayUnderObject' => []
		];

		$result = Arr::fromObject(ArrTest::$_object, false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodFromObjectCase4() : void
	{
		$expected = [
			'address' => []
		];

		$result = Arr::fromObject(ArrTest::$_object, false, 'address');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
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

		$result = Arr::fromObject(ArrTest::$_object, true, 'name,surname,address');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodFromObjectCase6() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'address' => []
		];

		$result = Arr::fromObject(ArrTest::$_object, false, 'name,surname,address');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::fromString()

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
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::toArray()

	public function testMethodToArrayCase1() : void
	{
		$result = Arr::toArray([]);

		$this->assertEquals([], $result);
	}

	public function testMethodToArrayCase2() : void
	{
		$result = Arr::toArray('');

		$this->assertEquals([''], $result);
	}

	public function testMethodToArrayCase3() : void
	{
		$result = Arr::toArray(null);

		$this->assertEquals([null], $result);
	}

	public function testMethodToArrayCase4() : void
	{
		$result = Arr::toArray(true);

		$this->assertEquals([true], $result);
	}

	public function testMethodToArrayCase5() : void
	{
		$result = Arr::toArray('Nat');

		$this->assertEquals(['Nat'], $result);
	}

	public function testMethodToArrayCase6() : void
	{
		$expected = ArrTest::$_array;

		$result = Arr::toArray(ArrTest::$_array);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase7() : void
	{
		$expected = ArrTest::$_arrayMulti;

		$result = Arr::toArray(ArrTest::$_arrayMulti);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase8() : void
	{
		$expected = ArrTest::$_assocArray;

		$result = Arr::toArray(ArrTest::$_assocArray);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase9() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
		];

		$result = Arr::toArray(ArrTest::$_assocArray, true, 'name,surname');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase10() : void
	{
		$expected = ArrTest::$_assocArrayMulti;

		$result = Arr::toArray(ArrTest::$_assocArrayMulti);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase11() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true,
			'ugly' => false,
			'other' => '',
			'extra' => null
		];

		$result = Arr::toArray(ArrTest::$_assocArrayMulti, false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase12() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => []
		];

		$result = Arr::toArray(ArrTest::$_assocArrayMulti, false, 'name,surname,work');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase13() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
		];

		$result = Arr::toArray(ArrTest::$_assocArrayMulti, true, 'name,surname,work');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase14() : void
	{
		$result = Arr::toArray(ArrTest::$_objectEmpty);

		$this->assertEquals([], $result);
	}

	public function testMethodToArrayCase15() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => 'Web Developer',
			'salary' => 10000,
			'address' => [
				'province' => 'Chonburi',
				'country' => 'Thailand',
				'postcode' => '20270'
			],
			'arrayUnderObject' => [
				'objectUnderArray' => [
					'foo' => 'Foo',
					'bar' => 'Bar'
				]
			]
		];

		$result = Arr::toArray(ArrTest::$_object);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase16() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => 'Web Developer',
			'salary' => 10000,
			'address' => [],
			'arrayUnderObject' => []
		];

		$result = Arr::toArray(ArrTest::$_object, false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase17() : void
	{
		$expected = [
			'address' => []
		];

		$result = Arr::toArray(ArrTest::$_object, false, 'address');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase18() : void
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

		$result = Arr::toArray(ArrTest::$_object, true, 'name,surname,address');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToArrayCase19() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'address' => []
		];

		$result = Arr::toArray(ArrTest::$_object, false, 'name,surname,address');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::toObject()

	public function testMethodToObjectCase1() : void
	{
		$expected = new stdClass();
		$result = Arr::toObject([]);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToObjectCase2() : void
	{
		$expected = ArrTest::$_array;

		$result = Arr::toObject(ArrTest::$_array);

		$this->assertIsObject($result);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase3() : void
	{
		$expected = [
			10,
			20
		];

		$result = Arr::toObject(ArrTest::$_array, 'stdClass', false, '0,1');

		$this->assertIsObject($result);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase4() : void
	{
		$expected = ArrTest::$_arrayMulti;

		$result = Arr::toObject(ArrTest::$_arrayMulti);

		$this->assertIsObject($result);
		$this->assertIsObject($result->{'4'});

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[4] = (array)$result[4];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase5() : void
	{
		$expected = ArrTest::$_arrayMulti;
		$expected[4] = [];

		$result = Arr::toObject(ArrTest::$_arrayMulti, 'stdClass', false);

		$this->assertIsObject($result);
		$this->assertIsObject($result->{'4'});

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[4] = (array)$result[4];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase6() : void
	{
		$expected = ArrTest::$_arrayMulti;
		unset($expected[5]);
		unset($expected[6]);
		unset($expected[7]);

		$result = Arr::toObject(ArrTest::$_arrayMulti, 'stdClass', true, '0,1,2,3,4');

		$this->assertIsObject($result);
		$this->assertIsObject($result->{'4'});

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[4] = (array)$result[4];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase7() : void
	{
		$expected = ArrTest::$_assocArrayMulti;

		$result = Arr::toObject(ArrTest::$_assocArrayMulti);

		$this->assertIsObject($result);
		$this->assertIsObject($result->work);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result['work'] = (array)$result['work'];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase8() : void
	{
		$expected = ArrTest::$_assocArrayMulti;
		$expected['work'] = [];

		$result = Arr::toObject(ArrTest::$_assocArrayMulti, 'stdClass', false);

		$this->assertIsObject($result);
		$this->assertIsObject($result->work);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result['work'] = (array)$result['work'];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase9() : void
	{
		$expected = ArrTest::$_assocArrayMulti;
		unset($expected['surname']);
		unset($expected['age']);
		unset($expected['height']);
		unset($expected['weight']);
		unset($expected['handsome']);
		unset($expected['ugly']);
		unset($expected['other']);
		unset($expected['extra']);

		$result = Arr::toObject(ArrTest::$_assocArrayMulti, 'stdClass', true, 'name,work');

		$this->assertIsObject($result);
		$this->assertIsObject($result->work);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result['work'] = (array)$result['work'];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase10() : void
	{
		$expected = ArrTest::$_datasetArray;

		$result = Arr::toObject(ArrTest::$_datasetArray);

		$this->assertIsObject($result);
		$this->assertIsObject($result->{'0'});
		$this->assertIsObject($result->{'1'});
		$this->assertIsObject($result->{'2'});
		$this->assertIsObject($result->{'3'});
		$this->assertIsObject($result->{'4'});
		$this->assertIsObject($result->{'0'}->work);
		$this->assertIsObject($result->{'1'}->work);
		$this->assertIsObject($result->{'2'}->work);
		$this->assertIsObject($result->{'3'}->work);
		$this->assertIsObject($result->{'4'}->work);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[0] = (array)$result[0];
		$result[1] = (array)$result[1];
		$result[2] = (array)$result[2];
		$result[3] = (array)$result[3];
		$result[4] = (array)$result[4];
		$result[0]['work'] = (array)$result[0]['work'];
		$result[1]['work'] = (array)$result[1]['work'];
		$result[2]['work'] = (array)$result[2]['work'];
		$result[3]['work'] = (array)$result[3]['work'];
		$result[4]['work'] = (array)$result[4]['work'];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase11() : void
	{
		$expected = [
			[],
			[],
			[],
			[],
			[]
		];

		$result = Arr::toObject(ArrTest::$_datasetArray, 'stdClass', false);

		$this->assertIsObject($result);
		$this->assertIsObject($result->{'0'});
		$this->assertIsObject($result->{'1'});
		$this->assertIsObject($result->{'2'});
		$this->assertIsObject($result->{'3'});
		$this->assertIsObject($result->{'4'});

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[0] = (array)$result[0];
		$result[1] = (array)$result[1];
		$result[2] = (array)$result[2];
		$result[3] = (array)$result[3];
		$result[4] = (array)$result[4];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToObjectCase12() : void
	{
		$expected = ArrTest::$_datasetArray;
		unset($expected[1]);
		unset($expected[2]);
		unset($expected[3]);
		unset($expected[4]);

		$result = Arr::toObject(ArrTest::$_datasetArray, 'stdClass', true, '0');

		$this->assertIsObject($result);
		$this->assertIsObject($result->{'0'});
		$this->assertIsObject($result->{'0'}->work);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[0] = (array)$result[0];
		$result[0]['work'] = (array)$result[0]['work'];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Test recursive for object under array
	public function testMethodToObjectCase13() : void
	{
		$object = new stdClass();
		$object->arrayUnderObject = [
			'foo' => 'Foo',
			'bar' => 'Bar'
		];

		ArrTest::$_assocArrayMulti['objectUnderArray'] = $object;

		$result = Arr::toObject(ArrTest::$_assocArrayMulti);

		$this->assertIsObject($result);
		$this->assertIsObject($result->work);
		$this->assertIsObject($result->objectUnderArray);
	}

	// Arr::toString()

	public function testMethodToStringCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::toString([], 'innerGlue', 'outerGlue', 'valueDelimiter', true, 0.1);
	}

	public function testMethodToStringCase2() : void
	{
		$result = Arr::toString([]);

		$this->assertEquals('', $result);
	}

	public function testMethodToStringCase3() : void
	{
		$expected = '0="10" 1="20" 2="A" 3="b" 0="x" 1="y" 5="" 6="1" 7="100"';
		$result = Arr::toString(ArrTest::$_arrayMulti);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase4() : void
	{
		$expected = '0=\'10\' 1=\'20\' 2=\'A\' 3=\'b\' 5=\'\' 6=\'1\' 7=\'100\'';
		$result = Arr::toString(ArrTest::$_arrayMulti, '=', ' ', '\'', false);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase5() : void
	{
		$expected = '1="20"';
		$result = Arr::toString(ArrTest::$_arrayMulti, '=', ' ', '"', false, 1);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase6() : void
	{
		$expected = '1="20"';
		$result = Arr::toString(ArrTest::$_arrayMulti, '=', ' ', '"', false, '1');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase7() : void
	{
		$expected = '0="10" 1="20"';
		$result = Arr::toString(ArrTest::$_arrayMulti, '=', ' ', '"', false, '0,1');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase8() : void
	{
		$expected = 'name="Nat" position="Web Developer" salary="10000" hrscore="9.8" excellent="1" other=""';
		$result = Arr::toString(ArrTest::$_assocArrayMulti, '=', ' ', '"', true, 'name,work');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase9() : void
	{
		$expected = 'name="Nat"';
		$result = Arr::toString(ArrTest::$_assocArrayMulti, '=', ' ', '"', false, 'name,work');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToStringCase10() : void
	{
		$result = Arr::toString(ArrTest::$_datasetArray, '=', ' ', '"', true, 'name');

		$this->assertEquals('', $result);
	}

	public function testMethodToStringCase11() : void
	{
		$result = Arr::toString(ArrTest::$_datasetArray, '=', ' ', '"', false);

		$this->assertEquals('', $result);
	}

	public function testMethodToStringCase12() : void
	{
		$expected = 'name="Nat" surname="Withe" position="Web Developer" salary="10000" '
			. 'name="Rosie" surname="Marshman" position="Staff" salary="8000" '
			. 'name="Emma" surname="McCormick" position="Staff" salary="8000" '
			. 'name="Emma" surname="Miller" position="Project Coordinator" salary="10000" '
			. 'name="Angela" surname="SG" position="Marketing Director" salary="10000"';

		$result = Arr::toString(ArrTest::$_datasetArray);

		$this->assertEquals($expected, $result);
	}

	// Arr::toQueryString

	public function testMethodToQueryStringCase1() : void
	{
		$expected = 'name=Nat&surname=Withe&age=38&height=181&weight=87.5&handsome=1&ugly=0&other=';

		$result = Arr::toQueryString(ArrTest::$_assocArray);

		$this->assertEquals($expected, $result);
	}

	// Arr::toDataset()

	public function testMethodToDatasetCase1() : void
	{
		$expected = [
			['']
		];

		$result = Arr::toDataset('');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToDatasetCase2() : void
	{
		$expected = [
			['value']
		];

		$result = Arr::toDataset('value');

		$this->assertEquals($expected, $result);
	}

	public function testMethodToDatasetCase3() : void
	{
		$expected = [
			['value']
		];

		$result = Arr::toDataset(['value']);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToDatasetCase4() : void
	{
		$expected = [
			['key' => 'value']
		];

		$result = Arr::toDataset(['key' => 'value']);

		$this->assertEquals($expected, $result);
	}

	public function testMethodToDatasetCase5() : void
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

		$result = Arr::toDataset($data);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToDatasetCase6() : void
	{
		$expected = [
			[
				'name' => 'Nat',
				'surname' => 'Withe',
				'work' => 'Web Developer',
				'salary' => 10000
			],
			[
				'name' => 'Rosie',
				'surname' => 'Marshman',
				'work' => 'Staff',
				'salary' => 8000
			],
			[
				'name' => 'Emma',
				'surname' => 'McCormick',
				'work' => 'Staff',
				'salary' => 8000
			],
			[
				'name' => 'Emma',
				'surname' => 'Miller',
				'work' => 'Project Coordinator',
				'salary' => 10000
			],
			[
				'name' => 'Angela',
				'surname' => 'SG',
				'work' => 'Marketing Director',
				'salary' => 10000
			]
		];

		$result = Arr::toDataset(ArrTest::$_recordsetArray);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::toRecordset()

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

		$expected = [
			[
				'name' => 'Nat',
				'surname' => 'Withe'
			]
		];

		$result = Arr::toRecordset($data);

		$this->assertIsArray($result);
		$this->assertIsObject($result[0]);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[0] = (array)$result[0];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToRecordsetCase6() : void
	{
		$data = [
			[
				'name' => 'Nat',
				'surname' => 'Withe',
				'work' => 'Web Developer'
			],
			[
				'name' => 'Angela',
				'surname' => 'SG',
				'work' => 'Marketing Director'
			]
		];

		$expected = $data;

		$result = Arr::toRecordset($data);

		$this->assertIsArray($result);
		$this->assertIsObject($result[0]);
		$this->assertIsObject($result[1]);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result[0] = (array)$result[0];
		$result[1] = (array)$result[1];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::toMultidimensional()

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

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$compare = ($result === $expected);

		$this->assertTrue($compare);
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

	// Arr::toSequential()

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

		$result = Arr::toSequential(ArrTest::$_assocArrayMulti);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$compare = ($result === $expected);

		$this->assertTrue($compare);
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
			],
			[
				[
					'Foo',
					'Bar'
				]
			]
		];

		$result = Arr::toSequential(ArrTest::$_object);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodToSequentialCase11() : void
	{
		$result = Arr::toSequential(ArrTest::$_objectEmpty);

		$this->assertEquals([], $result);
	}

	// Arr::toJSON()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 * @throws ErrorException
	 */
	public function testMethodToJson() : void
	{
		$mockedJson = Mockery::mock('alias:\System\JSON');
		$mockedJson->shouldReceive('encode')->andReturn('[]');

		$result = Arr::toJSON([]);

		$this->assertEquals('[]', $result);
	}

	// Arr::remove()

	public function testMethodRemoveCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::remove([], new stdClass());
	}

	public function testMethodRemoveCase2() : void
	{
		$result = Arr::remove([], 'value');

		$this->assertEquals([], $result);
	}

	public function testMethodRemoveCase3() : void
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

	public function testMethodRemoveCase4() : void
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
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveCase5() : void
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

	public function testMethodRemoveCase6() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
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

		$result = Arr::remove(ArrTest::$_assocArrayMulti, 'Web Developer');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveCase7() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'salary' => 10000,
				'hrscore' => 9.8,
				'other' => ''
			],
			'height' => 181,
			'ugly' => false,
			'other' => ''
		];

		$result = Arr::remove(ArrTest::$_assocArrayMulti, ['Web Developer', 87.5, true, null], false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::removeKey()

	public function testMethodRemoveKeyCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::removeKey([], 3.14);
	}

	public function testMethodRemoveKeyCase2() : void
	{
		$result = Arr::removeKey([], 'missingkey');

		$this->assertEquals([], $result);
	}

	public function testMethodRemoveKeyCase3() : void
	{
		$expected = [
			1 => 20,
			2 => 'A', // upper case
			3 => 'b', // lower case
			4 => null,
			5 => true,
			6 => 100
		];

		$result = Arr::removeKey(ArrTest::$_array, 0);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveKeyCase4() : void
	{
		$expected = [
			10,
			20
		];

		$result = Arr::removeKey(ArrTest::$_array, '2,3,4,5,6');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveKeyCase5() : void
	{
		$expected = [
			1 => 20,
			2 => 'A'
		];

		$result = Arr::removeKey(ArrTest::$_array, '0,3,4,5,6');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveKeyCase6() : void
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

		$result = Arr::removeKey(ArrTest::$_assocArrayMulti, 'work');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveKeyCase7() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::removeKey(ArrTest::$_assocArrayMulti, 'age,work,height,weight,handsome,ugly,other,extra');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveKeyCase8() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
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

		$result = Arr::removeKey(ArrTest::$_assocArrayMulti, 'other', false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::removeType()

	public function testMethodRemoveTypeCase1() : void
	{
		$result = Arr::removeType([], 'string');

		$this->assertEquals([], $result);
	}

	public function testMethodRemoveTypeCase2() : void
	{
		$expected = [
			'age' => 38,
			'work' => [
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

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'string');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => [
				'position' => 'Web Developer',
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

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'int');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase4() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => [
				'position' => 'Web Developer',
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

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'integer');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase6() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
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

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'float');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase7() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
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

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'double');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase8() : void
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

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'array');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase9() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'bool');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase10() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'other' => '',
			'extra' => null
		];

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'boolean');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase11() : void
	{
		$testData = ArrTest::$_assocArrayMulti;
		$data = new stdClass();
		$testData['object'] = $data;

		$expected = ArrTest::$_assocArrayMulti;

		$result = Arr::removeType($testData, 'object');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase12() : void
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

	public function testMethodRemoveTypeCase13() : void
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

	public function testMethodRemoveTypeCase14() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => [
				'position' => 'Web Developer',
				'other' => ''
			],
			'other' => ''
		];

		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'int,float,bool,null');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveTypeCase5() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'work' => [
				'position' => 'Web Developer',
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

		// Set recursive to false.
		$result = Arr::removeType(ArrTest::$_assocArrayMulti, 'int', false);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::removeBlank()

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

		$result = Arr::removeBlank(ArrTest::$_arrayMulti);

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveBlankCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true
			],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true
		];

		$result = Arr::removeBlank(ArrTest::$_assocArrayMulti);

		$this->assertEquals($expected, $result);
	}

	public function testMethodRemoveBlankCase4() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000,
				'hrscore' => 9.8,
				'excellent' => true,
				'other' => ''
			],
			'height' => 181,
			'weight' => 87.5,
			'handsome' => true
		];

		$result = Arr::removeBlank(ArrTest::$_assocArrayMulti, false);

		$this->assertEquals($expected, $result);
	}

	// Arr::pullColumns()

	public function testMethodPullColumnCase1() : void
	{
		$array = [];

		$this->expectException(InvalidArgumentException::class);

		Arr::pullColumns($array, 'missingkey');
	}

	public function testMethodPullColumnCase2() : void
	{
		$expected = [
			[
				'name' => 'Nat',
				'surname' => 'Withe'
			],
			[
				'name' => 'Rosie',
				'surname' => 'Marshman'
			],
			[
				'name' => 'Emma',
				'surname' => 'McCormick'
			],
			[
				'name' => 'Emma',
				'surname' => 'Miller'
			],
			[
				'name' => 'Angela',
				'surname' => 'SG'
			]
		];

		$result = Arr::pullColumns(ArrTest::$_datasetArray, 'name,surname');
		$compare = ($result === $expected);

		$this->assertTrue($compare);

		$this->assertArrayNotHasKey('name', ArrTest::$_datasetArray);
		$this->assertArrayNotHasKey('surname', ArrTest::$_datasetArray);
	}

	public function testMethodPullColumnCase3() : void
	{
		$expected = [
			[
				'name' => 'Nat',
				'surname' => 'Withe'
			],
			[
				'name' => 'Rosie',
				'surname' => 'Marshman'
			],
			[
				'name' => 'Emma',
				'surname' => 'McCormick'
			],
			[
				'name' => 'Emma',
				'surname' => 'Miller'
			],
			[
				'name' => 'Angela',
				'surname' => 'SG'
			]
		];

		$result = Arr::pullColumns(ArrTest::$_recordsetArray, 'name,surname');

		$this->assertIsArray($result);
		$this->assertIsObject($result[0]);
		$this->assertIsObject($result[1]);
		$this->assertIsObject($result[2]);
		$this->assertIsObject($result[3]);
		$this->assertIsObject($result[4]);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result[0] = (array)$result[0];
		$result[1] = (array)$result[1];
		$result[2] = (array)$result[2];
		$result[3] = (array)$result[3];
		$result[4] = (array)$result[4];

		$compare = ($result === $expected);

		$this->assertTrue($compare);

		$this->assertObjectNotHasAttribute('name', ArrTest::$_recordsetArray[0]);
		$this->assertObjectNotHasAttribute('surname', ArrTest::$_recordsetArray[0]);
		$this->assertObjectNotHasAttribute('name', ArrTest::$_recordsetArray[1]);
		$this->assertObjectNotHasAttribute('surname', ArrTest::$_recordsetArray[1]);
		$this->assertObjectNotHasAttribute('name', ArrTest::$_recordsetArray[2]);
		$this->assertObjectNotHasAttribute('surname', ArrTest::$_recordsetArray[2]);
		$this->assertObjectNotHasAttribute('name', ArrTest::$_recordsetArray[3]);
		$this->assertObjectNotHasAttribute('surname', ArrTest::$_recordsetArray[3]);
		$this->assertObjectNotHasAttribute('name', ArrTest::$_recordsetArray[4]);
		$this->assertObjectNotHasAttribute('surname', ArrTest::$_recordsetArray[4]);
	}

	// Arr::removeColumn()

	public function testMethodRemoveColumnCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Arr::removeColumn([], 'missingkey');
	}

	public function testMethodRemoveColumnCase2() : void
	{
		$expected = [
			['name' => 'Nat'],
			['name' => 'Rosie'],
			['name' => 'Emma'],
			['name' => 'Emma'],
			['name' => 'Angela']
		];

		$result = Arr::removeColumn(ArrTest::$_datasetArray, 'surname,work');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodRemoveColumnCase3() : void
	{
		$expected = [];

		$data = new stdClass();
		$data->name = 'Nat';
		$expected[] = $data;

		$data = new stdClass();
		$data->name = 'Rosie';
		$expected[] = $data;

		$data = new stdClass();
		$data->name = 'Emma';
		$expected[] = $data;

		$data = new stdClass();
		$data->name = 'Emma';
		$expected[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';
		$expected[] = $data;

		$result = Arr::removeColumn(ArrTest::$_recordsetArray, 'surname,work,salary');

		$this->assertEquals($expected, $result);
	}

	// Arr::slice()

	public function testMethodSliceCase1() : void
	{
		$result = Arr::slice([], 0);

		$this->assertEquals([], $result);
	}

	public function testMethodSliceCase2() : void
	{
		$expected = [2 => 3, 3 => 4];

		$result = Arr::slice([1 ,2 ,3, 4, 5], 2, 2);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSliceCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe'
		];

		$result = Arr::slice(ArrTest::$_assocArrayMulti, 0, 2);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Arr::unique()

	public function testMethodUniqueCase1() : void
	{
		$result = Arr::unique([]);

		$this->assertEquals([], $result);
	}

	public function testMethodUniqueCase2() : void
	{
		$expected = [1, 'a', 'b', 'c'];

		$result = Arr::unique([1, 1, 'a', 'a', 'b', 'c']);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
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
		$compare = ($result === $expected);

		$this->assertTrue($compare);
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
		$compare = ($result === $expected);

		$this->assertTrue($compare);
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
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}
}
