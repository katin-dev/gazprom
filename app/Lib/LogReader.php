<?php

namespace App\Lib;

class LogReader
{
    /** @var string - полный путь к файлу с логами */
    public $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Получить строки из указанного файла
     * @return \Generator
     * @throws \Exception
     */
    public function getLines()
    {
        // Первым делом Visitors
        $fd = fopen($this->filename, 'r');
        if (!$fd) {
            throw new \Exception("Unable to open file '{$this->filename}'");
        }

        while ($row = fgetcsv($fd, 0, '|')) {
            yield $row;
        }

        fclose($fd);
    }
}