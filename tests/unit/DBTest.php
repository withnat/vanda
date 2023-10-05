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

use PHPUnit\Framework\TestCase;
use stdClass;
use System\DB;

/**
 * Class DBTest
 * @package Tests\Unit
 */
final class DBTest extends TestCase
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

		$result = DB::select('*')->from('Table')->where(1)->load();

		$this->assertInstanceOf('System\DB\Platforms\Mysql', $result);
	}
}
