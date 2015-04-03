<?php namespace App\Test;

use App\Fridge;
use Carbon\Carbon;

/**
 * Please note the use of Carbon in here which is probably not "best practice".
 * However, Carbon is not the suite under test, and it has extensive testing
 * implemented in its development - plus they're static method calls, so I can't
 * really mock them.
 * Also, there's not point testing basic setters/getters.
 */

class FridgeTest extends \PHPUnit_Framework_TestCase {

    public function test_it_loads_data()
    {
        $testData = [
            ['item' => 'bread', 'amount' => '10', 'unit' => 'slices', 'expiry' => '1/6/2015'],
            ['item' => 'cheese', 'amount' => '10', 'unit' => 'slices', 'expiry' => '20/4/2015'],
        ];

        $fridge = new Fridge('tests/data/loads_data.csv');

        $contents = $fridge->getContents();

        $this->assertEquals($contents, $testData);
    }

    /**
     * @expectedException Exception
     */
    public function test_it_throws_exception_for_no_file()
    {
        $fridge = new Fridge('tests/data/no_file_exists.csv');
    }

    public function test_it_excludes_expired_food_and_is_ordered_by_date()
    {
        $testData = [
            ['item' => 'bread', 'amount' => '10', 'unit' => 'slices', 'expiry' => Carbon::createFromFormat('j/n/Y', '1/6/2015')],
            ['item' => 'cheese', 'amount' => '10', 'unit' => 'slices', 'expiry' => Carbon::createFromFormat('j/n/Y', '2/6/2015')],
            ['item' => 'peanut butter', 'amount' => '250', 'unit' => 'grams', 'expiry' => Carbon::createFromFormat('j/n/Y', '3/6/2015')],
            ['item' => 'butter', 'amount' => '250', 'unit' => 'grams', 'expiry' => Carbon::createFromFormat('j/n/Y', '4/6/2015')],
        ];

        $fridge = new Fridge('tests/data/exclude_expired.csv');

        $contents = $fridge->getUnexpiredContents();

        $this->assertEquals($contents, $testData);
    }

    public function test_it_filters_date_before_now()
    {
        $dummyData[3] = '1/1/2012';

        $fridge = new Fridge('tests/data/exclude_expired.csv');

        $this->assertFalse($fridge->filterByDate($dummyData));
    }

    public function test_it_does_not_filter_date_after_now()
    {
        $dummyData[3] = Carbon::now()->addDay()->format('j/n/Y');

        $fridge = new Fridge('tests/data/exclude_expired.csv');

        $this->assertTrue($fridge->filterByDate($dummyData));
    }

    public function test_it_orders_by_date()
    {
        $dateHigh[3] = Carbon::createFromFormat('j/n/Y', '20/6/2015');
        $dateLow[3] = Carbon::createFromFormat('j/n/Y', '20/5/2015');

        $fridge = new Fridge('tests/data/exclude_expired.csv');

        $this->assertTrue($fridge->orderByDate($dateHigh, $dateLow));
        $this->assertFalse($fridge->orderByDate($dateLow, $dateHigh));
    }

}