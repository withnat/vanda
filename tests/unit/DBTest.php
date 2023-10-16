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

use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\DB;

/**
 * Class DBTest
 * @package Tests\Unit
 */
class DBTest extends TestCase
{
	protected function setUp() : void
	{
		//$fakePdo = \Mockery::mock(\PDO::class);
		//$statement = \Mockery::mock(\PDOStatement::class);

/*		$fakePdo
			->shouldReceive('query')
			->withArgs(['UPDATE my table SET field2 = :field2 WHERE field1 = :field1'])
			->once()
			->andReturn(true);*/



		$mockedFolder = \Mockery::mock('alias:\System\Folder');
		$mockedFolder->shouldReceive(['create' => true]);
		$mockedFolder->shouldReceive(['delete' => true]);
	}

	protected function getDataSet()
	{
		echo 'a';
	}

	protected function tearDown() : void
	{
		\Mockery::close();
	}

	// Select

	public function testSelectCase1() : void
	{
		$result = DB::select('*');

		$this->assertInstanceOf('System\DB\Platforms\Mysql', $result);
	}

	public function testSelectCase2() : void
	{
		// ตัวอย่าง
		// https://codepoets.co.uk/2019/mockery-test-doubles-mocking-dependencies/
		// lib ที่น่าจะนำมาใช้ได้ https://github.com/jimbojsb/pseudo
		// https://stackoverflow.com/questions/31903097/mocking-pdo-with-phpunit

		$dataset = new stdClass();
		$dataset->name = 'Nat Withe';


		/*
		$astp = Mockery::mock('System\DB\AbstractPlatform')->makePartial();
		//$astp->shouldAllowMockingProtectedMethods()->makePartial();
		//$astp->shouldReceive('_connect')->andReturnTrue();
		$astp->shouldReceive('query')->with('x')->andReturn($dataset);
		*/

		$db = Mockery::mock('System\DB')->makePartial();
		$db->shouldReceive('query')->andReturn($dataset);

		DB::select('*')->from('Table')->where(1)->load();
		echo DB::getLastQuery();

		/*
		// Create a Mockery mock for the PDO class
		$mockPDO = Mockery::mock(\PDO::class);

		// Set up an expectation for the prepare method
		$statement = Mockery::mock(\PDOStatement::class);
		$mockPDO->shouldReceive('prepare')->andReturn($statement);
		$mockPDO->shouldReceive('query')->andReturnTrue();

		// Use the mock PDO object in your application
		//$myClass = new YourClassUsingPDO($mockPDO);

		$query = Mockery::mock('\PDOStatement');
		$query->shouldReceive('execute')->andReturnTrue();

		$db = Mockery::mock('\PDO');
		$db->shouldReceive('query')->andReturn($query);


		DB::select('*')->from('Table')->where(1)->load();
		echo DB::getLastQuery();

		//$this->assertInstanceOf('System\DB\Platforms\Mysql', $result);
		*/
		$this->assertTrue(true);
	}
}
