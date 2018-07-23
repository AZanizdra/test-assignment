<?php
include_once(__DIR__.'/../autoload.php');
use Common\PerfomanceMapper;
use Common\StorageAdapter;

use PHPUnit\Framework\TestCase;

class PerfomanceTest extends TestCase{
    protected $file = 'shows.csv';
    protected $mapper;
    protected function setUp()
    {
        $storage = new StorageAdapter( $this->file);
        $this->mapper = new PerfomanceMapper($storage);
    }
    /**
     * @expectedException StorageAdapterException
     */

    public function testWithInvalidFileData()
    {
        $storage = new StorageAdapter('111.csv');
        $mapper = new PerfomanceMapper($storage);
    }
}