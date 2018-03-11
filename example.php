<?php

use Tg\Fuzzymock\ReturnType\FuzzyFloat;
use Tg\Fuzzymock\ReturnType\FuzzyInt;

require __DIR__.'/vendor/autoload.php';

class Person {

    public function getAge() {
        return 42;
    }

}

$fuzzyPersonMock = new \Tg\Fuzzymock\FuzzyBuilder(Person::class);
$fuzzyPersonMock->fn('getAge')
    ->couldReturn(new FuzzyFloat(1, 2, 2))
    ->couldReturn(new FuzzyInt())
;
/** @var Person $person */
$person = $fuzzyPersonMock->build();

while (true) {
    echo var_export($person->getAge(), true)."\n";
}

