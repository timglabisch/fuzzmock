<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyReference implements ReturnTypeInterface
{

    private static $indexCounter = 0;

    private static $variableStorage = [];

    /** @var int */
    private $index;

    public function __construct(&$var)
    {
        $this->index = self::$indexCounter++;
        self::$variableStorage[$this->index] = &$var;
    }

    public function getCode()
    {
        return 'return \\' . self::class . '::unsafeGetVariable(' . $this->index . ');';
    }

    public static function unsafeGetVariable(int $index)
    {
        return self::$variableStorage[$index];
    }
}