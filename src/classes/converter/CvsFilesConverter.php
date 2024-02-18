<?php
namespace TaskForce\converter;

use DirectoryIterator;
use SplFileInfo;
use SplFileObject;
use TaskForce\exceptions\ConverterException;

class CvsFilesConverter
{
    protected array $filesToConvert = [];

    public function __construct(string $directory)
    {
        if (!is_dir($directory)) {
            throw new ConverterException('Указанная директория не существует!');
        }

        $this->loadCsvFiles($directory);
    }

    protected function loadCsvFiles(string $directory): void
    {
        foreach(new DirectoryIterator($directory) as $file) {
            if ($file->getExtension() === 'csv') {
                $this->filesToConvert[] = $file->getFileInfo();
            }
        }
    }

    protected function getSqlContent(string $tableName, array $columns, array $values): string
    {
        $columnsString = implode(', ', $columns);
        $sql = "INSERT INTO $tableName($columnsString) VALUES ";

        foreach ($values as $row) {
            array_walk($row, function(&$value) {
                $value = addslashes($value);
                $value = "'$value'";
            });
            $sql .= "(" . implode(', ', $row) . "), ";
        }

        $sql = substr($sql, 0, -2);

        return $sql;
    }

    protected function saveSqlContent(string $tableName, string $directory, string $content): string
    {
        if (!is_dir($directory)) {
            throw new ConverterException('Директория для выходных данных не существует!');
        }

        $filename = $directory . DIRECTORY_SEPARATOR . $tableName . '.sql';
        file_put_contents($filename, $content);

        return $filename;
    }

    protected function convertFile(SplFileInfo $file, string $outputDirectory): string
    {
        $fileObject = new SplFileObject($file->getRealPath());
        $fileObject->setFlags(SplFileObject::READ_CSV);

        $columns = $fileObject->fgetcsv();
        $values = [];

        while (!$fileObject->eof()) {
            $values[] = $fileObject->fgetcsv();
        }

        $tableName = $fileObject->getBasename('.csv');
        $sqlContent = $this->getSqlContent($tableName, $columns, $values);

        return $this->saveSqlContent($tableName, $outputDirectory, $sqlContent);
    }

    public function convertFiles(string $outputDirectory): array
    {
        $result = [];

        foreach ($this->filesToConvert as $file) {
            $result[] = $this->convertFile($file, $outputDirectory);
        }

        return $result;
    }
}
