<?php

require '../vendor/autoload.php';
use Stratis\Component\Migrator\Migrator;

// Show usage information
if ($argc < 2 || in_array('-h', $argv) || in_array('--help', $argv)) {
    Migrator::showUsage();
}

// Remove 1st element
array_shift($argv);

// Output current version
if (in_array('-v', $argv) || in_array('--version', $argv)) {
    Migrator::showVersion();
}

// Concat all the files together
$migrator = new Migrator($argv);
$migrator->process();
