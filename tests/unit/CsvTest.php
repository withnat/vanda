<?php
/*
 * __      __             _
 * \ \    / /            | |
 *  \ \  / /_ _ _ __   __| | __ _
 *   \ \/ / _` | '_ \ / _` |/ _` |
 *    \  / (_| | | | | (_| | (_| |
 *     \/ \__,_|_| |_|\__,_|\__,_|
 *
 * Vanda
 *
 * A lightweight & flexible PHP web framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use Mockery;
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
	protected static $_csv;

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

		static::$_csv = '"name","surname","job","salary"' . "\n";
		static::$_csv .= '"Nat","Withe","Web Developer","10000"' . "\n";
		static::$_csv .= '"Angela","SG","Marketing Director","10000"' . "\n";
	}

	protected function tearDown() : void
	{
		static::$_dataset = null;
		static::$_recordset = null;
		static::$_csv = null;

		Mockery::close();
	}

	// Csv::fromDataset()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromDatasetCase1() : void
	{
		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('isDataset')->andReturnFalse();

		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('dataset');

		$this->expectException(InvalidArgumentException::class);

		Csv::fromDataset(['string']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromDatasetCase2() : void
	{
		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('isDataset')->andReturnTrue();

		$result = Csv::fromDataset(static::$_dataset);

		$this->assertEquals(static::$_csv, $result);
	}

	// Csv::fromRecordset()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromRecordsetCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('recordset');

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('isRecordset')->andReturnFalse();

		$this->expectException(InvalidArgumentException::class);

		Csv::fromRecordset(['string']);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromRecordsetCase2() : void
	{
		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('isRecordset')->andReturnTrue();

		$result = Csv::fromRecordset(static::$_recordset);

		$this->assertEquals(static::$_csv, $result);
	}

	// Csv::toArray()

	public function testMethodToArrayCase1() : void
	{
		$expected = [
			[
				'name',
				'surname',
				'job',
				'salary'
			],
			[
				'Nat',
				'Withe',
				'Web Developer',
				'10000' // Csv::toArray() converts number to string.
			],
			[
				'Angela',
				'SG',
				'Marketing Director',
				'10000' // Csv::toArray() converts number to string.
			]
		];

		$result = Csv::toArray(static::$_csv);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::toDataset()

	public function testMethodToDatasetCase1() : void
	{
		// Csv::toArray() converts number to string.
		$expected = static::$_dataset;
		$expected[0]['salary'] = '10000';
		$expected[1]['salary'] = '10000';

		$result = Csv::toDataset(static::$_csv);

		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::toRecordset()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodToRecordsetCase1() : void
	{
		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toObject')->andReturn(new stdClass());

		$result = Csv::toRecordset(static::$_csv);

		$this->assertInstanceOf('stdClass', $result);
	}

	// Csv::safe() (tested via another methods)
}
