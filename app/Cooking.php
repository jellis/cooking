<?php namespace App;

use App\Fridge;
use App\Recipe;
use Carbon\Carbon;

class Cooking {

    /**
     * The fridge
     * 
     * @var \App\Fridge;
     */
    private $fridge;

    /**
     * An associatively indexed array of the items in the fridge
     * 
     * @var array
     */
    private $indexedFridge;

    /**
     * The recipes
     * 
     * @var \App\Recipe;
     */
    private $recipe;

    /**
     * Establish our dependencies
     * 
     * @param Fridge $fridge
     * @param Recipe $recipe
     */
    public function __construct(Fridge $fridge, Recipe $recipe)
    {
        $this->fridge = $fridge;
        $this->recipe = $recipe;
    }

    /**
     * Set the date for when we want to cook it (can be future, present or past)
     * 
     * @param  Carbon $date
     * @return \App\Fridge::setToday() Set the date we want to cook
     */
    public function setDate(Carbon $date)
    {
        return $this->fridge->setToday($date);
    }

    /**
     * Create associative array of fridge ingredients
     * 
     * @return void
     */
    public function indexFridgeContents()
    {
        foreach ($this->fridge->getUnexpiredContents() as $item) {
            $this->indexedFridge[static::slug($item['item'])] = $item;
        }
    }

    /**
     * Determine if all of the items specified are in the fridge
     * 
     * @param  array $items The ingredients for a recipe
     * @return bool
     */
    public function allInFridge($items)
    {
        foreach ($items as $item) {
            if (!$this->inFridgeInQuantity($item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determines if the item is in the fridge, and also in enough quantity
     * 
     * @param  array $item Item from the recipe
     * @return bool
     */
    public function inFridgeInQuantity($item)
    {
        $theSlug = static::slug($item['item']);
        if (array_key_exists($theSlug, $this->indexedFridge)) {
            return (int) $this->indexedFridge[$theSlug]['amount'] > (int) $item['amount'];
        }
        return false;
    }

    /**
     * The bulk of the logic to determine what we can cook
     * 
     * @return string 
     */
    public function tellMeWhatToCook()
    {
        $this->indexFridgeContents();
        $possibilities = [];

        if (is_array($this->indexedFridge) && !empty($this->indexedFridge)) {
            foreach ($this->recipe->getRecipes() as $recipe) {
                if (is_array($recipe['ingredients']) && $this->allInFridge($recipe['ingredients'])) {
                    $possibilities[] = $recipe;
                }
            }
        }

        return $this->orderedPossibilities($possibilities);
    }

    /**
     * Because we haven't ordered the possibilities by
     * the nearest expiry date yet
     * 
     * @param  array $possibilities The things we can possibly cook
     * @return string
     */
    public function orderedPossibilities($possibilities)
    {
        if (count($possibilities) === 0) return 'Order Takeout';

        if (count($possibilities) === 1) return $possibilities[0]['name'];

        usort($possibilities, [$this, 'orderByNearestExpiry']);

        return $possibilities[0]['name'];
    }

    /**
     * Helper for usort
     * 
     * @param  array $a
     * @param  array $b
     * @return bool
     */
    public function orderByNearestExpiry($a, $b)
    {
        return $this->nearestExpiry($a['ingredients']) > $this->nearestExpiry($b['ingredients']);
    }

    /**
     * I feel bad iterating over these again - Ideally would perform this
     * logic somewhere back up in allInFridge
     * 
     * @param  array $ingredients
     * @return \Carbon\Carbon
     */
    public function nearestExpiry($ingredients)
    {
        $nearest = false;
        foreach ($ingredients as $ingredient) {
            $slug = static::slug($ingredient['item']);
            if ($this->indexedFridge[$slug]['expiry'] < $nearest || $nearest == false) {
                $nearest = $this->indexedFridge[$slug]['expiry'];
            }
        }
        return $nearest;
    }

    /**
     * Create a slug (my-thing-with-spaces) so the array index behaves normally
     * 
     * @param  string $phrase The untouched phrase we need to convert
     * @return string
     */
    public static function slug($phrase)
    {
        $result = strtolower($phrase);
        $result = preg_replace('/[^A-Za-z0-9\s-._\/]/', "", $result);
        $result = trim(preg_replace("/[\s-\/]+/", " ", $result));
        $result = preg_replace("/\s/", "-", $result);
        return $result;
    }

}