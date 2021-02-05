<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2021, Nat Withe. All rights reserved.
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Data;

/**
 * Class DataTest
 * @package Tests\Unit
 */
class DataTest extends TestCase
{
	protected static $_dataArray;
	protected static $_dataArrayAssoc;
	protected static $_dataObject;

	protected function setUp() : void
	{
		static::$_dataArray = [
			'A',
			'B',
			['X', 'Y']
		];

		$work = new stdClass();
		$work->position = 'Web Developer';
		$work->salary = 10000;

		static::$_dataArrayAssoc = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => $work
		];

		static::$_dataObject = new stdClass();
		static::$_dataObject->name = 'Nat';
		static::$_dataObject->surname = 'Withe';
		static::$_dataObject->age = 38;
		static::$_dataObject->work = $work;
	}

	protected function tearDown() : void
	{
		static::$_dataArray = null;
		static::$_dataArrayAssoc = null;
		static::$_dataObject = null;
	}

	// Data::get()

	public function testMethodGetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Data::get('', 'key');
	}

	public function testMethodGetCase2() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Data::get([], 3.14);
	}

	public function testMethodGetCase3() : void
	{
		$result = Data::get([], 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase4() : void
	{
		$result = Data::get([], 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase5() : void
	{
		$result = Data::get(static::$_dataArray, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase6() : void
	{
		$result = Data::get(static::$_dataArray, 0);

		$this->assertEquals('A', $result);
	}

	public function testMethodGetCase7() : void
	{
		$result = Data::get(static::$_dataArray, '2.0.missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase8() : void
	{
		$result = Data::get(static::$_dataArray, '2.0');

		$this->assertEquals('X', $result);
	}

	public function testMethodGetCase9() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase10() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase11() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'name');

		$this->assertEquals('Nat', $result);
	}

	public function testMethodGetCase12() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'work.missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase13() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'work.position.missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase14() : void
	{
		$result = Data::get(static::$_dataArrayAssoc, 'work.position');

		$this->assertEquals('Web Developer', $result);
	}

	public function testMethodGetCase15() : void
	{
		$result = Data::get(static::$_dataObject, 'missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase16() : void
	{
		$result = Data::get(static::$_dataObject, 'missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase17() : void
	{
		$result = Data::get(static::$_dataObject, 'name');

		$this->assertEquals('Nat', $result);
	}

	public function testMethodGetCase18() : void
	{
		$result = Data::get(static::$_dataObject, 'work.missingkey', 'I love you.');

		$this->assertEquals('I love you.', $result);
	}

	public function testMethodGetCase19() : void
	{
		$result = Data::get(static::$_dataObject, 'work.missingkey');

		$this->assertNull($result);
	}

	public function testMethodGetCase20() : void
	{
		$result = Data::get(static::$_dataObject, 'work.position');

		$this->assertEquals('Web Developer', $result);
	}

	// Data::set()

	public function testMethodSetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Data::set('', 'key', 'value');
	}

	public function testMethodSetCase2() : void
	{
		$expected = [
			[[['C']]],
			'B',
			['X', 'Y']
		];

		$result = Data::set(static::$_dataArray, '0.0.0.0', 'C');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodSetCase3() : void
	{
		$expected = [
			'name' => 'Nat',
			'surname' => 'Withe',
			'age' => 38,
			'work' => [
				'position' => 'Web Developer',
				'salary' => 10000,
				'a' => [
					'b' => 'C'
				]
			]
		];

		$result = Data::set(static::$_dataObject, 'work.a.b', 'C');

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result['work'] = (array)$result['work'];
		$result['work']['a'] = (array)$result['work']['a'];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Data::ensureBool()

	public function testMethodEnsureBoolCase1() : void
	{
		$result = Data::ensureBool('');

		$this->assertFalse($result);
	}

	public function testMethodEnsureBoolCase2() : void
	{
		$result = Data::ensureBool('null');

		$this->assertFalse($result);
	}

	public function testMethodEnsureBoolCase3() : void
	{
		$result = Data::ensureBool(null);

		$this->assertFalse($result);
	}

	public function testMethodEnsureBoolCase4() : void
	{
		$result = Data::ensureBool(0);

		$this->assertFalse($result);
	}

	public function testMethodEnsureBoolCase5() : void
	{
		$result = Data::ensureBool(-1);

		$this->assertFalse($result);
	}

	public function testMethodEnsureBoolCase6() : void
	{
		$result = Data::ensureBool(1);

		$this->assertTrue($result);
	}

	public function testMethodEnsureBoolCase7() : void
	{
		$result = Data::ensureBool('1');

		$this->assertTrue($result);
	}

	public function testMethodEnsureBoolCase8() : void
	{
		$result = Data::ensureBool(2);

		$this->assertTrue($result);
	}

	public function testMethodEnsureBoolCase9() : void
	{
		$result = Data::ensureBool(true);

		$this->assertTrue($result);
	}

	public function testMethodEnsureBoolCase10() : void
	{
		$result = Data::ensureBool('true');

		$this->assertTrue($result);
	}

	public function testMethodEnsureBoolCase11() : void
	{
		$result = Data::ensureBool(false);

		$this->assertFalse($result);
	}

	public function testMethodEnsureBoolCase12() : void
	{
		$result = Data::ensureBool('on');

		$this->assertTrue($result);
	}

	public function testMethodEnsureBoolCase13() : void
	{
		$result = Data::ensureBool('yes');

		$this->assertTrue($result);
	}

	// Data::ensureString()

	public function testMethodEnsureStringCase1() : void
	{
		$result = Data::ensureString(true);

		$this->assertIsString($result);
		$this->assertEquals('true', $result);
	}

	public function testMethodEnsureStringCase2() : void
	{
		$result = Data::ensureString(false);

		$this->assertIsString($result);
		$this->assertEquals('false', $result);
	}

	public function testMethodEnsureStringCase3() : void
	{
		$result = Data::ensureString(3.14);

		$this->assertIsString($result);
		$this->assertEquals('3.14', $result);
	}

	// Data::ensureInt()

	public function testMethodEnsureIntCase1() : void
	{
		$result = Data::ensureInt(3.14);

		$this->assertIsInt($result);
		$this->assertEquals(3, $result);
	}

	// Data::ensureFloat()

	public function testMethodEnsureFloatCase1() : void
	{
		$result = Data::ensureFloat(13);

		$this->assertIsFloat($result);
		$this->assertEquals(13, $result);
	}

	// Data::ensureArray()

	public function testMethodEnsureArrayCase1() : void
	{
		$result = Data::ensureArray('');

		$this->assertIsArray($result);
		$this->assertEquals([], $result);
	}

	public function testMethodEnsureArrayCase2() : void
	{
		$result = Data::ensureArray(3.14);

		$this->assertIsArray($result);
		$this->assertEquals([3.14], $result);
	}

	public function testMethodEnsureArrayCase3() : void
	{
		$result = Data::ensureArray('value');

		$this->assertIsArray($result);
		$this->assertEquals(['value'], $result);
	}

	public function testMethodEnsureArrayCase4() : void
	{
		$result = Data::ensureArray('(0)');

		$this->assertIsArray($result);
		$this->assertEquals([0], $result);
	}

	public function testMethodEnsureArrayCase5() : void
	{
		$result = Data::ensureArray('(invalidsyntax)');

		$this->assertIsArray($result);
		$this->assertEquals([], $result);
	}

	public function testMethodEnsureArrayCase6() : void
	{
		$result = Data::ensureArray('[0]');

		$this->assertIsArray($result);
		$this->assertEquals([0], $result);
	}

	public function testMethodEnsureArrayCase7() : void
	{
		$result = Data::ensureArray('[invalidsyntax]');

		$this->assertIsArray($result);
		$this->assertEquals([], $result);
	}

	public function testMethodEnsureArrayCase8() : void
	{
		$result = Data::ensureArray('(\'value\')');

		$this->assertIsArray($result);
		$this->assertEquals(['value'], $result);
	}

	public function testMethodEnsureArrayCase9() : void
	{
		$result = Data::ensureArray('[\'value\']');

		$this->assertIsArray($result);
		$this->assertEquals(['value'], $result);
	}

	// Data::ensureObject()

	public function testMethodEnsureObjectCase1() : void
	{
		$result = Data::ensureObject('value');

		$this->assertIsObject($result);
		$this->assertEquals('value', $result->scalar);
	}

	// Data::isCountable()

	public function testMethodIsCountableCase1() : void
	{
		$result = Data::isCountable([]);

		$this->assertTrue($result);
	}

	public function testMethodIsCountableCase2() : void
	{
		$result = Data::isCountable('string');

		$this->assertFalse($result);
	}

	// Data::expects()

	// PHPUnit 7.5 doesn’t have a doNotExpectException assertion,
	// nor does it allow a test without an assertion.

	// We can easily overcome these limitations by adding an assertion
	// to the end of the test ($this->assertTrue(true)) or by increasing
	// the assertion count ($this->addToAssertionCount(1)). If the
	// MyClass::doSomething method implementation is incorrect,
	// an exception will be thrown, otherwise, the assertion will be
	// accounted for and PHPUnit will not “complain” about the test.

	public function testMethodExpectsCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Data::expects(['string'], 1, 3.14);
	}

	public function testMethodExpectsCase2() : void
	{
		Data::expects('string', 1, 'value');

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase3() : void
	{
		Data::expects('int', 1, 13);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase4() : void
	{
		Data::expects('integer', 1, 13);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase5() : void
	{
		Data::expects('float', 1, 3.14);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase6() : void
	{
		Data::expects('double', 1, 3.14);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase7() : void
	{
		Data::expects('bool', 1, true);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase8() : void
	{
		Data::expects('boolean', 1, true);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase9() : void
	{
		Data::expects('array', 1, []);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase10() : void
	{
		Data::expects('object', 1, new stdClass());

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase11() : void
	{
		Data::expects('null', 1, null);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase12() : void
	{
		Data::expects('resource', 1, tmpfile());

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase13() : void
	{
		Data::expects(['string', 'int'], 1, 13);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase14() : void
	{
		Data::expects(['int', 'float'], 1, 3.14);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase15() : void
	{
		Data::expects(['array','bool'], 1, true);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase16() : void
	{
		Data::expects('string,int', 1, 13);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase17() : void
	{
		Data::expects('int,float', 1, 3.14);

		$this->assertTrue(true);
	}

	public function testMethodExpectsCase18() : void
	{
		Data::expects('array,bool', 1, true);

		$this->assertTrue(true);
	}

	// Data::convert()

	public function testMethodConvertCase1() : void
	{
		$data = 3.14;

		Data::convert('float', 'string', $data);

		$this->assertIsString($data);
		$this->assertEquals('3.14', $data);
	}

	public function testMethodConvertCase2() : void
	{
		$data = 3.14;

		Data::convert('float', 'int', $data);

		$this->assertIsInt($data);
		$this->assertEquals(3, $data);
	}

	public function testMethodConvertCase3() : void
	{
		$data = '3.14';

		Data::convert('string', 'float', $data);

		$this->assertIsFloat($data);
		$this->assertEquals(3.14, $data);
	}

	public function testMethodConvertCase4() : void
	{
		$data = '3.14';

		Data::convert('string', 'bool', $data);

		$this->assertIsBool($data);
		$this->assertFalse($data);
	}

	public function testMethodConvertCase5() : void
	{
		$data = 13;

		Data::convert('int', 'null', $data);

		$this->assertNull($data);
	}

	public function testMethodConvertCase6() : void
	{
		$data = 3.14;

		Data::convert('float', 'bool', $data);

		$this->assertIsBool($data);
		$this->assertTrue($data);
	}

	public function testMethodConvertCase7() : void
	{
		$data = true;

		Data::convert('bool', 'string', $data);

		$this->assertEquals('true', $data);
	}

	public function testMethodConvertCase8() : void
	{
		$data = 3.14;

		Data::convert('float', 'array', $data);

		$this->assertIsArray($data);
		$this->assertEquals([3.14], $data);
	}

	public function testMethodConvertCase9() : void
	{
		$data = 3.14;

		Data::convert('float', 'object', $data);

		$this->assertIsObject($data);
		$this->assertEquals(3.14, $data->{'scalar'});
	}

	public function testMethodConvertCase10() : void
	{
		$data = 3.14;

		Data::convert('float', 'null', $data);

		$this->assertNull($data);
	}
}
