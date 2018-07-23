<?php
include_once(__DIR__.'/../autoload.php');

use Common\PerfomanceMapper;
use Common\StorageAdapter;

$data = array_combine(array('file_run', 'file_csv', 'queryDate', 'showDate'), $argv);

$storage = new StorageAdapter( $data['file_csv']);
$mapper = new PerfomanceMapper($storage);
$where = array(
    'queryDate' => $data['queryDate'],
    'showDate' => $data['showDate']
);
$select = array(
    'genre',
    'title',
    'numberTicketsLeft',
    'numberTicketsAvailable',
    'state',
    'price','openingDay'
);
$perfomanceData = $mapper->findByDate( $where, $select);

$return = $mapper->prepearDataForConsole($perfomanceData);
print_r($return);
