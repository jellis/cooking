<?php namespace App\Test;

use App\Recipe;

class RecipeTest extends \PHPUnit_Framework_TestCase {

    public function test_it_loads_data()
    {
        $testData = [[
            'name' => 'grilled cheese on toast',
            'ingredients' => [
                ['item' => 'bread', 'amount' => '2', 'unit' => 'slices']
            ]
        ]];

        $recipe = new Recipe('tests/data/loads_data.json');

        $this->assertEquals($recipe->getRecipes(), $testData);
    }

    /**
     * @expectedException Exception
     */
    public function test_it_throws_exception_for_no_file()
    {
        $fridge = new Recipe('tests/data/no_file_exists.json');
    }

}