<?php
include_once(__DIR__.'/../autoload.php');

use Common\Perfomance;
use Common\PerfomanceMapper;
use Common\StorageAdapter;

header("Content-type: application/json; charset=utf-8");

$data = $_POST;
if(
    !empty($_POST)
    && !empty($_POST['search'])
    && strtotime($_POST['search'])>0
){
    header("HTTP/1.1 200 OK");
    $storage = new StorageAdapter( 'shows.csv');
    $mapper = new PerfomanceMapper($storage);
    $where = array(
        'queryDate' => date('Y-m-d'),
        'showDate' => $_POST['search']
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
    $data = $mapper->prepearDataForWeb($perfomanceData);
    echo json_encode(array(
        'status'=> 200,
        'data' => $data
    ));
    return;
}else{
    header("HTTP/1.1 400");
    echo json_encode(array(
        'status'=> 400
    ));
    return;
}


