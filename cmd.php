<?php

// Usage: cmd.php config.yaml...

require __DIR__ . '/vendor/autoload.php';
require 'Migrator.php';

for ( $i = 1; $i < $argc; $i ++ ) {
	
	$migrator = new Stratis\Migrator( $argv[ $i ] );
}