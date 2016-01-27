<?php

// https://github.com/symfony/console
// http://symfony.com/doc/current/components/console/introduction.html

require __DIR__ . '/vendor/autoload.php';
require 'migrator.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

class MigratorCommand extends Command {
	
	protected function configure () {
		
		$this
			->setName( 'migrator' )
			->setDescription( 'Convert data' )
			->addArgument( 'yaml', InputArgument::IS_ARRAY, 'YAML configuration file' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		
		foreach ( $input->getArgument( 'yaml' ) as $yaml ) {
			$converter = new Migrator( $yaml );
			$converter->run();
		}
		
		// $output->writeln( print_r( $files ) );
	}
}

$app = new Application();
$app->add( new MigratorCommand());
$app->run();