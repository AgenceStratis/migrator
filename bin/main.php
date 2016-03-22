<?php

require 'vendor/autoload.php';
use Stratis\Component\Migrator\Migrator;

// Remove 1st element
array_shift($argv);

// Concat all the files together
$migrator = new Migrator($argv);
$migrator->process();
