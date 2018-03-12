<?php

use Tg\Fuzzymock\ReturnType\FuzzyCastString;
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
    ->couldReturn(new FuzzyCastString(new FuzzyInt()))
;
/** @var Person $person */
$personBuilder = $fuzzyPersonMock->build();

while (true) {
    $person = $personBuilder->createNew();
    echo var_export($person->getAge(), true)."\n";
    echo var_export($person->getAge(), true)."\n";
}



