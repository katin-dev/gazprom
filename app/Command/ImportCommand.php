<?php

namespace App\Command;

use App\Lib\Container;
use App\Lib\LogReader;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    /** @var PDO */
    private $db;

    /** @var PDO */
    private $settings;

    public function __construct(Container $container)
    {
        $this->db = $container->db;
        $this->settings = $container->settings;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:import')
            ->setDescription('Import data form file to database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->settings['files'];

        // @TODO нверное такие классы уже написаны
        // @TODO добавить возомжность append
        $output->writeln("Import data from " . realpath($files['visitors']));

        $this->db->beginTransaction();

        $this->db->query('DELETE FROM visitor');

        try {
            $this->readChunks(new LogReader($files['visitors']), 1000, function ($questionMarks, $insertValues) {
                $sql = 'INSERT INTO visitor (ip, browser, os) VALUES ' . implode(',', $questionMarks);
                $stmt = $this->db->prepare($sql);
                if ($stmt->execute($insertValues) === false) {
                    throw new \Exception('SQL Error: ' . $stmt->errorInfo()[2]);
                }
            });
        } catch (\Exception $exception) {
            $output->writeln('Error: ' . $exception->getMessage());
            $this->db->rollBack();
            return;
        }
        $output->writeln("Done");


        // @TODO сделать нормальные тестовые данные
        $output->writeln("Import data from " . realpath($files['visits']));
        try {
            // Первые 2 поля - это дата, время. Их слеудет объединить
            $rowModifier = function ($row) {
                $date = date('Y-m-d H:i:s', strtotime($row[0] . ' ' . $row[1]));
                array_splice($row, 0, 2, $date);
                return $row;
            };

            $this->readChunks(
                new LogReader($files['visits']),
                1000,
                function ($questionMarks, $insertValues) {
                    $sql = 'INSERT INTO visit (date, ip, referer, path) VALUES ' . implode(',', $questionMarks);
                    $stmt = $this->db->prepare($sql);
                    if ($stmt->execute($insertValues) === false) {
                        throw new \Exception('SQL Error: ' . $stmt->errorInfo()[2]);
                    }
                },
                $rowModifier
            );
        } catch (\Exception $exception) {
            $output->writeln('Error: ' . $exception->getMessage());
            $this->db->rollBack();
            return;
        }

        $this->db->commit();

        $output->writeln("Done");
    }

    /**
     * Порционное считываение данных из файла
     * @param LogReader $reader
     * @param int $chunkSize - размер одной порции
     * @param callable $callback - обработчик, которому будет передана порция считанных данных
     * @param callable $rowModifier
     * @throws \Exception
     */
    private function readChunks(LogReader $reader, $chunkSize, callable $callback, callable $rowModifier = null)
    {
        $i            = 0;
        $values       = [];
        $placeholders = [];

        foreach ($reader->getLines() as $row) {
            if ($rowModifier) $row = $rowModifier($row);

            // иногда, бывает, считываем пустую строку (последнюю, например)
            if (!$row) continue;

            $values    = array_merge($values, $row);
            $placeholders[] = '(' . rtrim(str_repeat('?,', count($row)), ',') . ')'; // Делаем конструкцию вида (?, ?, ?)

            if ($i++ > $chunkSize) {
                $callback($placeholders, $values);
                $values  = [];
                $placeholders = [];
            }
        }

        if ($values) {
            $callback($placeholders, $values);
        }
    }
};