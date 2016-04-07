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
     * NewsTask constructor.
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        parent::__construct($config);

        var_dump($this->data); die;
    }
}