<?php

// Usage: cmd.php config.yaml...
require __DIR__ . '/vendor/autoload.php';

use Stratis\Component\Migrator\Migrator;

$migrator = new Migrator($argv[1]);
$migrator->process();