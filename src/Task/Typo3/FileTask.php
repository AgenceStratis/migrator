<?php

namespace Stratis\Component\Migrator\Task\Typo3;

use Stratis\Component\Migrator\Configuration;
use Stratis\Component\Migrator\Task\DefaultTask;
use Hoa\Iterator\Directory;
use Hoa\Mime\Mime;

/**
 * Class FileTask
 * Scan directory and insert file into typo3 sys_file table
 *
 * @package Stratis\Component\Migrator\Task
 * @config array $sources
 */
class FileTask extends DefaultTask
{
    /**
     * @param Configuration $config
     * @throws \Exception
     */
    public function main(Configuration &$config)
    {
        $siteRoot = $config->get(array('task', 'siteRoot'), '');
        $fileDir = $config->get(array('task', 'fileDir'), '');

        if (empty($siteRoot)) {
            throw new \Exception('Task/Typo3/File: No site root defined.');
        }

        if (empty($fileDir)) {
            throw new \Exception('Task/Typo3/File: No file directory defined.');
        }

        // Empty data
        $this->data = [];

        // Trim slashes from paths
        $siteRoot = rtrim($siteRoot, '/');
        $fileDir = '/' . trim($fileDir, '/');

        // List files in the directory
        $files = new Directory($siteRoot . $fileDir);

        // Download files from remote server
        foreach ($files as $file) {

            // Connect to the file
            $identifier = $fileDir . '/' . $file->getFilename();

            // Update record info with typo3 sys_file compatible data
            $this->data[] = [
                'identifier' => $identifier,
                'storage' => 1,
                'identifier_hash' => sha1($identifier),
                'folder_hash' => sha1($fileDir),
                'extension' => $file->getExtension(),
                'mime_type' => Mime::getMimeFromExtension($file->getExtension()),
                'name' => $file->getFilename(),
                'sha1' => sha1_file($file->getRealPath()),
                'size' => $file->getSize(),
                'tstamp' => time(),
                'last_indexed' => time(),
                'creation_date' => $file->getCTime(),
                'modification_date' => $file->getMTime(),
            ];
        }
    }
}