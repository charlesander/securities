# securities

The project was run on a lamp installation running php 7.4 and mysql 5.7

The main files worth looking at are:
```
src/Classes/Expression.php
src/Controller/DslController.php
tests/ExpressionTest.php
tests/DslControllerTest.php
```

Overall approach: build an object recursively representing the expressions within 
the post request body.

If I had more time I would write more tests, and format and comment the code better.

All the functions are public, this is me running out of time. I make everything public
to begin with to make testsing easier. There are problems with the code due to this 
because functions that should be private are coded expecting a specific  
object to be passed. I was unable to type cast many of the functions arguments
due to the data being passed around either being a string or an object.  


I am in two minds about injecting the FactRepository in the Expression class. The original
intention had been to build up an Exression object representing the request body. This 
is what has been done. However, due to the object being calculated recursively, I 
thought it more convinient to inject the repo as a reference so that the calculations 
requireing db access could be held within the same object. Passing a repo to another
class that is not a controller is not the symfony way.

## Installation instructions
cd to project's root directory

1) Edit .env and .env.test with the correct db credentials

2) run the following commands:

```
composer install
php ./bin/console   doctrine:database:drop --force #Careful!
php ./bin/console   doctrine:database:create 
php bin/console doctrine:migrations:migrate -n 
php bin/console import:csv data/attributes.csv,data/securities.csv,data/facts.csv # ensure this is only run once
```

## Using app

Please ensure you use the 'Content-Type application/json' with any requests.

Endpoint: 
```
Content-Type application/json
POST /dsl
{
  "expression": {"fn": "*", "a": "sales", "b": 2},
  "security": "ABC"
}

```
##Run tests
```
php ./bin/phpunit
```
 
