<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 16.07.2018
 * Time: 21:31
 */

namespace Common;

use Common\Perfomance;


/**
 * Class PerfomanceMapper
 * @package Common
 */
class PerfomanceMapper
{
    /**
     * @var StorageAdapter
     */
    private $adapter;

    /**
     * @param StorageAdapter $storage
     */
    public function __construct(StorageAdapter $storage)
    {
        $this->adapter = $storage;
    }

    /**
     * Select data by filters
     * @param array $where
     * @param array $select
     * @return array
     * @throws Exception
     */
    public function findByDate(array $where, array $select)
    {
        if (empty($where['queryDate'])) {
            throw new Exception('queryDate is required');
        }
        if (empty($where['showDate'])) {
            throw new Exception('showDate is required');
        }
        //can be current time
        $queryDate = strtotime($where['queryDate']);
        //day what we want to see perfomance
        $showDate = strtotime($where['showDate']);
        $datePeriod = array(
            'queryDate' => $queryDate,
            'showDate' => $showDate
        );
        $result = $this->adapter->findAll();
        $data = array();
        foreach ($result as $perfomance) {
            $data[] = $this->mapRowToPerfomance($perfomance, $datePeriod)->select($select);
        }
        return $data;
    }

    /**
     * Group array data by key, and remove it from result
     * @param $array
     * @param $key
     * @return array
     */
    protected function _group_by($array, $key)
    {
        $return = array();
        foreach ($array as $val) {
            $name = $val[$key];
            unset($val[$key]);
            $return[$name][] = $val;
        }
        return $return;
    }

    /**
     * Map data to perfomance object and return it
     * @param array $row
     * @param array $date
     * @return Perfomance
     */
    private function mapRowToPerfomance(array $row, array $date)
    {
        return Perfomance::fromState($row, $date);
    }

    /**
     * Compose appropriate data for web
     * @param array $data
     * @return array
     */
    public function prepearDataForWeb(array $data)
    {
        $result = array();
        foreach ($data as $perfomance) {

            if ($perfomance['state'] == Perfomance::TICKET_SALE_STATE_OPEN_FOR_SALE) {
                $perfomance['state'] = Perfomance::$ticketState[$perfomance['state']];
                $result[] = $perfomance;
            }
        }
        return $this->_group_by($result, 'genre');
    }

    /**
     * Compose appropriate data for console
     * @param array $data
     * @return string
     */
    public function prepearDataForConsole(array $data)
    {
        $result = array();
        foreach ($data as $perfomance) {
            $item = array(
                'title' => $perfomance['title'],
                'tickets left' => $perfomance['numberTicketsLeft'],
                "tickets available" => $perfomance['numberTicketsAvailable'],
                "status" => Perfomance::$ticketState[$perfomance['state']],
                'genre' => $perfomance['genre']

            );
            $result[] = $item;
        }
        $groupedData = $this->_group_by($result, 'genre');
        $return = array();
        foreach ($groupedData as $genre => $data) {
            $return["inventory"][] = array(
                'genre' => $genre,
                'shows' => $data
            );
        }
        return json_encode($return);
    }
}