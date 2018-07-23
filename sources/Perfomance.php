<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 16.07.2018
 * Time: 19:30
 */

namespace Common;

class PerfomanceException extends \Exception{

}

class Perfomance
{
    // Shows always run for exactly 100 days in the theatre;
    const  TOTAL_DAYS_RUN = 100;
    //• Shows run 7 days a week;
    const  WEEK_DAYS = 7;

    //Ticket sale starts 25 days before a show starts;
    const N_DAY_SALE_START_BEFORE_SHOW = 25;

    //10 tickets for shows in the big hall;
    const N_TICKET_AVAILABLE_BIG_HALL = 10;

    //5 tickets for shows in the small hall;
    const N_TICKET_AVAILABLE_SMALL_HALL = 5;
    //Shows always start in the big hall, which can hold up to 200 people;
    const BIG_HALL_PEOPLE_CAPACITY = 200;
    //After 60 days, the show moves to the small hall, which can hold up to 100 people;
    const SMALL_HALL_PEOPLE_CAPACITY = 100;

    const N_DAY_BIG_HALL = 1;
    const N_DAY_SMALL_HALL = 61;

    // After 80 days, the show price is discounted with 20%.

    const N_DAY_PRICE_DISCOUNT = 81;
    const N_PERC_PRICE_DISCOUNT = 20;

    const GENRE_MUSICALS = 'musical';
    const GENRE_COMEDY = 'comedy';
    const GENRE_DRAMA = 'drama';

    const DATE_FORMAT = 'YYYY-MM-DD';

    const TICKET_SALE_STATE_SALE_NOT_STARTED = 1;
    const TICKET_SALE_STATE_OPEN_FOR_SALE = 2;
    const TICKET_SALE_STATE_SOLD_OUT = 3;
    const TICKET_SALE_STATE_IN_THE_PAST = 4;


    protected $genrePrice = array(
        self::GENRE_MUSICALS => 70,
        self::GENRE_COMEDY => 50,
        self::GENRE_DRAMA => 40
    );

    public static $ticketState = array(
        self::TICKET_SALE_STATE_SALE_NOT_STARTED => 'Sale not started',
        self::TICKET_SALE_STATE_OPEN_FOR_SALE => 'Open for sale',
        self::TICKET_SALE_STATE_SOLD_OUT => 'Sold out',
        self::TICKET_SALE_STATE_IN_THE_PAST => 'In the past'
    );

    private $title;
    private $openingDay;
    private $genre;

    private $openingDayTimestamp;
    private $state;
    private $numberTicketsAvailable;
    private $numberTicketsLeft;
    private $price;

    private $queryDate;
    private $showDate;

    /**
     * @param string $title
     * @param string $openingDay
     * @param string $genre
     * @param array $datePeriod
     * @throws PerfomanceException
     */
    public function __construct(string $title, string $openingDay, string $genre, array $datePeriod)
    {

        $this->title = $title;
        $this->openingDay = $openingDay;
        $this->genre = $genre;
        $this->openingDayTimestamp = strtotime($openingDay);
        if (!empty($datePeriod['queryDate']) && $datePeriod['queryDate'] > 0) {
            $this->queryDate = $datePeriod['queryDate'];
        } else {
            throw new PerfomanceException('queryDate is not Existed');
        }

        if (!empty($datePeriod['showDate']) && $datePeriod['showDate'] > 0) {
            $this->showDate = $datePeriod['showDate'];
        } else {
            throw new PerfomanceException('showDate is not Existed');
        }

        $state = $this->getStateFromDatePeriod();

        $this->calculateNumberTickets();
        $this->calculatePriceTickets();

    }
    /**
     * Check perfomance status and set it to $this->state
     * @return mixed
     */
    public function getStateFromDatePeriod()
    {
        if (
            $this->queryDate + (self::N_DAY_SALE_START_BEFORE_SHOW * 24 * 60 * 60) <= $this->openingDayTimestamp
            || $this->showDate < $this->openingDayTimestamp
            || ($this->showDate - $this->queryDate) > (self::N_DAY_SALE_START_BEFORE_SHOW * 24 * 60 * 60)
        ) {
            $this->state = self::TICKET_SALE_STATE_SALE_NOT_STARTED;
        } elseif (
            $this->queryDate > ($this->openingDayTimestamp - (self::N_DAY_SALE_START_BEFORE_SHOW * 24 * 60 * 60))
            && $this->queryDate <= ($this->openingDayTimestamp + (self::TOTAL_DAYS_RUN * 24 * 60 * 60))
            && $this->showDate >= $this->openingDayTimestamp
            && $this->showDate <= ($this->openingDayTimestamp + (self::TOTAL_DAYS_RUN * 24 * 60 * 60))
            && $this->queryDate < ($this->showDate - (5 * 24 * 60 * 60))
            && ($this->showDate - $this->queryDate) <= (self::N_DAY_SALE_START_BEFORE_SHOW * 24 * 60 * 60)
        ) {
            $this->state = self::TICKET_SALE_STATE_OPEN_FOR_SALE;
        } elseif (
            $this->queryDate >= ($this->showDate - 5 * 24 * 60 * 60)
            && ($this->queryDate <= ($this->openingDayTimestamp + (self::TOTAL_DAYS_RUN * 24 * 60 * 60)))
        ) {
            $this->state = self::TICKET_SALE_STATE_SOLD_OUT;
        } elseif (
            $this->queryDate >= ($this->openingDayTimestamp + (self::TOTAL_DAYS_RUN * 24 * 60 * 60))
            || $this->showDate >= ($this->openingDayTimestamp + (self::TOTAL_DAYS_RUN * 24 * 60 * 60))
        ) {
            $this->state = self::TICKET_SALE_STATE_IN_THE_PAST;
        }
        return $this->state;
    }


    /**
     * Calculates price according to status, day of perfomance, and size of hall
     * return null
     */
    protected function calculatePriceTickets()
    {
        //calculate only in case of open for sale
        if ($this->state == self::TICKET_SALE_STATE_OPEN_FOR_SALE) {
            $genre = trim(strtolower($this->genre));
            // check price genre
            if (!empty($this->genrePrice[$genre])) {
                $this->price = $this->genrePrice[$genre];
            }
            //check 20% discount
            if ($this->showDate - $this->openingDayTimestamp >= (self::N_DAY_PRICE_DISCOUNT * 24 * 60 * 60)) {
                $this->price = $this->price * (100 - self::N_PERC_PRICE_DISCOUNT) / 100;
            }
        }
    }

    /**
     * calculates available and left number of tickets
     */
    protected function calculateNumberTickets()
    {
        //check size of hall according to day number
        if (
            $this->showDate - $this->openingDayTimestamp < (self::N_DAY_SMALL_HALL * 24 * 60 * 60)
        ) {
            $totalSize = self::BIG_HALL_PEOPLE_CAPACITY;
            $dailySale = self::N_TICKET_AVAILABLE_BIG_HALL;
        } else {
            $totalSize = self::SMALL_HALL_PEOPLE_CAPACITY;
            $dailySale = self::N_TICKET_AVAILABLE_SMALL_HALL;
        }

        switch ($this->state) {
            case self::TICKET_SALE_STATE_OPEN_FOR_SALE:
                $this->numberTicketsLeft = $totalSize - (self::N_DAY_SALE_START_BEFORE_SHOW - ($this->showDate - $this->queryDate) / (24 * 60 * 60)) * $dailySale;
                $this->numberTicketsAvailable = (($this->numberTicketsLeft >= $dailySale)) ? $dailySale : 0;
                if ($this->numberTicketsLeft <= 0) {
                    $this->state = self::TICKET_SALE_STATE_SOLD_OUT;
                }
                break;

            case self::TICKET_SALE_STATE_SALE_NOT_STARTED:
                $this->numberTicketsAvailable = 0;
                $this->numberTicketsLeft = $totalSize;
                break;

            default:
                $this->numberTicketsAvailable = 0;
                $this->numberTicketsLeft = 0;
                break;
        }
    }

    /**
     * Returns existed data
     * @param array $select
     * @return array
     */
    public function select(array $select)
    {
        $return = array();
        foreach ($select as $row) {
            if (property_exists($this, $row))
                $return[$row] = trim($this->$row);
        }
        return $return;
    }

    /**
     * return Perfomance object
     * @param array $state
     * @param array $datePeriod
     * @return Perfomance
     */
    public static function fromState(array $state, array $datePeriod)
    {
        return new self(
            $state['title'],
            $state['openingDay'],
            $state['genre'],
            $datePeriod
        );
    }

}