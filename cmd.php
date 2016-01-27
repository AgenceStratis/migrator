<?php

// Usage: cmd.php config.yaml...

require __DIR__ . '/vendor/autoload.php';
require 'migrator.php';

for ( $i = 0; $i < $argc; $i ++ ) {
	
	$migrator = new Migrator( $argv[ $i ] );
	$migrator->run();
}