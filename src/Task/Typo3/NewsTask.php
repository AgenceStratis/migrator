<?php

namespace Stratis\Component\Migrator\Task\Typo3;

use Stratis\Component\Migrator\Configuration;
use Stratis\Component\Migrator\Task\DefaultTask;

/**
 * Class NewsTask
 * @package Stratis\Component\Migrator\Task\Typo3
 */
class NewsTask extends DefaultTask
{
    /**
     * @param Configuration $config
     */
    public function main(Configuration $config)
    {
        var_dump($this->data); die;
    }
}