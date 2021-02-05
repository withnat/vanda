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
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Xml;

/**
 * Class XmlTest
 * @package Tests\Unit
 */
class XmlTest extends TestCase
{
	protected static $_dataset;
	protected static $_recordset;
	protected static $xml;

	protected function setUp() : void
	{
		static::$_dataset = [
			[
				'name' => 'Nat',
				'surname' => 'Withe',
				'job' => 'Web Developer',
				'salary' => 10000
			],
			[
				'name' => 'Angela',
				'surname' => 'SG',
				'job' => 'Marketing Director',
				'salary' => 10000
			]
		];

		//

		static::$_recordset = [];

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->job = 'Web Developer';
		$data->salary = 10000;

		static::$_recordset[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';
		$data->surname = 'SG';
		$data->job = 'Marketing Director';
		$data->salary = 10000;

		static::$_recordset[] = $data;

		//

		static::$xml = "<root>\n";
		static::$xml .= "\t<element>\n";
		static::$xml .= "\t\t<name>Nat</name>\n";
		static::$xml .= "\t\t<surname>Withe</surname>\n";
		static::$xml .= "\t\t<job>Web Developer</job>\n";
		static::$xml .= "\t\t<salary>10000</salary>\n";
		static::$xml .= "\t</element>\n";
		static::$xml .= "\t<element>\n";
		static::$xml .= "\t\t<name>Angela</name>\n";
		static::$xml .= "\t\t<surname>SG</surname>\n";
		static::$xml .= "\t\t<job>Marketing Director</job>\n";
		static::$xml .= "\t\t<salary>10000</salary>\n";
		static::$xml .= "\t</element>\n";
		static::$xml .= "</root>\n";
	}

	protected function tearDown() : void
	{
		static::$_dataset = null;
		static::$_recordset = null;
		static::$xml = null;

		Mockery::close();
	}

	// Xml::fromDataset()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromDatasetCase1() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('isDataset')->andReturnFalse();

		$this->expectException(InvalidArgumentException::class);

		Xml::fromDataset(['string']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromDatasetCase2() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('isDataset')->andReturnTrue();

		$result = Xml::fromDataset(static::$_dataset);

		$this->assertEquals(static::$xml, $result);
	}

	// Xml::fromRecordset()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromRecordsetCase1() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('isRecordset')->andReturnFalse();

		$this->expectException(InvalidArgumentException::class);

		Xml::fromRecordset(['string']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromRecordsetCase2() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('isRecordset')->andReturnTrue();

		$result = Xml::fromRecordset(static::$_recordset);

		$this->assertEquals(static::$xml, $result);
	}

	// Xml::toArray()

	public function testMethodToArrayCase1() : void
	{
		$result = Xml::toArray('');

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodToArrayCase2() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('fromObject')->andReturn([]);

		$result = Xml::toArray(static::$xml);

		$this->assertEquals([], $result);
	}

	// Xml::toObject()

	public function testMethodToObjectCase1() : void
	{
		$result = Xml::toObject('');

		$this->assertFalse($result);
	}

	public function testMethodToObjectCase2() : void
	{
		$expected = [
			'element' => [
				[
					'name' => 'Nat',
					'surname' => 'Withe',
					'job' => 'Web Developer',
					'salary' => '10000' // string.
				],
				[
					'name' => 'Angela',
					'surname' => 'SG',
					'job' => 'Marketing Director',
					'salary' => '10000' // string.
				]
			]
		];

		$result = Xml::toObject(static::$xml);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result['element'][0] = (array)$result['element'][0];
		$result['element'][1] = (array)$result['element'][1];
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Xml::toDataset()

	public function testMethodToDatasetCase1() : void
	{
		$result = Xml::toDataset('');

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodToDatasetCase2() : void
	{
		$mockedArr = Mockery::mock('alias:\System\Arr');
		$mockedArr->shouldReceive('fromObject')->andReturn([]);

		$result = Xml::toArray(static::$xml);

		$this->assertEquals([], $result);
	}

	// Xml::toRecordset()

	public function testMethodToRecordsetCase1() : void
	{
		$result = Xml::toRecordset('');

		$this->assertFalse($result);
	}

	public function testMethodToRecordsetCase2() : void
	{
		$expected = [
			'element' => [
				[
					'name' => 'Nat',
					'surname' => 'Withe',
					'job' => 'Web Developer',
					'salary' => '10000' // string.
				],
				[
					'name' => 'Angela',
					'surname' => 'SG',
					'job' => 'Marketing Director',
					'salary' => '10000' // string.
				]
			]
		];

		$result = Xml::toRecordset(static::$xml);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result['element'][0] = (array)$result['element'][0];
		$result['element'][1] = (array)$result['element'][1];
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Xml::safe()

	public function testMethodSafeCase1() : void
	{
		$expected = 'string&amp;&lt;&gt;&quot;&apos;&#45;';

		$result = Xml::safe('string&<>"\'-', true);

		$this->assertEquals($expected, $result);
	}
}
