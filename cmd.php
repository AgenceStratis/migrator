<?php

// Usage: cmd.php config.yaml...

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/autoload.php';

use Stratis\Migrator\Migrator;

for ($i = 1; $i < $argc; $i++) {
	$migrator = new Migrator($argv[$i]);
}