<?php namespace App\Test;

use App\Cooking;
use App\Fridge;
use App\Recipe;
use Carbon\Carbon;
use Mockery;

class CookingTest extends \PHPUnit_Framework_TestCase {

    private $cooking;

    public function setUp()
    {
        $this->fridge = Mockery::mock('App\Fridge');
        $this->recipe = Mockery::mock('App\Recipe');
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_it_sluggifies_a_phrase()
    {
        $phrase1 = 'A simple phrase';
        $phrase2 = 'My &fridge @#-item\'s **here';

        $this->assertEquals('a-simple-phrase', Cooking::slug($phrase1));
        $this->assertEquals('my-fridge-items-here', Cooking::slug($phrase2));
    }

    public function test_it_indexes_the_fridge()
    {
        $fridgeItems = [
            ['item' => 'Tasty cheese', 'nothing' => false],
            ['item' => 'Orange Juice', 'nothing' => false],
            ['item' => 'Water', 'nothing' => false]
        ];

        $indexedItems = [
            'tasty-cheese' => $fridgeItems[0],
            'orange-juice' => $fridgeItems[1],
            'water' => $fridgeItems[2]
        ];

        $cooking = new Cooking($this->fridge, $this->recipe);

        $this->fridge
            ->shouldReceive('getUnexpiredContents')
            ->andReturn($fridgeItems);

        $cooking->indexFridgeContents();

        $this->assertEquals($indexedItems, \PHPUnit_Framework_Assert::readAttribute($cooking, 'indexedFridge'));
    }

    public function test_end_to_end()
    {
        // This uses the real data
        $fridge = new Fridge('data/fridge.csv');
        $recipe = new Recipe('data/recipe.json');

        $cooking = new Cooking($fridge, $recipe);

        // Everything expired
        $now = Carbon::now();

        // One year ago - salad still expired
        $yearAgo = Carbon::now()->subYear();

        // Two years ago - everything still good
        $twoYearsAgo = Carbon::now()->subYears(2);

        // Nothing available
        $cooking->setDate($now);
        $this->assertEquals('Order Takeout', $cooking->tellMeWhatToCook());

        // Only thing available to cook
        $cooking->setDate($yearAgo);
        $this->assertEquals('grilled cheese on toast', $cooking->tellMeWhatToCook());

        // Salad is nearest expiry
        $cooking->setDate($twoYearsAgo);
        $this->assertEquals('salad sandwich', $cooking->tellMeWhatToCook());
    }

}