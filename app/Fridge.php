<?php namespace App;

use League\Csv\Reader;
use Carbon\Carbon;

class Fridge {

    /**
     * The CSV reader
     * 
     * @var \League\Csv\Reader
     */
    private $reader;

    /**
     * The date today - or overridden for historical record
     * 
     * @var \Carbon\Carbon
     */
    private $today;

    /**
     * Outlines the field names for the CSV
     * 
     * @var array
     */
    private $associativeMap = ['item', 'amount', 'unit', 'expiry'];

    /**
     * Create the instance of Csv\Reader and expose through fridge
     * 
     * @param string $location
     */
    public function __construct($location)
    {
        if ($location == '' || !file_exists($location)) {
            throw new \Exception('Fridge location is not defined or doesn\'t exist');
        }
        $this->reader = Reader::createFromPath($location);

        // Default to today
        $this->setToday();
    }

    /**
     * Returns the barebones version of what's in the fridge
     * 
     * @return array Everything
     */
    public function getContents()
    {
        return $this->reader
            ->fetchAssoc($this->associativeMap);
    }

    /**
     * Returns only the fridge contents that hasn't expired based on "today"
     * 
     * @return array The unexpired fridge contents in associate array format
     */
    public function getUnexpiredContents()
    {
        return $this->reader
            ->addFilter([$this, 'filterByDate'])
            ->addSortBy([$this, 'orderByDate'])
            ->fetchAssoc($this->associativeMap);
    }

    /**
     * Filters out anything that's already expired
     * Also converts the expiry to an instance of Carbon date object
     * 
     * @param  array &$row
     * @return bool
     */
    public function filterByDate(&$row)
    {
        $row[3] = Carbon::createFromFormat('j/n/Y', $row[3]);
        return $row[3] >= $this->today;
    }

    /**
     * Orders the items by expiry date ascending
     * 
     * @param  string
     * @param  string
     * @return bool
     */
    public function orderByDate($rowA, $rowB)
    {
        return $rowA[3] > $rowB[3];
    }

    /**
     * Sets the date as a Carbon object - defaults as today
     * This would be much neater if we had method overloading in PHP
     * 
     * @param void
     */
    public function setToday($carbon = false)
    {
        if ($carbon instanceof Carbon) {
            // Set it
            $this->today = $carbon;
        } else {
            // Set it to today
            $this->today = Carbon::now();
        }
    }

}