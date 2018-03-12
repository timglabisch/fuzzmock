# fuzzmock

```
composer require timg/fuzzymock
```

Fuzzymock is a small library that helps to create tests for edge-cases.

Sometimes you want to check if 2 different implementations that are using such a class are working the same way. 
Something like: 

```php
static::assertEquals((new GreeterA)->greet($person), (new GreeterB)->greet($person));
```

consider you've such a `Person` class:

```php

class Person {

    public $age;
  
    public function getAge() {
        return $this->age;
    }

}
```

you can create a FuzzyMock for the `Person`:

```php

$fuzzyPersonMock = new \Tg\Fuzzymock\FuzzyBuilder(Person::class);
$fuzzyPersonMock->fn('getAge')
    ->couldReturn(new FuzzyFloat(1, 2, 2))
    ->couldReturn(new FuzzyCastString(new FuzzyInt()))
;

/** @var Person $person */
$personBuilder = $fuzzyPersonMock->createBuilder();

while (true) {
    $person = $personBuilder->createNew();
    echo $person->getAge() . "\n"; // a float, or a string containing a random int
}

```

the `createBuilder` method creates a PHP-Class on the fly (no reflection etc.).
currently there are no benchmarks but FuzzyMock should be extrem fast.