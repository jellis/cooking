## What to cook!?!
A simple program to figure out what you should cook based on two datasets (fridge contents and recipes)

## Installation
```
git clone git@github.com:jellis/cooking.git
cd cooking
composer install
```

## Further information
The test data, as provided, is actually all expired. Therefore, the outcome will only ever be "Order Takeout". I provided a method to set the date of "today" to whatever you like, giving the ability to better test the functionality within the given dataset. Line 21 & 22 of index.php are commented out, but provide this functionality.

## Unit tests
The unit tests against the App\Cooking class are not as verbose as they could be - but testing some of those methods in isolation will be a bit of a PITA, and realistically provide no real benefit to the project. I decided to include one functional test at the bottom of that test case.

```
vendor/bin/phpunit
```
