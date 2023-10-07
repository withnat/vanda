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

use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\DB\AbstractPlatform;

/**
 * Class AbstractPlatformTest
 * @package Tests\Unit
 */
class AbstractPlatformTest extends TestCase
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

		$astp = Mockery::mock(AbstractPlatform::class);
		$astp->shouldAllowMockingProtectedMethods()->makePartial();
		$astp->shouldReceive('_connect')->andReturnTrue();
		/*
		$astp->shouldReceive('select')->andReturnUsing(function () {
			return new AbstractPlatform(); // Return an instance of MyClass
		});
		*/
		$astp->shouldReceive('query')->with('x')->andReturn($dataset);

		$astp->select('*')->from('Table')->where(1)->load();
		echo $astp->getLastQuery();

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

	public function testSelectCase3() : void
	{






		$dataset = new stdClass();
		$dataset->name = 'Nat Withe';

		//$ap = Mockery::mock(AbstractPlatform::class);
		$ap = Mockery::mock('System\DB\AbstractPlatform');
		$ap->shouldAllowMockingProtectedMethods()->makePartial();
		$ap->shouldReceive('_connect')->andReturnTrue();
		$ap->shouldReceive('query')->andReturn(new \PDOStatement());

		$ap->shouldReceive('quote')
			->andReturnUsing(function ($arg)
			{
				switch ($arg)
				{
					case 'Nat':
						return '\'Nat\'';
					case 'Withe':
						return '\'Withe\'';
					case '10':
						return '\'10\'';
				}
			});

		//$result = $ap->escapeLike('Nat');
		//echo $result;exit;

		//$result = $ap->escapeLike('Nat');
		//echo $result;exit;

		$ap->select('*')->from('Table')->where('name = :name AND surname = :surname', ['name'=>'Nat', 'surname'=>'Withe'])->load();
		echo $ap->getLastQuery();

		$ap->select('name')->from('User')->where('id', '<', 10)->take(5)->load();
		$ap->select('id');
		$ap->select('name')->from('User')->where('id', '<', 10)->take(5)->load();
		$ap->from('User')->avg('id');
		$ap->from('User')->count('id');
		$ap->from('User')->countDistinct('id');
		$ap->from('User')->min('id');
		$ap->from('User')->max('id');
		$ap->from('User')->std('id');
		$ap->from('User')->sum('id');
		//$ap->from('User')->distinct('id');
		echo $ap->getLastQuery();

		$this->assertTrue(true);
	}
}
