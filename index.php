<?php

// Enable autoloading
require_once 'vendor/autoload.php';

use App\Fridge;
use App\Recipe;
use App\Cooking;
use Carbon\Carbon;

// Get the fridge
$fridge = new Fridge('data/fridge.csv');

// Get the recipes
$recipe = new Recipe('data/recipe.json');

// Determine what to cook
$cooking = new Cooking($fridge, $recipe);

// We set the date in the past - all of our test ingredients are expired as given to us
// $theDate = Carbon::now()->subYears(2);
// $cooking->setDate($theDate);

// What shall we cook?
$plateUp = $cooking->tellMeWhatToCook();

var_dump($plateUp);
