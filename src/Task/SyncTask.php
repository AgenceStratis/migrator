<?php

namespace Stratis\Component\Migrator\Task;

use Stratis\Component\Migrator\Configuration;
use Hoa\File;

/**
 * Class SyncTask
 * @package Stratis\Component\Migrator\Task
 * @config array $sources
 */
class SyncTask extends DefaultTask
{
    /**
     * @param Configuration $config
     * @throws \Exception
     */
    public function main(Configuration $config)
    {
        $remote = $config->get(array('remote'), '');
        $local = $config->get(array('local'), '');

        if (empty($remote)) {
            throw new \Exception('Task/Sync: No remote path defined.');
        }

        if (empty($local)) {
            throw new \Exception('Task/Sync: No local path defined.');
        }

        if (count($this->sources) > 1) {
            throw new \Exception('Task/Sync: This task uses only 1 source.');
        }

        // Trim slashes from paths
        $remote = rtrim($remote, '/');
        $local = rtrim($local, '/');

        // Use only one source
        reset($this->sources);
        $source = current($this->sources);

        // Download files from remote server
        foreach ($source as $record) {

            if (!array_key_exists('file', $record)) {
                throw new \Exception('Task/Sync: The record does not have a \'file\' field!');
            }

            // No file found
            if (empty($record['file'])) continue;

            $file = new File\Read($remote . '/' . $record['file']);
            $file->copy($local . '/' . $record['file']);
        }

        exit;
    }
}