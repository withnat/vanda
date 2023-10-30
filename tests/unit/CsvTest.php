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
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Csv;

/**
 * Class CsvTest
 * @package Tests\Unit
 */
class CsvTest extends TestCase
{
	use \phpmock\phpunit\PHPMock;

	private $fs;

	protected static $_data;
	protected static $_csv1;

	protected static $_dataset;
	protected static $_recordset;
	protected static $_csv2;

	protected function setUp() : void
	{
		$baseDir = vfsStream::setup('project');

		$structure = [
			'file.csv' => '"name","surname","job","salary"' . PHP_EOL . PHP_EOL .
							'"Nat","Withe","Web Developer","10000"' . PHP_EOL .
							'"Angela","SG","Marketing Director","10000"' . PHP_EOL. PHP_EOL
		];

		vfsStream::create($structure, $baseDir);
		$this->fs = vfsStream::url('project');

		static::$_data = [
			['aaa', 'bbb', 'ccc', 'dddd'],
			['123', '456', '789'],
			['"aaa"', '"bbb"']
		];

		//

		static::$_csv1 = '"aaa","bbb","ccc","dddd"' . PHP_EOL
			. '"123","456","789"' . PHP_EOL
			. '"""aaa""","""bbb"""';

		//

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

		static::$_csv2 = '"name","surname","job","salary"' . PHP_EOL
					. '"Nat","Withe","Web Developer","10000"' . PHP_EOL
					. '"Angela","SG","Marketing Director","10000"';
	}

	protected function tearDown() : void
	{
		static::$_data = null;
		static::$_csv1 = null;
		static::$_dataset = null;
		static::$_recordset = null;
		static::$_csv2 = null;

		Mockery::close();
	}

	// Csv::read()

	public function testMethodReadCase1() : void
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
				'10000' // SplFileObject() converts number to string.
			],
			[
				'Angela',
				'SG',
				'Marketing Director',
				'10000' // SplFileObject() converts number to string.
			]
		];

		$result = Csv::read($this->fs . '/file.csv');
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Csv::write()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodWriteCase1() : void
	{
		$expected = static::$_csv2;

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('isDataset')->andReturnTrue();
		$stubArr->shouldReceive('isRecordset')->andReturnFalse();
		$stubArr->shouldReceive('isAssociative')->andReturnTrue();

		Csv::write($this->fs . '/file.csv', static::$_dataset);

		$content = file_get_contents($this->fs . '/file.csv');

		$this->assertEquals($expected, $content);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodWriteCase2() : void
	{
		$expected = static::$_csv2;

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('isDataset')->andReturnFalse();
		$stubArr->shouldReceive('isRecordset')->andReturnTrue();

		Csv::write($this->fs . '/file.csv', static::$_recordset);

		$content = file_get_contents($this->fs . '/file.csv');

		$this->assertEquals($expected, $content);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodWriteCase3() : void
	{
		$expected = static::$_csv1;

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('isDataset')->andReturnFalse();
		$stubArr->shouldReceive('isRecordset')->andReturnFalse();
		$stubArr->shouldReceive('toMultidimensional')->andReturn(static::$_data);

		Csv::write($this->fs . '/file.csv', static::$_data);

		$content = file_get_contents($this->fs . '/file.csv');

		$this->assertEquals($expected, $content);
	}

	// Csv::fromArray()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodFromArrayCase1() : void
	{
		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toMultidimensional')->andReturn(static::$_data);

		$result = Csv::fromArray(static::$_data);

		$this->assertEquals(static::$_csv1, $result);
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
		$stubArr->shouldReceive('isRecordset')->andReturnFalse();
		$stubArr->shouldReceive('isAssociative')->andReturnTrue();

		$result = Csv::fromDataset(static::$_dataset);

		$this->assertEquals(static::$_csv2, $result);
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

		$this->assertEquals(static::$_csv2, $result);
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

		$result = Csv::toArray(static::$_csv2);
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

		$result = Csv::toDataset(static::$_csv2);

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
		$stubArr->shouldReceive('toRecordset')->once()->andReturn([new stdClass()]);

		Csv::toRecordset(static::$_csv2);

		$this->assertTrue(true);
	}

	// Csv::safe() (tested via another methods)
}
