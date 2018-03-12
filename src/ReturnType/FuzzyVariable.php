<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyVariable implements ReturnTypeInterface
{

    private static $indexCounter = 0;

    private static $variableStorage = [];

    /** @var int */
    private $index;

    public function __construct($var)
    {
        $this->index = self::$indexCounter++;
        self::$variableStorage[$this->index] = $var;
    }

    public function getCode()
    {
        $var = self::$variableStorage[$this->index];

        if (is_scalar($var)) {
            unset(self::$variableStorage[$this->index]);
            return 'return ' . var_export($var, true) . ';';
        }

        return 'return \\' . self::class . '::unsafeGetVariable(' . $this->index . ');';
    }

    public static function unsafeGetVariable(int $index)
    {
        return self::$variableStorage[$index];
    }

    public function __destruct()
    {
        if (!isset(self::$variableStorage[$this->index])) {
            return;
        }

        unset(self::$variableStorage[$this->index]);
    }

}