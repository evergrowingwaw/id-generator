<?php

namespace Ave\IdGenerator;

class FileBasedIdGenerator implements IdGenerator
{
    public const GROUP_NAME_PATTERN = '/^\w+$/';

    private $dir;

    public function __construct(string $dir)
    {
        $realpath = \realpath($dir);

        if (false === $realpath) {
            throw new Exception(sprintf("Directory '%s' not exists or is not writable!", $dir), 1);
        }

        $this->dir = $realpath . DIRECTORY_SEPARATOR;
    }

    public function generateId(string $group = 'default', int $start = 1): int
    {
        if (false == \preg_match(static::GROUP_NAME_PATTERN, $group)) {
            throw new Exception("Group name is invalid!", 3);
        }

        $filePath = $this->dir . $group;

        if (!\file_exists($filePath)) {
            \file_put_contents($filePath, (string) $start - 1);
        } else if (!\is_file($filePath)) {
            throw new Exception('Given path is not a file!', 4);
        }

        $id = null;
        $fp = \fopen($filePath, 'r+');
        \flock($fp, LOCK_EX);
        
        $line = fgets($fp);

        if (false !== $line) {
            $id = intval($line) + 1;

            \ftruncate($fp, 0);
            \rewind($fp);
            \fwrite($fp, $id);
        }

        \flock($fp, LOCK_UN);
        \fclose($fp);

        if (null === $id) {
            throw new Exception(sprintf("Error reading file '%s'", $filePath), 2);
        }

        return $id;
    }
}
