<?php

// https://github.com/symfony/console
// http://symfony.com/doc/current/components/console/introduction.html

require __DIR__ . '/vendor/autoload.php';
require 'converter.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

class ConverterCommand extends Command {
	
	protected function configure () {
		
		$this
			->setName( 'converter' )
			->setDescription( 'Convert data' )
			->addArgument( 'yaml', InputArgument::IS_ARRAY, 'YAML configuration file' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		
		$files = $input->getArgument( 'yaml' );
		
		$output->writeln( print_r( $files ) );
		

// for ( $i = 1; $i < $argc; $i++ ) {
// 	$converter = new Converter( $argv[ $i ] );
// 	$converter->run();
// }
		
		
		// $converter = new Converter();
		// $converter->setReader( 'testdata.csv', 'csv' ); // array(file,type)
		// $converter->setWriter( '', 'sql' ); //array(type,table)
		// $converter->setScheme( $scheme );
		// $converter->run();
		
		// $output->writeln( print_r( $writer ) );
	}
}

$app = new Application();
$app->add( new ConverterCommand());
$app->run();