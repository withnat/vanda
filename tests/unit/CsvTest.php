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
use System\Csv;

/**
 * Class CsvTest
 * @package Tests\Unit
 */
class CsvTest extends TestCase
{
	protected static $_dataset;
	protected static $_recordset;
	protected static $_csvString;

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

		static::$_csvString = '"name","surname","work","salary"' . "\n";
		static::$_csvString .= '"Nat","Withe","Web Developer","10000"' . "\n";
		static::$_csvString .= '"Angela","SG","Marketing Director","10000"' . "\n";
	}

	protected function tearDown() : void
	{
		static::$_dataset = null;
		static::$_recordset = null;
		static::$_csvString = null;
	}

	// Csv::fromDataset()

	public function testMethodFromDatasetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Csv::fromDataset(['string']);
	}

	public function testMethodFromDatasetCase2() : void
	{
		$result = Csv::fromDataset(static::$_dataset);

		$this->assertEquals(static::$_csvString, $result);
	}

	// Csv::fromRecordset()

	public function testMethodFromRecordsetCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Csv::fromRecordset(['string']);
	}

	public function testMethodFromRecordsetCase2() : void
	{
		$result = Csv::fromRecordset(static::$_recordset);

		$this->assertEquals(static::$_csvString, $result);
	}

	// Csv::toArray()

	public function testMethodToArrayCase1() : void
	{
		$expected = [
			[
				'name',
				'surname',
				'work',
				'salary'
			],
			[
				'Nat',
				'Withe',
				'Web Developer',
				'10000' // Csv::toArray() will converts number to string.
			],
			[
				'Angela',
				'SG',
				'Marketing Director',
				'10000' // Csv::toArray() will converts number to string.
			]
		];

		$result = Csv::toArray(static::$_csvString);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::toDataset()

	public function testMethodToAssociativeCase1() : void
	{
		// Csv::toArray() will converts number to string.
		$expected = static::$_dataset;
		$expected[0]['salary'] = '10000';
		$expected[1]['salary'] = '10000';

		$result = Csv::toDataset(static::$_csvString);

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::toRecordset()

	public function testMethodToRecordsetCase1() : void
	{
		$expected = static::$_recordset;
		$expected = (array)$expected;

		$expected[0] = (array)$expected[0];
		$expected[1] = (array)$expected[1];

		// Csv::toArray() will converts number to string.
		$expected[0]['salary'] = '10000';
		$expected[1]['salary'] = '10000';

		$result = Csv::toRecordset(static::$_csvString);

		// Compare in array mode to ensure $expected and $result are
		// same key/value pairs in the same order and of the same types.
		$result = (array)$result;
		$result[0] = (array)$result[0];
		$result[1] = (array)$result[1];

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::safe() (tested via another methods)
}
