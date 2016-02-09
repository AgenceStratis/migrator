<?php

namespace Stratis\Component\Migrator\Tests;
use Stratis\Component\Migrator\Migrator;

class MigratorTest extends \PHPUnit_Framework_TestCase
{
	/**
	* @dataProvider confProvider
	*/
	public function testProcess($file)
	{
		$migrator = new Migrator($file);
		$migrator->process();
	}
	
	public static function confProvider()
	{
		return array(
			array(
				// __DIR__ . '/Fixtures/DbToCsv.yml',
				__DIR__ . '/Fixtures/CsvToJson.yml',
			)
		);
	}
}