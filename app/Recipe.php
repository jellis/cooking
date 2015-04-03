<?php namespace App;

class Recipe {

    /**
     * Recipes stored in associated array
     * 
     * @var array
     */
    private $recipes;

    /**
     * Read and decode the available recipes
     * 
     * @param string $location
     */
    public function __construct($location)
    {
        if ($location == '' || !file_exists($location)) {
            throw new \Exception('Recipe location is not defined or doesn\'t exist');
        }
        $this->recipes = json_decode(file_get_contents($location), true);
    }

    /**
     * Fetch the recipes
     * 
     * @return array
     */
    public function getRecipes()
    {
        return (array) $this->recipes;
    }

}