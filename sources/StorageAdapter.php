<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 16.07.2018
 * Time: 21:35
 */

namespace Common;

use \Exception;

/**
 * Class StorageAdapterException
 * @package Common
 */
class StorageAdapterException extends Exception
{

}

/**
 * Class StorageAdapter
 * @package Common
 */
class StorageAdapter
{

    /**
     * Path to folder with  all files
     */
    const FILE_PATH = '/../resources/';
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $fileName
     * @throws StorageAdapterException
     */
    public function __construct(string $fileName)
    {
        if (!file_exists(__DIR__ . self::FILE_PATH . $fileName)) {
            throw new StorageAdapterException('File is not Existed');
        }

        $this->data = $this->prepareData($fileName);
    }

    /** Get data from csv file and convert to array
     * @param string $fileName
     * @return array
     */
    private function prepareData(string $fileName)
    {
        $data = array_map('str_getcsv', file(__DIR__ . self::FILE_PATH . $fileName));
        array_walk($data, function (&$a) use ($data) {
            $a = array_combine(array('title', 'openingDay', 'genre'), $a);
        });
        return $data;
    }

    /**
     *
     * @return array|null
     */
    public function findAll()
    {
        return $this->data;
    }

}