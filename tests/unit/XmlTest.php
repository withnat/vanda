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
use System\Xml;

/**
 * Class XmlTest
 * @package Tests\Unit
 */
class XmlTest extends TestCase
{
	protected static $_dataset;
	protected static $_recordset;
	protected static $_xmlString;

	protected function setUp() : void
	{
		static::$_dataset = [
			[
				'name' => 'Nat',
				'surname' => 'Withe',
				'work' => 'Web Developer',
				'salary' => 10000
			],
			[
				'name' => 'Angela',
				'surname' => 'SG',
				'work' => 'Marketing Director',
				'salary' => 10000
			]
		];

		//

		static::$_recordset = [];

		$data = new stdClass();
		$data->name = 'Nat';
		$data->surname = 'Withe';
		$data->work = 'Web Developer';
		$data->salary = 10000;

		static::$_recordset[] = $data;

		$data = new stdClass();
		$data->name = 'Angela';
		$data->surname = 'SG';
		$data->work = 'Marketing Director';
		$data->salary = 10000;

		static::$_recordset[] = $data;

		//

		static::$_xmlString = "<root>\n";
		static::$_xmlString .= "\t<element>\n";
		static::$_xmlString .= "\t\t<name>Nat</name>\n";
		static::$_xmlString .= "\t\t<surname>Withe</surname>\n";
		static::$_xmlString .= "\t\t<work>Web Developer</work>\n";
		static::$_xmlString .= "\t\t<salary>10000</salary>\n";
		static::$_xmlString .= "\t</element>\n";
		static::$_xmlString .= "\t<element>\n";
		static::$_xmlString .= "\t\t<name>Angela</name>\n";
		static::$_xmlString .= "\t\t<surname>SG</surname>\n";
		static::$_xmlString .= "\t\t<work>Marketing Director</work>\n";
		static::$_xmlString .= "\t\t<salary>10000</salary>\n";
		static::$_xmlString .= "\t</element>\n";
		static::$_xmlString .= "</root>\n";
	}

	protected function tearDown() : void
	{
		static::$_dataset = null;
		static::$_recordset = null;
		static::$_xmlString = null;
	}

	// Xml::fromDataset()

	public function testMethodFromDatasetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Xml::fromDataset(['string']);
	}

	public function testMethodFromDatasetCase2() : void
	{
		$result = Xml::fromDataset(static::$_dataset);

		$this->assertEquals(static::$_xmlString, $result);
	}

	// Xml::fromRecordset()

	public function testMethodFromRecordsetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Xml::fromRecordset(['string']);
	}

	public function testMethodFromRecordsetCase2() : void
	{
		$result = Xml::fromRecordset(static::$_recordset);

		$this->assertEquals(static::$_xmlString, $result);
	}

	// Xml::toArray()

	public function testMethodToArrayCase1() : void
	{
		$expected = [
			'element' => [
				[
					'name' => 'Nat',
					'surname' => 'Withe',
					'work' => 'Web Developer',
					'salary' => '10000' // Xml::toArray() will converts number to string.
				],
				[
					'name' => 'Angela',
					'surname' => 'SG',
					'work' => 'Marketing Director',
					'salary' => '10000' // Xml::toArray() will converts number to string.
				]
			]
		];

		$result = Xml::toArray(static::$_xmlString);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
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
					'work' => 'Web Developer',
					'salary' => '10000' // string.
				],
				[
					'name' => 'Angela',
					'surname' => 'SG',
					'work' => 'Marketing Director',
					'salary' => '10000' // string.
				]
			]
		];

		$result = Xml::toObject(static::$_xmlString);

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
