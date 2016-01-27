<?php

// Usage: cmd.php config.yaml...

require __DIR__ . '/vendor/autoload.php';

include 'src/Migrator.php';
include 'src/Converter.php';
include 'src/Config.php';

for ( $i = 1; $i < $argc; $i ++ ) {
	
	$migrator = new Stratis\Migrator( $argv[ $i ] );
}